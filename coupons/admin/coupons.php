<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Coupons List</title>
    <link rel="stylesheet" href="coupon.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<?php
include '../includes/db.php';

if (isset($_GET['success'])) {
    $message = "";
    if ($_GET['success'] == 'added') {
        $message = "✅ Coupon added successfully!";
    } elseif ($_GET['success'] == 'updated') {
        $message = "✅ Coupon updated successfully!";
    } elseif ($_GET['success'] == 'restored') {
        $message = "✅ Coupon restored successfully!";
    } elseif ($_GET['success'] == 'disabled') {
        $message = "✅ Coupon disabled successfully!";
    }

    if ($message) {
        echo '<div class="success-message">' . $message . '</div>';
        echo '<script>setTimeout(function() { document.querySelector(".success-message").style.display = "none"; }, 3000);</script>';
    }
}

$stmt = $conn->prepare("SELECT * FROM coupons ORDER BY active DESC, updated_at DESC");
$stmt->execute();
$coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="add-coupon-button">
    <a href="add_coupon.php" class="btn btn-success">Add Coupon</a>
</div>

<table class="table" border="1" cellpadding="5">
    <thead>
        <tr>
            <th>Code</th>
            <th>Discount</th>
            <th>Expiration Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr>
        <td colspan="5" class="section-header" style="text-align: center;"><strong>Active Coupons</strong></td>
        </tr>
        <?php $hasActive = false; ?>
        <?php foreach ($coupons as $coupon): ?>
            <?php if ($coupon['active'] == 1): ?>
                <?php $hasActive = true; ?>
                <tr>
                    <td><?= $coupon['code'] ?></td>
                    <td><?= $coupon['discount_value'] ?>%</td>
                    <td><?= $coupon['expiration_date'] ?></td>
                    <td>Active</td>
                    <td>
                        <a href="edit_coupon.php?id=<?= $coupon['id'] ?>" class="btn btn-primary">Edit</a>
                        <a href="disable_coupon.php?id=<?= $coupon['id'] ?>" class="btn btn-danger">Disable</a>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if (!$hasActive): ?>
            <tr><td colspan="5" class="no-coupons">No active coupons available.</td></tr>
        <?php endif; ?>

        <?php $hasInactive = false; ?>
        <?php foreach ($coupons as $coupon): ?>
            <?php if ($coupon['active'] == 0): ?>
                <?php $hasInactive = true; break; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if ($hasInactive): ?>
            <tr>
            <td colspan="5" class="section-header" style="text-align: center;"><strong>Inactive Coupons</strong></td>
            </tr>
            <?php foreach ($coupons as $coupon): ?>
                <?php if ($coupon['active'] == 0): ?>
                    <tr>
                        <td><?= $coupon['code'] ?></td>
                        <td><?= $coupon['discount_value'] ?>%</td>
                        <td><?= $coupon['expiration_date'] ?></td>
                        <td>Inactive</td>
                        <td>
                            <a href="restore_coupon.php?id=<?= $coupon['id'] ?>" class="btn btn-warning">Restore</a>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
