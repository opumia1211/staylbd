<?php

namespace App\Http\Controllers\Gateway\TZSMMPAY;

use App\Models\Deposit;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\Controller;
use App\Lib\CurlRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class ProcessController extends Controller
{

    public static function process($deposit)
    {
         $credentials = json_decode($deposit->gatewayCurrency()->gateway_parameter);
		$apiKey =  $credentials->api_key;
		$url = $credentials->create_url ?? null;
		if (!$url) {
			$send['error'] = 'TRUE';
			$send['message'] = 'Create URL not configured in gateway settings.';
			return json_encode($send);
		}

        $paymentData = [
            'api_key' => $apiKey,
            'cus_name' =>  $deposit->user->firstname . ' ' . $deposit->user->lastname,
            'cus_email' =>  'demo@gmail.com',
            'cus_number' => Session::get('Track'),
            'amount' => $deposit->amount,
            'currency' => $deposit->method_currency,
            'success_url' => route(gatewayRedirectUrl(true)),
            'cancel_url' => route(gatewayRedirectUrl()),
            'callback_url' => route('ipn.'.$deposit->gateway->alias),
        ];

        $options = [
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($paymentData),
            ],
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        // Decode the JSON response
        $responseData = json_decode($response, true);

        if ($responseData && $responseData['success']) {
            $send['redirect'] = 'TRUE';
            $send['redirect_url'] = $responseData['payment_url'];
        } else {
            $send['error'] = 'TRUE';
            $send['message'] = $responseData['messages'] ?? 'An error occurred.';
        }


        return json_encode($send);

    }

    public function ipn(Request $request)
    {

        try {
            // Validate the request inputs
            $validator = \Validator::make($request->all(), [
                'amount' => 'required|numeric',
                'cus_name' => 'required',
                'cus_email' => 'required|email',
                'cus_number' => 'required',
                'method_transaction_id' => 'required',
                'status' => 'required',
                'extra' => 'nullable|array',
            ]);

            // Return validation errors if any
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'messages' => implode(', ', $validator->errors()->all()),
                ]);
            }

            // Retrieve the track number from the request
            $track = $request->cus_number;

            // Check if a deposit exists for the provided track number
            $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
            if (!$deposit) {
                return response()->json([
                    'success' => false,
                    'messages' => 'Deposit not found for track number: ' . $track,
                ]);
            }

            // Check the payment status
            if ($request->status === 'Completed') {
                $deposit->detail = $request->all();
                $deposit->save();
                // Update user data for the successful payment
                PaymentController::userDataUpdate($deposit);
                return response()->json([
                    'success' => true,
                    'messages' => 'Deposit successful.',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'messages' => 'Payment status not completed.',
                ]);
            }
        } catch (\Exception $e) {
            // Handle any unexpected errors
            return response()->json([
                'success' => false,
                'messages' => 'An error occurred: ' . $e->getMessage(),
            ]);
        }
    }

}
