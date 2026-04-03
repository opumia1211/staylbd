<?php

namespace App\Http\Controllers\Gateway\Paytm;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\Gateway\Paytm\PayTM;
use App\Models\Deposit;
use App\Models\PaymentEvent;
use App\Services\PaymentEventLogger;

class ProcessController extends Controller
{
    /*
     * PayTM Gateway
     */

    public static function process($deposit)
    {
        $PayTmAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);


        $alias = $deposit->gateway->alias;

        $val['MID'] = trim($PayTmAcc->MID);
        $val['WEBSITE'] = trim($PayTmAcc->WEBSITE);
        $val['CHANNEL_ID'] = trim($PayTmAcc->CHANNEL_ID);
        $val['INDUSTRY_TYPE_ID'] = trim($PayTmAcc->INDUSTRY_TYPE_ID);

        try {
            $checkSumHash = (new PayTM())->getChecksumFromArray($val, $PayTmAcc->merchant_key);
        } catch (\Exception $e) {
            $send['error'] = true;
            $send['message'] = $e->getMessage();
            return json_encode($send);
        }

        $val['ORDER_ID'] = $deposit->trx;
        $val['TXN_AMOUNT'] = round($deposit->final_amo,2);
        $val['CUST_ID'] = $deposit->user_id;
        $val['CALLBACK_URL'] = route('ipn.'.$alias);
        $val['CHECKSUMHASH'] = $checkSumHash;

        $send['val'] = $val;
        $send['view'] = 'user.payment.redirect';
        $send['method'] = 'post';
        $send['url'] = $PayTmAcc->transaction_url . "?orderid=" . $deposit->trx;

        return json_encode($send);
    }
    public function ipn()
    {
        $orderId = $_POST['ORDERID'] ?? null;
        if (!$orderId || !isset($_POST['CHECKSUMHASH'], $_POST['RESPCODE'], $_POST['TXNAMOUNT'])) {
            $notify[] = ['error', 'Invalid request'];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }

        $deposit = Deposit::where('trx', $orderId)->orderBy('id', 'DESC')->first();
        if ($deposit === null) {
            $notify[] = ['error', 'Deposit not found'];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }

        $gatewayCurrency = $deposit->gatewayCurrency();
        if ($gatewayCurrency === null) {
            $notify[] = ['error', 'Gateway error'];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }

        $PayTmAcc = json_decode($gatewayCurrency->gateway_parameter);
        $ptm = new PayTM();

        if ($ptm->verifychecksum_e($_POST, $PayTmAcc->merchant_key ?? '', $_POST['CHECKSUMHASH'] ?? '') !== "TRUE") {
            $notify[] = ['error', 'Security error!'];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }

        if (($_POST['RESPCODE'] ?? '') != "01") {
            $notify[] = ['error', $_POST['RESPMSG'] ?? 'Payment failed'];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }

        $txnId = $_POST['TXNID'] ?? $orderId;
        $idempotencyKey = 'paytm_' . $orderId . '_' . $txnId;
        if (PaymentEventLogger::isDuplicate('Paytm', $idempotencyKey)) {
            PaymentEventLogger::logReplayAttempt('Paytm', $idempotencyKey);
            $notify[] = ['success', 'Transaction is successful'];
            return to_route(gatewayRedirectUrl(true))->withNotify($notify);
        }

        $requestParamList = array("MID" => $PayTmAcc->MID, "ORDERID" => $_POST['ORDERID']);
        $StatusCheckSum = $ptm->getChecksumFromArray($requestParamList, $PayTmAcc->merchant_key);
        $requestParamList['CHECKSUMHASH'] = $StatusCheckSum;
        $responseParamList = $ptm->callNewAPI($PayTmAcc->transaction_status_url, $requestParamList);
        if (($responseParamList['STATUS'] ?? '') != 'TXN_SUCCESS' || ($responseParamList['TXNAMOUNT'] ?? 0) != ($_POST['TXNAMOUNT'] ?? 0) || $deposit->status != Status::PAYMENT_INITIATE) {
            $notify[] = ['error', 'It seems some issue in server to server communication. Kindly connect with administrator'];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }

        PaymentEvent::log('Paytm', 'ipn_processed', [
            'idempotency_key' => $idempotencyKey,
            'trx' => $deposit->trx,
            'deposit_id' => $deposit->id,
            'order_id' => $deposit->order_id,
        ]);
        PaymentController::userDataUpdate($deposit);
        $notify[] = ['success', 'Transaction is successful'];
        return to_route(gatewayRedirectUrl(true))->withNotify($notify);
    }
}
