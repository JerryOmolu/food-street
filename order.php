<?php
$pageTitle = "Order - Food Street";
?>
<?php include "includes/header.php"; ?>

  <!-- Navbar (reuse your existing one) -->
  <?php include 'includes/nav.php'; ?>

  <!-- Hero Section -->
  <section class="py-5 text-center bg-dark text-white" style="background: url('assets/img/gallery/order-hero.jpg') center/cover no-repeat;">
    <div class="container py-5">
      <h1 class="fw-bold text-white">Place Your Order</h1>
      <p class="lead">Fresh meals, local delicacies, and farm produce delivered to your doorstep</p>
    </div>
  </section>

  <!-- Order Form Section -->
  <section class="py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-5">
              <h3 class="mb-4 text-center fw-bold text-gradient">Order Form</h3>
              
              <form>
                <!-- Food Item -->
                <div class="mb-3">
                  <label for="foodItem" class="form-label">Select Food Item</label>
                  <select class="form-select" id="foodItem" required>
                    <option value="">Choose...</option>
                    <option value="jollof">Jollof Rice</option>
                    <option value="amala">Amala & Ewedu</option>
                    <option value="pounded">Pounded Yam & Egusi</option>
                    <option value="grilled">Grilled Fish</option>
                    <option value="fresh">Fresh Farm Produce</option>
                  </select>
                </div>

                <!-- Quantity -->
                <div class="mb-3">
                  <label for="quantity" class="form-label">Quantity</label>
                  <input type="number" class="form-control" id="quantity" min="1" value="1" required>
                </div>

                <!-- Delivery Details -->
                <div class="mb-3">
                  <label for="fullname" class="form-label">Full Name</label>
                  <input type="text" class="form-control" id="fullname" required>
                </div>
                <div class="mb-3">
                  <label for="phone" class="form-label">Phone Number</label>
                  <input type="tel" class="form-control" id="phone" required>
                </div>
                <div class="mb-3">
                  <label for="address" class="form-label">Delivery Address</label>
                  <textarea class="form-control" id="address" rows="3" required></textarea>
                </div>

                <!-- Payment Method -->
                <div class="mb-3">
                  <label class="form-label">Payment Method</label>
                  <select class="form-select" id="payment" required>
                    <option value="">Choose...</option>
                    <option value="cod">Cash on Delivery</option>
                    <option value="card">Debit/Credit Card</option>
                    <option value="transfer">Bank Transfer</option>
                  </select>
                </div>

                <!-- Submit -->
                <div class="d-grid">
                  <button type="submit" class="btn btn-danger btn-lg">Place Order</button>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer (reuse your existing one) -->
  <?php include 'includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
