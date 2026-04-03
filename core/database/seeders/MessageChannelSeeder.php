<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageChannelSeeder extends Seeder
{
    public function run(): void
    {
        $channels = [
            ['name' => 'Web Chat', 'slug' => 'web', 'config' => null, 'is_active' => true],
            ['name' => 'Telegram', 'slug' => 'telegram', 'config' => json_encode(['bot_token' => '', 'webhook_url' => '']), 'is_active' => true],
            ['name' => 'WhatsApp', 'slug' => 'whatsapp', 'config' => json_encode(['phone_id' => '', 'access_token' => '']), 'is_active' => true],
            ['name' => 'Facebook Messenger', 'slug' => 'facebook', 'config' => json_encode(['page_token' => '', 'verify_token' => '']), 'is_active' => true],
            ['name' => 'Instagram', 'slug' => 'instagram', 'config' => json_encode(['page_token' => '']), 'is_active' => true],
            ['name' => 'Email', 'slug' => 'email', 'config' => json_encode(['inbound_address' => '']), 'is_active' => true],
        ];

        foreach ($channels as $channel) {
            $channel['created_at'] = now();
            $channel['updated_at'] = now();
            DB::table('message_channels')->updateOrInsert(
                ['slug' => $channel['slug']],
                $channel
            );
        }
    }
}
