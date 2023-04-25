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


// Check if the form has been submitted
if(isset($_POST['submit'])) {
    $user_id = $_POST['user_id'];
    $folder_id = $_POST['folder_id'];
    $permission = $_POST['permission'];

    // Check if the user already has permissions for this folder
    $query = "SELECT * FROM folder_permissions WHERE user_id = $user_id AND folder_id = $folder_id";
    $result = mysqli_query($conn, $query);
    if(mysqli_num_rows($result) > 0) {
        // Update the existing permission record
        $query = "UPDATE folder_permissions SET permission = $permission WHERE user_id = $user_id AND folder_id = $folder_id";
        mysqli_query($conn, $query);
        header('Location: folder_permission.php?success=1');
        
    } else {
        // Insert a new permission record
        $query = "INSERT INTO folder_permissions (user_id, folder_id, permission) VALUES ($user_id, $folder_id, $permission)";
        mysqli_query($conn, $query);
        header("Location: folder_permission.php?success=1");
            exit();
        
    }

    
}

// Query the database for a list of users and folders
$query = "SELECT * FROM users";
$users_result = mysqli_query($conn, $query);

$query = "SELECT * FROM folders";
$folders_result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Assign Folder Permissions</title>
  <!-- Font Awesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
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
  
    <div class="container d-flex justify-content-center mt-5">
        <div class="col-lg-6">
            <h1 class="text-center mb-4">Assign Folder Permissions</h1>
            <form method="post" action="">
                <div class="mb-3">
                    <label for="user_id" class="form-label">User:</label>
                    <select name="user_id" id="user_id" class="form-select" required>
                        <?php while($user = mysqli_fetch_assoc($users_result)) { ?>
                            <option value="<?php echo $user['id']; ?>"><?php echo $user['username']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="folder_id" class="form-label">Folder:</label>
                    <select name="folder_id" id="folder_id" class="form-select" required>
    <?php
    // Keep track of the IDs of the parent folders that we've seen so far
    $parent_folder_ids = array();
    
    // Iterate over each folder in the query result
    while ($folder = mysqli_fetch_assoc($folders_result)) {
        $id = $folder['id'];
        $folder_name = $folder['folder_name'];
        $parent_id = $folder['parent_folder_id'];

        // If this is a parent folder, add it to the list of IDs and display it with the "fa-files" icon
        if ($parent_id == 0) {
            $parent_ids[] = $id;
            echo '<option value="' . $id . '"><i class="fas fa-fw fa-files"></i> ' . $folder_name . '</option>';
        } else {
            // Otherwise, check if this is a subfolder of a parent folder we've already seen
            $parent_index = array_search($parent_id, $parent_ids);
            
            if ($parent_index !== false) {
                // If it is, indent the subfolder and display it with the "fa-folder" icon
                $indentation = str_repeat('&nbsp;', 4);
                echo '<option value="' . $id . '">' . $indentation . '<i class="fas fa-fw fa-folder"></i> ' . $folder_name . '</option>';
            } else {
                // If it's not, display an error message
                echo '<option>Error: subfolder with parent ID ' . $parent_folder_id . ' does not appear before this folder in the query result</option>';
            }
        }
    }
    ?>
</select>
                </div>
                <div class="mb-3">
                    <label for="permission" class="form-label">Permission:</label>
                    <select name="permission" id="permission" class="form-select" required>
                        <option value="1">Read Only</option>
                        <option value="2">Read/Write</option>
                    </select>
                </div>
                <div class="mb-3 text-center">
                    <button type="submit" name="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Assign Permissions</button>
                </div>
            </form>
        </div>
    </div>
    <div class="success-message">
  <i class="fas fa-check-circle"></i> Permisison Assing successfully
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>