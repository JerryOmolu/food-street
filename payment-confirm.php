<?php
$pageTitle = "Payment Confirmation - Food Street";
include "includes/header.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

if (!isset($_GET['reference']) || !isset($_GET['order_number'])) {
    die("<div class='container py-5'><div class='alert alert-danger'>Invalid request. No payment reference or order number.</div></div>");
}

$reference    = $_GET['reference'];
$order_number = $_GET['order_number'];
$user_id      = $_SESSION['user_id'];

// Paystack secret key
$secret_key = "sk_test_82fa2974b322b8a10e552bdccaf5dab2bb1de05b";

// Verify payment
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/verify/$reference");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $secret_key"
]);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

// Check if payment was successful
if ($result['status'] && $result['data']['status'] === 'success') {
    $amount_paid = $result['data']['amount'] / 100; // Convert kobo to naira

    // Fetch order items
    $query = "SELECT * FROM cart WHERE user_id='$user_id' AND order_number='$order_number'";
    $result_cart = mysqli_query($connection, $query);
    if (!$result_cart) die("QUERY FAILED: " . mysqli_error($connection));

    // Fetch delivery address from cart
    $cart_address_query = "SELECT delivery_address FROM cart WHERE user_id='$user_id' AND order_number='$order_number' LIMIT 1";
    $cart_address_result = mysqli_query($connection, $cart_address_query);
    $cart_address_row = mysqli_fetch_assoc($cart_address_result);
    $delivery_address = !empty($cart_address_row['delivery_address']) ? $cart_address_row['delivery_address'] : "Not provided";

    // Get user full name
    $user_query = mysqli_query($connection, "SELECT full_name FROM user WHERE user_id='$user_id'");
    $user = mysqli_fetch_assoc($user_query);
    $username = $user['full_name'] ?? "Customer";
    ?>
    
    <section class="py-5 bg-light">
        <div class="container">
            <div class="card shadow p-4">
                <div class="text-center mb-4">
                    <h2 class="text-success"><i class="fas fa-check-circle"></i> Payment Successful</h2>
                    <p class="lead">Thank you, <strong><?= htmlspecialchars($username); ?></strong>. Your payment was successful!</p>
                </div>

                <hr>

                <div class="mb-4">
                    <h4>Order Receipt</h4>
                    <p><strong>Order Number:</strong> <?= htmlspecialchars($order_number); ?></p>
                    <p><strong>Payment Reference:</strong> <?= htmlspecialchars($reference); ?></p>
                    <p><strong>Delivery Address:</strong> <?= htmlspecialchars($delivery_address); ?></p>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Food Item</th>
                                <th>Qty</th>
                                <th>Price (₦)</th>
                                <th>Subtotal (₦)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total = 0;
                            while ($row = mysqli_fetch_assoc($result_cart)) {
                                $subtotal = $row['quantity'] * $row['price'];
                                $total += $subtotal;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['food_name']); ?></td>
                                    <td><?= $row['quantity']; ?></td>
                                    <td>₦<?= number_format($row['price'], 2); ?></td>
                                    <td>₦<?= number_format($subtotal, 2); ?></td>
                                </tr>
                            <?php } ?>
                            <tr class="table-light">
                                <th colspan="3" class="text-end">Total Paid</th>
                                <th>₦<?= number_format($amount_paid, 2); ?></th>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4 flex-wrap gap-2">
                    <a href="index.php" class="btn btn-primary btn-lg"><i class="fas fa-home me-2"></i>Continue to Order</a>
                    <a href="download_receipt.php?order_number=<?= urlencode($order_number); ?>&reference=<?= urlencode($reference); ?>" 
                       class="btn btn-outline-secondary btn-lg" target="_blank">
                        <i class="fas fa-file-download me-2"></i>Download PDF Receipt
                    </a>
                </div>
            </div>
        </div>
    </section>

    <style>
        .table td, .table th { vertical-align: middle; }
        @media(max-width: 575px){
            .table-responsive { overflow-x: auto; }
            .btn-lg { width: 100%; }
        }
    </style>

    <?php
} else {
    echo "<div class='container py-5'><div class='alert alert-danger text-center'>
            <h4><i class='fas fa-exclamation-circle'></i> Payment verification failed</h4>
            <p>Please try again or contact support.</p>
          </div></div>";
}
?>

<?php include "includes/footer.php"; ?>
