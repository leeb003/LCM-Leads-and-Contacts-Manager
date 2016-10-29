<?php
/**
  * This is the header file for the LCM. 
  *
**/
session_start();
// First check if we need to run the installer to set up the database
// If the variables.php file does not exist
if (!file_exists('classes/variables.php')) {
    header('Location: install.php');
    die();
}

require_once 'classes/dbClass.php';   // Require login
$db = new DB();
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != 'yes') {
    header('Location: login.php');
}
$token = md5(uniqid(rand(), TRUE));
$_SESSION['token'] = $token;

$access = $_SESSION['access'];

$tmp = explode('/',$_SERVER['PHP_SELF']); $currPage = end($tmp);  // Current page for matching files to include
// Send a utf-8 header
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>LCM - Leads & Contacts Manager</title>
<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" type="text/css" href="css/menu.css">
<link rel="shortcut icon" type="image/x-icon" href="favicon1.ico"/>
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/jquery.simplemodal-1.4.4.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="js/jquery.ui.touch-punch.js"></script>

<?php 
if ($currPage == 'config.php') {    // Settings page scripts  ?>         
<link rel="stylesheet" href="css/smoothness/jquery-ui-1.10.3.custom.min.css" />
<script src="js/sha1.js" type="text/javascript"></script>  <!-- This is for user management password functions -->
<script src="js/utf8.js" type="text/javascript"></script>
<script src="js/ajaxfileupload.js" type="text/javascript"></script>
<?php } elseif ($currPage == 'lead.php') {  // Load tinyMCE ?>
<script src="js/tinymce/js/tinymce/tinymce.min.js"></script>

<?php } ?>
    
</head>
<body>
<div class="wrapper">
  <?php require_once 'topBanner.php'; ?>
