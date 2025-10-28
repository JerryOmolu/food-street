<?php
session_start();
include 'includes/db.php';

if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($connection, $_GET['token']);

    // Check if token exists
    $verify_query = "SELECT verify_token, verify_status FROM user WHERE verify_token = ? LIMIT 1";
    $stmt = mysqli_prepare($connection, $verify_query);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if ($row['verify_status'] == "0") {
            // Update status to verified
            $update_query = "UPDATE user SET verify_status = '1' WHERE verify_token = ? LIMIT 1";
            $stmt_update = mysqli_prepare($connection, $update_query);
            mysqli_stmt_bind_param($stmt_update, "s", $token);
            $update_success = mysqli_stmt_execute($stmt_update);

            if ($update_success) {
                $_SESSION['status'] = "✅ Verification successful! Please sign in.";
            } else {
                $_SESSION['status'] = "⚠️ Verification failed. Try again.";
            }
        } else {
            $_SESSION['status'] = "ℹ️ Account already verified. Please sign in.";
        }
    } else {
        $_SESSION['status'] = "❌ Invalid token.";
    }

    header("Location: index");
    exit();
} else {
    $_SESSION['status'] = "⛔ Access denied.";
    header("Location: index");
    exit();
}
?>
