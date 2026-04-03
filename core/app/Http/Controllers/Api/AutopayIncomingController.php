<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\AutopayMessage;
use App\Models\Deposit;
use App\Models\Gateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutopayIncomingController extends Controller
{
    /**
     * Accept payment confirmation from app (e.g. Android) that captured SMS.
     * POST body: api_key, amount, trx_id (optional), sender (optional), message (optional).
     * Match pending deposit by method (api_key) and amount; approve and complete order.
     */
    public function incomingMessage(Request $request): JsonResponse
    {
        $request->validate([
            'api_key' => 'required|string',
            'amount'  => 'required|numeric|gt:0',
            'trx_id'  => 'nullable|string|max:190',
            'sender'  => 'nullable|string|max:100',
            'message' => 'nullable|string|max:2000',
        ]);

        $apiKey = $request->input('api_key');
        $amount = (float) $request->input('amount');
        $trxId = $request->input('trx_id');
        $sender = $request->input('sender');
        $rawMessage = $request->input('message');

        $gateway = Gateway::autopayMessage()->where('status', Status::ENABLE)->get()->first(function ($g) use ($apiKey) {
            $params = json_decode($g->gateway_parameters ?? '{}', true);
            return ($params['api_key'] ?? '') === $apiKey;
        });

        if (!$gateway) {
            Log::channel('single')->warning('Autopay API: invalid api_key');
            return response()->json(['success' => false, 'message' => 'Invalid api_key'], 400);
        }

        $methodCode = (int) $gateway->code;

        $deposit = Deposit::where('method_code', $methodCode)
            ->whereIn('status', [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])
            ->where(function ($q) use ($amount, $trxId) {
                $q->where('final_amo', '>=', $amount - 0.01)->where('final_amo', '<=', $amount + 0.01);
                if ($trxId) {
                    $q->orWhere('trx', $trxId);
                }
            })
            ->orderBy('id', 'desc')
            ->first();

        if (!$deposit) {
            try {
                AutopayMessage::create([
                    'method_code' => $methodCode,
                    'sender' => $sender,
                    'raw_message' => $rawMessage,
                    'amount' => $amount,
                    'trx_id' => $trxId,
                    'matched' => false,
                ]);
            } catch (\Throwable $e) {
                // table may not exist yet
            }
            return response()->json(['success' => false, 'message' => 'No matching pending payment'], 404);
        }

        try {
            DB::beginTransaction();
            PaymentController::userDataUpdate($deposit, true);
            try {
                AutopayMessage::create([
                    'method_code' => $methodCode,
                    'deposit_id' => $deposit->id,
                    'sender' => $sender,
                    'raw_message' => $rawMessage,
                    'amount' => $amount,
                    'trx_id' => $trxId,
                    'matched' => true,
                ]);
            } catch (\Throwable $e) {
                // table may not exist
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::channel('single')->error('Autopay API: confirm failed', ['deposit_id' => $deposit->id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Confirmation failed'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment confirmed',
            'deposit_id' => $deposit->id,
            'trx' => $deposit->trx,
            'order_id' => $deposit->order_id,
        ]);
    }
}
