<?php
header('Content-Type: text/html; charset=utf-8');
include '../includes/db.php';

$error_code = "";
$error_discount = "";
$error = "";

$code = "";
$discount = "";
$expiry = "";
$active = 1;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    $discount = $_POST['discount_value'];
    $expiry = $_POST['expiration_date'];
    $active = isset($_POST['active']) ? 1 : 0;

    if ($discount < 0 || $discount > 100) {
        $error_discount = "❌ Discount percentage must be between 0% and 100.";
    }

    $checkStmt = $conn->prepare("SELECT id FROM coupons WHERE code = ?");
    $checkStmt->execute([$code]);

    if ($checkStmt->rowCount() > 0) {
        $error_code = "❌ This coupon code already exists. Please use a different code.";
    }

    if (empty($error_code) && empty($error_discount)) {
        $stmt = $conn->prepare("INSERT INTO coupons (code, discount_value, expiration_date, active) VALUES (?, ?, ?, ?)");
        $stmt->execute([$code, $discount, $expiry, $active]);
        header("Location: coupons.php?success=added");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Coupon</title>
    <link rel="stylesheet" href="coupon.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<form method="post">
    Coupon Code: <input type="text" name="code" value="<?= htmlspecialchars($code) ?>" required><br>
    
    Discount Percentage: <input type="number" name="discount_value" step="0.01" value="<?= htmlspecialchars($discount) ?>" placeholder="%" required><br>

    Expiration Date: <input type="date" name="expiration_date" value="<?= htmlspecialchars($expiry) ?>" required><br>

    Active: <input type="checkbox" name="active" value="1" <?= $active ? 'checked' : '' ?>><br>

    <button type="submit">Add Coupon</button>

    <?php if (!empty($error_code) || !empty($error_discount)): ?>
        <div class="form-error">
            <?php
                if (!empty($error_code)) echo $error_code . "<br>";
                if (!empty($error_discount)) echo $error_discount;
            ?>
        </div>
    <?php endif; ?>
</form>

</body>
</html>