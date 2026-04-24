<?php
$path = 'core/resources/lang/bn.json';
$data = json_decode(file_get_contents($path), true);
$new = [
    'Flash Sale' => 'ফ্ল্যাশ সেল',
    'Save' => 'সঞ্চয়',
    'In Stock' => 'স্টক আছে',
    'Low Stock' => 'অল্প স্টক',
    'Out of Stock' => 'স্টক শেষ',
    'Add to wishlist' => 'উইশলিস্টে যোগ করুন',
    'Quick view' => 'দ্রুত দেখুন',
    'Compare' => 'তুলনা করুন',
    'Wishlist' => 'উইশলিস্ট',
    'Cart' => 'কার্ট'
];
$data = array_merge($data, $new);
file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo "Successfully updated bn.json for card labels\n";
