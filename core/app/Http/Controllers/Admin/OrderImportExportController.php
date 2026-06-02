<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderImportExportController extends Controller
{
    public function index()
    {
        $pageTitle = __('Import / Export Orders');

        return view('admin.orders.import_export', compact('pageTitle'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
            'order_source' => 'required|string|max:40',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            $notify[] = ['error', __('Could not read CSV file.')];
            return back()->withNotify($notify);
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            $notify[] = ['error', __('CSV is empty.')];
            return back()->withNotify($notify);
        }

        $header = array_map(fn ($h) => strtolower(trim((string) $h)), $header);
        $imported = 0;
        $skipped = 0;
        $source = $request->order_source;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3) {
                $skipped++;
                continue;
            }
            $data = array_combine($header, array_pad($row, count($header), ''));
            if ($data === false) {
                $skipped++;
                continue;
            }

            $externalRef = trim((string) ($data['external_id'] ?? $data['order_id'] ?? ''));
            if ($externalRef !== '' && Order::where('order_source', $source)->where('external_order_ref', $externalRef)->exists()) {
                $skipped++;
                continue;
            }

            $total = (float) ($data['total'] ?? $data['amount'] ?? 0);
            $order = new Order();
            $order->user_type = 'guest';
            $order->guest_name = trim((string) ($data['customer'] ?? $data['name'] ?? 'Guest'));
            $order->guest_phone = trim((string) ($data['phone'] ?? ''));
            $order->guest_email = trim((string) ($data['email'] ?? ''));
            $order->guest_address = trim((string) ($data['address'] ?? ''));
            $order->order_no = $this->uniqueOrderNo();
            $order->external_order_ref = $externalRef ?: null;
            $order->order_source = $source;
            $order->subtotal = max(0, $total);
            $order->total = max(0, $total);
            $order->payment_status = Status::PAYMENT_PENDING;
            $order->order_status = Status::ORDER_PENDING;
            $order->save();
            $imported++;
        }

        fclose($handle);

        $notify[] = ['success', __(':imported imported, :skipped skipped.', ['imported' => $imported, 'skipped' => $skipped])];

        return back()->withNotify($notify);
    }

    protected function uniqueOrderNo(): string
    {
        do {
            $no = 'IMP' . strtoupper(Str::random(8));
        } while (Order::where('order_no', $no)->exists());

        return $no;
    }
}
