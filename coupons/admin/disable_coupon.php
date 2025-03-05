<?php
include '../includes/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    header("Location: coupons.php");
    exit;
}

$checkStmt = $conn->prepare("SELECT id FROM coupons WHERE id = ?");
$checkStmt->execute([$id]);

if ($checkStmt->rowCount() == 0) {
    header("Location: coupons.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_disable'])) {
    $stmt = $conn->prepare("UPDATE coupons SET active = 0 WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: coupons.php?success=disabled");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Disable Coupon</title>
    <link rel="stylesheet" href="coupon.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="modal">
    <div class="modal-content">
        <span class="close" onclick="cancelDisable()">&times;</span>
        <h3>Are you sure you want to disable this coupon?</h3>
        <form method="post">
            <input type="hidden" name="confirm_disable" value="1">
            <button type="submit" class="btn btn-success">Yes, disable it!</button>
            <a href="coupons.php" class="btn btn-danger">No, keep it</a>
        </form>
    </div>
</div>

<script>
function cancelDisable() {
    window.location.href = 'coupons.php';
}
</script>

</body>
</html>
