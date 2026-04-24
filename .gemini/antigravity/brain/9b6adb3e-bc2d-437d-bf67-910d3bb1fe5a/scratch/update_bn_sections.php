<?php
$path = 'core/resources/lang/bn.json';
$data = json_decode(file_get_contents($path), true);
$new = [
    'Quick Deals' => 'কুইক ডিলস',
    'Hot Deals' => 'হট ডিলস',
    'Featured Products' => 'ফিচারড প্রোডাক্টস',
    'New Arrivals' => 'নতুন কালেকশন',
    'Trending Now' => 'ট্রেন্ডিং এখন',
    'Best Selling' => 'বেস্ট সেলিং',
    'Social proof' => 'সোশ্যাল প্রুফ',
    'Recommended For You' => 'আপনার জন্য পরামর্শ',
    'Categories' => 'ক্যাটাগরি',
    'Today\'s Deal' => 'আজকের ডিল',
    'Today’s Deal' => 'আজকের ডিল',
    'Deal Found yet' => 'এখনও কোন ডিল পাওয়া যায়নি'
];
$data = array_merge($data, $new);
file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo "Successfully updated bn.json for homepage sections\n";
