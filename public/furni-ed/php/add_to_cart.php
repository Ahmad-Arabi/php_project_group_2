<?php
session_start();
require_once '../../../includes/database/config.php'; 

// Get product_id and quantity from request (GET or POST)
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : (isset($_POST['product_id']) ? intval($_POST['product_id']) : 0);
$quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : (isset($_POST['quantity']) ? intval($_POST['quantity']) : 1);

// Validate inputs
if ($product_id <= 0 || $quantity <= 0) {
    die("Invalid product or quantity.");
}

// Fetch product from database
$stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if product exists
if (!$product) {
    die("Product not found.");
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if product already in cart
$found = false;

foreach ($_SESSION['cart'] as &$item) {
    if ($item['product_id'] == $product['id']) {
        $item['quantity'] += $quantity; // Update quantity
        $found = true;
        break;
    }
}

// If product not found in cart, add new item
if (!$found) {
    $_SESSION['cart'][] = [
        'product_id' => $product['id'],
        'name' => $product['name'],
        'price' => (float)$product['price'],
        'quantity' => $quantity
    ];
}

// Redirect back to shop or cart page 
header('Location: ../checkout.html');
exit;
