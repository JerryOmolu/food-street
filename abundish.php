<?php
$pageTitle = "Abundish - Food Street";
include "includes/header.php"; 
include "includes/nav.php";

// --- CART PROCESSING ---
if (isset($_POST['add'])) {

    // Ensure user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo "<script>
                alert('⚠️ Please sign in to add items to your cart.');
                window.location.href = 'signin.php';
              </script>";
        exit();
    }

    $user_id          = $_SESSION['user_id'];
    $user_name        = $_SESSION['full_name'];
    $food_id          = escape($_POST['food_id']);
    $food_name        = escape($_POST['hidden_food_name']);
    $food_description = escape($_POST['hidden_food_description']);
    $price            = escape($_POST['hidden_price']);
    $food_image       = escape($_POST['hidden_food_image']);
    $added_by         = escape($_POST['hidden_added_by']); // <-- capture vendor
    $order_number     = rand(1000000, 9999999);
    $payment_status   = 'Pending';

    // Check if food already exists in cart
    $check_query = "SELECT * FROM cart WHERE user_id='$user_id' AND food_id='$food_id' AND payment_status='Pending'";
    $check_result = mysqli_query($connection, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        mysqli_query($connection, "UPDATE cart SET quantity = quantity + 1 WHERE user_id='$user_id' AND food_id='$food_id'");
        $message = "👍 Another $food_name has been added to your cart!";
    } else {
        mysqli_query($connection, "INSERT INTO cart 
            (user_id, user_name, food_id, food_name, food_description, food_image, price, order_number, payment_status, vendor) 
            VALUES 
            ('$user_id','$user_name','$food_id','$food_name','$food_description','$food_image','$price','$order_number','$payment_status','$added_by')");
        $message = "🛒 $food_name has been added to your cart successfully!";
    }

    // Styled popup with next-step options
    echo "<script>
            if(confirm('$message \\n\\nWould you like to view your cart now?')) {
                window.location.href='cart.php';
            } else {
                window.location.href='abundish.php';
            }
          </script>";
    exit();
}
?>

<!-- Hero Section -->
<section class="hero-section">
  <div class="hero-overlay"></div>
  <div class="hero-content text-center text-white">
    <h1 class="fw-bold display-4">Abundish</h1>
    <p class="lead">Your Fresh foods delivered to your doorstep</p>
  </div>
</section>

<style>
.hero-section {
  background: url('images/plant.jpg') center center / cover no-repeat;
  min-height: 60vh;
  position: relative;
}
.hero-section::before {
  content: "";
  position: absolute; inset:0;
  background: rgba(0,0,0,0.4);
}
.hero-section .hero-content { position: relative; z-index:1; }
.hover-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}
</style>

<!-- Fresh Food Section -->
<section class="py-5" style="background: url('images/freshfood-bg.jpg') center/cover no-repeat;">
  <div class="container">
    <h1 class="display-4 fw-bold" style="color:#007965;">Become Abundish Agent and Earn Money From Your Home</h1>
    <p class="lead">Foodstreet Empowers <b style="color:#007965;" class="fw-bold">1,000,000 Youths </b> with jobs. Join our transformative initiative in the nation's largest food economy and start earning income today</p>
	 <a href="#"><button class="btn btn-success">Join Now</button> </a>
  </div>
</section>

<!-- Vendors List -->
<section class="pt-3 pb-5">
  <div class="container">
    <div class="row g-4">
    <?php  
      $perpage = 20;
      $page = isset($_GET['page']) ? (int)escape($_GET['page']) : 1;
      $page_1 = ($page == 1) ? 0 : ($page * $perpage) - $perpage;

      // Count total fresh food
      $query1 = "SELECT * FROM fresh_food ORDER BY added_on DESC";
      $view_products1 = mysqli_query($connection, $query1);
      $total = mysqli_num_rows($view_products1);
      $total_pages = ceil($total / $perpage);

      $Previous = max(1, $page - 1);
      $Next     = $page + 1;

      // Paginated query
      $query = "SELECT * FROM fresh_food ORDER BY added_on DESC LIMIT $page_1, $perpage";
      $select_food_query = mysqli_query($connection, $query);

      while($row = mysqli_fetch_array($select_food_query)){
          $fresh_id    = escape($row['fresh_id']);
          $food_name   = escape($row['food_name']);
          $description = escape($row['description']);
          $price       = escape($row['price']);
          $food_image  = escape($row['food_image']);                
          $added_on    = escape($row['added_on']);                
          $added_by    = escape($row['added_by']);                                

          // --- Check both directories for image ---
          $cooked_path = "admin/uploads/cooked-food/$food_image";
          $fresh_path  = "admin/uploads/fresh-food/$food_image";
          if(file_exists($cooked_path)) {
              $img_src = $cooked_path;
          } elseif(file_exists($fresh_path)) {
              $img_src = $fresh_path;
          } else {
              $img_src = "images/no-image.png"; // fallback
          }
    ?>     
      <div class="col-6 col-sm-6 col-md-4 mb-4">
        <div class="card shadow-sm h-100 hover-card">
            <img src="<?= $img_src ?>" 
                 class="card-img-top" 
                 alt="<?= htmlspecialchars($food_name); ?>" 
                 style="height: 220px; object-fit: cover;">

            <div class="card-body">
                <h5 class="card-title fw-bold"><?= htmlspecialchars($food_name) ?></h5>
                <p class="card-text text-muted" style="min-height: 50px;">
                    <?= htmlspecialchars($description) ?>
                </p>
                <p class="mb-1">
                    <span class="fw-bold text-success">₦ <?= number_format($price,2); ?></span>
                </p>
                <p class="mb-1">
                    <span class="badge bg-primary">🧑‍🍳 <?= htmlspecialchars($added_by); ?></span>
                </p>
                <p class="mb-3">
                    <span class="badge bg-light text-dark">📅 <?= date("M d, Y", strtotime($added_on)); ?></span>
                </p>

                <!-- Order Now Form -->
                <form action="" method="post">
                    <input type="hidden" name="food_id" value="<?= $fresh_id; ?>">
                    <input type="hidden" name="hidden_food_name" value="<?= htmlspecialchars($food_name); ?>">
                    <input type="hidden" name="hidden_food_description" value="<?= htmlspecialchars($description); ?>">
                    <input type="hidden" name="hidden_price" value="<?= $price; ?>">
                    <input type="hidden" name="hidden_food_image" value="<?= htmlspecialchars($food_image); ?>">
                    <input type="hidden" name="hidden_added_by" value="<?= htmlspecialchars($added_by); ?>">

                    <button class="btn btn-success w-100" type="submit" name="add">
                        <i class="fas fa-carrot fa-lg"></i> Order Now
                    </button>
                </form>
            </div>
        </div>
      </div>
    <?php } ?>
    </div>
  </div>
</section>



<!-- Pagination -->
<div class="shop-pagination pt-3">
  <div class="container">
    <div class="card">
      <div class="card-body py-3">
        <nav aria-label="Page navigation example">
          <ul class="pagination pagination-two justify-content-center">
            <li class="page-item"><a class="page-link" href="abundish?page=<?= $Previous; ?>"><i class="bi bi-chevron-left"></i></a></li>
            <?php 
              for($i=1; $i<=$total_pages; $i++){
                  $active = ($i == $page) ? 'active' : '';
                  echo "<li class='page-item $active'><a class='page-link' href='abundish?page=$i'>$i</a></li>";
              }
            ?>
            <li class="page-item"><a class="page-link" href="abundish?page=<?= $Next; ?>"><i class="bi bi-chevron-right"></i></a></li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/bottom-bar.php'; ?>
<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
