<?php

session_start();
require_once '../../../includes/database/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Initialize response array
$response = [
    'success' => true,
    'cart' => [],
    'total_price' => 0,
];
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

// Read cart from session
$cart = $_SESSION['cart'] ?? [];

// If cart is empty, return empty response
if (empty($cart)) {
    $response['success'] = false;
    $response['message'] = 'Your cart is empty.';
    echo json_encode($response);
    exit;
}

// Prepare to fetch product details from database
$productIds = array_column($cart, 'product_id');
if (empty($productIds)) {
    $response['success'] = false;
    $response['message'] = 'Invalid cart data.';
    echo json_encode($response);
    exit;
}

// Fetch products from database
$placeholders = implode(',', array_fill(0, count($productIds), '?'));
$sql = "SELECT id, name, price FROM products WHERE id IN ($placeholders)";
$stmt = $pdo->prepare($sql);
$stmt->execute($productIds);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convert products to associative array [id => product]
$productMap = [];
foreach ($products as $product) {
    $productMap[$product['id']] = $product;
}

// Calculate total price and build cart items response
$totalPrice = 0;
foreach ($cart as $item) {
    $productId = $item['product_id'];
    if (!isset($productMap[$productId])) {
        continue; // Skip if product not found in database
    }

    $product = $productMap[$productId];
    $quantity = $item['quantity'];
    $price = $product['price'] * $quantity;
    $totalPrice += $price;

    $response['cart'][] = [
        'product_id' => $productId,
        'name' => $product['name'],
        'quantity' => $quantity,
        'price' => $product['price'],
        'total' => $price
    ];
}

// Set total price in response
$response['total_price'] = $totalPrice;

// Return response as JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
