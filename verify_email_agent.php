<?php
session_start();
include 'includes/db.php';

if (isset($_GET['token']) && isset($_GET['email'])) {
    $token = mysqli_real_escape_string($connection, $_GET['token']);
    $email = mysqli_real_escape_string($connection, $_GET['email']);

    // Check if token exists for this agent
    $verify_query = "SELECT verify_token, verify_status FROM agent WHERE email = ? AND verify_token = ? LIMIT 1";
    $stmt = mysqli_prepare($connection, $verify_query);
    mysqli_stmt_bind_param($stmt, "ss", $email, $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if ($row['verify_status'] == "0") {
            // Update status to verified
            $update_query = "UPDATE agent SET verify_status = '1', verify_token = NULL WHERE email = ? LIMIT 1";
            $stmt_update = mysqli_prepare($connection, $update_query);
            mysqli_stmt_bind_param($stmt_update, "s", $email);
            $update_success = mysqli_stmt_execute($stmt_update);

            if ($update_success) {
                $_SESSION['status'] = "✅ Email verification successful! You can now sign in.";
            } else {
                $_SESSION['status'] = "⚠️ Verification failed. Please try again.";
            }
        } else {
            $_SESSION['status'] = "ℹ️ Your account is already verified. Please sign in.";
        }
    } else {
        $_SESSION['status'] = "❌ Invalid or expired verification link.";
    }

    header("Location: index");
    exit();
} else {
    $_SESSION['status'] = "⛔ Access denied.";
    header("Location: login");
    exit();
}
?>
