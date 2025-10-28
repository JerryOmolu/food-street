<?php 
include "includes/dashboard-header.php"; 
include "includes/db.php";

// Ensure user is logged in
if (!isset($_SESSION['vendor_id'])) {
  header("Location: index.php");
  exit();
}

$vendor_id = $_SESSION['vendor_id'];
$vendor_name = $_SESSION['vendor_name'];

// Fetch vendor details
$stmt = $connection->prepare("SELECT * FROM vendor WHERE vendor_id = ? LIMIT 1");
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();
$vendor = $result->fetch_assoc();

// Fetch agent stats
$query = "SELECT * FROM agent WHERE added_by = '$vendor_name'";
$agent_query = mysqli_query($connection, $query);
$number_of_agent = mysqli_num_rows($agent_query);
echo $number_of_agent;

// Handle profile update
if (isset($_POST['update_profile'])) {
    $vendor_name = mysqli_real_escape_string($connection, $_POST['vendor_name']);
    $phone = mysqli_real_escape_string($connection, $_POST['phone']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    // Handle profile picture upload
    $profile_picture = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = "uploads/vendors/"; // create this folder if not exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_tmp  = $_FILES['profile_picture']['tmp_name'];
        $file_name = time() . "_" . basename($_FILES['profile_picture']['name']);
        $target_path = $upload_dir . $file_name;

        // Only allow image types
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
        if (in_array($_FILES['profile_picture']['type'], $allowed_types)) {
            if (move_uploaded_file($file_tmp, $target_path)) {
                $profile_picture = $file_name;
            }
        }
    }

    // Build query dynamically
    if ($password && $profile_picture) {
        $update_query = "UPDATE vendor SET vendor_name = ?, phone = ?, password = ?, profile_picture = ? WHERE vendor_id = ?";
        $stmt_update = $connection->prepare($update_query);
        $stmt_update->bind_param("ssssi", $vendor_name, $phone, $password, $profile_picture, $vendor_id);
    } elseif ($password) {
        $update_query = "UPDATE vendor SET vendor_name = ?, phone = ?, password = ? WHERE vendor_id = ?";
        $stmt_update = $connection->prepare($update_query);
        $stmt_update->bind_param("sssi", $vendor_name, $phone, $password, $vendor_id);
    } elseif ($profile_picture) {
        $update_query = "UPDATE vendor SET vendor_name = ?, phone = ?, profile_picture = ? WHERE vendor_id = ?";
        $stmt_update = $connection->prepare($update_query);
        $stmt_update->bind_param("sssi", $vendor_name, $phone, $profile_picture, $vendor_id);
    } else {
        $update_query = "UPDATE vendor SET vendor_name = ?, phone = ? WHERE vendor_id = ?";
        $stmt_update = $connection->prepare($update_query);
        $stmt_update->bind_param("ssi", $vendor_name, $phone, $vendor_id);
    }

    if ($stmt_update->execute()) {
        $_SESSION['vendor_name'] = $vendor_name;
        if ($profile_picture) {
            $_SESSION['profile_picture'] = $profile_picture; // update session if needed
        }
        echo "<script>alert('✅ Profile updated successfully!'); window.location.href='profile';</script>";
    } else {
        echo "<script>alert('⚠️ Failed to update profile. Try again.');</script>";
    }
}

?>

<div class="container-scroller d-flex">
  <!-- Sidebar -->
  <?php include "includes/navbar.php"; ?>    

  <div class="container-fluid page-body-wrapper">
    <!-- Topbar -->
    <?php include "includes/topbar.php"; ?>

    <div class="main-panel">
      <div class="content-wrapper">
        <div class="row">

          <!-- Vendors Card -->
          <div class="col-md-4 grid-margin stretch-card">
            <div class="card bg-facebook d-flex align-items-center">
              <div class="card-body py-5">
                <div class="d-flex flex-row align-items-center flex-wrap justify-content-md-center justify-content-xl-start py-1">
                  <i class="mdi mdi-store text-white icon-lg"></i>
                  <div class="ml-3 ml-md-0 ml-xl-3">
                    <h5 class="text-white font-weight-bold">
                      <?php echo $number_of_agent; ?> Agents
                    </h5>
                    <p class="mt-2 text-white card-text">Your Agent Network</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Orders Card -->
          <div class="col-md-4 grid-margin stretch-card">
            <div class="card bg-google d-flex align-items-center">
              <div class="card-body py-5">
                <div class="d-flex flex-row align-items-center flex-wrap justify-content-md-center justify-content-xl-start py-1">
                  <i class="mdi mdi-receipt text-white icon-lg"></i>
                  <div class="ml-3 ml-md-0 ml-xl-3">
                    <h5 class="text-white font-weight-bold">
                      0 Orders
                    </h5>
                    <p class="mt-2 text-white card-text">Orders from your vendors</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Profile Card -->
          <div class="col-md-4 grid-margin stretch-card">
            <div class="card bg-twitter d-flex align-items-center">
              <div class="card-body py-5">
                <div class="d-flex flex-row align-items-center flex-wrap justify-content-md-center justify-content-xl-start py-1">
                  <i class="mdi mdi-account text-white icon-lg"></i>
                  <div class="ml-3 ml-md-0 ml-xl-3">
                    <h5 class="text-white font-weight-bold">
                      <?php echo htmlspecialchars($vendor['vendor_name']); ?>
                    </h5>
                    <p class="mt-2 text-white card-text">Your profile info</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Profile Details -->
        <div class="row">
          <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">My Profile</h4>
                <div class="table-responsive">
                  <table class="table table-striped">
                    <tbody>
                      <tr>
                        <th>Vendor Name</th>
                        <td><?php echo htmlspecialchars($vendor['vendor_name']); ?></td>
                      </tr>
                      <tr>
                        <th>Email</th>
                        <td><?php echo htmlspecialchars($vendor['email']); ?></td>
                      </tr>
                      <tr>
                        <th>Phone</th>
                        <td><?php echo htmlspecialchars($vendor['phone']); ?></td>
                      </tr>
                      <tr>
                        <th>Date Joined</th>
                        <td><?php echo date("F j, Y", strtotime($vendor['added_on'])); ?></td>
                      </tr>
                      <tr>
                        <th>Status</th>
                        <td>
                          <?php if ($vendor['verify_status'] == 1): ?>
                            <span class="badge badge-success">Verified</span>
                          <?php else: ?>
                            <span class="badge badge-warning">Pending Verification</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- Edit Profile Form -->
          <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Edit Profile</h4>
		<form method="POST" action="" enctype="multipart/form-data">
		<div class="form-group">
		<label>Vendor Name</label>
		<input type="text" name="vendor_name" class="form-control" value="<?php echo htmlspecialchars($vendor['vendor_name']); ?>" required>
		</div>

		<div class="form-group">
		<label>Phone</label>
		<input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($vendor['phone']); ?>" required>
		</div>

		<div class="form-group">
		<label>New Password (leave blank to keep current)</label>
		<input type="password" name="password" class="form-control">
		</div>

		<div class="form-group">
		<label>Profile Picture</label>
		<input type="file" name="profile_picture" class="form-control-file" accept="image/*">
		</div>

		<button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
		</form>

              </div>
            </div>
          </div>
        </div>

      </div>
      <!-- content-wrapper ends -->

      <!-- Footer -->
      <?php include "includes/dashboard-footer.php"; ?>
    </div>
  </div>
</div>
