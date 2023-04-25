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

$user_id = $_SESSION["user_id"];



// Initialize variables
$search_uploader = '';
$search_date_from = '';
$search_date_to = '';
$search_filetype = '';
$search_size_min = '';
$search_size_max = '';

$search_name = '';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Get search input values
  $search_uploader = $_POST['search_uploader'];
  $search_date_from = $_POST['search_date_from'];
  $search_date_to = $_POST['search_date_to'];
  $search_filetype = $_POST['search_filetype'];
  $search_size_min = $_POST['search_size_min'];
  $search_size_max = $_POST['search_size_max'];

  $search_name = $_POST['search_name'];

$folders_query = "SELECT DISTINCT files.*
FROM files
JOIN folders ON folders.id = files.folder_id
JOIN folder_permissions ON folder_permissions.folder_id = folders.id
WHERE (folders.user_id = $user_id OR folder_permissions.user_id = $user_id)
AND (folder_permissions.permission = 1 OR folder_permissions.permission = 2)
";



$files_query = "SELECT files.*, users.username 
                  FROM files 
                  JOIN users ON files.user_id = users.id 
                  WHERE folder_id IN (SELECT id 
                                      FROM folders 
                                      WHERE user_id = $user_id 
                                        OR id IN (SELECT folder_id 
                                                  FROM folder_permissions 
                                                  WHERE user_id = $user_id 
                                                    AND (permission = 1 OR permission = 2)))";


// Check if search term is set
if (isset($_GET['search_term'])) {
    $search_term = $_GET['search_term'];
  }

$folders_result = mysqli_query($conn, $folders_query);

  // Build query based on search input
  $files_query = "SELECT files.*, users.username FROM files JOIN users ON files.user_id = users.id WHERE folder_id IN (SELECT id FROM folders WHERE user_id = $user_id OR id IN (SELECT folder_id FROM folder_permissions WHERE user_id = $user_id))";


  
  if (!empty($search_uploader)) {
    $files_query .= " AND user_id = (SELECT id FROM users WHERE username = '$search_uploader')";
  }

  if (!empty($search_date_from) && !empty($search_date_to)) {
    $files_query .= " AND upload_date BETWEEN '$search_date_from' AND '$search_date_to'";
  }

  if (!empty($search_filetype)) {
    $files_query .= " AND file_type = '$search_filetype'";
  }

  if (!empty($search_size_min)) {
    $files_query .= " AND file_size >= $search_size_min";
  }

  if (!empty($search_size_max)) {
    $files_query .= " AND file_size <= $search_size_max";
  }

  if (!empty($search_name_range_min) && !empty($search_name_range_max)) {
    $files_query .= " AND file_name BETWEEN $search_name_range_min AND $search_name_range_max";
  }

  if (!empty($search_name)) {
    $files_query .= " AND file_name LIKE '%$search_name%'";
  }

  // Execute query
  $files_result = mysqli_query($conn, $files_query);
}
?>