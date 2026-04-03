<?php

namespace App\Http\Controllers\Gateway\Bkash;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Lib\CurlRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ProcessController extends Controller
{
    private $base_url;
    private $app_key;
    private $app_secret;
    private $username;
    private $password;

    public function __construct()
    {
        // Credentials are loaded from database per deposit; keep empty here
        $this->base_url = '';
        $this->app_key = '';
        $this->app_secret = '';
        $this->username = '';
        $this->password = '';
    }

    public static function process($deposit)
    {
        $bkashAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);

        $processController = new self();
        $processController->base_url = isset($bkashAcc->base_url) ? self::ensureTokenizedSuffix($bkashAcc->base_url) : '';
        $processController->app_key = $bkashAcc->app_key ?? '';
        $processController->app_secret = $bkashAcc->app_secret ?? '';
        $processController->username = $bkashAcc->username ?? '';
        $processController->password = $bkashAcc->password ?? '';

        // Validate configuration from DB
        if (!$processController->base_url || !$processController->app_key || !$processController->app_secret || !$processController->username || !$processController->password) {
            return json_encode([
                'error' => true,
                'message' => 'bKash gateway is not configured. Please set base_url, app_key, app_secret, username, password.'
            ]);
        }

        // Create payment with bKash API
        $response = $processController->create($deposit);
        $responseData = json_decode($response, true);

        if (isset($responseData['bkashURL'])) {
            $send['redirect'] = true;
            $send['redirect_url'] = $responseData['bkashURL'];
        } else {
            $send['error'] = true;
            $send['message'] = $responseData['statusMessage']
                ?? $responseData['message']
                ?? 'Failed to create bKash payment';
            if (isset($responseData['statusCode'])) {
                $send['message'] .= ' (Code: ' . $responseData['statusCode'] . ')';
            }
        }

        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $track = $request->track;
        $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();

        if ($deposit->status == Status::PAYMENT_SUCCESS) {
            $notify[] = ['error', 'Invalid request.'];
            return to_route('user.deposit.index', $deposit->order_id)->withNotify($notify);
        }

        $bkashAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $this->base_url = isset($bkashAcc->base_url) ? self::ensureTokenizedSuffix($bkashAcc->base_url) : '';
        $this->app_key = $bkashAcc->app_key ?? '';
        $this->app_secret = $bkashAcc->app_secret ?? '';
        $this->username = $bkashAcc->username ?? '';
        $this->password = $bkashAcc->password ?? '';

        if (!$this->base_url || !$this->app_key || !$this->app_secret || !$this->username || !$this->password) {
            $notify[] = ['error', 'bKash gateway is not configured.'];
            return to_route('user.deposit.index', $deposit->order_id)->withNotify($notify);
        }

        // Handle bKash callback
        if (isset($request->status) && $request->status == 'failure') {
            $notify[] = ['error', 'Payment failed'];
            return to_route('user.deposit.index', $deposit->order_id)->withNotify($notify);
        } elseif (isset($request->status) && $request->status == 'cancel') {
            $notify[] = ['error', 'Payment cancelled'];
            return to_route('user.deposit.index', $deposit->order_id)->withNotify($notify);
        } else {
            // Execute payment
            $response = $this->execute($request->paymentID);
            $arr = json_decode($response, true);

            if (array_key_exists("statusCode", $arr) && $arr['statusCode'] != '0000') {
                $notify[] = ['error', 'Payment failed'];
                return to_route('user.deposit.index', $deposit->order_id)->withNotify($notify);
            }

            if (array_key_exists("message", $arr)) {
                sleep(1);
                $queryResponse = $this->query($request->paymentID);
                $queryArr = json_decode($queryResponse, true);

                if (isset($queryArr['statusCode']) && $queryArr['statusCode'] == '0000') {
                    $deposit->detail = ['paymentID' => $request->paymentID, 'trxID' => $queryArr['trxID'] ?? ''];
                    $deposit->save();

                    PaymentController::userDataUpdate($deposit);

                    $notify[] = ['success', 'Payment successful'];
                    return to_route(gatewayRedirectUrl(true))->withNotify($notify);
                }
            }
        }

        $notify[] = ['error', 'Payment failed'];
        return to_route('user.deposit.index', $deposit->order_id)->withNotify($notify);
    }

    public function authHeaders()
    {
        return array(
            'Content-Type:application/json',
            'Authorization:' . $this->grant(),
            'X-APP-Key:' . $this->app_key
        );
    }

    public function curlWithBody($url, $header, $method, $body_data_json)
    {
        $curl = curl_init($this->buildUrl($url));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body_data_json);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    private function buildUrl($path)
    {
        $base = rtrim($this->base_url, '/');
        $path = ltrim($path, '/');
        return $base . '/' . $path;
    }

    private static function ensureTokenizedSuffix($url)
    {
        if (!$url) return 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/';
        $normalized = rtrim($url, '/') . '/';
        // If '/tokenized/' isn't present at the end, append it
        if (substr($normalized, -11) !== 'tokenized/') {
            $normalized .= 'tokenized/';
        }
        return $normalized;
    }

    public function grant()
    {
        $header = array(
            'Content-Type:application/json',
            'username:' . $this->username,
            'password:' . $this->password
        );

        $body_data = array('app_key' => $this->app_key, 'app_secret' => $this->app_secret);
        $body_data_json = json_encode($body_data);

        $response = $this->curlWithBody('checkout/token/grant', $header, 'POST', $body_data_json);

        $token = json_decode($response)->id_token ?? '';

        return $token;
    }

    public function create($deposit)
    {
        // Sanitize amount: bKash requires >= 1 and up to 2 decimal places
        $amountRaw = (float) $deposit->final_amo;
        $amount = number_format($amountRaw, 2, '.', '');
        if ((float)$amount < 1) {
            return json_encode([
                'statusCode'    => '2006',
                'statusMessage' => 'Invalid amount. Must be >= 1 BDT',
            ]);
        }
        if (strtoupper($deposit->method_currency) !== 'BDT') {
            return json_encode([
                'statusCode'    => '2006',
                'statusMessage' => 'Invalid currency. Only BDT supported in bKash',
            ]);
        }
        $orderId = $deposit->order_id;

        $header = $this->authHeaders();
        $body_data = array(
            'mode' => '0011',
            'payerReference' => ' ',
            'callbackURL' => route('ipn.Bkash') . '?track=' . $deposit->trx,
            'amount' => (string) $amount,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $deposit->trx
        );
        $body_data_json = json_encode($body_data);

        // Old implementation used base_url ending with /tokenized/ and appended 'checkout/create'
        $response = $this->curlWithBody('checkout/create', $header, 'POST', $body_data_json);

        return $response;
    }

    public function execute($paymentID)
    {
        $header = $this->authHeaders();

        $body_data = array(
            'paymentID' => $paymentID
        );
        $body_data_json = json_encode($body_data);

        $response = $this->curlWithBody('checkout/execute', $header, 'POST', $body_data_json);

        return $response;
    }

    public function query($paymentID)
    {
        $header = $this->authHeaders();

        $body_data = array(
            'paymentID' => $paymentID,
        );
        $body_data_json = json_encode($body_data);

        $response = $this->curlWithBody('checkout/payment/status', $header, 'POST', $body_data_json);
        return $response;
    }
}
