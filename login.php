<?php
/**
  *  Login page for LCM
  *
**/
session_start();
if (!file_exists('classes/variables.php')) {
    header('Location: install.php');
    die();
}
$tmp = explode('/',$_SERVER['PHP_SELF']); $currPage = end($tmp);  // Current page for matching files to include

require_once 'classes/dbClass.php';
$token = md5(uniqid(rand(), TRUE));
$_SESSION['token'] = $token;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Login to LCM</title>
<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" type="text/css" href="css/menu.css">
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/jquery.simplemodal-1.4.4.js"></script>
<script src="js/sha1.js" type="text/javascript"></script>
<script src="js/utf8.js" type="text/javascript"></script>

<script>
var token = '<?php echo $token; ?>';                           // token form validation

$(document).on('click', '.loginSubmit', function() {           // Login with credentials
    login();
});

$(document).on('keypress', '.password', function(e) {          // Submit on Enter Key in password field
    if (e.which == 13) {
        login();
    }
});

function login() {
    var type = 'login';
    var user = $('.username').val();
    var password = $('.password').val();
    // Check inputs
    if (user == '') {
        $('.username').closest('tr').find('.warning').fadeIn('slow', function(){});
        return false;
    } else if (password == '') {
        $('.username').closest('tr').find('.warning').hide();
        $('.password').closest('tr').find('.warning').fadeIn('slow', function(){});
        return false;
    } else {
        $('.username').closest('tr').find('.warning').hide();
        $('.password').closest('tr').find('.warning').hide();

        password = Sha1.hash($('.password').val());  // hash our password
        var dataString = 'type='+ type + '&user=' + user + '&password=' + password + '&token=' + token;  
        $.ajax({  
            type: "POST",  
            dataType: 'json',
            url: "ajax/ajaxFunctions.php",  
            data: dataString,  
            success: function(response) {
                if (response.result == '1') {
                    $('.username').removeClass('BadLogin');
                    $('.password').removeClass('BadLogin');
                    window.location = 'index.php';
                } else {
                    $('.response').hide();
                    $('.response').text(response.text).fadeIn('slow', function(){});
                }
            }
        });  
    }
};
</script>
</head>
<body>
<div class="wrapper">
 <?php require_once 'topBanner.php'; ?>
 <div class="outer">
  <div class='loginDiv'>
    <h3 class="blue"><u>Welcome To LCM, please login.</u></h3>
    <table class='login'>
      <tr>
        <td>
          User Name:
        </td>
        <td>
          <input type="text" class="username lnField" />
        </td>
        <td class="warning">
          Enter your User Name.
        </td>
      </tr>
      <tr>
        <td>
          Password:
        </td>
        <td>
          <input type="password" class="password lnField" />
        </td>
        <td class="warning">
          Enter your password.
        </td>
      </tr>
    </table>
    <div class='buttonRow'>
      <span class="response"></span>
      <button class="buttons blueButton loginSubmit"><span class='centerImg'>
        <img src='img/application_go.png' alt="Login" /></span> Login</button>
    </div>
  </div>
 </div>
<div class="push"></div>
</div>

<?php require_once 'footer.php'; ?>

