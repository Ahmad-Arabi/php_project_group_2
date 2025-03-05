<?php
session_start();

// نفرغ السلة لضمان منتج واحد فقط
$_SESSION['cart'] = [];

// إضافة المنتج التجريبي
$_SESSION['cart'][] = [
    'product_id' => 1,
    'name' => 'Test Product',
    'price' => 100,
    'quantity' => 1
];

// توجيه إلى صفحة إتمام الطلب
header('Location: checkout.php');
exit;
