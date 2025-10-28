<?php
include "includes/header.php"; // Includes session, db connection, etc.

if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Please sign in to modify your cart.');
            window.location.href='signin.php';
          </script>";
    exit();
}

if (isset($_GET['cart_id'])) {
    $cart_id = escape($_GET['cart_id']);
    $user_id = $_SESSION['user_id'];

    $delete_query = "DELETE FROM cart WHERE cart_id = '$cart_id' AND user_id = '$user_id'";
    if (mysqli_query($connection, $delete_query)) {
        echo "<script>
                alert('Item removed from cart.');
                window.location.href='cart.php';
              </script>";
        exit();
    } else {
        die('QUERY FAILED: ' . mysqli_error($connection));
    }
} else {
    header("Location: cart.php");
    exit();
}
?>
