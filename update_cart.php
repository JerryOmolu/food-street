<?php
include "includes/header.php"; // Includes session, db connection, etc.

if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Please sign in to update your cart.');
            window.location.href='signin.php';
          </script>";
    exit();
}

if (isset($_POST['update'])) {
    $cart_id = escape($_POST['cart_id']);
    $quantity = escape($_POST['quantity']);

    if ($quantity < 1) {
        $quantity = 1; // Minimum quantity
    }

    $user_id = $_SESSION['user_id'];

    $update_query = "UPDATE cart SET quantity = '$quantity' WHERE cart_id = '$cart_id' AND user_id = '$user_id'";
    if (mysqli_query($connection, $update_query)) {
        echo "<script>
                alert('Cart updated successfully!');
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
