<?php
$pageTitle = "Checkout - Food Street";
include "includes/header.php";

if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Please sign in to proceed to checkout.');
            window.location.href='signin.php';
          </script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['full_name'];

// Fetch user email from database if not in session
if (!isset($_SESSION['email'])) {
    $user_query = "SELECT email FROM users WHERE user_id = '$user_id'";
    $user_result = mysqli_query($connection, $user_query);
    if ($user_result && mysqli_num_rows($user_result) > 0) {
        $row_user = mysqli_fetch_assoc($user_result);
        $_SESSION['email'] = $row_user['email'];
    } else {
        $_SESSION['email'] = ''; // fallback
    }
}
$paystack_email = $_SESSION['email'];

// Handle delivery address submission
if (isset($_POST['save_address'])) {
    $delivery_address = mysqli_real_escape_string($connection, $_POST['delivery_address']);
    $update_address = "UPDATE cart 
                       SET delivery_address = '$delivery_address' 
                       WHERE user_id = '$user_id' AND payment_status = 'Pending'";
    mysqli_query($connection, $update_address);

    $_SESSION['delivery_address'] = $delivery_address; // store in session
    echo "<script>alert('Delivery address saved successfully!');</script>";
}

// Fetch cart items
$query = "SELECT * FROM cart WHERE user_id = '$user_id' AND payment_status = 'Pending'";
$result = mysqli_query($connection, $query);
if (!$result) {
    die('QUERY FAILED: ' . mysqli_error($connection));
}

$total_amount = 0;
$cart_items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $cart_items[] = $row;
    $total_amount += $row['price'] * $row['quantity'];
}

// Paystack
$paystack_public_key = "pk_test_a57ecd12cac9e74aa191be0c9210919f92aae107"; 
$paystack_amount_kobo = $total_amount * 100;
?>
<?php include 'includes/nav.php'; ?>

<!-- Hero -->
<section class="hero-section text-center text-white d-flex align-items-center justify-content-center">
  <div class="container">
    <h1 class="fw-bold display-4">Checkout</h1>
    <p class="lead">Review your cart, confirm your address, and complete payment securely</p>
  </div>
</section>

<style>
.hero-section {
  background: url('images/bg-white2.jpg') center center/cover no-repeat;
  min-height: 35vh;
  position: relative;
}
.hero-section::before {
  content:"";
  position:absolute; inset:0;
  background:rgba(0,0,0,0.55);
}
.hero-section .container { position:relative; z-index:1; }
.checkout-card {
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.08);
  background: #fff;
}
.cart-item-card {
  border: 1px solid #eee;
  border-radius: 10px;
  overflow: hidden;
  transition: transform .2s;
}
.cart-item-card:hover { transform: scale(1.01); }
</style>

<section class="py-5 bg-light">
  <div class="container">
    <?php if (empty($cart_items)): ?>
      <div class="alert alert-warning text-center shadow-sm">
        <h5>Your cart is empty</h5>
        <p><a href="cooked-food.php" class="btn btn-primary mt-2">🍲 Order some food</a></p>
      </div>
    <?php else: ?>
      <div class="row g-4">
        
        <!-- Cart Items -->
        <div class="col-lg-8">
          <div class="checkout-card p-4 mb-4">
            <h4 class="fw-bold mb-3">Your Order</h4>
            <div class="row g-3">
              <?php foreach ($cart_items as $item): ?>
              <?php
                  // Dual directory image check
                  $cooked_path = "admin/uploads/cooked-food/" . $item['food_image'];
                  $fresh_path = "admin/uploads/fresh-food/" . $item['food_image'];

                  if (file_exists($cooked_path)) {
                      $image_path = $cooked_path;
                  } elseif (file_exists($fresh_path)) {
                      $image_path = $fresh_path;
                  } else {
                      $image_path = "images/no-image.png"; // fallback placeholder
                  }
              ?>
              <div class="col-12 col-md-6">
                <div class="cart-item-card p-3 h-100">
                  <div class="d-flex align-items-center mb-2">
                    <img src="<?= $image_path ?>" 
                         class="rounded me-3" 
                         style="width:70px; height:70px; object-fit:cover;">
                    <div>
                      <h6 class="mb-1"><?= htmlspecialchars($item['food_name']); ?></h6>
                      <small class="text-muted">₦<?= number_format($item['price'],2); ?></small>
                    </div>
                  </div>
                  <form action="update_cart.php" method="post" class="d-flex mb-2">
                    <input type="number" name="quantity" value="<?= $item['quantity']; ?>" 
                           min="1" class="form-control form-control-sm me-2" style="width:70px;">
                    <input type="hidden" name="cart_id" value="<?= $item['cart_id']; ?>">
                    <button type="submit" name="update" class="btn btn-sm btn-outline-warning">Update</button>
                  </form>
                  <div class="d-flex justify-content-between align-items-center">
                    <span class="badge bg-light text-dark">Subtotal: ₦<?= number_format($item['price']*$item['quantity'],2); ?></span>
                    <a href="delete_cart.php?cart_id=<?= $item['cart_id']; ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Remove this item?')">Remove</a>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Delivery Address -->
          <div class="checkout-card p-4">
            <h4 class="fw-bold mb-3">Delivery Address</h4>
            <form action="" method="post">
              <div class="mb-3">
				<input type="text" 
				name="delivery_address" 
				id="delivery_address" 
				class="form-control" 
				placeholder="Enter your delivery address..." 
				required
				value="<?php echo isset($_SESSION['delivery_address']) ? htmlspecialchars($_SESSION['delivery_address']) : ''; ?>">
 
              </div>
              <button type="submit" name="save_address" class="btn btn-primary">Save Address</button>
            </form>
          </div>
        </div>

        <!-- Summary -->
        <div class="col-lg-4">
          <div class="checkout-card p-4 sticky-top" style="top: 90px;">
            <h4 class="fw-bold mb-3">Order Summary</h4>
            <p class="d-flex justify-content-between">
              <span>Total:</span>
              <strong>₦<?= number_format($total_amount, 2); ?></strong>
            </p>
            <hr>
            <button type="button" class="btn btn-success w-100 btn-lg mb-2" id="paystackBtn"
              <?= empty($_SESSION['delivery_address']) ? 'disabled' : ''; ?>>
              Pay Now <i class="fas fa-credit-card ms-2"></i>
            </button>
            <?php if (empty($_SESSION['delivery_address'])): ?>
              <p class="text-danger small mt-2">⚠️ Please save your delivery address before payment.</p>
            <?php endif; ?>
            <a href="cooked-food.php" class="btn btn-outline-secondary w-100">➕ Add More Food</a>
          </div>
        </div>

      </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
const paystackBtn = document.getElementById('paystackBtn');
if (paystackBtn) {
  paystackBtn.addEventListener('click', function () {
    var handler = PaystackPop.setup({
      key: '<?php echo $paystack_public_key; ?>',
      email: '<?php echo $paystack_email; ?>',
      amount: <?php echo $paystack_amount_kobo; ?>,
      currency: "NGN",
      ref: 'FS-<?php echo rand(1000000,9999999); ?>',
      onClose: function(){
        alert('Payment was not completed, window closed.');
      },
      callback: function(response){
        window.location.href = 'verify_payment.php?reference=' + response.reference;
      }
    });
    handler.openIframe();
  });
}
</script>
</body>
</html>
