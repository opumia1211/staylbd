<?php

namespace App\Http\Controllers\Gateway\WINTERSMM;

use App\Models\Deposit;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class ProcessController extends Controller
{

	public static function process($deposit)
	{
		$credentials = json_decode($deposit->gatewayCurrency()->gateway_parameter);
		// WintersMM SMM panel style: API-KEY header; brand_key used as API key if provided
		$apiKeyFromConfig = $credentials->brand_key ?? ($credentials->api_key ?? null);
		$createUrl = $credentials->create_url ?? null;

		if (!$createUrl) {
			$send['error'] = 'TRUE';
			$send['message'] = 'Create URL not configured in gateway settings.';
			return json_encode($send);
		}
		if (!$apiKeyFromConfig) {
			$send['error'] = 'TRUE';
			$send['message'] = 'API Key (brand_key) not configured in gateway settings.';
			return json_encode($send);
		}

		$payload = [
			'cus_name' => trim(($deposit->user->firstname ?? '') . ' ' . ($deposit->user->lastname ?? '')) ?: ($deposit->user->username ?? 'Customer'),
			'cus_email' => $deposit->user->email ?? 'test@example.com',
			'amount' => (string) $deposit->amount,
			'success_url' => route(gatewayRedirectUrl(true)),
			'cancel_url' => route(gatewayRedirectUrl()),
			'webhook_url' => route('ipn.'.$deposit->gateway->alias),
			'metadata' => [
				'order_id' => Session::get('Track'),
			],
		];

		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $createUrl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode($payload),
			CURLOPT_HTTPHEADER => [
				'API-KEY: ' . $apiKeyFromConfig,
				'Content-Type: application/json',
				'Accept: application/json',
			],
		]);

		$response = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);

		if ($error) {
			$send['error'] = 'TRUE';
			$send['message'] = 'Curl error: ' . $error;
			return json_encode($send);
		}

		$responseData = json_decode($response, true);
		$paymentUrl = null;
		if (is_array($responseData)) {
			// common shapes: {success: true, payment_url: "..."} or {status: "success", data: {payment_url: "..."}}
			if (!empty($responseData['payment_url'])) {
				$paymentUrl = $responseData['payment_url'];
			} elseif (!empty($responseData['data']['payment_url'])) {
				$paymentUrl = $responseData['data']['payment_url'];
			} elseif ((!empty($responseData['status']) && strtolower((string)$responseData['status']) === 'success') && !empty($responseData['payment_url'])) {
				$paymentUrl = $responseData['payment_url'];
			}
		}
		if ($paymentUrl) {
			$send['redirect'] = 'TRUE';
			$send['redirect_url'] = $paymentUrl;
		} else {
			$send['error'] = 'TRUE';
			$send['message'] = ($responseData['message'] ?? $responseData['error'] ?? 'Unable to create payment.');
		}

		return json_encode($send);
	}

	public function ipn(Request $request)
	{
		try {
			$transactionId = $request->get('transaction_id');
			if (!$transactionId) {
				return response()->json([
					'success' => false,
					'messages' => 'Missing transaction_id',
				]);
			}

			// Find latest deposit by track if provided in query, else fallback by trx in metadata later
			$deposit = Deposit::where('trx', $request->get('track'))
				->orderBy('id', 'DESC')
				->first();

			$gatewayCredentials = json_decode(optional($deposit)->gatewayCurrency()->gateway_parameter ?? '{}');
			$brandKey = $gatewayCredentials->brand_key ?? null;
			$verifyUrl = $gatewayCredentials->verify_url ?? null;

			if (!$verifyUrl || !$brandKey) {
				return response()->json([
					'success' => false,
					'messages' => 'Brand Key or verify URL not configured.',
				]);
			}

			$ch = curl_init();
			curl_setopt_array($ch, [
				CURLOPT_URL => $verifyUrl,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => json_encode([
					'transaction_id' => $transactionId,
				]),
				CURLOPT_HTTPHEADER => [
					'BRAND-KEY: ' . $brandKey,
					'Content-Type: application/json',
				],
			]);

			$response = curl_exec($ch);
			$error = curl_error($ch);
			curl_close($ch);

			if ($error) {
				return response()->json([
					'success' => false,
					'messages' => 'Curl error: ' . $error,
				]);
			}

			$data = json_decode($response, true);
			if (!is_array($data) || empty($data['success'])) {
				return response()->json([
					'success' => false,
					'messages' => $data['message'] ?? 'Verification failed.',
				]);
			}

			// On success, bind deposit by track if not yet resolved
			if (!$deposit && !empty($data['metadata']['track'])) {
				$deposit = Deposit::where('trx', $data['metadata']['track'])
					->orderBy('id', 'DESC')
					->first();
			}

			if (!$deposit) {
				return response()->json([
					'success' => false,
					'messages' => 'Deposit not found for provided track.',
				]);
			}

			$deposit->detail = $data;
			$deposit->save();
			PaymentController::userDataUpdate($deposit);

			return response()->json([
				'success' => true,
				'messages' => 'Deposit successful.',
			]);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'messages' => 'An error occurred: ' . $e->getMessage(),
			]);
		}
	}
}


