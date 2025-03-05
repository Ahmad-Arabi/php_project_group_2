<?php
session_start();
include '../includes/db.php';

// التأكد أن السلة فيها منتج واحد فقط
if (empty($_SESSION['cart']) || count($_SESSION['cart']) !== 1) {
    die('❌ السلة فارغة أو تحتوي على أكثر من منتج!');
}

// قراءة المنتج
$product = $_SESSION['cart'][0];

// السعر الأساسي
$originalTotal = $product['price'] * $product['quantity'];

// تحميل الكوبون والخصم (لو تم تطبيقه مسبقًا)
$couponID = $_SESSION['coupon_id'] ?? null;
$discount = $_SESSION['discount'] ?? 0;

// تطبيق كود الخصم
if (isset($_POST['apply_coupon'])) {
    $couponCode = trim($_POST['coupon_code'] ?? '');
    if (!empty($couponCode)) {
        $stmt = $conn->prepare("SELECT * FROM coupons WHERE code = ? AND active = 1 AND expiration_date >= CURDATE()");
        $stmt->execute([$couponCode]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($coupon) {
            $_SESSION['coupon_id'] = $coupon['id'];
            $_SESSION['discount'] = floatval($coupon['discount_value']);
            $couponID = $coupon['id'];
            $discount = $_SESSION['discount'];
        } else {
            echo "<p style='color:red;'>❌ كود الخصم غير صالح أو منتهي.</p>";
        }
    }
}

// عند تأكيد الطلب
if (isset($_POST['place_order'])) {
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // تحديد user_id (لو مسجل دخول يأخذها من الجلسة، لو ضيف تكون 0)
    $userID = $_SESSION['user_id'] ?? 999;

    // حساب الإجمالي بعد الخصم
    $finalTotal = $originalTotal - $discount;

    // حفظ الطلب في orders
    $stmt = $conn->prepare("
        INSERT INTO orders (user_id, total_price, coupon_id, shipping_phone, shipping_address, payment_method, status)
        VALUES (?, ?, ?, ?, ?, 'COD', 'pending')
    ");
    $stmt->execute([$userID, $finalTotal, $couponID, $phone, $address]);
    $orderID = $conn->lastInsertId();

    // حفظ المنتج في order_items
    $stmt = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$orderID, $product['product_id'], $product['quantity'], $product['price']]);

    // تنظيف الجلسة
    unset($_SESSION['cart']);
    unset($_SESSION['coupon_id']);
    unset($_SESSION['discount']);

    // حفظ رقم الطلب في الجلسة عشان نعرضه في صفحة النجاح
    $_SESSION['last_order_id'] = $orderID;

    // توجيه إلى صفحة النجاح
    header('Location: order_success.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <title>إتمام الطلب</title>
</head>
<body>

<h3>تفاصيل المنتج</h3>
<table border="1">
    <tr><th>المنتج</th><td><?= htmlspecialchars($product['name']) ?></td></tr>
    <tr><th>الكمية</th><td><?= $product['quantity'] ?></td></tr>
    <tr><th>السعر</th><td><?= number_format($product['price'], 2) ?> USD</td></tr>
</table>

<h3>إجمالي السلة</h3>
<p><?= number_format($originalTotal, 2) ?> USD</p>

<h3>كود الخصم (اختياري)</h3>
<form method="post">
    <input type="text" name="coupon_code" value="<?= $_POST['coupon_code'] ?? '' ?>">
    <button type="submit" name="apply_coupon">تطبيق</button>
</form>

<h3>الخصم المطبق</h3>
<p><?= number_format($discount, 2) ?> USD</p>

<h3>الإجمالي بعد الخصم</h3>
<p><strong><?= number_format($originalTotal - $discount, 2) ?> USD</strong></p>

<h3>بيانات الشحن</h3>
<form method="post">
    <input type="text" name="phone" placeholder="رقم الهاتف" required><br>
    <textarea name="address" placeholder="العنوان" required></textarea><br>
    <button type="submit" name="place_order">تأكيد الطلب</button>
</form>

</body>
</html>
