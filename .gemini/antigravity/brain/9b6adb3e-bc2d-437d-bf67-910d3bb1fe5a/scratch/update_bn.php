<?php
$path = 'core/resources/lang/bn.json';
$data = json_decode(file_get_contents($path), true);
if (!$data) {
    die("Error decoding JSON: " . json_last_error_msg());
}
$new = [
    'Gift Voucher' => 'গিফট ভাউচার',
    'Online Support' => 'অনলাইন সাপোর্ট',
    'Online Support 24/7' => 'অনলাইন সাপোর্ট ২৪/৭',
    'Money Back Guarantee' => 'মানি ব্যাক গ্যারান্টি',
    'Free Shipping' => 'ফ্রি শিপিং',
    'Shop Products' => 'শপ প্রোডাক্টস',
    'Latest Blog' => 'লেটেস্ট ব্লগ',
    'Become a Seller' => 'সেলার হন',
    'BECOME A SELLER' => 'সেলার হন',
    'ALL CATEGORIES' => 'সব ক্যাটাগরি',
    'All Categories' => 'সব ক্যাটাগরি',
    'Cart' => 'কার্ট',
    'Wishlist' => 'উইশলিস্ট',
    'Compare' => 'তুলনা',
    'Quick View' => 'কুইক ভিউ',
    'Account' => 'অ্যাকাউন্ট',
    'Search products, brands, and more' => 'পণ্য, ব্র্যান্ড এবং আরও অনুসন্ধান করুন',
    'Cash on Delivery available nationwide' => 'সারা দেশে ক্যাশ অন ডেলিভারি পাওয়া যাচ্ছে',
    'NAVIGATION' => 'নেভিগেশন',
    'VIEW DASHBOARD' => 'ড্যাশবোর্ড দেখুন',
    'CREATE NEW ACCOUNT' => 'নতুন একাউন্ট খুলুন',
    'LOGIN TO ACCOUNT' => 'লগইন করুন',
    'Home Page' => 'হোম পেজ',
    'Quick Links' => 'কুইক লিঙ্ক',
    'About Us' => 'আমাদের সম্পর্কে',
    'Contact Us' => 'যোগাযোগ করুন'
];
$data = array_merge($data, $new);
file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo "Successfully updated bn.json\n";
