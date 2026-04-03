<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Gateway;
use App\Models\GatewayCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AutopayController extends Controller
{
    public function index()
    {
        $pageTitle = 'Autopay Gateways';
        $externalGateways = Gateway::autopayExternal()->with('singleCurrency')->orderBy('id', 'desc')->get();
        $messageGateways  = Gateway::autopayMessage()->with('singleCurrency')->orderBy('id', 'desc')->get();
        return view('admin.gateways.autopay.index', compact('pageTitle', 'externalGateways', 'messageGateways'));
    }

    // ---------- External (redirect to other website) ----------
    public function createExternal()
    {
        $pageTitle = 'Add External Payment Gateway';
        return view('admin.gateways.autopay.create_external', compact('pageTitle'));
    }

    public function storeExternal(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:190',
            'redirect_url'   => 'required|string|max:2000',
            'success_path'   => 'nullable|string|max:500',
            'cancel_path'    => 'nullable|string|max:500',
            'currency'       => 'required|string|max:20',
            'symbol'         => 'nullable|string|max:10',
            'rate'           => 'required|numeric|gt:0',
            'min_limit'      => 'required|numeric|gt:0',
            'max_limit'      => 'required|numeric|gt:0',
            'fixed_charge'   => 'required|numeric|gte:0',
            'percent_charge'  => 'required|numeric|between:0,100',
        ]);

        $last = Gateway::autopayExternal()->orderBy('code', 'desc')->first();
        $methodCode = $last ? (int) $last->code + 1 : 2000;

        $alias = Str::slug($request->name);
        $params = [
            'type'         => 'autopay_external',
            'redirect_url'  => $request->redirect_url,
            'success_path'  => $request->success_path ?: '/user/deposit/autopay-return',
            'cancel_path'  => $request->cancel_path ?: '/user/orders',
        ];

        $method = new Gateway();
        $method->code = $methodCode;
        $method->name = $request->name;
        $method->alias = $alias;
        $method->status = Status::ENABLE;
        $method->gateway_parameters = json_encode($params);
        $method->supported_currencies = [];
        $method->crypto = Status::DISABLE;
        $method->description = 'Redirect to external payment website. Placeholders: {amount}, {order_id}, {trx}, {user_id}';
        $method->form_id = 0;
        $method->save();

        $gc = new GatewayCurrency();
        $gc->name = $request->name;
        $gc->gateway_alias = $alias;
        $gc->currency = $request->currency;
        $gc->symbol = $request->input('symbol', '');
        $gc->method_code = $methodCode;
        $gc->min_amount = $request->min_limit;
        $gc->max_amount = $request->max_limit;
        $gc->fixed_charge = $request->fixed_charge;
        $gc->percent_charge = $request->percent_charge;
        $gc->rate = $request->rate;
        $gc->save();

        $notify[] = ['success', 'External payment gateway added. User will be redirected to the given URL; use return URL to confirm payment.'];
        return to_route('admin.gateway.autopay.index')->withNotify($notify);
    }

    public function editExternal($alias)
    {
        $method = Gateway::autopayExternal()->with('singleCurrency')->where('alias', $alias)->firstOrFail();
        $pageTitle = 'Edit External Gateway';
        $params = json_decode($method->gateway_parameters, true) ?: [];
        return view('admin.gateways.autopay.edit_external', compact('pageTitle', 'method', 'params'));
    }

    public function updateExternal(Request $request, $code)
    {
        $method = Gateway::autopayExternal()->where('code', $code)->firstOrFail();
        $request->validate([
            'name'           => 'required|string|max:190',
            'logo'           => 'nullable|image|mimes:png,jpeg,jpg,webp|max:2048',
            'redirect_url'   => 'required|string|max:2000',
            'success_path'   => 'nullable|string|max:500',
            'cancel_path'    => 'nullable|string|max:500',
            'currency'       => 'required|string|max:20',
            'symbol'         => 'nullable|string|max:10',
            'rate'           => 'required|numeric|gt:0',
            'min_limit'      => 'required|numeric|gt:0',
            'max_limit'      => 'required|numeric|gt:0',
            'fixed_charge'   => 'required|numeric|gte:0',
            'percent_charge' => 'required|numeric|between:0,100',
        ]);

        $method->name = $request->name;
        $method->alias = Str::slug($request->name);
        $method->gateway_parameters = json_encode([
            'type'         => 'autopay_external',
            'redirect_url'  => $request->redirect_url,
            'success_path'  => $request->success_path ?: '/user/deposit/autopay-return',
            'cancel_path'   => $request->cancel_path ?: '/user/orders',
        ]);
        if ($request->hasFile('logo')) {
            $path = public_path(getFilePath('gatewayLogo'));
            if (!is_dir($path)) {
                @mkdir($path, 0755, true);
            }
            try {
                $method->logo = fileUploader($request->logo, $path, getFileSize('gatewayLogo'), $method->logo);
            } catch (\Exception $e) {
                // keep existing logo on upload error
            }
        }
        $method->save();

        $gc = $method->singleCurrency;
        if ($gc) {
            $gc->name = $request->name;
            $gc->gateway_alias = $method->alias;
            $gc->currency = $request->currency;
            $gc->symbol = $request->input('symbol', '');
            $gc->min_amount = $request->min_limit;
            $gc->max_amount = $request->max_limit;
            $gc->fixed_charge = $request->fixed_charge;
            $gc->percent_charge = $request->percent_charge;
            $gc->rate = $request->rate;
            $gc->save();
        }

        $notify[] = ['success', 'External gateway updated.'];
        return to_route('admin.gateway.autopay.index')->withNotify($notify);
    }

    // ---------- Message (app/SMS bridge) ----------
    public function createMessage()
    {
        $pageTitle = 'Add Message / App Gateway';
        return view('admin.gateways.autopay.create_message', compact('pageTitle'));
    }

    public function storeMessage(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:190',
            'currency'      => 'required|string|max:20',
            'symbol'        => 'nullable|string|max:10',
            'rate'          => 'required|numeric|gt:0',
            'min_limit'     => 'required|numeric|gt:0',
            'max_limit'     => 'required|numeric|gt:0',
            'fixed_charge'  => 'required|numeric|gte:0',
            'percent_charge'=> 'required|numeric|between:0,100',
            'instructions'  => 'nullable|string|max:5000',
            'amount_regex'  => 'nullable|string|max:500',
            'trx_regex'     => 'nullable|string|max:500',
        ]);

        $last = Gateway::autopayMessage()->orderBy('code', 'desc')->first();
        $methodCode = $last ? (int) $last->code + 1 : 3000;

        $apiKey = 'apm_' . Str::random(32);
        $alias = Str::slug($request->name);
        $params = [
            'type'         => 'autopay_message',
            'api_key'      => $apiKey,
            'instructions' => $request->instructions,
            'amount_regex' => $request->amount_regex,
            'trx_regex'    => $request->trx_regex,
        ];

        $method = new Gateway();
        $method->code = $methodCode;
        $method->name = $request->name;
        $method->alias = $alias;
        $method->status = Status::ENABLE;
        $method->gateway_parameters = json_encode($params);
        $method->supported_currencies = [];
        $method->crypto = Status::DISABLE;
        $method->description = $request->instructions ?: 'Payment confirmed when app sends message to website API.';
        $method->form_id = 0;
        $method->save();

        $gc = new GatewayCurrency();
        $gc->name = $request->name;
        $gc->gateway_alias = $alias;
        $gc->currency = $request->currency;
        $gc->symbol = $request->input('symbol', '');
        $gc->method_code = $methodCode;
        $gc->min_amount = $request->min_limit;
        $gc->max_amount = $request->max_limit;
        $gc->fixed_charge = $request->fixed_charge;
        $gc->percent_charge = $request->percent_charge;
        $gc->rate = $request->rate;
        $gc->save();

        $notify[] = ['success', 'Message gateway added. Use the API URL and key in your app to send payment confirmations.'];
        return to_route('admin.gateway.autopay.message.edit', $method->alias)->withNotify($notify);
    }

    public function editMessage($alias)
    {
        $method = Gateway::autopayMessage()->with('singleCurrency')->where('alias', $alias)->firstOrFail();
        $pageTitle = 'Edit Message / App Gateway';
        $params = json_decode($method->gateway_parameters, true) ?: [];
        $apiUrl = url('/api/autopay/incoming-message');
        return view('admin.gateways.autopay.edit_message', compact('pageTitle', 'method', 'params', 'apiUrl'));
    }

    public function updateMessage(Request $request, $code)
    {
        $method = Gateway::autopayMessage()->where('code', $code)->firstOrFail();
        $request->validate([
            'name'           => 'required|string|max:190',
            'logo'           => 'nullable|image|mimes:png,jpeg,jpg,webp|max:2048',
            'currency'       => 'required|string|max:20',
            'symbol'         => 'nullable|string|max:10',
            'rate'           => 'required|numeric|gt:0',
            'min_limit'      => 'required|numeric|gt:0',
            'max_limit'      => 'required|numeric|gt:0',
            'fixed_charge'   => 'required|numeric|gte:0',
            'percent_charge' => 'required|numeric|between:0,100',
            'instructions'   => 'nullable|string|max:5000',
            'amount_regex'   => 'nullable|string|max:500',
            'trx_regex'      => 'nullable|string|max:500',
        ]);

        $params = json_decode($method->gateway_parameters, true) ?: [];
        $params['type'] = 'autopay_message';
        $params['instructions'] = $request->instructions;
        $params['amount_regex'] = $request->amount_regex;
        $params['trx_regex'] = $request->trx_regex;
        if (empty($params['api_key'])) {
            $params['api_key'] = 'apm_' . Str::random(32);
        }
        $method->name = $request->name;
        $method->alias = Str::slug($request->name);
        $method->gateway_parameters = json_encode($params);
        $method->description = $request->instructions ?: $method->description;
        if ($request->hasFile('logo')) {
            $path = public_path(getFilePath('gatewayLogo'));
            if (!is_dir($path)) {
                @mkdir($path, 0755, true);
            }
            try {
                $method->logo = fileUploader($request->logo, $path, getFileSize('gatewayLogo'), $method->logo);
            } catch (\Exception $e) {
                // keep existing logo on upload error
            }
        }
        $method->save();

        $gc = $method->singleCurrency;
        if ($gc) {
            $gc->name = $request->name;
            $gc->gateway_alias = $method->alias;
            $gc->currency = $request->currency;
            $gc->symbol = $request->input('symbol', '');
            $gc->min_amount = $request->min_limit;
            $gc->max_amount = $request->max_limit;
            $gc->fixed_charge = $request->fixed_charge;
            $gc->percent_charge = $request->percent_charge;
            $gc->rate = $request->rate;
            $gc->save();
        }

        $notify[] = ['success', 'Message gateway updated.'];
        return to_route('admin.gateway.autopay.index')->withNotify($notify);
    }

    public function regenerateApiKey($code)
    {
        $method = Gateway::autopayMessage()->where('code', $code)->firstOrFail();
        $params = json_decode($method->gateway_parameters, true) ?: [];
        $params['api_key'] = 'apm_' . Str::random(32);
        $method->gateway_parameters = json_encode($params);
        $method->save();
        $notify[] = ['success', 'API key regenerated. Update your app with the new key.'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Gateway::changeStatus($id);
    }
}
