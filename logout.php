<?php
/**
  * Script to logout session
  *
**/
session_start();
require_once 'classes/dbClass.php';
$db = new DB;
require_once 'classes/logging.php';
$log = new Log($dbPre);

// logging
$event = 'Logged Out';
$log->logEvent($_SESSION['userID'], $event);
unset($_SESSION['loggedIn']);
unset($_SESSION['userID']);
session_unset();  // Clear all the rest
header('Location: login.php');

