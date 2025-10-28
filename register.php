<?php
$pageTitle = "Register - Food Street";
?>
<?php include "includes/header.php"; ?>
<?php include "includes/nav.php"; ?>
<style>
  body { background: #f8f9fa; }
  .register-container { max-width: 900px; margin: 40px auto; }
  .card { border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
  .nav-pills .nav-link.active { background-color: #ff6600; }
  .form-section { padding: 20px; }
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
			
		<?php if (isset($error)) { ?>
                  <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } ?>
                <?php if (isset($success)) { ?>
                  <div class="alert alert-success"><?php echo $success; ?></div>
                <?php } ?>
				  
	<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// -------------------- EMAIL FUNCTION --------------------
function sendemail_verify($email, $phone, $verify_token){
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP(); 
        $mail->SMTPAuth   = true; 
        $mail->Host       = 'wakocoding.com'; 
        $mail->Username   = 'foodstreet@wakocoding.com';   // ⚠️ Move to env/config
        $mail->Password   = 'michaelking123';        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465; 

        $mail->setFrom('foodstreet@wakocoding.com', 'Food Street');
        $mail->addAddress($email); 

        $mail->isHTML(true);
        $mail->Subject = 'Vendor Email Verification - Food Street';

        $verify_link = "http://localhost/food-street/agent/vendor_verify.php?token=$verify_token&email=$email&phone=$phone";

        $mail->Body = "
<div style='font-family: Arial, sans-serif; background-color:#f8f9fa; padding:20px;'>
    <div style='max-width:600px; margin:0 auto; background:#ffffff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); padding:30px;'>
        
        <div style='text-align:center; margin-bottom:20px;'>
            <img src='http://localhost/food-street/images/Logo.png' alt='Food Street Logo' style='max-width:150px;'>
        </div>
        
        <div style='text-align:center;'>
            <h2 style='color:#ff6600; margin-bottom:10px;'>🍴 Welcome to Food Street!</h2>
            <p style='font-size:16px; color:#555;'>Hi Vendor,</p>
        </div>

        <p style='font-size:15px; color:#333; line-height:1.6;'>
            Thank you for registering as a <strong>Food Street Vendor</strong>!  
            Please verify your email address to activate your vendor account.
        </p>

        <div style='text-align:center; margin:30px 0;'>
            <a href='$verify_link' style='background:#ff6600; color:#fff; text-decoration:none; padding:12px 24px; border-radius:6px; font-size:16px;'>
                ✅ Verify My Email
            </a>
        </div>

        <p style='font-size:14px; color:#777;'>
            If the button above doesn't work, copy and paste this link into your browser:  
            <br><a href='$verify_link' style='color:#ff6600;'>$verify_link</a>
        </p>

        <hr style='border:none; border-top:1px solid #eee; margin:20px 0;'>
        <p style='font-size:12px; color:#999; text-align:center;'>
            &copy; " . date('Y') . " Food Street. All rights reserved.
        </p>
    </div>
</div>
";

        $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
    }
}

// -------------------- REGISTER LOGIC --------------------
if (isset($_POST['register_vendor'])) {
    $vendor_name = trim($_POST['vendor_name']);
    $email       = trim($_POST['email']);
    $phone       = trim($_POST['phone']);
    $address     = trim($_POST['address']);
    $password    = $_POST['password'];
    $verify_token = md5(rand());
	
    $errors = [];

    // Validate email
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } else {
        $stmt = $connection->prepare("SELECT email FROM vendor WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $errors['email'] = "Email already exists.";
        $stmt->close();
    }

    // Validate phone
    if (empty($phone)) {
        $errors['phone'] = "Phone number is required.";
    } else {
        $stmt = $connection->prepare("SELECT phone FROM vendor WHERE phone = ? LIMIT 1");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $errors['phone'] = "Phone number already exists.";
        $stmt->close();
    }

    // If no errors, register vendor
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
        $status = "Pending";
        $added_by = "Self";

        $stmt = $connection->prepare("INSERT INTO vendor 
            (vendor_name, email, phone, address, password, verify_token, reg_status, added_on, added_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)");

        $stmt->bind_param("ssssssss", 
            $vendor_name, 
            $email, 
            $phone, 
            $address, 
            $hashed_password, 
            $verify_token, 
            $status,
			$added_by			  
        );

        if ($stmt->execute()) {
            sendemail_verify($email, $phone, $verify_token);

            $_SESSION['head'] = "Congratulations!";
            $_SESSION['status'] = "Vendor registration successful. Please check '$email' for the verification link.";
            $_SESSION['status_code'] = "success";
            header("Location: register");
            exit;
        } else {
            $_SESSION['head'] = "Error!";
            $_SESSION['status'] = "Something went wrong. Please try again.";
            $_SESSION['status_code'] = "error";
            header("Location: register");
            exit;
        }
        $stmt->close();
    } else {
        $_SESSION['head'] = "Error!";
        $_SESSION['status'] = implode("<br>", $errors);
        $_SESSION['status_code'] = "error";
        header("Location: register");
        exit;
    }
}
?>	
			
			
			
          <form method="POST" action="">
                  <div class="form-group">
                    <label for="vendor_name">Vendor Name</label>
                    <input type="text" class="form-control" id="vendor_name" name="vendor_name" required>
                  </div>

                  <div class="form-group">
                    <label for="email">Vendor Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                  </div>

                  <div class="form-group">
                    <label for="phone">Vendor Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" required>
                  </div>

                  <div class="form-group">
                    <label for="address">Vendor Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                  </div>

                  <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                  </div><br>

                  <button type="submit" name= "register_vendor" class="btn btn-primary mr-2">Register Vendor</button>
                  <button type="reset" class="btn btn-light">Reset</button>
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
                       name="full_name" placeholder="Enter your full name" required>
              </div>
              <div class="invalid-feedback">Please enter your full name.</div>
            </div>

            <!-- Email -->
            <div class="mb-3">
              <label for="agent_email" class="form-label">Email Address</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control" id="agent_email"
                       name="email" placeholder="Enter your email" required>
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
                       placeholder="e.g. 08012345678" required>
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

          <form action="user_form.php" method="post" novalidate>
            <!-- Full Name -->
            <div class="mb-3">
              <label for="full_name" class="form-label">Full Name</label>
              <input
                type="text"
                id="full_name"
                name="full_name"
                class="form-control"
                placeholder="Enter your full name"
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
                required>
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
                required>
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

  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  // Basic Bootstrap validation (no confirm-password logic here)
  (() => {
    'use strict';
    const forms = document.querySelectorAll('form');
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  })();
</script>

<?php 
// SweetAlert2 + keep active tab after redirect
if (isset($_SESSION['status']) && $_SESSION['status'] !== ''):
  $active_tab = $_SESSION['active_tab'] ?? 'vendor'; // 'vendor' | 'agent' | 'user'
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Show the correct tab after redirect
  const tabId = 'pills-<?= $active_tab ?>-tab';
  const triggerTab = document.getElementById(tabId);
  if (triggerTab) {
    const tab = new bootstrap.Tab(triggerTab);
    tab.show();
  }

  // SweetAlert2 popup
  Swal.fire({
    icon: "<?= $_SESSION['status_code']; ?>", // success | error | warning | info | question
    title: "<?= $_SESSION['head']; ?>",
    text: "<?= $_SESSION['status']; ?>",
    confirmButtonText: "OK"
  });
});
</script>
<?php
  // Clear flash
  unset($_SESSION['status'], $_SESSION['head'], $_SESSION['status_code'], $_SESSION['active_tab']);
endif;
?>

</body>
</html>
