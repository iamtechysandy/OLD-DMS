<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
  // Redirect to appropriate page based on user's permission level
  if ($_SESSION['permission_id'] == 1) {
    header('Location: admin.php');
  } else if ($_SESSION['permission_id'] == 2) {
    header('Location: operation.php');
  } else {
    header('Location: home.php');
  }
  exit;
}

// Connect to the database
$pdo = new PDO('mysql:host=localhost;dbname=dms', 'root', '');

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  // Get the form data
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Check if the user exists in the database
  $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
  $stmt->execute([$username]);
  $user = $stmt->fetch();

  // Verify the password
  if ($user && password_verify($password, $user['password'])) {

    // Store user data in session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['permission_id'] = $user['permission_id'];

    // Redirect to appropriate page based on user's permission level
    if ($_SESSION['permission_id'] == 1) {
      header('Location: admin.php');
    } else if ($_SESSION['permission_id'] == 2) {
      header('Location: operation.php');
    } else {
      header('Location: home.php');
    }
    exit;

  } else {
    // Invalid login credentials
    $error = 'Invalid username or password';
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.2/css/bootstrap.min.css"/>
  <style>
    body {
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    h1 {
  text-align: center;
}

h1 img {
  display: inline-block;
  width: 100px; /* adjust the width as needed */
}
body {
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background-image: url("bg2.jpg");
  background-size: cover;
}
.card {
width: 500px!important; 
border-radius: 10px!important;
}


  </style>
</head>
<body>

  <div class="card p-3">
    <h1 class="text-center"><img src="shar.png"></h1>
    <h2 class="text-center mb-3">Login</h2>
    <?php if (isset($error)) { ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } ?>
    <form method="POST">
      <div class="mb-3">
        <label for="username" class="form-label">Username:</label>
        <input type="text" placeholder="Your Username" class="form-control" id="username" name="username" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password:</label>
        <input type="password" placeholder="Your Password" class="form-control" id="password" name="password" required>
      </div>
      <div class="mb-3 form-check">
  <input type="checkbox" class="form-check-input" id="remember-me" name="remember-me">
  <label class="form-check-label" for="remember-me">Remember me</label>
</div>

      <div class="text-center">
        <button type="submit" class="btn btn-primary">Login</button>
      </div>
    </form>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
