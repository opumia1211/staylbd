<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Models\Deposit;
use App\Models\Gateway;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use App\Traits\OrderConfirmation;

class DepositController extends Controller
{
    use OrderConfirmation;

    /**
     * Ensure deposits table exists and is queryable (not missing/corrupt).
     * Prevents 500 and "doesn't exist in engine" errors.
     */
    protected function ensureDepositsTable()
    {
        try {
            if (!Schema::hasTable('deposits')) {
                return view('admin.deposit.setup_required', ['pageTitle' => __('Setup Required')]);
            }
            // Verify table is actually queryable (fixes "doesn't exist in engine")
            DB::table('deposits')->limit(1)->count();
            return null;
        } catch (QueryException $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'deposits') || str_contains($msg, 'exist in engine')) {
                Log::warning('Deposits table missing or corrupt', ['message' => $msg]);
                return view('admin.deposit.setup_required', [
                    'pageTitle' => __('Setup Required'),
                    'corrupt'   => str_contains($msg, 'exist in engine'),
                ]);
            }
            throw $e;
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'deposits')) {
                return view('admin.deposit.setup_required', ['pageTitle' => __('Setup Required')]);
            }
            throw $e;
        }
    }

    protected function getGatewaysForFilter(): array
    {
        try {
            return Gateway::orderBy('name')->get(['id', 'name', 'alias', 'code'])->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    protected function getDepositSummary(): array
    {
        try {
            $base = Deposit::query();
            return [
                'successful' => (clone $base)->where('status', Status::PAYMENT_SUCCESS)->sum('amount'),
                'pending'    => (clone $base)->where('method_code', '>=', 1000)->where('status', Status::PAYMENT_PENDING)->sum('amount'),
                'rejected'   => (clone $base)->where('method_code', '>=', 1000)->where('status', Status::PAYMENT_REJECT)->sum('amount'),
                'initiated'  => (clone $base)->where('status', Status::PAYMENT_INITIATE)->sum('amount'),
                'count_successful' => (clone $base)->where('status', Status::PAYMENT_SUCCESS)->count(),
                'count_pending'    => (clone $base)->where('method_code', '>=', 1000)->where('status', Status::PAYMENT_PENDING)->count(),
                'count_rejected'   => (clone $base)->where('method_code', '>=', 1000)->where('status', Status::PAYMENT_REJECT)->count(),
                'count_initiated'  => (clone $base)->where('status', Status::PAYMENT_INITIATE)->count(),
                'count_approved'   => (clone $base)->where('method_code', '>=', 1000)->where('status', Status::PAYMENT_SUCCESS)->count(),
            ];
        } catch (\Throwable $e) {
            return [
                'successful' => 0, 'pending' => 0, 'rejected' => 0, 'initiated' => 0,
                'count_successful' => 0, 'count_pending' => 0, 'count_rejected' => 0,
                'count_initiated' => 0, 'count_approved' => 0,
            ];
        }
    }

    public function pending()
    {
        if ($response = $this->ensureDepositsTable()) {
            return $response;
        }
        try {
            $deposits = $this->depositData('pending');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'deposits')) {
                return view('admin.deposit.setup_required', ['pageTitle' => __('Setup Required')]);
            }
            throw $e;
        }
        $pageTitle = 'Pending Payments';
        $emptyMessage = __('No pending payments');
        $gateways = $this->getGatewaysForFilter();
        $summary = $this->getDepositSummary();
        return view('admin.deposit.log', compact('pageTitle', 'deposits', 'emptyMessage', 'gateways') + $summary);
    }

    public function approved()
    {
        if ($response = $this->ensureDepositsTable()) {
            return $response;
        }
        try {
            $deposits = $this->depositData('approved');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'deposits')) {
                return view('admin.deposit.setup_required', ['pageTitle' => __('Setup Required')]);
            }
            throw $e;
        }
        $pageTitle = 'Approved Payments';
        $emptyMessage = __('No approved payments');
        $gateways = $this->getGatewaysForFilter();
        $summary = $this->getDepositSummary();
        return view('admin.deposit.log', compact('pageTitle', 'deposits', 'emptyMessage', 'gateways') + $summary);
    }

    public function successful()
    {
        if ($response = $this->ensureDepositsTable()) {
            return $response;
        }
        try {
            $deposits = $this->depositData('successful');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'deposits')) {
                return view('admin.deposit.setup_required', ['pageTitle' => __('Setup Required')]);
            }
            throw $e;
        }
        $pageTitle = 'Successful Payments';
        $emptyMessage = __('No successful payments');
        $gateways = $this->getGatewaysForFilter();
        $summary = $this->getDepositSummary();
        return view('admin.deposit.log', compact('pageTitle', 'deposits', 'emptyMessage', 'gateways') + $summary);
    }

    public function rejected()
    {
        if ($response = $this->ensureDepositsTable()) {
            return $response;
        }
        try {
            $deposits = $this->depositData('rejected');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'deposits')) {
                return view('admin.deposit.setup_required', ['pageTitle' => __('Setup Required')]);
            }
            throw $e;
        }
        $pageTitle = 'Rejected Payments';
        $emptyMessage = __('No rejected payments');
        $gateways = $this->getGatewaysForFilter();
        $summary = $this->getDepositSummary();
        return view('admin.deposit.log', compact('pageTitle', 'deposits', 'emptyMessage', 'gateways') + $summary);
    }

    public function initiated()
    {
        if ($response = $this->ensureDepositsTable()) {
            return $response;
        }
        try {
            $deposits = $this->depositData('initiated');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'deposits')) {
                return view('admin.deposit.setup_required', ['pageTitle' => __('Setup Required')]);
            }
            throw $e;
        }
        $pageTitle = 'Initiated Payments';
        $emptyMessage = __('No initiated payments');
        $gateways = $this->getGatewaysForFilter();
        $summary = $this->getDepositSummary();
        return view('admin.deposit.log', compact('pageTitle', 'deposits', 'emptyMessage', 'gateways') + $summary);
    }

    public function deposit()
    {
        if ($response = $this->ensureDepositsTable()) {
            return $response;
        }
        try {
            $depositData = $this->depositData(null, true);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'deposits')) {
                return view('admin.deposit.setup_required', ['pageTitle' => __('Setup Required')]);
            }
            throw $e;
        }
        $deposits    = $depositData['data'];
        $summery     = $depositData['summery'];
        $pageTitle   = 'Payment History';
        $successful  = $summery['successful'];
        $pending     = $summery['pending'];
        $rejected    = $summery['rejected'];
        $initiated   = $summery['initiated'];
        $emptyMessage = __('No payments found');
        $gateways = $this->getGatewaysForFilter();
        $summary = $this->getDepositSummary();
        return view('admin.deposit.log', compact('pageTitle', 'deposits', 'successful', 'pending', 'rejected', 'initiated', 'emptyMessage', 'gateways') + $summary);
    }

    protected function depositData($scope = null, $summery = false)
    {
        if ($scope) {
            $deposits = Deposit::$scope()->with(['user', 'gateway']);
        } else {
            $deposits = Deposit::with(['user', 'gateway']);
        }

        $deposits = $deposits->searchable(['trx', 'user:username'])->dateFilter();

        $request = request();
        if ($request->method) {
            $method = Gateway::where('alias', $request->method)->first();
            if ($method) {
                $deposits = $deposits->where('method_code', $method->code);
            }
        }

        if (!$summery) {
            return $deposits->orderBy('id', 'desc')->paginate(getPaginate());
        }

        $successful = clone $deposits;
        $pending    = clone $deposits;
        $rejected   = clone $deposits;
        $initiated  = clone $deposits;

        $successfulSummery = $successful->where('status', Status::PAYMENT_SUCCESS)->sum('amount');
        $pendingSummery    = $pending->where('status', Status::PAYMENT_PENDING)->sum('amount');
        $rejectedSummery   = $rejected->where('status', Status::PAYMENT_REJECT)->sum('amount');
        $initiatedSummery  = $initiated->where('status', Status::PAYMENT_INITIATE)->sum('amount');

        return [
            'data'    => $deposits->orderBy('id', 'desc')->paginate(getPaginate()),
            'summery' => [
                'successful' => $successfulSummery,
                'pending'    => $pendingSummery,
                'rejected'   => $rejectedSummery,
                'initiated'  => $initiatedSummery,
            ],
        ];
    }

    public function details($id)
    {
        if ($response = $this->ensureDepositsTable()) {
            return $response;
        }
        $general = gs();
        $deposit = Deposit::where('id', $id)->with(['user', 'gateway'])->firstOrFail();
        $order   = $deposit->order_id
            ? Order::where('id', $deposit->order_id)->with(['orderDetail.product', 'coupon', 'shipping', 'deposit', 'user', 'orderDetail'])->first()
            : null;

        $pageTitle = ($deposit->user ? $deposit->user->username : 'N/A') . ' — ' . showAmount($deposit->amount) . ' ' . gs('cur_text');
        $details   = ($deposit->detail != null) ? json_encode($deposit->detail) : null;
        return view('admin.deposit.detail', compact('pageTitle', 'deposit', 'details', 'order', 'general'));
    }

    public function approve($id)
    {
        if ($response = $this->ensureDepositsTable()) {
            return $response;
        }
        $deposit = Deposit::where('id', $id)->where('status', Status::PAYMENT_PENDING)->firstOrFail();

        DB::transaction(function () use ($deposit) {
            PaymentController::userDataUpdate($deposit, true);
        });

        $notify[] = ['success', 'Deposit request approved successfully'];
        return to_route('admin.deposit.pending')->withNotify($notify);
    }

    public function reject(Request $request)
    {
        if ($response = $this->ensureDepositsTable()) {
            return $response;
        }
        $request->validate([
            'id'      => 'required|integer',
            'message' => 'required|string|max:255',
        ]);

        $deposit = Deposit::where('id', $request->id)->where('status', Status::PAYMENT_PENDING)->firstOrFail();

        DB::transaction(function () use ($deposit, $request) {
            $deposit->admin_feedback = $request->message;
            $deposit->status = Status::PAYMENT_REJECT;
            $deposit->save();

            if ($deposit->order_id) {
                $order = Order::find($deposit->order_id);
                if ($order) {
                    static::orderCancel($order);
                }
            }

            notify($deposit->user, 'DEPOSIT_REJECT', [
                'method_name'       => $deposit->gatewayCurrency()->name,
                'method_currency'   => $deposit->method_currency,
                'method_amount'    => showAmount($deposit->final_amo),
                'amount'            => showAmount($deposit->amount),
                'charge'            => showAmount($deposit->charge),
                'rate'              => showAmount($deposit->rate),
                'trx'               => $deposit->trx,
                'rejection_message' => $request->message,
                'link'              => route('user.transactions'),
            ]);
        });

        $notify[] = ['success', 'Deposit request rejected successfully'];
        return to_route('admin.deposit.pending')->withNotify($notify);
    }

    /** Export current list to CSV (scope + search/date/method filters). */
    public function export(Request $request)
    {
        if ($response = $this->ensureDepositsTable()) {
            return $response;
        }
        $scope = $request->get('scope', 'pending');
        $allowed = ['pending', 'approved', 'successful', 'rejected', 'initiated', 'all'];
        if (!in_array($scope, $allowed)) {
            $scope = 'pending';
        }
        try {
            if ($scope === 'all') {
                $deposits = Deposit::with(['user', 'gateway'])->searchable(['trx', 'user:username'])->dateFilter();
            } else {
                $deposits = Deposit::$scope()->with(['user', 'gateway'])->searchable(['trx', 'user:username'])->dateFilter();
            }
            if ($request->method) {
                $method = Gateway::where('alias', $request->method)->first();
                if ($method) {
                    $deposits = $deposits->where('method_code', $method->code);
                }
            }
            $deposits = $deposits->orderBy('id', 'desc')->limit(5000)->get();
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'deposits')) {
                return redirect()->route('admin.deposit.pending')->withNotify([['error', __('Setup Required')]]);
            }
            throw $e;
        }
        $general = gs();
        $filename = 'deposits-' . $scope . '-' . date('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($deposits, $general) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['ID', 'Date', 'TRX', 'User', 'Gateway', 'Amount', 'Charge', 'Total', 'Rate', 'Payable', 'Status']);
            foreach ($deposits as $d) {
                $status = $d->status == Status::PAYMENT_SUCCESS ? 'Success' : ($d->status == Status::PAYMENT_PENDING ? 'Pending' : ($d->status == Status::PAYMENT_REJECT ? 'Rejected' : 'Initiated'));
                fputcsv($out, [
                    $d->id,
                    $d->created_at ? $d->created_at->format('Y-m-d H:i') : '',
                    $d->trx,
                    $d->user ? $d->user->username : '',
                    $d->gateway ? $d->gateway->name : '',
                    showAmount($d->amount),
                    showAmount($d->charge),
                    showAmount($d->amount + $d->charge),
                    showAmount($d->rate),
                    showAmount($d->final_amo) . ' ' . $d->method_currency,
                    $status,
                ]);
            }
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }
}
