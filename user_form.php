<?php 
session_start();
include "includes/db.php";
include "includes/function.php";

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
        $mail->Username   = 'foodstreet@wakocoding.com';   // SMTP username
        $mail->Password   = 'michaelking123';        // ⚠️ Move this to .env / config
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465; 

        $mail->setFrom('foodstreet@wakocoding.com', 'Food Street');
        $mail->addAddress($email); 

        $mail->isHTML(true);
        $mail->Subject = 'Email Verification - Food Street';

        $verify_link = "http://localhost/food-street/verify_email.php?token=$verify_token&email=$email&phone=$phone";

        $mail->Body = "
<div style='font-family: Arial, sans-serif; background-color:#f8f9fa; padding:20px;'>
    <div style='max-width:600px; margin:0 auto; background:#ffffff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); padding:30px;'>
        
        <!-- Logo Section -->
        <div style='text-align:center; margin-bottom:20px;'>
            <img src='http://localhost/food-street/images/Logo.png' alt='Food Street Logo' style='max-width:150px;'>
        </div>
        
        <!-- Welcome Header -->
        <div style='text-align:center;'>
            <h2 style='color:#ff6600; margin-bottom:10px;'>🍴 Welcome to Food Street!</h2>
            <p style='font-size:16px; color:#555;'>Hi there,</p>
        </div>

        <!-- Body Content -->
        <p style='font-size:15px; color:#333; line-height:1.6;'>
            Thank you for joining <strong>Food Street</strong>! We're excited to have you on board.  
            To get started, please verify your email address and activate your account.
        </p>

        <!-- CTA Button -->
        <div style='text-align:center; margin:30px 0;'>
            <a href='$verify_link' style='background:#ff6600; color:#fff; text-decoration:none; padding:12px 24px; border-radius:6px; font-size:16px;'>
                ✅ Verify My Email
            </a>
        </div>

        <!-- Fallback Link -->
        <p style='font-size:14px; color:#777;'>
            If the button above doesn't work, copy and paste the following link into your browser:  
            <br><a href='$verify_link' style='color:#ff6600;'>$verify_link</a>
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

// -------------------- REGISTER LOGIC --------------------
if (isset($_POST['register_user'])) {
    $name     = trim($_POST['full_name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = $_POST['password'];
    $verify_token = md5(rand());

    $errors = [];

    // Validate email
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } else {
        $stmt = $connection->prepare("SELECT email FROM user WHERE email = ? LIMIT 1");
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
        $stmt = $connection->prepare("SELECT phone FROM user WHERE phone = ? LIMIT 1");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) $errors['phone'] = "Phone number already exists.";
        $stmt->close();
    }

    // If no errors, register user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

        $stmt = $connection->prepare("INSERT INTO user (full_name, email, phone, password, added_on, verify_token) VALUES (?, ?, ?, ?, NOW(), ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $hashed_password, $verify_token);

        if ($stmt->execute()) {
            sendemail_verify($email, $phone, $verify_token);

            $_SESSION['head'] = "Thank You!";
            $_SESSION['status'] = "Registration successful. Please check '$email' for verification link.";
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
    } else {
        $_SESSION['head'] = "Error!";
        $_SESSION['status'] = implode("\n", $errors);
        $_SESSION['status_code'] = "error";
        header("Location: register");
        exit;
    }
}
?>
