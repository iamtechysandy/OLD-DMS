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

// Handle form submission
if (isset($_POST['folder_name'])) {
    $folder_name = $_POST['folder_name'];
    $parent_folder_id = null;
    
    // Create folder in DmsFolders directory
    $path = 'DmsFolders/' . $folder_name;
    
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
        chmod($path, 0755); // Set folder permissions to 755
    }
    
    // Insert folder details into database
$sql = "INSERT INTO folders (folder_name, folder_path, parent_folder_id, created_by, created_at) VALUES ('$folder_name', '$path', '$parent_folder_id', 1, NOW())";

    
if (mysqli_query($conn, $sql)) {
    header('Location: create_folder.php?success=1');
    exit;
  } else {
    echo "Error creating folder: " . mysqli_error($conn);
  }
  
}

mysqli_close($conn);
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Folder</title>
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
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
        <h2 class="my-3">Create Department Folder</h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="folder_name" class="form-label">Folder Name:</label>
                <input type="text" class="form-control" id="folder_name" name="folder_name" required>
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    </div>
    <div class="success-message">
  <i class="fas fa-check-circle"></i> Folder created successfully
</div>
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

<script>
  // Show success message if URL parameter "success" is present
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('success')) {
    const successMsg = document.querySelector('.success-message');
    successMsg.style.display = 'block';
    setTimeout(() => {
      successMsg.style.display = 'none';
    }, 2000);
  }
</script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

