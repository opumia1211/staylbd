<?php

namespace App\Constants;

class FileInfo
{

    /*
    |--------------------------------------------------------------------------
    | File Information
    |--------------------------------------------------------------------------
    |
    | This class basically contain the path of files and size of images.
    | All information are stored as an array. Developer will be able to access
    | this info as method and property using FileManager class.
    |
     */

    public function fileInfo()
    {
        $data['withdrawVerify'] = [
            'path' => 'assets/images/verify/withdraw',
        ];
        $data['depositVerify'] = [
            'path' => 'assets/images/verify/deposit',
        ];
        $data['verify'] = [
            'path' => 'assets/verify',
        ];
        $data['default'] = [
            'path' => 'assets/images/default.png',
        ];
        $data['withdrawMethod'] = [
            'path' => 'assets/images/withdraw/method',
            'size' => '800x800',
        ];
        $data['ticket'] = [
            'path' => 'assets/support',
        ];
        $data['logoIcon'] = [
            'path' => 'assets/images/logoIcon',
        ];
        // Main logo: 320×70 (min) to 460×100 (max) px - Professional e-commerce standard
        $data['logo_minimum'] = ['size' => '320x70'];
        $data['logo_maximum'] = ['size' => '460x100'];
        // Favicon: 32×32, 64×64, 180×180 (PWA/Mobile tab)
        $data['favicon'] = [
            'size' => '180x180',
            'sizes' => ['32x32', '64x64', '180x180'],
        ];
        $data['extensions'] = [
            'path' => 'assets/images/extensions',
            'size' => '36x36',
        ];
        $data['seo'] = [
            'path' => 'assets/images/seo',
            'size' => '1180x600',
        ];
        $data['userProfile'] = [
            'path' => 'assets/images/user/profile',
            'size' => '350x300',
        ];
        $data['adminProfile'] = [
            'path' => 'assets/admin/images/profile',
            'size' => '400x400',
        ];
        $data['brand'] = [
            'path' => 'assets/images/brand',
            'size' => '100x100',
        ];
        $data['category'] = [
            'path' => 'assets/images/category',
            'size' => '100x100',
        ];
        $data['productFile'] = [
            'path' => 'assets/images/product/file',
        ];
        $data['product'] = [
            'path' => 'assets/images/product',
            'size' => '500x500',
        ];
        $data['productGallery'] = [
            'path' => 'assets/images/product/gallery',
            'size' => '500x500',
        ];
        $data['productVideo'] = [
            'path' => 'assets/images/product/video',
        ];
        $data['topFeature'] = [
            'path' => 'assets/images/topFeature',
            'size' => '200x200',
        ];
        $data['gatewayLogo'] = [
            'path' => 'assets/images/gateways',
            'size' => '200x80',
        ];
        $data['appPromotionFile'] = [
            'path' => 'assets/files/frontend/apps',
        ];
        $data['popupAd'] = [
            'path' => 'assets/images/popupAd',
            'size' => '1200x800',
        ];
        $data['reviewImage'] = [
            'path' => 'assets/images/review',
            'size' => '800x600',
        ];
        return $data;
    }
}
