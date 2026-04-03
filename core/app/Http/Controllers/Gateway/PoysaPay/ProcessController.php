<?php

namespace App\Http\Controllers\Gateway\PoysaPay;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Constants\Status;
use App\Models\Deposit;
use Illuminate\Http\Request;

/**
 * PoysaPay (poysapay.com) — Custom Payment Gateway
 * Admin config: gateway_parameter (api_key, secret_key, merchant_id, sandbox, base_url, callback_url).
 */
class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $params = $deposit->gatewayCurrency()->gateway_parameter;
        $config = is_string($params) ? json_decode($params, true) : (array) $params;
        $apiKey = $config['api_key'] ?? '';
        $secretKey = $config['secret_key'] ?? '';
        $merchantId = $config['merchant_id'] ?? '';
        $baseUrl = rtrim($config['base_url'] ?? 'https://pay.poysapay.com', '/');
        $sandbox = !empty($config['sandbox']);

        if ($sandbox) {
            $baseUrl = rtrim($config['sandbox_url'] ?? 'https://sandbox.poysapay.com', '/');
        }

        if (!$apiKey || !$secretKey || !$merchantId) {
            return json_encode([
                'error' => true,
                'message' => __('PoysaPay is not configured. Set API Key, Secret Key and Merchant ID in Payment Gateways.'),
            ]);
        }

        $amount = (float) $deposit->final_amo;
        $orderId = $deposit->order_id;
        $trx = $deposit->trx;
        $callbackUrl = route('payment.webhook.poysapay');
        $successUrl = route('user.order.index');
        $cancelUrl = route('user.deposit.index', $orderId);

        $signString = $merchantId . $trx . number_format($amount, 2, '.', '') . $secretKey;
        $signature = hash_hmac('sha256', $signString, $secretKey);

        $redirectUrl = $baseUrl . '/payment/init?' . http_build_query([
            'merchant_id' => $merchantId,
            'order_id' => $trx,
            'amount' => $amount,
            'currency' => $deposit->method_currency ?? 'BDT',
            'callback_url' => $callbackUrl,
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'signature' => $signature,
        ]);

        return json_encode([
            'redirect' => true,
            'redirect_url' => $redirectUrl,
        ]);
    }

    /**
     * IPN/callback (GET or POST from gateway redirect) — same as webhook for browser redirect.
     */
    public function ipn(Request $request)
    {
        $track = $request->get('track') ?: $request->get('order_id');
        if (!$track) {
            return redirect()->route('user.order.index')->with('error', __('Invalid request.'));
        }

        $deposit = Deposit::where('trx', $track)->where('order_id', '>', 0)->orderBy('id', 'DESC')->first();
        if (!$deposit) {
            return redirect()->route('user.order.index')->with('error', __('Payment not found.'));
        }

        if ($deposit->status == Status::PAYMENT_SUCCESS) {
            return redirect()->route('user.order.index')->with('success', __('Payment already completed.'));
        }

        $status = $request->get('status');
        if (strtolower($status) === 'success' || $request->get('payment_status') === 'success') {
            $deposit->detail = array_merge((array) $deposit->detail, $request->except(['track', 'order_id']));
            $deposit->save();
            PaymentController::userDataUpdate($deposit);
            return redirect()->route('user.order.index')->with('success', __('Payment successful.'));
        }

        return redirect()->route('user.deposit.index', $deposit->order_id)->with('error', __('Payment failed or cancelled.'));
    }
}
