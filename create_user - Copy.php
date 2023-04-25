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

// Connect to database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dms";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // get the form data
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  $email = $_POST['email'];
  $permission_id = $_POST['permission_id'];

  // hash the password
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  // insert the user into the database
  $sql = "INSERT INTO users (first_name, last_name, username, password, email, created_at, permission_id)
          VALUES ('$first_name', '$last_name', '$username', '$hashed_password', '$email', NOW(), $permission_id)";

  if (mysqli_query($conn, $sql)) {
    echo "User created successfully.";
  } else {
    echo "Error creating user: " . mysqli_error($conn);
  }
}

// close the database connection
mysqli_close($conn);
?>


<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Create User</title>
	<!-- Bootstrap 5 CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body>
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
            <a class="nav-link" href="admin.php">
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
	<div class="container">
		<h1 class="mt-3">Create User</h1>
		<form method="post">
      <div class="mb-3">
        <label for="first_name" class="form-label">First Name:</label>
        <input type="text" class="form-control" name="first_name" id="first_name" required>
      </div>
      <div class="mb-3">
        <label for="last_name" class="form-label">Last Name:</label>
        <input type="text" class="form-control" name="last_name" id="last_name">
      </div>
			<div class="mb-3">
				<label for="username" class="form-label">Username:</label>
				<input type="text" class="form-control" name="username" id="username" required>
			</div>
			<div class="mb-3">
				<label for="password" class="form-label">Password:</label>
				<input type="password" class="form-control" name="password" id="password" required>
			</div>
			<div class="mb-3">
				<label for="email" class="form-label">Email:</label>
				<input type="email" class="form-control" name="email" id="email" required>
			</div>
			<div class="mb-3">
				<label for="permission_id" class="form-label">Permission ID:</label>
				<input type="number" class="form-control" name="permission_id" id="permission_id" required>
			</div>
			<button type="submit" class="btn btn-primary">Create User</button>
		</form>
	</div>
	<!-- Bootstrap 5 JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fd6CZKSSeZG6n8fWVKBvMaINmhA+sG7hng8bz92WVxvODpDZ67VhsmIFp+fyWzyA" crossorigin="anonymous"></script>
</body>
</html>
