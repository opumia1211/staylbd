<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactChannelIntegration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ContactChannelController extends Controller
{
    public function index()
    {
        $pageTitle = __('Contact Channels');

        if (!Schema::hasTable('contact_channel_integrations')) {
            return view('admin.contact_channels.setup_required', compact('pageTitle'));
        }

        $channels = ContactChannelIntegration::orderBy('channel')->get();
        $grouped = $channels->groupBy('channel');

        $webhookUrls = [
            'whatsapp' => route('webhook.contact.whatsapp'),
            'telegram'  => route('webhook.contact.telegram'),
        ];

        return view('admin.contact_channels.index', compact('pageTitle', 'channels', 'grouped', 'webhookUrls'));
    }

    /**
     * Run migration to create contact_channel_integrations table (one-click setup).
     * In production we rely on master staylbd_wintersm.sql only – do not run migrations.
     */
    public function runMigration(Request $request)
    {
        if (Schema::hasTable('contact_channel_integrations')) {
            $notify[] = ['success', __('Table already exists.')];
            return redirect()->route('admin.contact.channels.index')->withNotify($notify);
        }

        if (config('app.env') === 'production') {
            $notify[] = ['error', __('Database table missing. Please import the master SQL file (staylbd_wintersm.sql) in cPanel. No migrations are run in production.')];
            return redirect()->route('admin.contact.channels.index')->withNotify($notify);
        }

        try {
            Artisan::call('migrate', [
                '--path'  => 'database/migrations/2026_02_11_000001_create_contact_channel_integrations_table.php',
                '--force' => true,
            ]);
            $output = trim(Artisan::output());
            $notify[] = ['success', __('Table created successfully.') . ' ' . $output];
            return redirect()->route('admin.contact.channels.index')->withNotify($notify);
        } catch (\Throwable $e) {
            $notify[] = ['error', __('Migration failed: ') . $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('contact_channel_integrations')) {
            $notify[] = ['error', __('Database table missing. Please run the setup SQL first.')];
            return back()->withNotify($notify);
        }

        $request->validate([
            'channel' => 'required|in:whatsapp,telegram,email',
            'name' => 'nullable|string|max:80',
        ]);

        $channel = $request->channel;
        $payload = $this->validateChannelPayload($channel, $request);

        $integration = ContactChannelIntegration::firstOrNew(['channel' => $channel]);
        $integration->name = $request->name ?: Str::title($channel);
        $integration->is_active = $request->boolean('is_active', true);
        $integration->is_primary = $request->boolean('is_primary', $integration->is_primary ?? false);

        $settings = array_merge($integration->settings ?? [], $payload['settings']);
        $integration->settings = $settings;

        $authMeta = $integration->auth_meta ?? [];
        foreach ($payload['secrets'] as $key => $value) {
            if ($value) {
                $authMeta[$key] = encrypt($value);
            }
        }
        $integration->auth_meta = $authMeta;

        $integration->save();

        if ($integration->is_primary) {
            ContactChannelIntegration::where('id', '!=', $integration->id)->update(['is_primary' => false]);
        }

        Cache::forget('contact_channels.active');

        $notify[] = ['success', __('Channel configuration updated successfully.')];
        return back()->withNotify($notify);
    }

    public function toggle(ContactChannelIntegration $integration)
    {
        $integration->is_active = !$integration->is_active;
        $integration->save();
        Cache::forget('contact_channels.active');

        $notify[] = ['success', __('Channel ":channel" is now :status.', [
            'channel' => Str::title($integration->channel),
            'status' => $integration->is_active ? __('online') : __('offline'),
        ])];

        return back()->withNotify($notify);
    }

    public function test(ContactChannelIntegration $integration)
    {
        try {
            $this->runConnectivityTest($integration);
            $integration->last_synced_at = now();
            $integration->last_error_at = null;
            $integration->last_error_message = null;
            $integration->save();

            $notify[] = ['success', __('Connection verified for :channel.', ['channel' => Str::title($integration->channel)])];
        } catch (\Throwable $e) {
            $integration->last_error_at = now();
            $integration->last_error_message = Str::limit($e->getMessage(), 255);
            $integration->save();

            $notify[] = ['error', __('Connection failed: :message', ['message' => $e->getMessage()])];
        }

        return back()->withNotify($notify);
    }

    protected function validateChannelPayload(string $channel, Request $request): array
    {
        $settings = [];
        $secrets = [];

        if ($channel === 'whatsapp') {
            $data = $request->validate([
                'whatsapp_phone_number' => 'required|string|max:32',
                'whatsapp_business_id' => 'required|string|max:64',
                'whatsapp_phone_id' => 'required|string|max:64',
                'whatsapp_verify_token' => 'required|string|max:64',
                'whatsapp_access_token' => 'nullable|string|max:255',
            ]);

            $settings = [
                'phone_number' => preg_replace('/\D+/', '', $data['whatsapp_phone_number']),
                'business_id' => $data['whatsapp_business_id'],
                'phone_number_id' => $data['whatsapp_phone_id'],
                'verify_token' => $data['whatsapp_verify_token'],
            ];
            $secrets['access_token'] = $data['whatsapp_access_token'] ?? null;
        } elseif ($channel === 'telegram') {
            $data = $request->validate([
                'telegram_bot_name' => 'required|string|max:80',
                'telegram_bot_token' => 'required|string|max:255',
                'telegram_webhook_secret' => 'nullable|string|max:120',
            ]);

            $settings = [
                'bot_name' => $data['telegram_bot_name'],
            ];
            $secrets['bot_token'] = $data['telegram_bot_token'];
            $secrets['webhook_secret'] = $data['telegram_webhook_secret'] ?? null;
        } else {
            $data = $request->validate([
                'email_forward_address' => 'required|email',
                'email_subject_prefix' => 'nullable|string|max:80',
            ]);

            $settings = [
                'forward_address' => strtolower($data['email_forward_address']),
                'subject_prefix' => $data['email_subject_prefix'] ?? null,
            ];
        }

        return compact('settings', 'secrets');
    }

    protected function runConnectivityTest(ContactChannelIntegration $integration): void
    {
        if ($integration->channel === 'telegram') {
            $token = $integration->getSecret('bot_token');
            if (!$token) {
                throw new \RuntimeException(__('Missing Telegram bot token.'));
            }
            Http::timeout(5)->get("https://api.telegram.org/bot{$token}/getMe")->throw();
            return;
        }

        if ($integration->channel === 'whatsapp') {
            $token = $integration->getSecret('access_token');
            $phoneId = $integration->getSetting('phone_number_id');
            if (!$token || !$phoneId) {
                throw new \RuntimeException(__('WhatsApp phone number ID or token missing.'));
            }
            Http::timeout(5)
                ->withToken($token)
                ->get("https://graph.facebook.com/v20.0/{$phoneId}")
                ->throw();
            return;
        }
    }
}
