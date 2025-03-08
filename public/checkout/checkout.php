<?php

session_start();
require_once '../../includes/database/config.php';

// التحقق من تسجيل دخول المستخدم
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// الحصول على معرف المستخدم
$user_id = $_SESSION['user_id'];

// جلب الطلب بحالة pending إن وجد
$sql = "SELECT id FROM orders WHERE user_id = :user_id AND status = 'pending'";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Your cart is empty.']);
    exit;
}

$order_id = $order['id'];

// جلب المنتجات الموجودة في الطلب
$sql = "SELECT 
            order_items.product_id,
            order_items.quantity,
            order_items.price,
            products.name,
            products.image,
            (order_items.quantity * order_items.price) AS total_amount
        FROM order_items
        JOIN products ON order_items.product_id = products.id
        WHERE order_items.order_id = :order_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
$stmt->execute();
$cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

// حساب السعر الإجمالي
$totalPrice = 0;
$response = [
    'success' => true,
    'cart' => [],
    'total_price' => 0,
];

foreach ($cart as $item) {
    $totalPrice += $item['total_amount'];

    $response['cart'][] = [
        'product_id' => $item['product_id'],
        'name' => $item['name'],
        'quantity' => $item['quantity'],
        'price' => $item['price'],
        'total' => $item['total_amount'],
        'image' => $item['image']
    ];
}

// تحديث السعر الإجمالي في جدول الطلبات
$sql = "UPDATE orders SET total_price = :total_price, status = 'processing' WHERE id = :order_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':total_price', $totalPrice, PDO::PARAM_STR);
$stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
$stmt->execute();

// تحديث البيانات المسترجعة بعد تغيير حالة الطلب
$response['total_price'] = $totalPrice;
$response['message'] = "Order placed successfully and is now processing.";

// إرجاع البيانات بصيغة JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;

?>
