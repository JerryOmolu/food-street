<?php
$pageTitle = "Forgot Password - Food Street";
?>
<!-- forgot-password.php -->
<?php include 'includes/header.php'; ?>
<?php include 'includes/nav.php'; ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-lg rounded-4 border-0">
        <div class="card-body p-4">
          <h3 class="text-center mb-4 text-dark">Forgot Password</h3>
          <p class="text-center text-muted mb-4">
            Enter your registered email address and we’ll send you instructions to reset your password.
          </p>

          <!-- Error Message -->
          <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger text-center">
              <?= htmlspecialchars($_GET['error']) ?>
            </div>
          <?php endif; ?>

          <!-- Success Message -->
          <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success text-center">
              <?= htmlspecialchars($_GET['success']) ?>
            </div>
          <?php endif; ?>

          <!-- Forgot Password Form -->
          <form action="forgot-password_logic.php" method="POST">
            
            <!-- Email -->
            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="email" 
                     name="email" placeholder="Enter your registered email" 
                     autocomplete="username" required>
            </div>

            <!-- Reset Button -->
            <button type="submit" class="btn btn-warning w-100 text-dark fw-bold">
              <i class="fas fa-paper-plane me-2"></i> Send Reset Link
            </button>

          </form>

          <!-- Divider -->
          <hr class="my-4">

          <!-- Back to Sign In Link -->
          <p class="text-center mb-0">
            Remembered your password? 
            <a href="signin" class="fw-bold text-primary">Sign in here</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
