<?php
include "includes/header.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

if (!isset($_GET['reference'])) {
    die("No payment reference found.");
}

$reference = $_GET['reference'];
$user_id   = $_SESSION['user_id'];

// Paystack secret key
$secret_key = "sk_test_82fa2974b322b8a10e552bdccaf5dab2bb1de05b";

// Verify payment with Paystack
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/verify/$reference");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $secret_key"
]);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if ($result['status'] && $result['data']['status'] === 'success') {
    // Generate unique order number
    $order_number = "ORD" . time();

    // Update cart: set Paid and attach order number
    $update = "UPDATE cart 
               SET payment_status='Paid', order_number='$order_number' 
               WHERE user_id='$user_id' AND payment_status='Pending'";
    mysqli_query($connection, $update);

    // Redirect to payment confirmation with order details
    header("Location: payment-confirm.php?reference=$reference&order_number=$order_number");
    exit();
} else {
    echo "<script>
            alert('Payment verification failed. Please try again.');
            window.location.href='checkout.php';
          </script>";
    exit();
}
?>
