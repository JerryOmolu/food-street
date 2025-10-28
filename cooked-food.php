<?php
$pageTitle = "Cooked Food - Food Street";
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
    $check_query = "SELECT * FROM cart 
                    WHERE user_id='$user_id' 
                      AND food_id='$food_id' 
                      AND payment_status='Pending'";
    $check_result = mysqli_query($connection, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        mysqli_query($connection, "UPDATE cart 
                                   SET quantity = quantity + 1 
                                   WHERE user_id='$user_id' AND food_id='$food_id'");
        $message = "👍 Another $food_name has been added to your cart!";
    } else {
        mysqli_query($connection, "INSERT INTO cart 
                                   (user_id,user_name,food_id,food_name,food_description,food_image,price,order_number,payment_status,vendor) 
                                   VALUES 
                                   ('$user_id','$user_name','$food_id','$food_name','$food_description','$food_image','$price','$order_number','$payment_status','$added_by')");
        $message = "🛒 $food_name has been added to your cart successfully!";
    }

    echo "<script>
            if(confirm('$message \\n\\nWould you like to view your cart now?')) {
                window.location.href='cart.php';
            } else {
                window.location.href='cooked-food.php';
            }
          </script>";
    exit();
}
?>

<!-- Hero Section -->
<section class="hero-section d-flex align-items-center text-white text-center">
  <div class="container">
    <h1 class="display-4 fw-bold">Cooked Food</h1>
    <p class="lead">Order fresh produce, farm products, and groceries from trusted vendors around you.</p>
  </div>
</section>

<style>
.hero-section {
  background: url('images/bg-white1.jpg') center center / cover no-repeat;
  min-height: 60vh;
  position: relative;
}
.hero-section::before {
  content: "";
  position: absolute; inset:0;
  background: rgba(0,0,0,0.4);
}
.hero-section .container { position: relative; z-index:1; }
.hover-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.2);
}
</style>

<!-- Food Listing -->
<section class="py-5 bg-light">
  <div class="container">
    <!-- Section Header -->
    <div class="col-lg-8 mx-auto text-center mb-5">
      <h5 class="fw-bold fs-2 text-danger">🍛 Explore Our Cooked Food Menu</h5>
      <p class="text-muted">Browse our freshly made meals and place your order with just one click.</p>
    </div>

    <div class="row g-4">
      <?php  
        $perpage = 20;
        $page = isset($_GET['page']) ? escape($_GET['page']) : 1;
        $page_1 = ($page == 1) ? 0 : ($page * $perpage) - $perpage;

        $query1 = "SELECT * FROM cooked_food ORDER BY added_on DESC";
        $view_products1 = mysqli_query($connection, $query1);
        $total = mysqli_num_rows($view_products1);
        $total_pages = ceil($total/$perpage);

        $Previous = (int)$page - 1;
        $Next     = (int)$page + 1;

        $query = "SELECT * FROM cooked_food ORDER BY added_on DESC LIMIT $page_1, $perpage";
        $select_food_query = mysqli_query($connection, $query);

        while($row = mysqli_fetch_array($select_food_query)){
            $cook_id     = escape($row['cook_id']);
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

      <!-- Food Card -->
      <div class="col-6 col-md-4 col-lg-3">
        <div class="card shadow-sm h-100 border-0 rounded-4 food-card">
          <!-- Image -->
          <div class="position-relative overflow-hidden rounded-top-4">
            <img src="<?= $img_src ?>" 
                 class="card-img-top food-img" 
                 alt="<?= htmlspecialchars($food_name); ?>">
            <!-- Price Badge -->
            <span class="badge bg-danger position-absolute top-0 start-0 m-2 px-3 py-2 fs-6 shadow-sm">
              ₦ <?= number_format($price,2); ?>
            </span>
          </div>

          <!-- Card Body -->
          <div class="card-body d-flex flex-column">
            <h5 class="fw-bold text-dark text-truncate"><?= htmlspecialchars($food_name) ?></h5>
            <p class="text-muted small food-desc mb-3"><?= htmlspecialchars($description) ?></p>

            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="badge bg-success">👨‍🍳 <?= htmlspecialchars($added_by); ?></span>
              <span class="badge bg-light text-dark">📅 <?= date("M d, Y", strtotime($added_on)); ?></span>
            </div>

            <!-- Order Now Form -->
            <form action="" method="post" class="mt-auto">
              <input type="hidden" name="food_id" value="<?= $cook_id; ?>">
              <input type="hidden" name="hidden_food_name" value="<?= htmlspecialchars($food_name); ?>">
              <input type="hidden" name="hidden_food_description" value="<?= htmlspecialchars($description); ?>">
              <input type="hidden" name="hidden_price" value="<?= $price; ?>">
              <input type="hidden" name="hidden_food_image" value="<?= htmlspecialchars($food_image); ?>">
              <input type="hidden" name="hidden_added_by" value="<?= htmlspecialchars($added_by); ?>">
              <button class="btn btn-danger w-100 fw-bold rounded-3" type="submit" name="add">
                <i class="fas fa-utensils me-2"></i> Order Now
              </button>
            </form>
          </div>
        </div>
      </div>
      <?php } ?>
    </div>

    <!-- Pagination -->
<!--
    <nav aria-label="Page navigation" class="mt-5">
      <ul class="pagination justify-content-center">
        <?php if($page > 1): ?>
          <li class="page-item">
            <a class="page-link" href="?page=<?= $Previous; ?>" aria-label="Previous">
              <span aria-hidden="true">&laquo; Prev</span>
            </a>
          </li>
        <?php endif; ?>

        <?php for($i = 1; $i <= $total_pages; $i++): ?>
          <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
            <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
          </li>
        <?php endfor; ?>

        <?php if($page < $total_pages): ?>
          <li class="page-item">
            <a class="page-link" href="?page=<?= $Next; ?>" aria-label="Next">
              <span aria-hidden="true">Next &raquo;</span>
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
-->
  </div>
</section>

<!-- Extra Styling -->
<style>
  .food-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .food-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
  }
  .food-img {
    height: 220px;
    object-fit: cover;
    transition: transform 0.4s ease;
  }
  .food-card:hover .food-img {
    transform: scale(1.05);
  }
  .food-desc {
    min-height: 50px;
    line-height: 1.5;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
  }
  .pagination .page-link {
    border-radius: 50% !important;
    margin: 0 4px;
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .pagination .active .page-link {
    background-color: #dc3545;
    border-color: #dc3545;
  }
</style>


<!-- Pagination -->
<div class="shop-pagination pt-3">
  <div class="container">
    <div class="card">
      <div class="card-body py-3">
        <nav aria-label="Page navigation example">
          <ul class="pagination pagination-two justify-content-center">
            <li class="page-item"><a class="page-link" href="cooked-food?page=<?= $Previous; ?>"><i class="bi bi-chevron-left"></i></a></li>
            <?php 
              for($i=1; $i<=$total_pages; $i++){
                  $active = ($i == $page) ? 'active' : '';
                  echo "<li class='page-item $active'><a class='page-link' href='cooked-food?page=$i'>$i</a></li>";
              }
            ?>
            <li class="page-item"><a class="page-link" href="cooked-food?page=<?= $Next; ?>"><i class="bi bi-chevron-right"></i></a></li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</div>
<?php include 'includes/bottom-bar.php'; ?>
<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
