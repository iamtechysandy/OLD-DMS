
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

// Connect to the database
$pdo = new PDO('mysql:host=localhost;dbname=dms', 'root', '');

// Check if the form has been submitted to create a folder
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_folder'])) {

  // Get the form data
  $name = $_POST['name'];

  // Insert the new folder into the database
  $stmt = $pdo->prepare('INSERT INTO folders (name, created_by) VALUES (?, ?)');
  $stmt->execute([$name, $_SESSION['user_id']]);

  // Redirect to the admin page
  header('Location: admin.php');
  exit;
}

// Check if the form has been submitted to create a user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_user'])) {

  // Get the form data
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $email = $_POST['email'];
  $permission_id = $_POST['permission_id'];

  // Insert the new user into the database
  $stmt = $pdo->prepare('INSERT INTO users (username, password, email, created_at, permission_id) VALUES (?, ?, ?, NOW(), ?)');
  $stmt->execute([$username, $password, $email, $permission_id]);

  // Redirect to the admin page
  header('Location: admin.php');
  exit;
}

// Check if the form has been submitted to delete a folder
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_folder'])) {

  // Get the folder ID
  $id = $_POST['id'];

  // Delete the folder from the database
  $stmt = $pdo->prepare('DELETE FROM folders WHERE id = ?');
  $stmt->execute([$id]);

  // Redirect to the admin page
  header('Location: admin.php');
  exit;
}

// Get the list of folders from the database
$stmt = $pdo->query('SELECT * FROM folders ORDER BY created_at DESC');
$folders = $stmt->fetchAll();

// Get the list of users from the database
$stmt = $pdo->query('SELECT * FROM users ORDER BY created_at DESC');
$users = $stmt->fetchAll();

// Get the list of permissions from the database
$stmt = $pdo->query('SELECT * FROM permissions');
$permissions = $stmt->fetchAll();
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
    footer {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  font-size: 14px;
}

</style>
</head>
<body class="d-flex flex-column min-vh-100">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
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

  <!-- Main content -->
  <main class="flex-fill">
    <div class="container my-4">
      <h1 class="mb-4">Admin Dashboard</h1>
      <div class="row">
        <div class="col-lg-4 mb-4">
          <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
              <h2 class="card-title">
                <i class="fas fa-folder"></i>
                Create Folder
              </h2>
              <p class="card-text">Create the Department Folder</p>
              <a href="create_folder.php" class="btn btn-primary mt-auto">Go to page</a>
            </div>
          </div>
        </div>
        <div class="col-lg-4 mb-4">
          <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
              <h2 class="card-title">
                <i class="fas fa-user"></i>
                Create User
              </h2>
              <p class="card-text">Create the Users</p>
              <a href="create_user.php" class="btn btn-primary mt-auto">Go to page</a>
            </div>
          </div>
        </div>
        <div class="col-lg-4 mb-4">
          <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
              <h2 class="card-title">
                <i class="fas fa-lock"></i>
                Set Folder Permission
              </h2>
              <p class="card-text">Set the folder permission to the user where user having the option to uplaod the file and view the folder.</p>
<a href="folder_permission.php" class="btn btn-primary mt-auto">Go to page</a>

</div>
</div>
</div>
<div class="col-lg-4 mb-4">
<div class="card h-100">
<div class="card-body d-flex flex-column justify-content-between">
<h2 class="card-title">
<i class="fas fa-trash"></i>
User Details
</h2>
<p class="card-text">Check Users details and their access</p>
<a href="vu.php" class="btn btn-primary mt-auto">Go to page</a>
</div>
</div>
</div>
<div class="col-lg-4 mb-4">
<div class="card h-100">
<div class="card-body d-flex flex-column justify-content-between">
<h2 class="card-title">
<i class="fas fa-key"></i>
Reset User Password
</h2>
<p class="card-text">Reset User Password</p>
<a href="reset_pass.php" class="btn btn-primary mt-auto">Go to page</a>
</div>
</div>
</div>
<div class="col-lg-4 mb-4">
<div class="card h-100">
<div class="card-body d-flex flex-column justify-content-between">
<h2 class="card-title">
<i class="fas fa-folder"></i>
Create Subfolder
</h2>
<p class="card-text">Create Sub Folder Under Departnment</p>
<a href="create_sub_folder.php" class="btn btn-primary mt-auto">Go to page</a>
</div>
</div>
</div>
</div>
</div>

  </main>
  <footer class="bg-dark text-white py-3">
  <div class="container">
    <div class="row">
      <div class="col-lg-6 col-md-6 mb-3">
        <p class="text-muted">Copyright &copy; 2023
          <a href="https://shar.com.sa">Techysandy.com</a>. All rights reserved.</p>
          <p class="text-muted">Document Management System</p>
      </div>
      <div class="col-lg-6 col-md-6 mb-3 text-end">
        <a href="#" class="text-white me-3"><i class="fas fa-envelope"></i>mail@youremail.com</a>
        <a href="#" class="text-white"><i class="fas fa-phone"></i> +1 123 456 7890</a>
      </div>
    </div>
  </div>
</footer>
  <!-- Bootstrap 5 JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




