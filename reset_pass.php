<?php
session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// Check if user is not an admin
if ($_SESSION['permission_id'] != 1) {
  header('Location: index.php');
  exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=dms', 'root', '');

    $username = "";
    $new_password = "";
    $confirm_password = "";

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $new_password = $_POST["new_password"];
        $confirm_password = $_POST["confirm_password"];

        // Validate form data
        $errors = array();
        if (empty($username)) {
            $errors[] = "Username is required.";
        }
        if (empty($new_password)) {
            $errors[] = "New password is required.";
        }
        if (empty($confirm_password)) {
            $errors[] = "Confirm password is required.";
        }
        if ($new_password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }

        // If form data is valid, update user's password
        if (empty($errors)) {
            // Hash new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update user's password in the database
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE username = :username");
            $stmt->execute(array(":password" => $hashed_password, ":username" => $username));

            // Redirect to success page
            header("Location: reset_pass_success.php");
            exit();
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>

  <!-- Bootstrap 5 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css">

  <!-- Font Awesome icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    /* Center the form */
    .form-container {
      max-width: 500px;
      margin: 0 auto;
    }
  </style>
</head>
<body class="d-flex flex-column min-vh-100">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="admin.php">
        <i class="fas fa-folder"></i>
        Admin Dashboard
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="#">
              <i class="fas fa-user"></i>
              Profile
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout.php">
              <i class="fas fa-sign-out-alt"></i>
              Logout
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <main>
    <div class=form-container>
<form method="post" action="" class="needs-validation" novalidate>
  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $error): ?>
        <div><?php echo $error; ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <div class="form-group">
    <label for="username"><i class="fas fa-user"></i> Username:</label>
    <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
    <div class="invalid-feedback">
      Please enter a username.
    </div>
  </div>
  <div class="form-group">
    <label for="new_password"><i class="fas fa-lock"></i> New Password:</label>
    <input type="password" name="new_password" id="new_password" class="form-control" value="<?php echo htmlspecialchars($new_password); ?>" required>
    <div class="invalid-feedback">
      Please enter a new password.
    </div>
  </div>
  <div class="form-group">
    <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password:</label>
    <input type="password" name="confirm_password" id="confirm_password" class="form-control" value="<?php echo htmlspecialchars($confirm_password); ?>" required>
    <div class="invalid-feedback">
      Please confirm your new password.
    </div>
  </div>
  <button type="submit" class="btn btn-primary"><i class="fas fa-key"></i> Reset Password</button>
</form>
</div>
 </main>
  <!-- Bootstrap 5 JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
