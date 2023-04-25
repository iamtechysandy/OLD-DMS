<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
// Connect to database
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'dms';
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// retrieve user information
$sql = "SELECT users.id, users.username, users.email, users.permission_id, folders.parent_folder_id, GROUP_CONCAT(folders.folder_name SEPARATOR ', ') AS folder_names
FROM users 
LEFT JOIN folder_permissions ON users.id = folder_permissions.user_id
LEFT JOIN folders ON folder_permissions.folder_id = folders.id
GROUP BY users.id";




$result = mysqli_query($conn, $sql);
if (!$result) {
    die('Query failed: ' . mysqli_error($conn));
}

// Connect to the database
$pdo = new PDO('mysql:host=localhost;dbname=dms', 'root', '');

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Loop through each user and update their permission
    foreach ($_POST['permission'] as $user_id => $permission_id) {
        $stmt = $pdo->prepare('UPDATE users SET permission_id = ? WHERE id = ?');
        $stmt->execute([$permission_id, $user_id]);
    }
}

// Retrieve all users and their permissions
$stmt = $pdo->query('SELECT users.*, permissions.name AS permission_name FROM users LEFT JOIN permissions ON users.permission_id = permissions.id');
$users = $stmt->fetchAll();

// Retrieve all permissions
$stmt = $pdo->query('SELECT * FROM permissions');
$permissions = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>User Access</title>
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <!-- Font Awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<style>
  .success-message {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #28a745;
    color: #fff;
    padding: 20px;
    border-radius: 5px;
    animation: fade-in-out 2s ease-in-out;
  }
  
  @keyframes fade-in-out {
    0% {
      opacity: 0;
    }
    10% {
      opacity: 1;
    }
    90% {
      opacity: 1;
    }
    100% {
      opacity: 0;
    }
  }

    footer {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  font-size: 14px;
}
</style>
<body>
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
<?php
// Show success message if exists
if (isset($_SESSION['success_message'])) {
  echo '<div class="alert alert-success">'.$_SESSION['success_message'].'</div>';
  unset($_SESSION['success_message']);
}
?>
<table class="table">
  <thead>
    <tr>
      <th>Username</th>
      <th>Email</th>
      <th>Permission</th>
      <th>Folders Access</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
  <?php while($row = mysqli_fetch_assoc($result)) { 
    $folder_icon = ($row['parent_folder_id'] == 0) ? 'fa-files' : 'fa-folder';?>
<tr>
  <td><?php echo $row['username']; ?></td>
  <td><?php echo $row['email']; ?></td>
  <td><?php echo $row['permission_id']; ?></td>
<td>
    <?php 
    if($row['parent_folder_id'] == 0){
        // if parent_folder_id is 0, show file icon and folder name
        echo '<i class="fa fa-file"></i> ' . $row['folder_names'];
    } else {
        // otherwise, show folder icon and folder name
        echo '<i class="fa fa-folder"></i> ' . $row['folder_names'];
    }
?> 

</td>

<td><a href="#" data-bs-toggle="modal" data-bs-target="#resetPasswordModal<?php echo $row['id']; ?>"><i class="fa fa-key"></i> Reset Password</a></td>
</tr>


    
    <!-- Reset Password Modal -->
   <!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="resetPasswordModalLabel">Reset Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="reset.php" method="post">
          <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
          <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
          </div>
          <button type="submit" class="btn btn-primary">Reset Password</button>
        </form>
       
      </div>
    </div>
  </div>
</div>

    
    <?php } ?>
  </tbody>
</table>
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
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
