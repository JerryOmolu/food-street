<?php
session_start();
include "includes/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $_SESSION['head'] = "Error!";
        $_SESSION['status'] = "Passwords do not match.";
        $_SESSION['status_code'] = "error";
        header("Location: reset-password.php?token=$token&email=$email&error=Passwords do not match");
        exit;
    }

    // Verify token again for safety
    $stmt = $connection->prepare("SELECT password_reset_token, token_expiry FROM user WHERE email = ? AND password_reset_token = ? LIMIT 1");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user || strtotime($user['token_expiry']) < time()) {
        $_SESSION['head'] = "Error!";
        $_SESSION['status'] = "Password reset link is invalid or expired.";
        $_SESSION['status_code'] = "error";
        header("Location: forgot-password.php?error=Invalid or expired token");
        exit;
    }

    // Hash new password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

    // Update password & clear token
    $stmt = $connection->prepare("UPDATE user SET password = ?, password_reset_token = NULL, token_expiry = NULL WHERE email = ?");
    $stmt->bind_param("ss", $hashed_password, $email);

		if ($stmt->execute()) {
		$_SESSION['head'] = "Success!";
		$_SESSION['status'] = "Your password has been reset successfully. You can now sign in.";
		$_SESSION['status_code'] = "success";
		header("Location: signin.php?reset=success");
		exit;
		}

    } else {
        $_SESSION['head'] = "Error!";
        $_SESSION['status'] = "Something went wrong. Please try again.";
        $_SESSION['status_code'] = "error";
        header("Location: reset-password.php?token=$token&email=$email&error=Update failed");
        exit;
    }
?>
