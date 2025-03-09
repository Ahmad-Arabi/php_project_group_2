<?php
require_once "../../includes/database/config.php";

$error = '';
$success = false;
$products = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];

    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);

    if ($stmt->rowCount() == 0) {
        $error = "Category not found.";
    } else {
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
        $stmt_check->execute([$category_id]);
        $product_count = $stmt_check->fetchColumn();

        if ($product_count > 0) {
            $error = "Cannot delete category because it has linked products.";

                
        } else {
            try {
                $stmt_delete = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                $stmt_delete->execute([$category_id]);

                if ($stmt_delete->rowCount() > 0) {
                    $success = true;
                } else {
                    $error = "Category could not be deleted.";
                }
            } catch (PDOException $e) {
                $error = "Error deleting category: " . $e->getMessage();
            }
        }
    }
} else {
    $error = "No category ID provided.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Category</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2>Delete Category</h2>
    <?php if ($success): ?>
        <div class="alert alert-success">Category deleted successfully!</div>
        <a href="category.php?page=category" class="btn btn-primary">Back to Category Management</a>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php if ($product_count > 0): ?>
            
            
        <?php endif; ?>
        <a href="category.php?page=category" class="btn btn-secondary mt-3">Cancel</a>
        <a href="/php_copy/admin/product/product.php?page=category" class="btn btn-secondary mt-3">Back to product List</a>
        <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>
</html>
