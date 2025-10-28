<?php include "includes/header.php"; ?>
<?php include "includes/nav.php"; ?>
<style>
    body {
      background: #f8f9fa;
    }
    .register-container {
      max-width: 900px;
      margin: 40px auto;
    }
    .card {
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .nav-pills .nav-link.active {
      background-color: #ff6600;
    }
    .form-section {
      padding: 20px;
    }
  </style>
<div class="container register-container">
  <div class="text-center mb-4">
    <h2 class="fw-bold">Join Foodstreet</h2>
    <p>Select your category and get started today!</p>
  </div>

  <!-- Tabs for Category Selection -->
  <ul class="nav nav-pills mb-3 justify-content-center" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="pills-vendor-tab" data-bs-toggle="pill" data-bs-target="#pills-vendor" type="button" role="tab">Vendor</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="pills-agent-tab" data-bs-toggle="pill" data-bs-target="#pills-agent" type="button" role="tab">Agent</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="pills-user-tab" data-bs-toggle="pill" data-bs-target="#pills-user" type="button" role="tab">General User</button>
    </li>
  </ul>

  <div class="tab-content" id="pills-tabContent">

    <!-- Vendor Form -->
    <div class="tab-pane fade show active" id="pills-vendor" role="tabpanel">
      <div class="card">
        <div class="card-header bg-warning text-dark fw-bold">Vendor Registration (₦55,000 or ₦125,000)</div>
        <div class="card-body form-section">
          <p><strong>Benefits:</strong> Own a branded shop, sell food, earn commissions, and qualify for Super Vendor status.</p>
          <form>
            <div class="mb-3">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control" placeholder="Enter your full name">
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" placeholder="Enter your email">
            </div>
            <div class="mb-3">
              <label class="form-label">Phone Number</label>
              <input type="text" class="form-control" placeholder="Enter phone number">
            </div>
            <div class="mb-3">
              <label class="form-label">Choose Vendor Type</label>
              <select class="form-select">
                <option>Regular Vendor - ₦55,000</option>
                <option>Mix Vendor - ₦35,000</option>
                <option>Super Vendor - ₦125,000</option>
              </select>
            </div>
            <button class="btn btn-warning w-100">Register as Vendor</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Agent Form -->
  <div class="tab-pane fade" id="pills-agent" role="tabpanel">
  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white fw-bold">
      Agent Registration (₦18,500)
    </div>
    <div class="card-body">

      <!-- Alert Messages -->
      <?php
      if (isset($_SESSION['status']) && $_SESSION['status_code']) {
          $alertClass = ($_SESSION['status_code'] == "success") ? "alert-success" : "alert-danger";
          echo "
          <div class='alert $alertClass alert-dismissible fade show' role='alert'>
              <strong>{$_SESSION['head']}</strong> {$_SESSION['status']}
              <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
          unset($_SESSION['head'], $_SESSION['status'], $_SESSION['status_code']);
      }
      ?>

      <p class="mb-3">
        <strong>Benefits:</strong> Earn by recruiting vendors, selling Abundish Premium Cards, and grow into Super Vendor.
      </p>

      <form action="agent_form.php" method="POST" novalidate>
        
        <!-- Full Name -->
        <div class="mb-3">
          <label for="agent_fullname" class="form-label">Full Name</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-user"></i></span>
            <input type="text" class="form-control" id="agent_fullname" 
                   name="full_name" placeholder="Enter your full name"
                   value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>" required>
          </div>
          <div class="invalid-feedback">Please enter your full name.</div>
        </div>

        <!-- Email -->
        <div class="mb-3">
          <label for="agent_email" class="form-label">Email Address</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
            <input type="email" class="form-control" id="agent_email" 
                   name="email" placeholder="Enter your email"
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
          </div>
          <div class="invalid-feedback">Enter a valid email address.</div>
        </div>

        <!-- Phone -->
        <div class="mb-3">
          <label for="agent_phone" class="form-label">Phone Number</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-phone"></i></span>
            <input type="tel" class="form-control" id="agent_phone" 
                   name="phone" pattern="^0[789][01]\d{8}$" 
                   placeholder="e.g. 08012345678"
                   value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>" required>
          </div>
          <div class="invalid-feedback">Enter a valid Nigerian phone number.</div>
        </div>

        <!-- Password -->
        <div class="mb-3">
          <label for="agent_password" class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control" id="agent_password" 
                   name="password" placeholder="Enter password" minlength="6" required>
          </div>
          <div class="invalid-feedback">Password must be at least 6 characters.</div>
        </div>

        <!-- CSRF Protection -->
        <input type="hidden" name="csrf_token" value="<?php echo bin2hex(random_bytes(32)); ?>">

        <!-- Register Button -->
        <button type="submit" name="register_agent" class="btn btn-primary w-100 fw-bold">
          <i class="fas fa-user-plus me-2"></i> Register as Agent
        </button>
      </form>
    </div>
  </div>
</div>



    <!-- General User Form -->
    <div class="tab-pane fade" id="pills-user" role="tabpanel">
  <div class="card">
    <div class="card-header bg-success text-white fw-bold">General User Registration</div>
    <div class="card-body form-section">
      <p><strong>Benefits:</strong> Order food, enjoy discounts, and connect with Foodstreet vendors easily.</p>
      
      <form action="user_form.php" method="post">
        
        <!-- Full Name -->
        <div class="mb-3">
          <label for="full_name" class="form-label">Full Name</label>
          <input 
            type="text" 
            id="full_name" 
            name="full_name" 
            class="form-control" 
            placeholder="Enter your full name"
            value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>" 
            required>
        </div>

        <!-- Email -->
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            class="form-control" 
            placeholder="Enter your email"
            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" 
            required>
          <?php if(isset($errors['e'])): ?>
            <small class="text-danger"><?= $errors['e']; ?></small>
          <?php endif; ?>
        </div>

        <!-- Phone -->
        <div class="mb-3">
          <label for="phone" class="form-label">Phone Number</label>
          <input 
            type="tel" 
            id="phone" 
            name="phone" 
            class="form-control" 
            placeholder="Enter phone number"
            value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>" 
            required>
          <?php if(isset($errors['p'])): ?>
            <small class="text-danger"><?= $errors['p']; ?></small>
          <?php endif; ?>
        </div>

        <!-- Password -->
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input 
            type="password" 
            id="password" 
            name="password" 
            class="form-control" 
            placeholder="Enter password" 
            required>
        </div>

        <!-- Submit -->
        <button class="btn btn-success w-100" type="submit" name="register_user">Register as User</button>
      </form>
    </div>
  </div>
</div>
	  
<script>
  // Bootstrap validation
  (() => {
    'use strict';
    const forms = document.querySelectorAll('form');
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity() || 
            document.getElementById('agent_password').value !== 
            document.getElementById('agent_confirm_password').value) {
          event.preventDefault();
          event.stopPropagation();
          document.getElementById('agent_confirm_password').setCustomValidity("Passwords do not match");
        } else {
          document.getElementById('agent_confirm_password').setCustomValidity("");
        }
        form.classList.add('was-validated');
      }, false);
    });
  })();

  // Auto-hide alerts
  setTimeout(() => {
    let alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
      let bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    });
  }, 4000);
</script>	  
	  
	  


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	  
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>	
	  
<!-- Sweet Alert -->
<script src="assets/js/sweetalert.js"></script>
<?php 
if(isset($_SESSION['status']) && $_SESSION['status'] != ''): ?>
  <script>
    swal({
      title: "<?= $_SESSION['head']; ?>",
      text: "<?= $_SESSION['status']; ?>",
      icon: "<?= $_SESSION['status_code']; ?>",
      button: "OK",
    }).then(() => {
      window.location.href = "register.php";
    });
  </script>
<?php 
  unset($_SESSION['status'], $_SESSION['head'], $_SESSION['status_code']);
endif;
?>
	  
</body>
</html>
