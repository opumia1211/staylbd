<?php

namespace App\Http\Controllers\Gateway\Instamojo;

use App\Constants\Status;
use App\Models\Deposit;
use App\Models\PaymentEvent;
use App\Http\Controllers\Gateway\PaymentController;
use App\Services\PaymentEventLogger;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProcessController extends Controller
{

    /*
     * Instamojo Gateway
     */
    public static function process($deposit)
    {
        $instaMojoAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.instamojo.com/api/1.1/payment-requests/');
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                "X-Api-Key:$instaMojoAcc->api_key",
                "X-Auth-Token:$instaMojoAcc->auth_token"
            )
        );
        $payload = array(
            'purpose' => 'Payment to ' . gs('site_name'),
            'amount' => round($deposit->final_amo,2),
            'buyer_name' => $deposit->user->username,
            'redirect_url' => route(gatewayRedirectUrl()),
            'webhook' => route('ipn.'.$deposit->gateway->alias),
            'email' => $deposit->user->email,
            'send_email' => true,
            'allow_repeated_payments' => false
        );

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        $response = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($response);
        if (@$res->success) {
            if(!@$res->payment_request->id){
                $send['error'] = true;
                $send['message'] = "Response not given from API. Please re-check the API credentials.";
            }else{
                $deposit->btc_wallet = $res->payment_request->id;
                $deposit->save();
                $send['redirect'] = true;
                $send['redirect_url'] = $res->payment_request->longurl;
            }
        } else {
            $send['error'] = true;
            $send['message'] = "Credentials mismatch. Please contact with admin";
        }
        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $paymentRequestId = $_POST['payment_request_id'] ?? $request->payment_request_id ?? null;
        $paymentId = $_POST['payment_id'] ?? $request->payment_id ?? null;
        if (!$paymentRequestId || !isset($_POST['mac'], $_POST['status'])) {
            return;
        }

        $deposit = Deposit::where('btc_wallet', $paymentRequestId)->orderBy('id', 'DESC')->first();
        if ($deposit === null || $deposit->status != Status::PAYMENT_INITIATE) {
            return;
        }

        $gatewayCurrency = $deposit->gatewayCurrency();
        if ($gatewayCurrency === null) {
            return;
        }

        $instaMojoAcc = json_decode($gatewayCurrency->gateway_parameter);
        $imData = $_POST;
        $macSent = $imData['mac'] ?? '';
        unset($imData['mac']);
        ksort($imData, SORT_STRING | SORT_FLAG_CASE);
        $mac = hash_hmac("sha1", implode("|", $imData), $instaMojoAcc->salt ?? '');

        if ($macSent !== $mac || ($imData['status'] ?? '') !== "Credit") {
            return;
        }

        $idempotencyKey = 'instamojo_' . $paymentRequestId . '_' . ($paymentId ?? $paymentRequestId);
        if (PaymentEventLogger::isDuplicate('Instamojo', $idempotencyKey)) {
            PaymentEventLogger::logReplayAttempt('Instamojo', $idempotencyKey);
            return;
        }

        $deposit->detail = $request->all();
        $deposit->save();
        PaymentEvent::log('Instamojo', 'ipn_processed', [
            'idempotency_key' => $idempotencyKey,
            'trx' => $deposit->trx,
            'deposit_id' => $deposit->id,
            'order_id' => $deposit->order_id,
        ]);
        PaymentController::userDataUpdate($deposit);
    }
}
