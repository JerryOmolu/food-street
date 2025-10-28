<?php
session_start();
include "includes/db.php";
include "includes/function.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = escape($_POST['email']);
    $password = escape($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['head'] = "Error!";
        $_SESSION['status'] = "Email and Password are required.";
        $_SESSION['status_code'] = "error";
        header("Location: signin");
        exit();
    }

    $query = "SELECT * FROM user WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            // ✅ Save user session
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['logged_in'] = true; // 👈 indicator for login

            $_SESSION['head'] = "Welcome Back!";
            $_SESSION['status'] = "Login successful. Hello, " . $row['full_name'] . "!";
            $_SESSION['status_code'] = "success";

            header("Location: index"); // 👈 Redirect to homepage
            exit();
        } else {
            $_SESSION['head'] = "Error!";
            $_SESSION['status'] = "Invalid email or password.";
            $_SESSION['status_code'] = "error";
            header("Location: signin");
            exit();
        }
    } else {
        $_SESSION['head'] = "Error!";
        $_SESSION['status'] = "No account found with that email.";
        $_SESSION['status_code'] = "error";
        header("Location: signin");
        exit();
    }
}
?>
