<?php
session_start();
require_once '../../../includes/database/config.php';

// Prepare response array
$response = [
    'success' => false,
    'message' => '',
    'original_total' => 0,
    'discount_value' => 0,
    'new_total' => 0,
];

// Fetch cart from session
$cart = $_SESSION['cart'] ?? [];
//error
$original_total = 0;

foreach ($cart as $item) {
    $original_total += $item['price'] * $item['quantity'];
}   

$response['original_total'] = $original_total;

// Read coupon code from request
$coupon_code = $_POST['coupon_code'] ?? '';

// Check if coupon exists in the database
$stmt = $pdo->prepare("SELECT id, discount_value, active, expiration_date 
                       FROM coupons 
                       WHERE code = ?");
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
    $discount_value = $coupon['discount_value'];

    // Optional: Ensure discount does not exceed original total
    if ($discount_value > $original_total) {
        $discount_value = $original_total;
    }

    // Calculate new total
    $new_total = $original_total - $discount_value;

    // Ensure total is not negative
    if ($new_total < 0) {
        $new_total = 0;
    }

    // Success response
    $response['success'] = true;
    $response['message'] = 'Coupon applied successfully.';
    $response['discount_value'] = $discount_value;
    $response['new_total'] = $new_total;

    // Save applied coupon in session to use later in checkout.php
    $_SESSION['applied_coupon'] = [
        'id' => $coupon['id'],
        'code' => $coupon_code,
        'discount' => $discount_value
    ];
}

// Return response as JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;

?>