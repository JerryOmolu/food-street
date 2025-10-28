<?php
$pageTitle = "Cooked Food - Food Street";
?>
<?php include "includes/header.php"; ?>

<!-- Include Navbar -->
<?php include 'includes/nav.php'; ?>

<!-- Restaurant Content -->
<!-- Hero Section -->
<section class="hero-section d-flex align-items-center text-white text-center">
  <div class="container">
    <h1 class="display-4 fw-bold">Cooked Food</h1>
    <p class="lead">Order fresh produce, farm products, and groceries from trusted vendors around you.</p>
  </div>
</section>

<!-- Custom CSS -->
<style>
.hero-section {
  background: url('images/bg-white1.jpg') center center / cover no-repeat;
  min-height: 60vh; /* adjust height as needed */
  position: relative;
}

.hero-section::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.4); /* dark overlay for better text readability */
}

.hero-section .container {
  position: relative;
  z-index: 1;
}
</style>


  <!-- Vendors List -->
  <section class="py-5">
    <div class="container">
      <div class="row g-4">

        <!-- Vendor Card 1 -->
    <?php  
	$perpage = 20;
        if(isset($_GET['page'])){
            $page = escape($_GET['page']);
        }else{
            $page = "";
        }
        if($page == "" || $page == 1){
            $page_1 = 0;
        }else{
            $page_1 = ($page * $perpage)-$perpage;
        }

        $query1 = "SELECT * FROM cooked_food ORDER BY added_on DESC";
        $view_products1 = mysqli_query($connection, $query1);
        $total = mysqli_num_rows($view_products1);
        $total = ceil($total/$perpage);
        $Previous = (int)$page - 1;
        $Next = (int)$page + 1;	  
		  
		  
    $query = "SELECT * FROM cooked_food ORDER BY added_on DESC LIMIT $page_1, $perpage";
    $select_food_query = mysqli_query($connection, $query);

    if(!$select_food_query){
        die('QUERY FAILED: ' . mysqli_error($connection));
    }

    while($row = mysqli_fetch_array($select_food_query)){
        $cook_id    = escape($row['cook_id']);
        $food_name  = escape($row['food_name']);
        $description= escape($row['description']);
        $price      = escape($row['price']);
        $food_image = escape($row['food_image']);                
        $added_on   = escape($row['added_on']);                
        $added_by   = escape($row['added_by']);                                
?>     
		  
    <div class="col-6 col-sm-6 col-md-4 mb-4">
    <div class="card shadow-sm h-100">
        <img src="admin/uploads/cooked-food/<?php echo $food_image ?>" 
             class="card-img-top" 
             alt="<?php echo htmlspecialchars($food_name); ?>" 
             style="height: 220px; object-fit: cover;">
        
        <div class="card-body">
            <!-- Food name -->
            <h5 class="card-title fw-bold"><?php echo $food_name ?></h5>
            
            <!-- Description -->
            <p class="card-text text-muted" style="min-height: 50px;">
                <?php echo $description ?>
            </p>

            <!-- Price -->
            <p class="mb-1">
                <span class="fw-bold text-success">₦ <?php echo number_format($price, 2); ?></span>
            </p>

            <!-- Added by -->
            <p class="mb-1">
                <span class="badge bg-primary">🧑‍🍳 <?php echo $added_by; ?></span>
            </p>

            <!-- Date added -->
            <p class="mb-3">
                <span class="badge bg-light text-dark">📅 <?php echo date("M d, Y", strtotime($added_on)); ?></span>
            </p>

            <!-- Button -->
            <a href="#" class="btn btn-danger w-100">Order Now</a>
        </div>
    </div>
</div>

<?php 
    } 
?>

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
                  
                <li class="page-item">
                <a class="page-link" href="cooked-food?page=<?= $Previous; ?>" aria-label="Previous">
                <span aria-hidden="true"><i class="bi bi-chevron-left"></i></span>   
                </a>
                </li>
                <?php 
                for($i=1; $i<=$total; $i++){
                if($i == $page){
                echo "<li class='page-item'>
                <a class='page-link' href='cooked-food?page={$i}'>{$i}</a></li>";
                }else{
                echo "<li class='page-item'>
                <a class='page-link' href='cooked-food?page={$i}'>{$i}</a></li>"; 
                }
                }
                ?>  
                <li class="page-item">
                <a class="page-link" href="cooked-food?page=<?= $Next; ?>" aria-label="Next">
                <span aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
                </a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
  

<!-- Footer -->
<?php include 'includes/footer.php'; ?>

<!-- Hover Effect CSS -->
<style>
  .hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .hover-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
  }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
