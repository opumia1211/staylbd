<?php

namespace Database\Seeders;

use App\Models\Gateway;
use App\Models\GatewayCurrency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class GatewaySeeder extends Seeder
{
    /**
     * Seeds default payment gateways (bKash, Aamarpay, Nagad) if tables exist.
     */
    public function run(): void
    {
        if (!Schema::hasTable('gateways') || !Schema::hasTable('gateway_currencies')) {
            return;
        }

        $gateways = [
            [
                'code'   => 902,
                'name'   => 'bKash',
                'alias'  => 'Bkash',
                'params' => [
                    'sandbox'     => ['title' => 'Sandbox Mode', 'global' => true, 'value' => '1'],
                    'app_key'     => ['title' => 'App Key', 'global' => true, 'value' => ''],
                    'app_secret'  => ['title' => 'App Secret', 'global' => true, 'value' => ''],
                    'username'    => ['title' => 'Username', 'global' => true, 'value' => ''],
                    'password'    => ['title' => 'Password', 'global' => true, 'value' => ''],
                    'callback_url'=> ['title' => 'Callback URL', 'global' => true, 'value' => ''],
                ],
                'description' => 'bKash Tokenized Checkout',
            ],
            [
                'code'   => 903,
                'name'   => 'Aamarpay',
                'alias'  => 'Aamarpay',
                'params' => [
                    'store_id'     => ['title' => 'Store ID', 'global' => true, 'value' => ''],
                    'signature_key'=> ['title' => 'Signature Key', 'global' => true, 'value' => ''],
                    'sandbox'      => ['title' => 'Sandbox Mode', 'global' => true, 'value' => '1'],
                    'callback_url' => ['title' => 'Callback URL', 'global' => true, 'value' => ''],
                ],
                'description' => 'Aamarpay Payment Gateway',
            ],
            [
                'code'   => 904,
                'name'   => 'Nagad',
                'alias'  => 'Nagad',
                'params' => [
                    'merchant_id'    => ['title' => 'Merchant ID', 'global' => true, 'value' => ''],
                    'merchant_number'=> ['title' => 'Merchant Number', 'global' => true, 'value' => ''],
                    'private_key'    => ['title' => 'Private Key', 'global' => true, 'value' => ''],
                    'public_key'     => ['title' => 'Public Key', 'global' => true, 'value' => ''],
                    'sandbox'        => ['title' => 'Sandbox Mode', 'global' => true, 'value' => '1'],
                    'callback_url'   => ['title' => 'Callback URL', 'global' => true, 'value' => ''],
                ],
                'description' => 'Nagad Payment Gateway',
            ],
            [
                'code'   => 905,
                'name'   => 'PoysaPay',
                'alias'  => 'PoysaPay',
                'params' => [
                    'merchant_id'   => ['title' => 'Merchant ID', 'global' => true, 'value' => ''],
                    'api_key'       => ['title' => 'API Key', 'global' => true, 'value' => ''],
                    'secret_key'    => ['title' => 'Secret Key', 'global' => true, 'value' => ''],
                    'sandbox'       => ['title' => 'Sandbox Mode', 'global' => true, 'value' => '1'],
                    'base_url'      => ['title' => 'Live Base URL', 'global' => true, 'value' => 'https://pay.poysapay.com'],
                    'sandbox_url'   => ['title' => 'Sandbox Base URL', 'global' => true, 'value' => 'https://sandbox.poysapay.com'],
                    'callback_url'  => ['title' => 'Webhook URL', 'global' => true, 'value' => ''],
                ],
                'description' => 'PoysaPay (poysapay.com) — Custom Payment Gateway',
            ],
        ];

        foreach ($gateways as $g) {
            $gateway = Gateway::firstOrCreate(
                ['code' => $g['code']],
                [
                    'form_id'              => 0,
                    'name'                 => $g['name'],
                    'alias'                => $g['alias'],
                    'status'               => 1,
                    'gateway_parameters'   => json_encode($g['params']),
                    'supported_currencies' => json_encode(['BDT' => '৳']),
                    'crypto'               => 0,
                    'description'          => $g['description'],
                ]
            );

            GatewayCurrency::firstOrCreate(
                ['method_code' => $g['code'], 'currency' => 'BDT'],
                [
                    'name'             => $g['name'] . ' BDT',
                    'symbol'           => '৳',
                    'gateway_alias'    => $g['alias'],
                    'min_amount'       => 1,
                    'max_amount'       => 1000000,
                    'percent_charge'   => 0,
                    'fixed_charge'     => 0,
                    'rate'             => 1,
                    'gateway_parameter'=> json_encode(['instruction' => 'Pay with ' . $g['name']]),
                ]
            );
        }
    }
}
