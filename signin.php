<?php
$pageTitle = "Sign In - Food Street";
?>
<?php 
include 'includes/header.php'; 
include 'includes/nav.php'; 
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-lg rounded-4 border-0">
        <div class="card-body p-4">
          <h3 class="text-center mb-4 text-dark">Sign In</h3>

          <!-- SweetAlert for Success -->
          <?php 
			error_reporting(0);
			if (isset($_SESSION['status']) && $_SESSION['status_code'] == "success"): ?>
            <script>
              Swal.fire({
                icon: 'success',
                title: '<?= $_SESSION['head'] ?>',
                text: '<?= $_SESSION['status'] ?>',
                confirmButtonColor: '#ffc107'
              });
            </script>
            <?php 
              unset($_SESSION['head']);
              unset($_SESSION['status']);
              unset($_SESSION['status_code']);
            ?>
          <?php endif; ?>

          <!-- Error Message (GET method) -->
          <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
              <?= htmlspecialchars($_GET['error']) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>
          
          <!-- Sign In Form -->
<form action="signin_logic.php" method="POST">
  <!-- Email -->
  <div class="mb-3">
    <label for="email" class="form-label">Email Address</label>
    <input type="email" class="form-control" id="email" 
           name="email" placeholder="Enter your email" 
           autocomplete="username" required>
  </div>

  <!-- Password -->
  <div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <input type="password" class="form-control" id="password" 
           name="password" placeholder="Enter your password" 
           autocomplete="current-password" required>
    <!-- Show Password -->
    <div class="form-check mt-2">
      <input class="form-check-input" type="checkbox" id="showPassword">
      <label class="form-check-label" for="showPassword">Show Password</label>
    </div>
  </div>

  <!-- Forgot Password -->
  <div class="d-flex justify-content-end mb-3">
    <a href="forgot-password" class="text-decoration-none">Forgot Password?</a>
  </div>
  
  <!-- Sign In Button -->
  <button type="submit" class="btn btn-warning w-100 text-dark fw-bold">
    <i class="fas fa-sign-in-alt me-2"></i> Sign In
  </button>
</form>

          <!-- Divider -->
          <hr class="my-4">

          <!-- Sign Up Link -->
          <p class="text-center mb-0">
            Don’t have an account? 
            <a href="register" class="fw-bold text-primary">Register here</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
