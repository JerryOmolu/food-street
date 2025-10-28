<?php 
session_start();
include "includes/db.php";
include "includes/function.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// -------------------- EMAIL FUNCTION --------------------
function sendemail_password_reset($email, $reset_token) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP(); 
        $mail->SMTPAuth   = true; 
        $mail->Host       = 'wakocoding.com'; 
        $mail->Username   = 'foodstreet@wakocoding.com';   // SMTP username
        $mail->Password   = 'michaelking123';        // ⚠️ Move to .env or config
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465; 

        $mail->setFrom('foodstreet@wakocoding.com', 'Food Street');
        $mail->addAddress($email); 

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset - Food Street';

        $reset_link = "http://localhost/food-street/reset-password.php?token=$reset_token&email=$email";

        $mail->Body = "
<div style='font-family: Arial, sans-serif; background-color:#f8f9fa; padding:20px;'>
    <div style='max-width:600px; margin:0 auto; background:#ffffff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); padding:30px;'>
        
        <!-- Logo Section -->
        <div style='text-align:center; margin-bottom:20px;'>
            <img src='http://localhost/food-street/images/Logo.png' alt='Food Street Logo' style='max-width:150px;'>
        </div>
        
        <!-- Header -->
        <div style='text-align:center;'>
            <h2 style='color:#ff6600; margin-bottom:10px;'>🔑 Password Reset Request</h2>
            <p style='font-size:16px; color:#555;'>Hi there,</p>
        </div>

        <!-- Body Content -->
        <p style='font-size:15px; color:#333; line-height:1.6;'>
            We received a request to reset your <strong>Food Street</strong> account password.  
            If this was you, click the button below to reset your password.
        </p>

        <!-- CTA Button -->
        <div style='text-align:center; margin:30px 0;'>
            <a href='$reset_link' style='background:#ff6600; color:#fff; text-decoration:none; padding:12px 24px; border-radius:6px; font-size:16px;'>
                🔒 Reset My Password
            </a>
        </div>

        <!-- Fallback Link -->
        <p style='font-size:14px; color:#777;'>
            If the button above doesn't work, copy and paste the following link into your browser:  
            <br><a href='$reset_link' style='color:#ff6600;'>$reset_link</a>
        </p>

        <!-- Footer -->
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

// -------------------- FORGOT PASSWORD LOGIC --------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $_SESSION['head'] = "Error!";
        $_SESSION['status'] = "Email is required.";
        $_SESSION['status_code'] = "error";
        header("Location: forgot-password.php?error=Email is required");
        exit;
    }

    // Check if email exists
    $stmt = $connection->prepare("SELECT email FROM user WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $_SESSION['head'] = "Error!";
        $_SESSION['status'] = "No account found with that email address.";
        $_SESSION['status_code'] = "error";
        header("Location: forgot-password.php?error=No account found with that email");
        exit;
    } else {
        // Generate reset token & expiry
        $reset_token = md5(rand());
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Store token in DB
        $stmt->close();
        $stmt = $connection->prepare("UPDATE user SET password_reset_token = ?, token_expiry = ? WHERE email = ?");
        $stmt->bind_param("sss", $reset_token, $expiry, $email);

        if ($stmt->execute()) {
            sendemail_password_reset($email, $reset_token);

            $_SESSION['head'] = "Check Your Email!";
            $_SESSION['status'] = "A password reset link has been sent to '$email'.";
            $_SESSION['status_code'] = "success";
            header("Location: forgot-password.php?success=Password reset link sent");
            exit;
        } else {
            $_SESSION['head'] = "Error!";
            $_SESSION['status'] = "Something went wrong. Please try again.";
            $_SESSION['status_code'] = "error";
            header("Location: forgot-password.php?error=Something went wrong");
            exit;
        }
    }
}
?>
