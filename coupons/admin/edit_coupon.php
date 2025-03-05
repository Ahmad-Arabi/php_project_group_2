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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    header("Location: coupons.php");
    exit;
}

$checkStmt = $conn->prepare("SELECT * FROM coupons WHERE id = ?");
$checkStmt->execute([$id]);
$coupon = $checkStmt->fetch(PDO::FETCH_ASSOC);

if (!$coupon) {
    header("Location: coupons.php");
    exit;
}

$code = $coupon['code'];
$discount = $coupon['discount_value'];
$expiry = $coupon['expiration_date'];
$active = $coupon['active'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    $discount = $_POST['discount_value'];
    $expiry = $_POST['expiration_date'];
    $active = isset($_POST['active']) ? 1 : 0;

    if ($discount < 0 || $discount > 100) {
        $error_discount = "❌ Discount percentage must be between 0% and 100.";
    }

    $checkStmt = $conn->prepare("SELECT id FROM coupons WHERE code = ? AND id != ?");
    $checkStmt->execute([$code, $id]);

    if ($checkStmt->rowCount() > 0) {
        $error_code = "❌ This coupon code already exists. Please use a different code.";
    }

    if (empty($error_code) && empty($error_discount)) {
        if ($coupon['code'] != $code || $coupon['discount_value'] != $discount || $coupon['expiration_date'] != $expiry || $coupon['active'] != $active) {
            $updateStmt = $conn->prepare("UPDATE coupons SET code = ?, discount_value = ?, expiration_date = ?, active = ? WHERE id = ?");
            $updateStmt->execute([$code, $discount, $expiry, $active, $id]);
            header("Location: coupons.php?success=updated");
            exit;
        } else {
            $error = "❌ No changes made to the coupon.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Coupon</title>
    <link rel="stylesheet" href="coupon.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<form method="post">
    Coupon Code: <input type="text" name="code" value="<?= htmlspecialchars($code) ?>" required><br>
    
    Discount Percentage: <input type="number" name="discount_value" step="0.01" value="<?= htmlspecialchars($discount) ?>" placeholder="%" required><br>

    Expiration Date: <input type="date" name="expiration_date" value="<?= htmlspecialchars($expiry) ?>" required><br>

    Active: <input type="checkbox" name="active" value="1" <?= $active ? 'checked' : '' ?>><br>

    <button type="submit">Update Coupon</button>
    <a href="coupons.php" class="btn btn-secondary">Back to List</a>

    <?php if (!empty($error)): ?>
        <div class="form-error"><?= $error ?></div>
    <?php endif; ?>
</form>

</body>
</html>
