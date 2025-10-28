<?php 
include "includes/db.php";

if (!isset($_GET['token']) || !isset($_GET['email'])) {
    $_SESSION['head'] = "Error!";
    $_SESSION['status'] = "Invalid password reset link.";
    $_SESSION['status_code'] = "error";
    header("Location: signin");
    exit;
}

$token = $_GET['token'];
$email = $_GET['email'];

// Check if token + email exist and are valid
$stmt = $connection->prepare("SELECT password_reset_token, token_expiry FROM user WHERE email = ? AND password_reset_token = ? LIMIT 1");
$stmt->bind_param("ss", $email, $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || strtotime($user['token_expiry']) < time()) {
    $_SESSION['head'] = "Error!";
    $_SESSION['status'] = "Password reset link is invalid or has expired.";
    $_SESSION['status_code'] = "error";
    header("Location: forgot-password.php?error=Invalid or expired token");
    exit;
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/nav.php'; ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-lg rounded-4 border-0">
        <div class="card-body p-4">
          <h3 class="text-center mb-4 text-dark">Reset Password</h3>
			
<!-- Error Alert -->
<?php if (isset($_GET['error'])): ?>
  <div id="alertBox" class="alert alert-danger alert-dismissible fade show text-center rounded-3 shadow-sm" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>
    <?= htmlspecialchars($_GET['error']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<!-- Success Alert -->
<?php if (isset($_GET['success'])): ?>
  <div id="alertBox" class="alert alert-success alert-dismissible fade show text-center rounded-3 shadow-sm" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    <?= htmlspecialchars($_GET['success']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

	

          <!-- Reset Password Form -->
          <form action="reset-password_logic.php" method="POST">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <!-- New Password -->
            <div class="mb-3">
              <label for="password" class="form-label">New Password</label>
              <input type="password" class="form-control" id="password" 
                     name="password" placeholder="Enter new password" required>
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
              <label for="confirm_password" class="form-label">Confirm Password</label>
              <input type="password" class="form-control" id="confirm_password" 
                     name="confirm_password" placeholder="Confirm new password" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-warning w-100 text-dark fw-bold">
              <i class="fas fa-key me-2"></i> Reset Password
            </button>
          </form>

          <!-- Divider -->
          <hr class="my-4">

          <p class="text-center mb-0">
            Remembered your password?  
            <a href="signin" class="fw-bold text-primary">Sign in here</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Auto Fade Script -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const alertBox = document.getElementById("alertBox");
    if (alertBox) {
      setTimeout(() => {
        // Fade out effect
        alertBox.classList.remove("show");
        alertBox.classList.add("fade");
        // Remove from DOM after fade
        setTimeout(() => alertBox.remove(), 500);
      }, 4000); // 4 seconds
    }
  });
</script>

<?php include 'includes/footer.php'; ?>
