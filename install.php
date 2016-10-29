<?php
/**
  *  Installation page for LCM
  *
**/
session_start();
//require_once 'classes/dbClass.php';
$token = md5(uniqid(rand(), TRUE));
$_SESSION['token'] = $token;
$tmp = explode('/',$_SERVER['PHP_SELF']); $currPage = end($tmp);  // Current page for matching files to include

if (file_exists('classes/variables.php')) {
    $configExists = true;
} else {
    $configExists = false;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" type="text/css" href="css/menu.css">
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/jquery.simplemodal-1.4.4.js"></script>

<script>
var token = '<?php echo $token; ?>';                           // token form validation

function gatherVars(hostName, database, dbUser, dbPassword, dbPre) {  // Both test and install use this
    // Check inputs
    $('.warning').hide();
    if (hostName == '') {
        $('.hostName').closest('tr').find('.warning').fadeIn('slow', function(){});
        return false;
    } else if (database == '') {
        $('.database').closest('tr').find('.warning').fadeIn('slow', function(){});
        return false;
    } else if (dbUser == '') {
        $('.dbUsername').closest('tr').find('.warning').fadeIn('slow', function(){});
        return false;
    } else if (dbPassword == '') {
        $('.dbPassword').closest('tr').find('.warning').fadeIn('slow', function(){});
        return false;
    } else if (dbPre =='') {
        $('.dbPre').closest('tr').find('.warning').fadeIn('slow', function(){});
        return false;
    } else {
        $('.dbUsername').closest('tr').find('.warning').hide();
        $('.dbPassword').closest('tr').find('.warning').hide();
    }
};


$(document).on('click', '.testDB', function() {                 // Test Database
    var type = 'testDB';
    var hostName = $('.hostName').val();
    var database = $('.database').val();
    var dbUser = $('.dbUsername').val();
    var dbPassword = $('.dbPassword').val();
    var dbPre = $('.dbPre').val();
    var result = gatherVars(hostName, database, dbUser, dbPassword,dbPre);
    if (result == false) {
        return false;
    } else {
        $.ajax({  
            type: "POST",  
            dataType: 'json',
            url: "ajax/install.php",  
            data : { type:type, hostName:hostName, database:database, dbUser:dbUser, 
                    dbPassword:dbPassword, dbPre:dbPre, token:token},
            success: function(response) {
                if (response.error == '0') {
                    $('.msg').hide();
                    var data='<span class="centerImg"><img src="img/accept.png" /></span>'
                            +'<span style="color: green;font-size:18px;">' + response.msg + '</span>'
                        +'<br />Click the Run Install button to create the tables and config file.';
                    $('.msg').html(data).fadeIn('slow', function(){});
                    $('.hideInstall').removeClass('hidden');
                    $('.hideTest').addClass('hidden');
                } else {
                    $('.msg').hide();
                    var data='<span style="color: red;font-size:18px;">' + response.msg + '</span>';
                    $('.msg').html(data).fadeIn('slow', function(){});
                    $('.hideInstall').addClass('hidden');
                    $('.hideTest').removeClass('hidden');
                }
            }
        });  
    }
});

$(document).on('click', '.installLCM', function() {                 // Install Database and config
    var type = 'installLCM';
    var hostName = $('.hostName').val();
    var database = $('.database').val();
    var dbUser = $('.dbUsername').val();
    var dbPassword = $('.dbPassword').val();
    var dbPre = $('.dbPre').val();
    var result = gatherVars(hostName, database, dbUser, dbPassword, dbPre); 
    if (result == false) {
        return false;
    } else {
        $.ajax({  
            type: "POST",  
            dataType: 'json',
            url: "ajax/install.php",  
            data : { type:type, hostName:hostName, database:database, dbUser:dbUser, 
                    dbPassword:dbPassword, dbPre: dbPre, token:token},
            success: function(response) {
                if (response.error == '0') {
                    $('.msg').hide();
                    var data='<span class="centerImg"><img src="img/accept.png" /></span>'
                            +'<span style="color: green;font-size:18px;">' + response.msg + '</span>';
                            
                    $('.msg').html(data).fadeIn('slow', function(){});

                    var form='<br /><br /><form action="ajax/install.php" method="post">'
                            + '<input type="hidden" name="token" value="' + token + '" />'
                            + '<input type="hidden" name="hostName" value="' + hostName + '" />'
                            + '<input type="hidden" name="database" value="' + database + '" />'
                            + '<input type="hidden" name="dbUser" value="' + dbUser + '" />'
                            + '<input type="hidden" name="dbPassword" value="' + dbPassword + '" />'
                            + '<input type="hidden" name="dbPre" value="' + dbPre + '" />'
                            + '<input type="hidden" name="type" value="returnConfig" />'
                            + '<button class="buttons yellowButton" name="submit">Download Variables File</button></form>';

                    $('.msg').append(form);
                    $('.hideInstall').addClass('hidden');
                    $('.hideLogin').removeClass('hidden');
                } else {
                    $('.msg').hide();
                    var data='<span style="color: red;font-size:18px;">' + response.msg + '</span>';
                    $('.msg').html(data).fadeIn('slow', function(){});
                    $('.hideInstall').addClass('hidden');
                    $('.hideTest').removeClass('hidden');

                }
            }
        });  
    }
});

$(document).on('click', '.login', function() {      // Send them to the login page
    window.location.href = "login.php";
});

</script>
</head>
<body>
<div class="wrapper">
 <?php require_once 'topBanner.php'; ?>
 <div class="outer">
  <div class='setupDiv'>
    <h2 class="blue"><u>Welcome To LCM.</u></h2>
    <?php if ($configExists == true) { ?>
        <p> You already have a configuration file created, if you want to reinstall please remove classes/variables.php</p>
        <div class='buttonRow'>
          <button class="buttons greenButton login">Go Login</button>
        </div>

    <?php } else { ?>

    <p> It looks like this is your first time setting up Leads and Contacts.  This installer will help you to set up 
    your database tables and your config file.  You will need to know your database name, username and password in order to 
    install.</p>
    <br />
    <hr class='thinLine'>
    <br />
    <table class='setup'>
      <tr>
        <td>
            Host Name:
        </td>
        <td>
          <input type="text" class="hostName lnField" />
        <td class="warning">
            Your Server hostname (localhost ?)
        </td>
      </tr>
      <tr>
        <td>
          Database:
        </td>
        <td>
          <input type="text" class="database lnField" />
        </td>
        <td class="warning">
            Your Database Name.
        </td>
      </tr>
      <tr>
        <td>
          Database User Name:
        </td>
        <td>
          <input type="text" class="dbUsername lnField" />
        </td>
        <td class="warning">
          Enter your User Name.
        </td>
      </tr>
      <tr>
        <td>
          Database Password:
        </td>
        <td>
          <input type="password" class="dbPassword lnField" />
        </td>
        <td class="warning">
          Enter your password.
        </td>
      </tr>
      <tr>
        <td>
          Table Prefix:
        </td>
        <td>
          <input type="text" class="dbPre lnField" value="lcm_" />
        </td>
        <td class="warning">
          Table Prefix can be changed, can't be blank 
        </td>
      </tr>
    </table>
    <div class="msg"></div>
    <div class='buttonRow'>
      <div class="hideTest">
        <button class="buttons blueButton testDB"><span class='centerImg'>
        <img src='img/accept.png' /></span> Test Database</button>
      </div>
      <div class="hideInstall hidden">
        <button class="buttons greenButton installLCM" ><span class='centerImg'>
        <img src='img/application_go.png' /></span> Run Install</button>
      </div>
      <div class="hideLogin hidden">
       <button class="buttons greenButton login" >Login to LCM</button>
      </div>
    </div>

    <?php } ?>

  </div>
 </div>
<div class="push"></div>
</div>
<?php require_once 'footer.php'; ?>

</body>
</html>


