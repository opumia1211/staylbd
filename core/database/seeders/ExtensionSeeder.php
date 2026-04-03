<?php

namespace Database\Seeders;

use App\Models\Extension;
use Illuminate\Database\Seeder;

class ExtensionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Adds professional/advanced extensions. Run once or when you want to add new extension types.
     */
    public function run(): void
    {
        $extensions = [
            [
                'act' => 'facebook-pixel',
                'name' => 'Facebook Pixel',
                'description' => 'Track conversions, build audiences and get detailed analytics. Paste your Pixel ID from Facebook Events Manager.',
                'image' => 'Facebook.png',
                'script' => "<!-- Facebook Pixel -->\n<script>\n!function(f,b,e,v,n,t,s)\n{if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};\nif(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';\nn.queue=[];t=b.createElement(e);t.async=!0;\nt.src=v;s=b.getElementsByTagName(e)[0];\ns.parentNode.insertBefore(t,s)}(window, document,'script',\n'https://connect.facebook.net/en_US/fbevents.js');\nfbq('init', '{{pixel_id}}');\nfbq('track', 'PageView');\n</script>\n<noscript><img height=\"1\" width=\"1\" style=\"display:none\" src=\"https://www.facebook.com/tr?id={{pixel_id}}&ev=PageView&noscript=1\"/></noscript>",
                'shortcode' => ['pixel_id' => ['title' => 'Pixel ID', 'value' => '']],
                'support' => 'na',
                'status' => 0,
            ],
            [
                'act' => 'gtag-manager',
                'name' => 'Google Tag Manager',
                'description' => 'Manage all your tracking and marketing tags from one place. Enter your GTM container ID (e.g. GTM-XXXXXXX).',
                'image' => 'google_analytics.png',
                'script' => "<!-- Google Tag Manager -->\n<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':\nnew Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],\nj=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=\n'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);\n})(window,document,'script','dataLayer','{{container_id}}');</script>",
                'shortcode' => ['container_id' => ['title' => 'GTM Container ID (e.g. GTM-XXXXXXX)', 'value' => '']],
                'support' => 'na',
                'status' => 0,
            ],
            [
                'act' => 'recaptcha3',
                'name' => 'Google reCAPTCHA v3',
                'description' => 'Invisible reCAPTCHA that scores user interaction. Get keys from Google reCAPTCHA admin (v3).',
                'image' => 'recaptcha3.png',
                'script' => "<script src=\"https://www.google.com/recaptcha/api.js?render={{site_key}}\"></script>\n<script>window.recaptchaSiteKeyV3='{{site_key}}';</script>",
                'shortcode' => [
                    'site_key' => ['title' => 'Site Key (reCAPTCHA v3)', 'value' => ''],
                    'secret_key' => ['title' => 'Secret Key (for server-side verify)', 'value' => ''],
                ],
                'support' => 'recaptcha.png',
                'status' => 0,
            ],
            [
                'act' => 'custom-code',
                'name' => 'Custom Code (HTML/JS)',
                'description' => 'Paste any custom HTML or JavaScript (e.g. chat widgets, tracking scripts). For advanced users only.',
                'image' => 'ganalytics.png',
                'script' => '{{custom_script}}',
                'shortcode' => ['custom_script' => ['title' => 'Custom HTML/JS', 'value' => '']],
                'support' => 'na',
                'status' => 0,
            ],
        ];

        foreach ($extensions as $data) {
            Extension::updateOrCreate(
                ['act' => $data['act']],
                array_diff_key($data, ['act' => 0])
            );
        }
    }
}
