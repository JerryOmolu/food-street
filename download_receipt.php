<?php
// Make sure Composer's autoload is loaded
require 'vendor/dompdf/vendor/autoload.php'; // Adjust path if needed

use Dompdf\Dompdf;
use Dompdf\Options;

session_start(); // start session if not already

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

if (!isset($_GET['reference']) || !isset($_GET['order_number'])) {
    die("Invalid request. No payment reference or order number.");
}

$reference    = $_GET['reference'];
$order_number = $_GET['order_number'];
$user_id      = $_SESSION['user_id'];

// Include your database connection
include "includes/db.php";

// Fetch order details from cart
$query = "SELECT * FROM cart WHERE user_id='$user_id' AND order_number='$order_number'";
$result_cart = mysqli_query($connection, $query);

if (!$result_cart) {
    die("QUERY FAILED: " . mysqli_error($connection));
}

// Fetch user details
$user_query = mysqli_query($connection, "SELECT * FROM user WHERE user_id='$user_id'");
$user = mysqli_fetch_assoc($user_query);

// Fetch delivery address from cart (if available)
$cart_address_query = "SELECT delivery_address FROM cart WHERE user_id='$user_id' AND order_number='$order_number' LIMIT 1";
$cart_address_result = mysqli_query($connection, $cart_address_query);
$cart_address_row = mysqli_fetch_assoc($cart_address_result);
$delivery_address = !empty($cart_address_row['delivery_address']) ? $cart_address_row['delivery_address'] : "Not Provided";

$username = $user['full_name'] ?? "Customer";

// Build HTML for PDF
$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Receipt - '.$order_number.'</title>
<style>
    body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
    .container { padding: 20px; }
    .header { text-align: center; color: #0046AA; }
    .header h2 { margin: 10px 0 5px 0; }
    .header p { margin: 3px 0; }
    .logo img { max-width: 120px; margin-bottom: 10px; }
    .receipt-details { margin-top: 20px; }
    .receipt-details p { margin: 5px 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
    th { background-color: #f4f4f4; color: #0046AA; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .total-row th, .total-row td { font-weight: bold; }
    .thank-you { margin-top: 30px; text-align: center; font-size: 16px; color: #E3272D; }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Food Street - Payment Receipt</h2>
        <p>Order Number: '.htmlspecialchars($order_number).'</p>
        <p>Payment Reference: '.htmlspecialchars($reference).'</p>
    </div>

    <div class="receipt-details">
        <p><strong>Customer:</strong> '.htmlspecialchars($username).'</p>
        <p><strong>Delivery Address:</strong> '.htmlspecialchars($delivery_address).'</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Food Item</th>
                <th>Qty</th>
                <th>Price (NGN)</th>
                <th>Subtotal (NGN)</th>
            </tr>
        </thead>
        <tbody>';

$total = 0;
while ($row = mysqli_fetch_assoc($result_cart)) {
    $subtotal = $row['quantity'] * $row['price'];
    $total += $subtotal;
    $html .= '<tr>
                <td>'.htmlspecialchars($row['food_name']).'</td>
                <td>'.$row['quantity'].'</td>
                <td>'.number_format($row['price'], 2).'</td>
                <td>'.number_format($subtotal, 2).'</td>
              </tr>';
}

$html .= '<tr class="total-row">
            <td colspan="3">Total Paid</td>
            <td>NGN'.number_format($total, 2).'</td>
          </tr>
        </tbody>
    </table>

    <div class="thank-you">
        Thank you for shopping with Food Street!
    </div>
</div>
</body>
</html>';

// Configure Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Force download without prior output
$dompdf->stream("receipt_$order_number.pdf", ["Attachment" => true]);
exit;
?>
