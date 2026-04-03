<?php

namespace App\Http\Controllers\Gateway\Skrill;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\Controller;
use App\Services\PaymentEventLogger;

class ProcessController extends Controller
{

    /*
     * Skrill Gateway
     */
    public static function process($deposit)
    {
        $general = gs();
        $skrillAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);


        $val['pay_to_email'] = trim($skrillAcc->pay_to_email);
        $val['transaction_id'] = "$deposit->trx";

        $val['return_url'] = route(gatewayRedirectUrl(true));
        $val['return_url_text'] = "Return $general->site_name";
        $val['cancel_url'] = route(gatewayRedirectUrl());
        $val['status_url'] = route('ipn.'.$deposit->gateway->alias);
        $val['language'] = 'EN';
        $val['amount'] = round($deposit->final_amo,2);
        $val['currency'] = "$deposit->method_currency";
        $val['detail1_description'] = "$general->site_name";
        $val['detail1_text'] = "Pay To $general->site_name";
        $val['logo_url'] = asset('assets/images/logoIcon/logo.png');

        $send['val'] = $val;
        $send['view'] = 'user.payment.redirect';
        $send['method'] = 'post';
        $send['url'] = 'https://www.moneybookers.com/app/payment.pl';
        return json_encode($send);
    }


    public function ipn()
    {
        $txId = isset($_POST['transaction_id']) ? (string) $_POST['transaction_id'] : null;
        if (!$txId || !isset($_POST['merchant_id'], $_POST['md5sig'], $_POST['mb_amount'], $_POST['mb_currency'], $_POST['status'], $_POST['pay_to_email'])) {
            return;
        }

        $deposit = Deposit::where('trx', $txId)->orderBy('id', 'DESC')->first();
        if ($deposit === null || $deposit->status != Status::PAYMENT_INITIATE) {
            return;
        }

        $gatewayCurrency = $deposit->gatewayCurrency();
        if ($gatewayCurrency === null) {
            return;
        }

        $idempotencyKey = 'skrill_' . $txId;
        if (PaymentEventLogger::isDuplicate('Skrill', $idempotencyKey)) {
            PaymentEventLogger::logReplayAttempt('Skrill', $idempotencyKey);
            return;
        }

        $skrillrAcc = json_decode($gatewayCurrency->gateway_parameter);
        $concatFields = $_POST['merchant_id']
            . $_POST['transaction_id']
            . strtoupper(md5($skrillrAcc->secret_key ?? ''))
            . $_POST['mb_amount']
            . $_POST['mb_currency']
            . $_POST['status'];

        if (strtoupper(md5($concatFields)) != ($_POST['md5sig'] ?? '') || (int) ($_POST['status'] ?? 0) != 2 || ($_POST['pay_to_email'] ?? '') != ($skrillrAcc->pay_to_email ?? '')) {
            return;
        }

        \App\Models\PaymentEvent::log('Skrill', 'ipn_processed', [
            'idempotency_key' => $idempotencyKey,
            'trx' => $deposit->trx,
            'deposit_id' => $deposit->id,
            'order_id' => $deposit->order_id,
        ]);
        PaymentController::userDataUpdate($deposit);
    }
}
