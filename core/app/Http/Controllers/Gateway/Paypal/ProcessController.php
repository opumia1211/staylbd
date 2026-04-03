<?php

namespace App\Http\Controllers\Gateway\Paypal;

use App\Constants\Status;
use App\Models\Deposit;
use App\Models\PaymentEvent;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\Controller;
use App\Lib\CurlRequest;
use App\Services\PaymentEventLogger;

class ProcessController extends Controller
{

    public static function process($deposit)
    {
        $general = gs();
        $paypalAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $val['cmd'] = '_xclick';
        $val['business'] = trim($paypalAcc->paypal_email);
        $val['cbt'] = $general->site_name;;
        $val['currency_code'] = "$deposit->method_currency";
        $val['quantity'] = 1;
        $val['item_name'] = "Payment To $general->site_name Account";
        $val['custom'] = "$deposit->trx";
        $val['amount'] = round($deposit->final_amo,2);
        $val['return'] = route(gatewayRedirectUrl(true));
        $val['cancel_return'] = route(gatewayRedirectUrl());
        $val['notify_url'] = route('ipn.'.$deposit->gateway->alias);
        $send['val'] = $val;
        $send['view'] = 'user.payment.redirect';
        $send['method'] = 'post';
        // $send['url'] = 'https://www.sandbox.paypal.com/'; // use for sandbod text
        $send['url'] = 'https://www.paypal.com/cgi-bin/webscr';
        return json_encode($send);
    }

    public function ipn()
    {
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2)
                $myPost[$keyval[0]] = urldecode($keyval[1]);
        }

        $req = 'cmd=_notify-validate';
        foreach ($myPost as $key => $value) {
            $value = urlencode(stripslashes($value));
            $req .= "&$key=$value";
            $details[$key] = $value;
        }

        // $paypalURL = "https://ipnpb.sandbox.paypal.com/cgi-bin/webscr?"; // use for sandbox text
        $paypalURL = "https://ipnpb.paypal.com/cgi-bin/webscr?";
        $url = $paypalURL . $req;
        $response = CurlRequest::curlContent($url);

        if ($response != "VERIFIED") {
            return;
        }

        $custom = $myPost['custom'] ?? $_POST['custom'] ?? null;
        if (!$custom || !isset($myPost['mc_gross'])) {
            return;
        }

        $deposit = Deposit::where('trx', $custom)->orderBy('id', 'DESC')->first();
        if ($deposit === null || $deposit->status != Status::PAYMENT_INITIATE) {
            return;
        }

        $txnId = $myPost['txn_id'] ?? $_POST['txn_id'] ?? null;
        $idempotencyKey = $txnId ? 'paypal_' . $txnId : 'paypal_custom_' . $custom;
        if (PaymentEventLogger::isDuplicate('Paypal', $idempotencyKey)) {
            PaymentEventLogger::logReplayAttempt('Paypal', $idempotencyKey);
            return;
        }

        $deposit->detail = $details ?? [];
        $deposit->save();

        $mcGross = $myPost['mc_gross'] ?? $_POST['mc_gross'] ?? null;
        if (!$mcGross || (float) $mcGross != round($deposit->final_amo, 2)) {
            return;
        }

        PaymentEvent::log('Paypal', 'ipn_processed', [
            'idempotency_key' => $idempotencyKey,
            'trx' => $deposit->trx,
            'deposit_id' => $deposit->id,
            'order_id' => $deposit->order_id,
        ]);
        PaymentController::userDataUpdate($deposit);
    }
}
