<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderChannel;
use App\Services\OrderChannelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class OrderChannelController extends Controller
{
    public function __construct(protected OrderChannelService $channelService)
    {
    }

    public function index()
    {
        if (!Schema::hasTable('order_channels')) {
            $notify[] = ['info', __('Run php artisan migrate to enable Order Channels.')];
            return redirect()->route('admin.orders.hub')->withNotify($notify);
        }

        $pageTitle = __('Order Channels');
        $channels = OrderChannel::latest('id')->paginate(getPaginate());

        return view('admin.orders.channels.index', compact('pageTitle', 'channels'));
    }

    public function create()
    {
        $pageTitle = __('Add Order Channel');
        $platforms = OrderChannel::platforms();
        $directions = OrderChannel::directions();
        $channel = new OrderChannel(['direction' => 'both', 'is_active' => true, 'platform' => 'woocommerce']);

        return view('admin.orders.channels.form', compact('pageTitle', 'platforms', 'directions', 'channel'));
    }

    public function store(Request $request)
    {
        $channel = $this->saveChannel($request, new OrderChannel());
        $notify[] = ['success', __('Order channel created successfully.')];

        return redirect()->route('admin.orders.channels.edit', $channel->id)->withNotify($notify);
    }

    public function edit(int $id)
    {
        $pageTitle = __('Edit Order Channel');
        $channel = OrderChannel::findOrFail($id);
        $platforms = OrderChannel::platforms();
        $directions = OrderChannel::directions();

        return view('admin.orders.channels.form', compact('pageTitle', 'platforms', 'directions', 'channel'));
    }

    public function update(Request $request, int $id)
    {
        $channel = OrderChannel::findOrFail($id);
        $this->saveChannel($request, $channel);
        $notify[] = ['success', __('Order channel updated successfully.')];

        return back()->withNotify($notify);
    }

    public function status(int $id)
    {
        $channel = OrderChannel::findOrFail($id);
        $channel->is_active = !$channel->is_active;
        $channel->save();
        $notify[] = ['success', __('Channel status updated.')];

        return back()->withNotify($notify);
    }

    public function regenerateToken(int $id)
    {
        $channel = OrderChannel::findOrFail($id);
        $channel->webhook_token = \Illuminate\Support\Str::random(48);
        $channel->save();
        $notify[] = ['success', __('Webhook URL regenerated.')];

        return back()->withNotify($notify);
    }

    protected function saveChannel(Request $request, OrderChannel $channel): OrderChannel
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'platform' => 'required|in:' . implode(',', array_keys(OrderChannel::platforms())),
            'direction' => 'required|in:import,export,both',
            'api_url' => 'nullable|url|max:500',
            'api_key' => 'nullable|string|max:2000',
            'webhook_secret' => 'nullable|string|max:255',
        ]);

        $channel->fill($request->only(['name', 'platform', 'direction', 'api_url', 'api_key']));
        $channel->is_active = $request->boolean('is_active');
        $settings = $channel->settings ?? [];
        if ($request->filled('webhook_secret')) {
            $settings['webhook_secret'] = $request->webhook_secret;
        }
        $channel->settings = $settings;
        $channel->save();

        return $channel;
    }
}
