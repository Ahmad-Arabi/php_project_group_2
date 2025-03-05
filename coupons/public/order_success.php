<?php
session_start();
$orderID = $_SESSION['last_order_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <title>تم استلام الطلب</title>
</head>
<body>

<h2 style="color: green;">✅ تم استلام طلبك بنجاح!</h2>
<?php if ($orderID): ?>
    <p>رقم الطلب: <strong>#<?= $orderID ?></strong></p>
<?php endif; ?>
<p>شكراً لتسوقك معنا. سنتواصل معك قريباً لتأكيد الطلب والتوصيل.</p>

<p><a href="cart.php">العودة إلى الصفحة الرئيسية</a></p>

</body>
</html>
