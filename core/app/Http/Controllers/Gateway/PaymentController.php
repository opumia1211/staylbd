<?php

namespace App\Http\Controllers\Gateway;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Traits\OrderConfirmation;

class PaymentController extends Controller
{
    use OrderConfirmation;

    public function deposit($orderId = null)
    {
        // If no orderId provided, redirect to orders page
        if (!$orderId) {
            return redirect()->route('user.order.index')->with('error', 'Order ID is required for payment');
        }

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
                            $gate->where('status', Status::ENABLE);
                        })->with('method')->get()->sortBy(function ($g) {
                            return $g->method && isset($g->method->sort_order) ? (int) $g->method->sort_order : 999;
                        })->values();

        $pageTitle = 'Deposit Methods';
        $order     = Order::where('user_id', auth()->id())->where('payment_status', Status::ORDER_PAYMENT_PENDING)->findOrFail($orderId);
        $pageTitle = 'Payment Methods';

        return view($this->activeTemplate . 'user.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'order'));
    }

    public function depositInsert(Request $request, $orderId)
    {
        $request->validate([
            'amount'   => 'required|numeric|gt:0',
            'gateway'  => 'required',
            'currency' => "required_if:gateway,!=,'deposit_wallet",
        ]);

        $order = Order::where('user_id', auth()->id())->where('payment_status', Status::ORDER_PAYMENT_PENDING)->findOrFail($orderId);
        $user  = auth()->user();

        // Double payment prevention: order already paid
        $alreadyPaid = Deposit::where('order_id', $orderId)->where('status', Status::PAYMENT_SUCCESS)->exists();
        if ($alreadyPaid) {
            $notify[] = ['error', __('This order is already paid.')];
            return redirect()->route('user.order.index')->withNotify($notify);
        }

        // Fraud detection: block if too many failed attempts from this IP
        if (\Illuminate\Support\Facades\Schema::hasTable('payment_fraud_attempts')) {
            $recentFailures = \App\Models\PaymentFraudAttempt::where('ip_address', $request->ip())
                ->where('created_at', '>=', now()->subHour())->count();
            if ($recentFailures >= 5) {
                $notify[] = ['error', __('Too many failed payment attempts. Please try again later or contact support.')];
                return back()->withNotify($notify);
            }
        }

        $gate  = GatewayCurrency::whereHas('method', function ($gate) {
                    $gate->where('status', Status::ENABLE);
                })->where('method_code', $request->gateway)->where('currency', $request->currency)->first();

        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        // Order payment: amount must equal order total (prevent fake / tampered payment)
        $orderTotal = (float) $order->total;
        $requestAmount = (float) $request->amount;
        $tolerance = 0.01;
        if (abs($requestAmount - $orderTotal) > $tolerance) {
            $notify[] = ['error', __('Payment amount must equal order total. Order total: ') . showAmount($orderTotal)];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please follow the limit'];
            return back()->withNotify($notify);
        }

        $charge      = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        $payable     = $request->amount + $charge;
        $finalAmount = $payable * $gate->rate;

        // Reuse existing pending deposit for this order if same gateway/amount (avoid duplicate trx)
        if ($orderId) {
            $existing = Deposit::where('user_id', $user->id)
                ->where('order_id', $orderId)
                ->where('status', Status::PAYMENT_INITIATE)
                ->where('method_code', $gate->method_code)
                ->where('final_amo', $finalAmount)
                ->orderBy('id', 'DESC')
                ->first();
            if ($existing) {
                session()->put('Track', $existing->trx);
                return to_route('user.deposit.confirm');
            }
        }

        // Ensure unique trx for each deposit (order_no can repeat for multiple payment attempts)
        $trx = getTrx(12);
        while (Deposit::where('trx', $trx)->exists()) {
            $trx = getTrx(12);
        }

        $data                  = new Deposit();
        $data->user_id         = $user->id;
        $data->order_id        = $orderId ?? 0;
        $data->method_code     = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount          = $request->amount;
        $data->charge          = $charge;
        $data->rate            = $gate->rate;
        $data->final_amo       = $finalAmount;
        $data->btc_amo         = 0;
        $data->btc_wallet      = "";
        $data->trx             = $trx;
        $data->save();

        activity_log(\App\Models\UserActivityLog::PAYMENT_ATTEMPT, 'Payment initiated: ' . $data->trx . ' (Order #' . $orderId . ')', 'deposit', $data->id);

        Log::channel('single')->info('Payment deposit initiated', [
            'order_id' => $orderId,
            'deposit_id' => $data->id,
            'trx' => $data->trx,
            'gateway' => $gate->method_code,
            'user_id' => $user->id,
        ]);

        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }

    public function depositConfirm()
    {
        $track   = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->with(['gateway', 'user'])->firstOrFail();

        if (!$deposit->gateway) {
            $notify[] = ['error', __('This payment method is no longer available. Please choose another.')];
            $orderId = $deposit->order_id ?: null;
            if ($orderId) {
                return redirect()->route('user.deposit.insert', $orderId)->withNotify($notify);
            }
            return to_route('user.order.index')->withNotify($notify);
        }

        // Autopay external: redirect to other payment website
        if ($deposit->method_code >= 2000 && $deposit->method_code < 3000) {
            $params = json_decode($deposit->gateway->gateway_parameters ?? '{}', true);
            $redirectUrl = $params['redirect_url'] ?? '';
            if ($redirectUrl) {
                $redirectUrl = str_replace(
                    ['{amount}', '{order_id}', '{trx}', '{user_id}'],
                    [$deposit->final_amo, $deposit->order_id, $deposit->trx, $deposit->user_id],
                    $redirectUrl
                );
                return redirect()->away($redirectUrl);
            }
        }

        // Autopay message: show "wait for confirmation" page (app will send message to API)
        if ($deposit->method_code >= 3000) {
            $pageTitle = 'Payment Pending';
            $method = $deposit->gatewayCurrency();
            $gateway = $deposit->gateway;
            $params = json_decode($gateway->gateway_parameters ?? '{}', true);
            $instructions = $params['instructions'] ?? __('Complete payment. Confirmation will appear automatically when we receive your transaction.');
            return view($this->activeTemplate . 'user.payment.autopay_message', compact('deposit', 'pageTitle', 'method', 'gateway', 'instructions'));
        }

        // Manual (1000–1999)
        if ($deposit->method_code >= 1000) {
            return to_route('user.deposit.manual.confirm');
        }

        $dirName = $deposit->gateway->alias ?? '';
        $new = $dirName ? (__NAMESPACE__ . '\\' . $dirName . '\\ProcessController') : '';

        if (!$dirName || !class_exists($new)) {
            Log::channel('single')->error('Payment gateway class not found', [
                'deposit_id' => $deposit->id,
                'order_id' => $deposit->order_id,
                'gateway_alias' => $dirName,
            ]);
            $notify[] = ['error', __('This payment method is not available right now. Please choose another.')];
            if ($deposit->order_id) {
                return redirect()->route('user.deposit.insert', $deposit->order_id)->withNotify($notify);
            }
            return to_route('user.order.index')->withNotify($notify);
        }

        $data = $new::process($deposit);
        $data = json_decode($data);

        if (isset($data->error)) {
            Log::channel('single')->error('Payment gateway process error', [
                'deposit_id' => $deposit->id,
                'order_id' => $deposit->order_id,
                'message' => $data->message ?? 'Unknown',
            ]);
            $notify[] = ['error', $data->message];
            return to_route('user.order.index')->withNotify($notify);
        }

        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if (@$data->session) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view($this->activeTemplate . $data->view, compact('data', 'pageTitle', 'deposit'));
    }

    public static function userDataUpdate($deposit, $isManual = null)
    {
        if ($deposit === null) {
            return;
        }
        if ($deposit->status == Status::PAYMENT_INITIATE || $deposit->status == Status::PAYMENT_PENDING) {
            $deposit->status = Status::PAYMENT_SUCCESS;
            $deposit->save();

            activity_log(\App\Models\UserActivityLog::PAYMENT_SUCCESS, 'Payment successful: ' . $deposit->trx, 'deposit', $deposit->id, $deposit->user_id);

            $user = User::find($deposit->user_id);
            if ($user === null) {
                return;
            }

            $gatewayCurrency = $deposit->gatewayCurrency();
            $methodName = $gatewayCurrency ? $gatewayCurrency->name : 'Payment Gateway';

            if (!$isManual) {
                $adminNotification            = new AdminNotification();
                $adminNotification->user_id   = $user->id;
                $adminNotification->title     = 'Payment successful via ' . $methodName;
                $adminNotification->click_url = urlPath('admin.deposit.details', $deposit->id);
                $adminNotification->save();
            }

            notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'method_name'     => $methodName,
                'method_currency' => $deposit->method_currency,
                'method_amount'   => showAmount($deposit->final_amo),
                'amount'          => showAmount($deposit->amount),
                'charge'          => showAmount($deposit->charge),
                'rate'            => showAmount($deposit->rate),
                'trx'             => $deposit->trx,
                'link'            => route('user.transactions'),
            ]);

            $user = User::find($deposit->user_id);
            if ($deposit->order_id) {
                $order = Order::find($deposit->order_id);
                if ($order) {
                    static::transactionCreate($order, $user, $deposit);
                }
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('payment_transactions')) {
                \App\Models\PaymentTransaction::recordFromDeposit($deposit);
            }
        }
    }

    public function autopayReturn(Request $request)
    {
        $trx = $request->get('trx') ?? session()->get('Track');
        if (!$trx) {
            $notify[] = ['error', 'Invalid return'];
            return to_route('user.order.index')->withNotify($notify);
        }
        $deposit = Deposit::where('trx', $trx)->whereIn('status', [Status::PAYMENT_INITIATE, Status::PAYMENT_PENDING])->with(['gateway', 'user'])->first();
        if (!$deposit || ($deposit->method_code < 2000 || $deposit->method_code >= 3000)) {
            $notify[] = ['error', 'Deposit not found or invalid'];
            return to_route('user.order.index')->withNotify($notify);
        }
        static::userDataUpdate($deposit, false);
        $notify[] = ['success', 'Payment successful'];
        if ($deposit->order_id) {
            return to_route('user.order.detail', $deposit->order_id)->withNotify($notify);
        }
        return to_route('user.transactions')->withNotify($notify);
    }

    public function manualDepositConfirm()
    {
        $track = session()->get('Track');
        $data  = Deposit::with(['gateway', 'user'])->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        if (!$data) {
            return to_route(gatewayRedirectUrl());
        }

        if ($data->method_code >= 1000 && $data->method_code < 2000) {
            $pageTitle = 'Deposit Confirm';
            $method    = $data->gatewayCurrency();
            $gateway   = $method->method;
            return view($this->activeTemplate . 'user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
        }

        return to_route(gatewayRedirectUrl());
    }

    public function manualDepositUpdate(Request $request)
    {
        $track = session()->get('Track');
        $data  = Deposit::with(['gateway', 'user', 'order'])->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();

        if (!$data) {
            return to_route(gatewayRedirectUrl());
        }

        $gatewayCurrency = $data->gatewayCurrency();
        $gateway         = $gatewayCurrency->method;
        $formData        = $gateway->form->form_data;

        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);

        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $data->user->id;
        $adminNotification->title     = 'Deposit request from ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        $order = Order::where('user_id', auth()->id())->where('payment_status', Status::ORDER_PAYMENT_PENDING)->findOrFail($data->order_id);
        $cartIds = session('checkout_cart_ids');
        $stockValidation = static::validateCartStockForOrder(auth()->id(), $cartIds);
        if (!$stockValidation['valid']) {
            static::notifyAdminStockOutAttempt(
                $stockValidation['out_of_stock_names'] ?? [],
                $stockValidation['out_of_stock_product_ids'] ?? []
            );
            $notify[] = ['error', $stockValidation['message'] ?: __('This product is currently out of stock. Please wait—we are restocking soon. You can keep it in your cart and try again later.')];
            return redirect()->route('user.cart')->withNotify($notify);
        }
        static::confirmOrder($order, $cartIds);

        notify($data->user, 'PAYMENT_REQUEST', [
            'method_name'     => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount'   => showAmount($data->final_amo),
            'amount'          => showAmount($data->amount),
            'charge'          => showAmount($data->charge),
            'rate'            => showAmount($data->rate),
            'trx'             => $data->trx,
            'order_no'        => $data->order->order_no,
            'link'            => route('user.transactions'),
        ]);

        $notify[] = ['success', 'Your payment request has been taken'];
        return to_route('user.transactions')->withNotify($notify);

    }
}
