<?php
session_start();
require_once '../../includes/database/config.php';

// Prepare response array
$response = [
    'success' => false,
    'message' => '',
    'original_total' => 0,
    'discount_value' => 0,
    'new_total' => 0,
];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'User not logged in';
    echo json_encode($response);
    exit;
}

// Get user_id from session
$user_id = $_SESSION['user_id'];

// Fetch the latest pending order for the user
$stmt = $pdo->prepare("SELECT id, total_price FROM orders WHERE user_id = :user_id AND status = 'pending' ORDER BY id DESC LIMIT 1");
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    $response['message'] = 'No pending order found.';
    echo json_encode($response);
    exit;
}

// Calculate the original total price from the order_items table
$order_id = $order['id'];
$stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity * price), 0) AS original_total FROM order_items WHERE order_id = :order_id");
$stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
$stmt->execute();
$total = $stmt->fetch(PDO::FETCH_ASSOC);
$original_total = $total['original_total'] ?? 0; // Ensure it's not NULL

$response['original_total'] = $original_total;

// Read coupon code from request
$coupon_code = $_POST['coupon_code'] ?? '';

// Check if coupon exists in the database
$stmt = $pdo->prepare("SELECT id, discount_value, active, expiration_date FROM coupons WHERE code = ?");
$stmt->execute([$coupon_code]);
$coupon = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$coupon) {
    $response['message'] = 'This coupon does not exist.';
} elseif ($coupon['active'] != 1) {
    $response['message'] = 'This coupon is not active.';
} elseif ($coupon['expiration_date'] < date('Y-m-d')) {
    $response['message'] = 'This coupon has expired.';
} else {
    // Apply discount if coupon is valid
    $discount_value = $coupon['discount_value'] ?? 0;

    // Ensure discount does not exceed original total
    if ($discount_value > $original_total) {
        $discount_value = $original_total;
    }

    // Calculate new total
    $new_total = max($original_total - $discount_value, 0); // Ensure total is not negative

    // Success response
    $response['success'] = true;
    $response['message'] = 'Coupon applied successfully.';
    $response['discount_value'] = $discount_value;
    $response['new_total'] = $new_total;

    // Save applied coupon in the orders table
    $stmt = $pdo->prepare("UPDATE orders SET coupon_id = :coupon_id, total_price = :new_total WHERE id = :order_id");
    $stmt->bindValue(':coupon_id', $coupon['id'], PDO::PARAM_INT);
    $stmt->bindValue(':new_total', $new_total, PDO::PARAM_INT);
    $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->execute();
}

// Return response as JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
