<?php
// start session and check if user is logged in
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION["permission_id"] == 2) {
    include("sub_fol.php");
}
// establish database connection
$db_conn = mysqli_connect("localhost", "root", "", "dms");

// check connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

// retrieve folder based on encrypted id
$encrypted_folder_id = isset($_GET['fid']) ? $_GET['fid'] : null;
$folder_id = base64_decode($encrypted_folder_id);

$user_id = $_SESSION["user_id"];

// retrieve folder details
$folder_query = "SELECT * FROM folders WHERE id = $folder_id";
$folder_result = mysqli_query($db_conn, $folder_query);

// check if folder exists and if user has access
if (mysqli_num_rows($folder_result) == 0) {
    die("Folder not found");
} else {
    $folder = mysqli_fetch_assoc($folder_result);
    $folder_path = $folder['folder_path'];

    $permission_query = "SELECT * FROM folder_permissions WHERE folder_id = $folder_id AND user_id = $user_id AND permission >= 1";
    $permission_result = mysqli_query($db_conn, $permission_query);

    if (mysqli_num_rows($permission_result) == 0) {
        die("You do not have permission to access this folder");
    }
}

// handle file uploads
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file_upload"])) {
    $file_name = $_FILES["file_upload"]["name"];
    $file_size = $_FILES["file_upload"]["size"];
    $file_tmp = $_FILES["file_upload"]["tmp_name"];
    $file_type = $_FILES["file_upload"]["type"];
    $file_type = mysqli_real_escape_string($db_conn, $file_type);

    $file_name_parts = explode(".", $file_name);
$file_ext = strtolower(end($file_name_parts));

// check if user provided a new name for the file
if (isset($_POST['new_file_name']) && !empty(trim($_POST['new_file_name']))) {
    $new_file_name = trim($_POST['new_file_name']);
    // remove the file extension from the new file name
    $new_file_name = pathinfo($new_file_name, PATHINFO_FILENAME);
    // create the new file name with the original file extension
    $new_file_name = $new_file_name . "." . $file_ext;
    $file_name = $new_file_name;
}

$allowed_extensions = array("txt", "pdf", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "zip", "rar");

if (in_array($file_ext, $allowed_extensions)) {
    if ($file_size <= 10485760) { // 10MB in bytes
        $file_path = $folder_path . "/" . $file_name;

        // check if file already exists in folder
        if (file_exists($file_path)) {
            $i = 1;
            do {
                $new_file_path = $folder_path . "/" . pathinfo($file_name, PATHINFO_FILENAME) . "_" . $i . "." . $file_ext;
                $i++;
            } while (file_exists($new_file_path));

            $file_path = $new_file_path;
            $file_name = pathinfo($file_path, PATHINFO_BASENAME);
        }

        // move uploaded file to folder
        move_uploaded_file($file_tmp, $file_path);

        // insert file information into database
        $insert_query = "INSERT INTO files (file_name, file_path, folder_id, user_id, upload_date, file_size, file_type) 
             VALUES ('$file_name', '$file_path', $folder_id, $user_id, NOW(), '$file_size', '$file_type')";
        mysqli_query($db_conn, $insert_query);
        header("Location: folder.php?fid=" . $encrypted_folder_id . "&success=1");
        exit();
    } else {
        $error_message = "File size must be less than or equal to 10MB";
    }
} else {
        $error_message = "Only the following file types are allowed: " . implode(", ", $allowed_extensions);
    }
}

// retrieve files in folder
$files_query = "SELECT *
                FROM files
                WHERE folder_id = $folder_id
                ORDER BY upload_date DESC";
$files_result = mysqli_query($db_conn, $files_query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $folder['folder_name']; ?></title>
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
</style>
<body>
     <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">My Files</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php"><i class="fas fa-home me-2"></i>Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-envelope me-2"></i>Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-user-cog me-2"></i>Admin</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container">
       <h1><?php echo $folder['folder_name']; ?></h1>
        <p><a href="home.php"><i class="fas fa-arrow-left"></i> Back to home</a></p>
        
       <form action="search.php" target="_blank">
  <div class="input-group">
    <input type="text" class="form-control" name="query" disabled placeholder="Search the Documents with Coutom Fields ">
    <button type="submit" class="btn btn-outline-primary">
      <i class="fas fa-search"></i>
    </button>
  </div>
</form>


        <?php if (isset($error_message)) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php } ?>

        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="file_upload" class="form-label">Select a file to upload:</label>
                <input type="file" name="file_upload" class="form-control" id="file_upload">
            </div>
            <div class="mb-3">
                        <label for="new_file_name" class="form-label">Rename file (optional):</label>
                        <input type="text" class="form-control" id="new_file_name" name="new_file_name">
                    </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-cloud-upload-alt"></i> Upload File</button>
        </form>
<br>
<br>
       <h1>All File are Below Which Uploaded Last</h1>
   <div class="row row-cols-1 row-cols-md-3 g-4">
    <?php
    // get files in folder
    $query = "SELECT files.*, users.username FROM files 
              JOIN users ON files.user_id = users.id 
              WHERE folder_id = $folder_id ORDER BY file_name";
    $result = mysqli_query($db_conn, $query);
    while ($file = mysqli_fetch_assoc($result)) {
        ?>
        <div class="col">
            <div class="card h-100">
                <?php if ($file['file_type'] == 'application/pdf') { ?>
                    <i class="fas fa-file-pdf fa-4x mx-auto mt-4" style="color: red;"></i>

                <?php } else { ?>
                    <i class="fas fa-file-alt fa-4x mx-auto mt-4"></i>
                <?php } ?>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $file['file_name']; ?></h5>
                    <p class="card-text">Uploaded by <?php echo $file['username']; ?> on <?php echo $file['upload_date']; ?></p>
                </div>
                <div class="card-footer">
                    <a href="<?php echo $file['file_path']; ?>" class="btn btn-primary"><i class="fas fa-download"></i> Download</a>
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#fileDetailsModal<?php echo $file['id']; ?>"><i class="fas fa-info-circle"></i> Details</button>
                </div>
            </div>
        </div>
        <!-- File details modal -->
        <div class="modal fade" id="fileDetailsModal<?php echo $file['id']; ?>" tabindex="-1" aria-labelledby="fileDetailsModal<?php echo $file['id']; ?>Label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="fileDetailsModal<?php echo $file['id']; ?>Label"><?php echo $file['file_name']; ?> Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>File name:</strong> <?php echo $file['file_name']; ?></p>
                        <p><strong>File type:</strong> <?php echo $file['file_type']; ?></p>
                        <p><strong>File size:</strong> <?php echo $file['file_size']; ?></p>
                        <p><strong>Uploaded by:</strong> <?php echo $file['username']; ?></p>
                        <p><strong>Upload date:</strong> <?php echo $file['upload_date']; ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>


    <div class="success-message">
  <i class="fas fa-check-circle"></i> File Uploaded successfully
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
