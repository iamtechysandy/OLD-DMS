<?php
// start session and check if user is logged in
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// establish database connection
$db_conn = mysqli_connect("localhost", "root", "", "dms");

// check connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

// retrieve folders that user has access to based on their permissions
$user_id = $_SESSION["user_id"];
$folders_query = "SELECT f.id, f.folder_name, f.folder_path, f.decribe FROM folders f 
                  JOIN folder_permissions p ON f.id = p.folder_id 
                  WHERE p.user_id = $user_id AND p.permission >= 1";


$folders_result = mysqli_query($db_conn, $folders_query);
$selected_folder_id = isset($_GET['fid']) ? base64_decode($_GET['fid']) : null;

?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .navbar {
            justify-content: space-between;
        }
        container mt-5.ul {
    list-style-type: none;
    padding: 0;
}

container mt-5.li {
    margin-bottom: 1rem;
}

container mt-5.a {
    text-decoration: none;
    color: black;
}

container mt-5.a:hover {
    text-decoration: underline;
}
footer {
  font-size: 14px;
}

footer a {
  color: #fff;
}

footer a:hover {
  color: #ccc;
}
   body {
  position: relative;
  min-height: 100vh;
}

footer {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  font-size: 14px;
}
 .fa-folder {
    font-size: 10rem;
    color: darkred;
  }
  
  /* style to remove underline */
  .text-decoration-none {
    text-decoration: none;
  }

/* rest of the CSS code remains the same */
 
    </style>
</head>
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
<main>
   <div class="container mt-5">
  <h1>Home</h1>
  <p>Here are the folders that you have access to:</p>
  <div class="row">
    <?php while ($folder = mysqli_fetch_assoc($folders_result)) { ?>
      <div class="col-md-4">
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title">
              <?php if ($selected_folder_id == $folder['id']) { ?>
                <strong><i class="fas fa-folder-open fa-lg me-2"></i> <span class="text-decoration-none"><?php echo $folder['folder_name']; ?></span></strong>
              <?php } else { ?>
                <a href="folder.php?fid=<?php echo base64_encode($folder['id']); ?>"><i class="fas fa-folder fa-lg me-2"></i> <span class="text-decoration-none"><?php echo $folder['folder_name']; ?></span></a>
              <?php } ?>
            </h5>
            <p class="card-text"><?php echo $folder['decribe']; ?></p>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</div>




</main>
<footer class="bg-dark text-white py-3">
  <div class="container">
    <div class="row">
      <div class="col-lg-6 col-md-6 mb-3">
        <p class="text-muted">Copyright &copy; 2023
          <a href="https://shar.com.sa">Shar Company</a>. All rights reserved.</p>
          <p class="text-muted">Document Management System</p>
      </div>
      <div class="col-lg-6 col-md-6 mb-3 text-end">
        <a href="#" class="text-white me-3"><i class="fas fa-envelope"></i> it.team@shar.com.sa</a>
        <a href="#" class="text-white"><i class="fas fa-phone"></i> +1 123 456 7890</a>
      </div>
    </div>
  </div>
</footer>

    <!-- Bootstrap 5 JS bundle -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>

