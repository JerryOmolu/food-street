<?php
$pageTitle = "My Cart - Food Street";
include "includes/header.php"; 
?>

<!-- Include Navbar -->
<?php include 'includes/nav.php'; ?>

<?php
// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Please sign in to view your cart.');
            window.location.href='signin.php';
          </script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_query = "SELECT * FROM cart WHERE user_id = '$user_id' AND payment_status = 'Pending'";
$cart_result = mysqli_query($connection, $cart_query);
?>

<!-- Hero Section -->
<section class="hero-section d-flex align-items-center justify-content-center text-center text-white">
    <div class="container">
        <h1 class="fw-bold display-4"> My Cart</h1>
        <p class="lead">Review your selected meals and proceed to checkout</p>
    </div>
</section>

<style>
.hero-section {
  background: url('images/food-cart.jpg') center center / cover no-repeat;
  min-height: 40vh;
  position: relative;
}
.hero-section::before {
  content: "";
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.55);
}
.hero-section .container {
  position: relative;
  z-index: 1;
}
.cart-card {
  transition: transform 0.2s ease-in-out;
  height: 100%;
}
.cart-card:hover {
  transform: scale(1.01);
}
.cart-summary {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.05);
}
</style>

<!-- Cart Content -->
<section class="py-5">
    <div class="container">
        <?php if(mysqli_num_rows($cart_result) == 0): ?>
            <div class="alert alert-warning text-center shadow-sm">
                <h5>Your cart is empty</h5>
                <p><a href="cooked-food.php" class="btn btn-primary mt-2">🍲 Order some food!</a></p>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="row g-3">
                        <?php 
                        $total = 0;
                        while($row = mysqli_fetch_assoc($cart_result)){
                            $cart_id = $row['cart_id'];
                            $food_name = $row['food_name'];
                            $price = $row['price'];
                            $quantity = $row['quantity'];
                            $food_image = $row['food_image'];
                            $subtotal = $price * $quantity;
                            $total += $subtotal;

                            // Check both directories for the image
                            $cooked_path = "admin/uploads/cooked-food/" . $food_image;
                            $fresh_path = "admin/uploads/fresh-food/" . $food_image;

                            if (file_exists($cooked_path)) {
                                $image_path = $cooked_path;
                            } elseif (file_exists($fresh_path)) {
                                $image_path = $fresh_path;
                            } else {
                                $image_path = "images/no-image.png"; // fallback
                            }
                        ?>
                        <!-- Cart Item Card -->
                        <div class="col-12 col-sm-6">
                            <div class="card cart-card shadow-sm">
                                <img src="<?= $image_path ?>" 
                                     class="card-img-top" 
                                     alt="<?= htmlspecialchars($food_name) ?>" 
                                     style="object-fit: cover; height: 150px;">
                                <div class="card-body">
                                    <h6 class="card-title mb-1"><?= htmlspecialchars($food_name) ?></h6>
                                    <p class="text-muted small mb-1">₦<?= number_format($price, 2) ?></p>
                                    <form action="update_cart.php" method="post" class="d-flex align-items-center mb-2">
                                        <input type="hidden" name="cart_id" value="<?= $cart_id ?>">
                                        <input type="number" name="quantity" value="<?= $quantity ?>" min="1" 
                                            class="form-control form-control-sm me-2" style="width: 70px;">
                                        <button type="submit" name="update" class="btn btn-sm btn-outline-warning">Update</button>
                                    </form>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-light text-dark">Subtotal: ₦<?= number_format($subtotal, 2) ?></span>
                                        <a href="delete_cart.php?cart_id=<?= $cart_id ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Remove this item?')">Remove</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Summary Section -->
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="cart-summary shadow-sm">
                        <h4 class="fw-bold mb-3">Order Summary</h4>
                        <p class="d-flex justify-content-between">
                            <span>Total:</span>
                            <strong>₦<?= number_format($total, 2) ?></strong>
                        </p>
                        <hr>
                        <a href="checkout.php" class="btn btn-success w-100 btn-lg mb-2">
                            Proceed to Checkout <i class="fas fa-credit-card ms-2"></i>
                        </a>
                        <a href="cooked-food.php" class="btn btn-outline-secondary w-100">
                            ➕ Add More Food
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Footer -->
<?php include 'includes/footer.php'; ?>
