<?php
session_start();
require_once "../../includes/database/config.php";

/* $_SESSION['user_id'] = 6; 
 */
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; 
}

function getProductDetails($product_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    return $stmt->fetch();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
            $product_id = $product['id'];
            $quantity = 1; 

            // Checking if the order exists
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? AND status = 'pending' LIMIT 1");
            $stmt->execute([$_SESSION['user_id']]);
            $order = $stmt->fetch();

            if ($order) {
                $order_id = $order['id'];
            } else {
                // Creating a new order if not found
                $stmt = $pdo->prepare("INSERT INTO orders (user_id, status, created_at) VALUES (?, 'pending', NOW())");
                $stmt->execute([$_SESSION['user_id']]);

                if ($stmt->rowCount() > 0) {
                    $order_id = $pdo->lastInsertId();
                } else {
                    echo "Error creating order.";
                    exit();
                }
            }

            // Inserting into order_items
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
			$stmt->execute([$order_id, $product_id, $quantity, $product['price']]);
			
			
            if ($stmt->rowCount() > 0) {
                echo "Product added to order successfully.";
            } else {
                echo "Error adding product to order.";
            }

            // Adding product to cart
            if (!isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] = [
                    'quantity' => $quantity,
                    'product_details' => $product 
                ];
            } else {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            }

            header('Location: cart.php');
            exit();
        }
    } else {
        echo "Product not found.";
    }
}
?>



<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="author" content="Untree.co">
  <link rel="shortcut icon" href="favicon.png">

  <meta name="description" content="" />
  <meta name="keywords" content="bootstrap, bootstrap4" />

		<!-- Bootstrap CSS -->
		<link href="../../public/furni-ed/css/bootstrap.min.css" rel="stylesheet">



		
		<link href="../../public/furni-ed/css/style.css" rel="stylesheet">
		<link href="../../public/furni-ed/css/tiny-slider.css" rel="stylesheet">
		<link href="../../public/furni-ed/css/customlmg.css" rel="stylesheet">



		
		<title>View Details </title>
	</head>

	<body>

		<!-- Start Header/Navigation -->
		<nav class="custom-navbar navbar navbar navbar-expand-md navbar-dark bg-dark" aria-label="Furni navigation bar">

			<div class="container">
				<a class="navbar-brand" href="index.html"><div><img src="img\EXCLUSIVE_VIP.png" width="290px" height="290px"></div><span>.</span></a>

				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsFurni" aria-controls="navbarsFurni" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>

				<div class="collapse navbar-collapse" id="navbarsFurni">
					<ul class="custom-navbar-nav navbar-nav ms-auto mb-2 mb-md-0">
						<li class="nav-item">
							<a class="nav-link" href="index.php">Home</a>
						</li>
						<li><a class="nav-link" href="shop.php">Shop</a></li>
						<li><a class="nav-link" href="about.html">About us</a></li>
						<li><a class="nav-link" href="services.html">Services</a></li>
						<li class="active"><a class="nav-link" href="blog.html">Blog</a></li>
						<li><a class="nav-link" href="policy.php">Privacy Policy</a></li>

						<li><a class="nav-link" href="contact.php">Contact us</a></li>
					</ul>

					<ul class="custom-navbar-cta navbar-nav mb-2 mb-md-0 ms-5">
						<li><a class="nav-link" href="#"><img src="images/user.svg"></a></li>
						<li><a class="nav-link" href="cart.html"><img src="images/cart.svg"></a></li>
					</ul>
				</div>
			</div>
				
		</nav>
		<!-- End Header/Navigation -->

		<!-- Start Hero Section -->
			
		<!-- End Hero Section -->

		

<!-- Start Blog Section -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="row g-0">
                    <div class="col-md-6">
                        <div class="product-image-container" style="position: relative;">
                            <img src="/E_commerce_PHP/admin/product/uploads/product_images/<?php echo htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'); ?>"
                                 alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                 class="img-fluid rounded-start w-100">
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="card-body text-center">
                            <h2 class="card-title text-primary"><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <h3 class="text-danger"><?php echo '$' . number_format($product['price'], 2); ?></h3>

                            <!-- إadd to cart -->
                            <form method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                                <button type="submit" name="add_to_cart" class="btn btn-outline-secondary btn-lg mt-4">Add to Cart</button>
                            </form>

                            <a href="../../public/furni-ed/shop.php" class="btn btn-outline-secondary btn-lg mt-4">Back to Store</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Product Details Section -->



		

		<!-- Start Testimonial Slider -->
		<div class="testimonial-section before-footer-section">
			<div class="container">
				<div class="row">
					<div class="col-lg-7 mx-auto text-center">
						<h2 class="section-title">Testimonials</h2>
					</div>
				</div>

				<div class="row justify-content-center">
					<div class="col-lg-12">
						<div class="testimonial-slider-wrap text-center">

							<div id="testimonial-nav">
								<span class="prev" data-controls="prev"><span class="fa fa-chevron-left"></span></span>
								<span class="next" data-controls="next"><span class="fa fa-chevron-right"></span></span>
							</div>

							<div class="testimonial-slider">
								
								<div class="item">
									<div class="row justify-content-center">
										<div class="col-lg-8 mx-auto">

											<div class="testimonial-block text-center">
												<blockquote class="mb-5">
													<p>&ldquo;Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Integer convallis volutpat dui quis scelerisque.&rdquo;</p>
												</blockquote>

												<div class="author-info">
													<div class="author-pic">
														<img src="images/person-1.png" alt="Maria Jones" class="img-fluid">
													</div>
													<h3 class="font-weight-bold">Maria Jones</h3>
													<span class="position d-block mb-3">CEO, Co-Founder, XYZ Inc.</span>
												</div>
											</div>

										</div>
									</div>
								</div> 
								<!-- END item -->

								<div class="item">
									<div class="row justify-content-center">
										<div class="col-lg-8 mx-auto">

											<div class="testimonial-block text-center">
												<blockquote class="mb-5">
													<p>&ldquo;Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Integer convallis volutpat dui quis scelerisque.&rdquo;</p>
												</blockquote>

												<div class="author-info">
													<div class="author-pic">
														<img src="images/person-1.png" alt="Maria Jones" class="img-fluid">
													</div>
													<h3 class="font-weight-bold">Maria Jones</h3>
													<span class="position d-block mb-3">CEO, Co-Founder, XYZ Inc.</span>
												</div>
											</div>

										</div>
									</div>
								</div> 
								<!-- END item -->

								<div class="item">
									<div class="row justify-content-center">
										<div class="col-lg-8 mx-auto">

											<div class="testimonial-block text-center">
												<blockquote class="mb-5">
													<p>&ldquo;Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Integer convallis volutpat dui quis scelerisque.&rdquo;</p>
												</blockquote>

												<div class="author-info">
													<div class="author-pic">
														<img src="images/person-1.png" alt="Maria Jones" class="img-fluid">
													</div>
													<h3 class="font-weight-bold">Maria Jones</h3>
													<span class="position d-block mb-3">CEO, Co-Founder, XYZ Inc.</span>
												</div>
											</div>

										</div>
									</div>
								</div> 
								<!-- END item -->

							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Testimonial Slider -->

		

		 <!-- Start Footer Section -->
		 <?php include '../../includes/footer/index.php'; ?>

        <!-- End Footer Section -->	

		<script src="js/bootstrap.bundle.min.js"></script>
		<script src="js/tiny-slider.js"></script>
		<script src="js/custom.js"></script>

	
			
	</body>

</html>
