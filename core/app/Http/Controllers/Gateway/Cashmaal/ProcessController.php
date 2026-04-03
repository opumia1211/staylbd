<?php

namespace App\Http\Controllers\Gateway\Cashmaal;

use App\Constants\Status;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\PaymentEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Services\PaymentEventLogger;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    /*
     * Cashmaal
     */

    public static function process($deposit)
    {
    	$cashmaal = json_decode($deposit->gatewayCurrency());
    	$param = json_decode($cashmaal->gateway_parameter);
        $val['pay_method'] = " ";
        $val['amount'] = getAmount($deposit->final_amo);
        $val['currency'] = $cashmaal->currency;
        $val['succes_url'] = route(gatewayRedirectUrl(true));
        $val['cancel_url'] = route(gatewayRedirectUrl());
        $val['client_email'] = auth()->user()->email;
        $val['web_id'] = $param->web_id;
        $val['order_id'] = $deposit->trx;
        $val['addi_info'] = "Deposit";
        $send['url'] = 'https://www.cashmaal.com/Pay/';
        $send['method'] = 'post';
        $send['view'] = 'user.payment.redirect';
        $send['val'] = $val;
        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $currency = $request->currency ?? $_POST['currency'] ?? null;
        if (!$currency) {
            $notify[] = ['error', 'Data invalid'];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }

        $gateway = GatewayCurrency::where('gateway_alias', 'Cashmaal')->where('currency', $currency)->first();
        if ($gateway === null) {
            $notify[] = ['error', 'Data invalid'];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }

        $param = json_decode($gateway->gateway_parameter);
        $IPN_key = $param->ipn_key ?? '';
        $web_id = $param->web_id ?? '';

        $orderId = $_POST['order_id'] ?? $request->order_id ?? null;
        if (!$orderId || !isset($_POST['status'], $_POST['currency'])) {
            $notify[] = ['error', 'Data invalid'];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }

        $deposit = Deposit::where('trx', $orderId)->orderBy('id', 'DESC')->first();
        if ($deposit === null) {
            $notify[] = ['error', 'Deposit not found'];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }

        $reqIpnKey = $request->ipn_key ?? $_POST['ipn_key'] ?? '';
        $reqWebId = $request->web_id ?? $_POST['web_id'] ?? '';
        if ($reqIpnKey !== $IPN_key || $reqWebId !== $web_id) {
            $notify[] = ['error', 'Data invalid'];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }

        $status = (int) ($request->status ?? $_POST['status'] ?? 0);
        if ($status == 2) {
            $notify[] = ['info', 'Payment in pending'];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }

        if ($status != 1) {
            $notify[] = ['error', 'Data invalid'];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }

        if ($deposit->status != Status::PAYMENT_INITIATE || ($_POST['currency'] ?? '') != $deposit->method_currency) {
            $notify[] = ['error', 'Payment failed'];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }

        $idempotencyKey = 'cashmaal_' . $orderId;
        if (PaymentEventLogger::isDuplicate('Cashmaal', $idempotencyKey)) {
            PaymentEventLogger::logReplayAttempt('Cashmaal', $idempotencyKey);
            $notify[] = ['success', 'Transaction is successful'];
            return to_route(gatewayRedirectUrl(true))->withNotify($notify);
        }

        PaymentEvent::log('Cashmaal', 'ipn_processed', [
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
