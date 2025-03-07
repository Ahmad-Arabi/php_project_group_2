<?php
session_start();

// Example: Add two products to cart (this is just for testing)
$_SESSION['cart'] = [
    [
        'product_id' => 1,
        'name' => 'Test Product 1',
        'price' => 100.00,
        'quantity' => 1
    ],
    [
        'product_id' => 2,
        'name' => 'Test Product 2',
        'price' => 50.00,
        'quantity' => 2
    ]
];

echo "Two products added to cart for testing.";
exit;
?>