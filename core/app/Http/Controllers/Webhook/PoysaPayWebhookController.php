<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Constants\Status;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * PoysaPay Webhook — URL: https://yoursite.com/payment/webhook/poysapay
 * Receives payment status from PoysaPay; verifies signature and updates order.
 */
class PoysaPayWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        Log::channel('single')->info('PoysaPay webhook received', $request->except(['signature', 'secret_key']));

        $orderId = $request->get('order_id'); // our deposit trx
        $trx = $orderId ?: $request->get('trx');
        $status = strtolower($request->get('status', $request->get('payment_status', '')));
        $gatewayTrxId = $request->get('transaction_id') ?: $request->get('payment_id');

        if (!$trx) {
            return response()->json(['error' => 'Missing order_id/trx'], 400);
        }

        $deposit = Deposit::where('trx', $trx)->where('order_id', '>', 0)->orderBy('id', 'DESC')->first();
        if (!$deposit) {
            return response()->json(['error' => 'Deposit not found'], 404);
        }

        $params = $deposit->gatewayCurrency()->gateway_parameter;
        $config = is_string($params) ? json_decode($params, true) : (array) $params;
        $secretKey = $config['secret_key'] ?? '';

        if ($secretKey && $request->has('signature')) {
            $expected = hash_hmac('sha256', $trx . $status . ($gatewayTrxId ?? ''), $secretKey);
            if (!hash_equals($expected, $request->get('signature'))) {
                Log::channel('single')->warning('PoysaPay webhook signature mismatch', ['trx' => $trx]);
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        }

        if ($deposit->status == Status::PAYMENT_SUCCESS) {
            return response()->json(['message' => 'Already processed']);
        }

        if ($status === 'success' || $status === 'completed' || $status === 'paid') {
            $deposit->detail = array_merge((array) $deposit->detail, [
                'gateway_transaction_id' => $gatewayTrxId,
                'webhook_payload' => $request->except(['signature', 'secret_key']),
            ]);
            $deposit->save();
            PaymentController::userDataUpdate($deposit);
            return response()->json(['message' => 'OK']);
        }

        return response()->json(['message' => 'Status not success']);
    }
}
