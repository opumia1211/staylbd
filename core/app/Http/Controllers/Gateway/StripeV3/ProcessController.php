<?php

namespace App\Http\Controllers\Gateway\StripeV3;

use App\Constants\Status;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\PaymentLedger;
use App\Services\PaymentEventLogger;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class ProcessController extends Controller
{

    public static function process($deposit)
    {
        $StripeAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $alias = $deposit->gateway->alias;

        \Stripe\Stripe::setApiKey("$StripeAcc->secret_key");
        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'name' => gs('site_name'),
                    'description' => 'Deposit  with Stripe',
                    'images' => [asset('assets/images/logoIcon/logo.png')],
                    'amount' => round($deposit->final_amo,2) * 100,
                    'currency' => "$deposit->method_currency",
                    'quantity' => 1,
                ]],
                'cancel_url' => route(gatewayRedirectUrl()),
                'success_url' => route(gatewayRedirectUrl(true)),
            ]);
        } catch (\Exception $e) {
            $send['error'] = true;
            $send['message'] = $e->getMessage();
            return json_encode($send);
        }

        $send['view'] = 'user.payment.'.$alias;
        $send['session'] = $session;
        $send['StripeJSAcc'] = $StripeAcc;
        $deposit->btc_wallet = json_decode(json_encode($session))->id;
        $deposit->save();
        return json_encode($send);
    }


    public function ipn(Request $request)
    {
        $StripeAcc = GatewayCurrency::where('gateway_alias', 'StripeV3')->orderBy('id', 'desc')->first();
        if ($StripeAcc === null) {
            \Log::error('StripeV3 IPN: Gateway not configured');
            http_response_code(503);
            exit();
        }

        $gateway_parameter = json_decode($StripeAcc->gateway_parameter);
        if (!$gateway_parameter || empty($gateway_parameter->secret_key ?? null) || empty($gateway_parameter->end_point ?? null)) {
            \Log::error('StripeV3 IPN: Gateway parameters invalid');
            http_response_code(503);
            exit();
        }

        \Stripe\Stripe::setApiKey($gateway_parameter->secret_key);

        // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = $gateway_parameter->end_point; // main
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];


        $event = null;
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
            PaymentEventLogger::logSignatureVerified('StripeV3', [
                'trx' => null,
                'webhook_payload' => json_decode($payload, true),
            ]);
        } catch(\UnexpectedValueException $e) {
            PaymentEventLogger::logSignatureFailed('StripeV3', [
                'notes' => 'Invalid payload: ' . $e->getMessage(),
                'webhook_payload' => [],
            ]);
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            PaymentEventLogger::logSignatureFailed('StripeV3', [
                'notes' => 'Invalid signature: ' . $e->getMessage(),
                'webhook_payload' => [],
            ]);
            http_response_code(400);
            exit();
        }

        // Handle the checkout.session.completed event (idempotency: prevent replay)
        if ($event->type == 'checkout.session.completed') {
            $idempotencyKey = $event->id ?? null;
            if ($idempotencyKey && PaymentEventLogger::isDuplicate('StripeV3', $idempotencyKey)) {
                PaymentEventLogger::logReplayAttempt('StripeV3', $idempotencyKey);
                http_response_code(200);
                exit();
            }

            $session = $event->data->object;
            $deposit = Deposit::where('btc_wallet',  $session->id)->orderBy('id', 'DESC')->first();

            if($deposit && $deposit->status==Status::PAYMENT_INITIATE){
                PaymentEventLogger::logStatusChange('StripeV3', $deposit->status, Status::PAYMENT_SUCCESS, [
                    'idempotency_key' => $idempotencyKey,
                    'deposit_id' => $deposit->id,
                    'order_id' => $deposit->order_id ?? null,
                    'trx' => $deposit->trx,
                    'gateway_response' => ['session_id' => $session->id ?? null],
                ]);
                PaymentLedger::appendEntry([
                    'order_id' => $deposit->order_id ?? null,
                    'transaction_id' => $deposit->id,
                    'deposit_id' => $deposit->id,
                    'gateway' => 'StripeV3',
                    'amount' => $deposit->final_amo ?? $deposit->amount,
                    'currency' => $deposit->method_currency ?? 'USD',
                    'status' => 'success',
                    'trx' => $deposit->trx,
                    'notes' => 'Stripe webhook checkout.session.completed',
                ]);
                PaymentController::userDataUpdate($deposit);
            }
        }
        http_response_code(200);
    }
}
