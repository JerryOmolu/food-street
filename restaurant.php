<?php
$pageTitle = "Restaurant - Food Street";
?>
<?php include "includes/header.php"; ?>

<!-- Include Navbar -->
<?php include 'includes/nav.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <h1 class="fw-bold display-4">Restaurants</h1>
    <p class="lead">Your favorite meals, fresh and local, delivered to your doorstep</p>
  </div>
</section>

<!-- Custom CSS -->
<style>
.hero-section {
  background: url('images/restaurant.jpg') center center / cover no-repeat;
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

<!-- Restaurant Content -->
<section id="restaurants" class="py-5">
  <div class="container">
    <div class="row h-100">
      <div class="col-lg-7 mx-auto text-center mb-6">
        <h5 class="fw-bold fs-3 fs-lg-5 lh-sm mb-3">Discover Restaurants</h5>
      </div>
    </div>
	  
	  
	<div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <form class="d-flex shadow-sm rounded-3 overflow-hidden">
          <input class="form-control form-control-lg border-0" type="search" placeholder="Search restaurants..." aria-label="Search">
          <button class="btn btn-danger px-4" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </form>
      </div>
    </div>
  </div>  
	  
<br><br>
    <div class="row g-4">
      <!-- Checkpoint -->
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

        $query1 = "SELECT * FROM vendor ORDER BY added_on DESC";
        $view_products1 = mysqli_query($connection, $query1);
        $total = mysqli_num_rows($view_products1);
        $total = ceil($total/$perpage);
        $Previous = (int)$page - 1;
        $Next = (int)$page + 1;	 
		
    $query = "SELECT * FROM vendor ORDER BY added_on DESC LIMIT $page_1, $perpage";
    $select_vendor_query = mysqli_query($connection, $query);

    if(!$select_vendor_query){
        die('QUERY FAILED: ' . mysqli_error($connection));
    }

    while($row = mysqli_fetch_array($select_vendor_query)){
        $vendor_id      = escape($row['vendor_id']);
        $vendor_name    = escape($row['vendor_name']);
        $email          = escape($row['email']);
        $phone          = escape($row['phone']);
        $profile_picture= escape($row['profile_picture']);                
        $address        = escape($row['address']);                
        $added_on       = escape($row['added_on']);                                
        $added_by       = escape($row['added_by']);                                
        $verify_status  = escape($row['verify_status']);
        $reg_amount     = escape($row['reg_amount']);                         
        $reg_status     = escape($row['reg_status']);                                
?>	  
			  
    <div class="col-6 col-sm-6 col-md-4 col-lg-3 h-100 mb-5">
    <div class="card h-100 shadow-lg border-0 rounded-4 hover-card">
        
        <!-- Main vendor image with fixed size -->
        <img class="img-fluid rounded-3" 
             src="foodstreet-vendor/uploads/vendors/<?php echo $profile_picture ?>" 
             alt="<?php echo htmlspecialchars($vendor_name); ?>" 
             style="height: 220px; width: 100%; object-fit: cover;">
        
        <div class="card-body ps-0">
            <div class="d-flex align-items-center mb-3">
                
                <!-- Small profile icon with fixed size -->
                <img class="rounded-circle" 
                     src="foodstreet-vendor/uploads/vendors/<?php echo $profile_picture ?>" 
                     alt="<?php echo htmlspecialchars($vendor_name); ?>" 
                     style="width: 60px; height: 60px; object-fit: cover;">
                
                <div class="flex-1 ms-3">
                    <h5 class="mb-0 fw-bold text-1000"><?php echo $vendor_name ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>

			  
<?php 
    } 
?>	


    <div class="shop-pagination pt-3">
      <div class="container">
        <div class="card">
          <div class="card-body py-3">
            <nav aria-label="Page navigation example">
              <ul class="pagination pagination-two justify-content-center">
                  
                <li class="page-item">
                <a class="page-link" href="restaurant?page=<?= $Previous; ?>" aria-label="Previous">
                <span aria-hidden="true"><i class="bi bi-chevron-left"></i></span>   
                </a>
                </li>
                <?php 
                for($i=1; $i<=$total; $i++){
                if($i == $page){
                echo "<li class='page-item'>
                <a class='page-link' href='restaurant?page={$i}'>{$i}</a></li>";
                }else{
                echo "<li class='page-item'>
                <a class='page-link' href='restaurant?page={$i}'>{$i}</a></li>"; 
                }
                }
                ?>  
                <li class="page-item">
                <a class="page-link" href="restaurant?page=<?= $Next; ?>" aria-label="Next">
                <span aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
                </a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
	</div>
</section>


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
