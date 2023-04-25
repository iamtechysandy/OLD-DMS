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

// Get parent folders from database
$parent_folders = array();
$sql = "SELECT id, folder_name FROM folders WHERE parent_folder_id IS NULL OR parent_folder_id = 0";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($parent_folders, $row);
    }
} else {
    echo "Error fetching parent folders: " . mysqli_error($conn);
}


// Handle form submission
if (isset($_POST['sub_folder_name']) && isset($_POST['parent_folder_id'])) {
    $sub_folder_name = $_POST['sub_folder_name'];
    $parent_folder_id = $_POST['parent_folder_id'];
    
    // Get parent folder path from database
    $sql = "SELECT folder_path FROM folders WHERE id = $parent_folder_id";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $parent_folder_path = $row['folder_path'];
        
        // Create subfolder in parent folder
        $path = $parent_folder_path . '/' . $sub_folder_name;
        
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
            chmod($path, 0755); // Set folder permissions to 755
        }
        
        // Insert subfolder details into database
        $sql = "INSERT INTO folders (folder_name, folder_path, parent_folder_id, created_by, created_at) VALUES ('$sub_folder_name', '$path', $parent_folder_id, 1, NOW())";
        
        if (mysqli_query($conn, $sql)) {
            header('Location: create_sub_folder.php?success=1');
            exit;
          } else {
            echo "Error creating subfolder: " . mysqli_error($conn);
          }
          
    } else {
        echo "Error fetching parent folder path: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Subfolder</title>
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        

        .container {
            width: 65%;
        }

       
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

    </style>
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
  
<div class="container">
    <h1 class="my-3 text-center">Create Subfolder</h1>
    <form method="post">
        <div class="mb-3">
            <label for="sub_folder_name" class="form-label">Subfolder Name:</label>
            <input type="text" name="sub_folder_name" id="sub_folder_name" class="form-control" required><br><br>
            <label for="parent_folder_id" class="form-label">Parent Folder:</label>
            <select name="parent_folder_id" id="parent_folder_id" class="form-control" required>
                <option value="">--Select Parent Folder--</option>
                <?php foreach ($parent_folders as $folder) { ?>
                    <option value="<?php echo $folder['id']; ?>"><?php echo $folder['folder_name']; ?></option>
                <?php } ?>
            </select><br><br>
        </div>
        <button type="submit" class="btn btn-primary">Create Sub Folder</button>
    </form>

    <div class="alert alert-success success-message is-success">
    <div class="success-message">
  <i class="fas fa-check-circle"></i> Folder created successfully
</div>

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
