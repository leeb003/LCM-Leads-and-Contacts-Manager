<?php
/**
  * This is the main page for the LCM. 
  *
**/
require_once 'header.php';
if ($_SESSION['access'] != '1') {        // Only admins have access to config area
    header('Location: index.php');
}

$sql = "select * from {$dbPre}leadSource order by sourceName asc";
$leadSource = $db->extQuery($sql);
$sql = "select * from {$dbPre}leadType order by typeName asc";
$leadType = $db->extQuery($sql);
$sql = "select * from {$dbPre}leadStatus order by statusName asc";
$leadStatus = $db->extQuery($sql);
$sql = "select * from {$dbPre}users order by last asc";
$users = $db->extQuery($sql);
$sql = "select * from {$dbPre}siteSettings";
$siteSettings = $db->extQueryRowObj($sql);
$sql = "select * from {$dbPre}sortOrder order by orderSet";
$sortOrder = $db->extQuery($sql);
?>
<script>
    var token = "<?php echo $token; ?>";
    var page = "<?php echo isset($_GET['page']) ? $_GET['page'] : ''; ?>";

    var siteSettings = {};
    siteSettings = ( <?php echo json_encode($siteSettings);?> );
</script>
<!-- Settings specific js file -->
<script type="text/javascript" src="js/config.js"></script>

<div class="outer">
  <div class="statusSelect">
    <ul>
      <li><a href="#" class="manageSource sections selected">Sources</a></li>
      <li><a href="#" class="manageType sections">Types</a></li>
      <li><a href="#" class="manageStatus sections">Status</a></li>
      <li><a href="#" class="manageUsers sections">Users</a></li>
      <li><a href="#" class="manageExp sections">Import / Export</a></li>
      <li><a href="#" class="manageSite sections">Site Settings</a></li>
      <li><a href="#" class="emptyDatabase sections">Empty Database</a></li>
      <li><a href="#" class="showLogging sections">Activity Log</a></li>
    </ul>
  </div>

  <div class="sourceDisplay section">

    <div class="configLeft">
      <br />
      <h3 class="secTitle">Manage Your Lead Sources.</h3>
      <hr class="thinLine">
      <br /><br />
      <table class="configure">
      <?php 
      foreach ($leadSource as $row => $source) {
          if ($source->sourceName != 'None') {   // Do not display None group to edit
      ?>
        <tr class="entryItem <?php echo $source->sourceID;?>">
          <td>Name: </td>
          <td class="itemName">
            <input type="text" class="name lnField" size="50" value="<?php echo $source->sourceName; ?>" />
          </td>
          <td>Description:</td>
          <td class="notes"><input type="text" class="description lnDesc" value="<?php echo $source->description; ?>" />
          </td>
          <td><button class="smallButtons blueButton saveSource">Save</button>&nbsp;
              <button class="smallButtons redButton removeSource">Delete</button>
          </td>
        </tr>
          <?php } ?>
      <?php } ?>
      </table>
    </div>
    <div class="configRight">
      <br /><br /><br /><br />
      Add A New Source?
      <hr class="thinLine">
      <table class="addNewSource">
      <tr>
        <td>Name:</td><td><input type="text" class="newLeadSource lnField" /></td>
      </tr>
      <tr>
        <td>Description:</td><td><input type="text" class="newLeadDesc lnDesc" /></td>
      </tr>
      <tr>
        <td></td><td><button class="buttons yellowButton saveNewSource">Save</button></td>
      </tr>
      </table>
    </div>
  </div>

  <div class="typeDisplay section hidden">
    <div class="configLeft">
      <br />
      <h3 class="secTitle">Manage Your Lead Types.</h3>
      <hr class="thinLine">
      <br /><br />
      <table class="configureT">
    <?php 
    foreach ($leadType as $row => $type) {
        if ($type->typeName != 'None') {   // Do not display None group to edit
    ?>
        <tr class="entryItem <?php echo $type->typeID;?>">
          <td>Name: </td>
          <td class="itemName">
            <input type="text" class="name lnField" size="50" value="<?php echo $type->typeName; ?>" />
          </td>
          <td>Description:</td>
          <td class="notes"><input type="text" class="description lnDesc" value="<?php echo $type->description; ?>" />
          </td>
          <td><button class="smallButtons blueButton saveType">Save</button>&nbsp;
              <button class="smallButtons redButton removeType">Delete</button>
          </td>
        </tr>
          <?php } ?>
      <?php } ?>
      </table>
    </div>
    <div class="configRight">
      <br /><br /><br /><br />
      Add A New Type? 
      <hr class="thinLine">
      <table class="addNewType">
      <tr>
        <td>Name:</td>
        <td><input type="text" class="newLeadType lnField" /></td>
      </tr>
      <tr>
        <td>Description:</td><td><input type="text" class="newLeadDesc lnDesc" /></td>
      </tr>
      <tr>
        <td></td><td><button class="buttons yellowButton saveNewType">Save</button></td>
      </tr>
      </table>
    </div>

  </div>

  <div class="statusDisplay section hidden">

    <div class="configLeft">
      <br />
      <h3 class="secTitle">Manage Your Lead Status.</h3>
      <hr class="thinLine">
      <br /><br />
      <table class="configureS">
    <?php
    foreach ($leadStatus as $row => $status) {
        if ($status->statusName != 'None') {   // Do not display None group to edit
    ?>
        <tr class="entryItem <?php echo $status->id;?>">
          <td>Name: </td>
          <td class="itemName">
            <input type="text" class="name lnField" size="50" value="<?php echo $status->statusName; ?>" />
          </td>
          <td>Description:</td>
          <td class="notes"><input type="text" class="description lnDesc" value="<?php echo $status->description; ?>" />
          </td>
          <td><button class="smallButtons blueButton saveStatus">Save</button>&nbsp;
              <button class="smallButtons redButton removeStatus">Delete</button>
          </td>
        </tr>
          <?php } ?>
      <?php } ?>
      </table>
    </div>
    <div class="configRight">
      <br /><br /><br /><br />
      Add A New Status?
      <hr class="thinLine">
      <table class="addNewStatus">
      <tr>
        <td>Name:</td>
        <td><input type="text" class="newLeadStatus lnField" /></td>
      </tr>
      <tr>
        <td>Description:</td><td><input type="text" class="newLeadDesc lnDesc" /></td>
      </tr>
      <tr>
        <td></td><td><button class="buttons yellowButton saveNewStatus">Save</button></td>
      </tr>
      </table>
    </div>
  </div>

  <div class="usersDisplay section hidden">
    <div class="configLeft">
      <br />
      <h3 class="secTitle">Manage Users.</h3>
      <hr class="thinLine">
      <br /><br />
      <table class="currentUsers">
      <tr><th></th><th>User</th><th>User Name</th><th>Email</th><th>Created</th><th>Role</th><th>Edit</th></tr>
        <?php
        $i = 0;
        foreach ($users as $row => $user) {
            $created = strtotime($user->created);
            $created = date('m-d-Y h:m:s', $created);
            if ($user->isAdmin == '1') {
                $adminUser = 'Admin';
            } elseif ($user->isAdmin =='2') {
                $adminUser = 'User';
            } elseif ($user->isAdmin == '0') {
                $adminUser = 'Read Only';
            }
            if ($adminUser == 'User' && $user->ownLeadsOnly == 1) { // User manages only their own leads
                $ownLeads = 'ownLeads';
            } else {
                $ownLeads = '';
            }
            $i++;
        ?>
        <tr>
          <td><?php echo $i;?>).
          <td><?php echo $user->first . ' ' . $user->last;?></td>
          <td><?php echo $user->userName;?></td>
          <td><?php echo $user->email;?></td>
          <td><?php echo $created; ?></td>
          <td class="<?php echo $ownLeads;?>"><?php echo $adminUser; ?></td>
          <td>
          <?php
              if ($_SESSION['firstName'] == 'Demo') {
                  $disabled = 'disabled="disabled"';
                  $message = '<p style="color: #569a46; font-weight: bold;">User Functions have been disabled in Demo mode.</p>';
              } else {
                  $disabled = '';
                  $message = '';
              }
            ?>
            <button class="smallButtons blackButton changeAccount <?php echo $user->id;?>" <?php echo $disabled;?>>Update</button>&nbsp;
            <button class="smallButtons redButton removeUser <?php echo $user->id;?>" <?php echo $disabled;?>>Delete</button>
          </td>
        </tr>
        <?php } ?>
      </table>
    </div>

    <div class="configRight">
      <br /><br /><br /><br />
      <table class="addNewUser">
      <tr class="addUser">
        <td><button class="buttons yellowButton addNewUser" <?php echo $disabled;?>>Add A New User</button></td>
      </tr>
      </table>
      <?php echo $message;?>
    </div>

  </div>

  <div class="exportDisplay section hidden">
    <div id="tabs">
      <ul>
        <li><a href="#imports">Import Contacts</a></li>
        <li><a href="#exports">Export Contacts</a></li>
      </ul>
      
      <div id="imports">
        <h3 class="modalH">Import Contacts</h3>
        <div class="importSteps">
          <b>Step 1.</b> Import Contacts from a file containing data.<br />
          Important Notes: 
          <ul>
            <li>Supported formats are csv, xlsx, xls. </li>
            <li>Be sure to at least have a first and last name field in your data or it will be ignored.</li>
            <li>Email addresses must either be valid or blank or the entry will be rejected.</li>
            <li>Import Data must have a header row so you can line up fields correctly.</li>
          </ul>
          <br />
          <form method="post" enctype="multipart/form-data" id="UploadForm">
             <input id="fileToUpload" type="file" name="fileToUpload" class="input">
             <button class="buttons blueButton uploadContacts" id="buttonUpload" onclick="return ajaxFileUpload();">Upload</button>
             <img id="loading" src="img/loaderb32.gif" alt="Loading" style="float:right; display:none;">  </form>
        </div>
      </div>

      <div id="exports">
        <h3 class="modalH">Export Contacts</h3>
        <ul>
          <li>
            <form action="ajax/exportDirect.php" method="post">
              Export as <a href="#" onclick="$(this).closest('form').submit()" class="exportExcel">Excel Spreadsheet</a>
              <input type="hidden" name="type" value="excel" />
            </form>
          </li>
          <li>
            <form action="ajax/exportDirect.php" method="post">
              Export as <a href="#" onclick="$(this).closest('form').submit()" class="exportCSV">CSV</a>
              <input type="hidden" name="type" value="csv" />
            </form>
          </li>
        </ul>
        <div class="results"></div>
      </div>
    </div>
  </div>

  <div class="siteDisplay section hidden">

    <div class="configLeft">
      <br />
      <h3 class="secTitle">Manage Your Site Settings.</h3>
      <hr class="thinLine">
      <br /><br />
      
      <h3 class="secTitle">1.) Pagination</h3> 
      <p>Choose how many results to display per page on sections of the site that are paginated. </p>
      <p class="pageResultsP">
         Results Per Page: 
         <input type="text" class="pageResults lnField" size="10" value="<?php echo $siteSettings->pageResults; ?>" />
         <button class="buttons blackButton savePageResults">Save</button>
      </p>
      <hr class="thinLine">
      <br />
      <h3 class="secTitle">2.) Name your Fields</h3>
      <p>Customize your leads and change the name of the following fields to your liking, including the 3 extra (custom) fields.</p>
      <table class="fieldNames">
        <tr>
          <td class="addresstd">
            <span class="fieldNameC">Address:</span>
            <input type="text" class="Address lnField" size="12" value="<?php echo $siteSettings->Address; ?>" />
            <button class="buttons blackButton saveField">Save</button>
          </td>
          <td class="citytd">
            <span class="fieldNameC">City:</span>
            <input type="text" class="City lnField" size="12" value="<?php echo $siteSettings->City; ?>" />
            <button class="buttons blackButton saveField">Save</button>
          </td>
          <td class="statetd">
            <span class="fieldNameC">State:</span>
            <input type="text" class="State lnField" size="12" value="<?php echo $siteSettings->State; ?>" />
            <button class="buttons blackButton saveField">Save</button>
          </td>
        </tr>
        <tr>
          <td class="countrytd">
            <span class="fieldNameC">Country:</span>
            <input type="text" class="Country lnField" size="12" value="<?php echo $siteSettings->Country; ?>" />
            <button class="buttons blackButton saveField">Save</button>
          </td>
          <td class="ziptd">
            <span class="fieldNameC">Zip:</span>
            <input type="text" class="Zip lnField" size="12" value="<?php echo $siteSettings->Zip; ?>" />
            <button class="buttons blackButton saveField">Save</button>
          </td>
          <td class="phonetd">
            <span class="fieldNameC">Phone:</span>
            <input type="text" class="Phone lnField" size="12" value="<?php echo $siteSettings->Phone; ?>" />
            <button class="buttons blackButton saveField">Save</button>
          </td>
        </tr>
        <tr>
          <td class="secondaryPtd">
            <span class="fieldNameC">Phone 2:</span>
            <input type="text" class="secondaryPhone lnField" size="12" value="<?php echo $siteSettings->secondaryPhone; ?>" />
            <button class="buttons blackButton saveField">Save</button>
          </td>
          <td class="faxtd">
            <span class="fieldNameC">Fax:</span>
            <input type="text" class="Fax lnField" size="12" value="<?php echo $siteSettings->Fax; ?>" />
            <button class="buttons blackButton saveField">Save</button>
          </td>
          <td>
          </td>        
        </tr>
        <tr>
          <td class="customFieldP">
            <span class="fieldNameC">Extra1:</span>
            <input type="text" class="customField1 lnField" size="12" value="<?php echo $siteSettings->customField1; ?>" />
            <button class="buttons blackButton saveField">Save</button>
          </td>
          <td class="customField2td">
            <span class="fieldNameC">Extra2:</span>
            <input type="text" class="customField2 lnField" size="12" value="<?php echo $siteSettings->customField2; ?>" />
            <button class="buttons blackButton saveField">Save</button>
          </td>
          <td class="customField3td">
            <span class="fieldNameC">Extra3:</span>
            <input type="text" class="customField3 lnField" size="12" value="<?php echo $siteSettings->customField3; ?>" />
            <button class="buttons blackButton saveField">Save</button>
          </td>
        </tr>
      </table>
      <hr class="thinLine">
      <br />

      <h3 class="secTitle">3.) Sort Results</h3>
      <p>Choose the Fields and the order displayed in the overall view of Leads.  The top list are the
      Columns that will be used, the bottom are the Columns not to be used on the leads page.  Drag and Drop to place them.
      
      </p>
      <div class='colContainer'>
        <b>Columns Used:</b>
        <ul id="sortableCols" class="connectedSortable">
        <?php
        require_once 'classes/general.php';
        $GEN = new GEN();

        foreach ($sortOrder as $row => $field) {
            if ($field->used == 1) {
                $name = $GEN->nameField($field->setName, $siteSettings);
        ?>
          <li class="id<?php echo $field->id;?>"><?php echo $name; ?></li>
        <?php 
            } 
        }    
        ?>
        </ul>
        <br />
        <b>Columns Not Used:</b>
        <ul id="sortableCols2" class="connectedSortable">
        <?php
        foreach ($sortOrder as $row => $field) {
            if ($field->used == 0) {
                $name = $GEN->nameField($field->setName, $siteSettings);
        ?>
          <li class="id<?php echo $field->id;?>"><?php echo $name; ?></li>
        <?php 
            } 
        }    
        ?>
        </ul>
        <p class="buttonRow"><button class="buttons blueButton saveOrder">Save Order</button></p>
      </div>
    </div>
  </div>


  <div class="emptyDBDisplay section hidden">
    <div class="configLeft">
      <br />
      <h3 class="secTitle">Remove Lead data from database</h3>
      <hr class="thinLine">
      <br /><br />
      <p>You can choose to remove all lead data from the database for fast removal of data.</p>
      <p><b>Warning!!</b> This will delete all leads from the database!  If you are sure you want to do this proceed.</p>
      <br /><br />
	  <button class="buttons redButton emptyDB" <?php echo $disabled; ?>>Remove all Leads from Database</button>
	  <?php if ($_SESSION['firstName'] == 'Demo') { ?>
	  <p style="color: #569a46; font-weight: bold;">This has been disabled in Demo mode.</p>
	  <?php } ?>
    </div>
  </div>

  <div class="loggingDisplay section hidden">
    <div class="configLeft">
      <br />
      <h3 class="secTitle">Activity Log</h3> Current Server Time: <?php echo date('Y-m-d H:i:s'); ?>
      <hr class="thinLine">
      <br /><br />
      <div class="activityLog">
      </div>
    </div>
    <div class="configRight">
        <br /><br /><br /><br />
        <p class="buttonRow"><button class="buttons blueButton showLogging">Refresh List</button></p>
    </div>
  </div>


</div>
<div class="push"></div>
</div>
<script>
// Trigger events
    if (page == 'users') {
        $('.manageUsers').trigger('click');
    }
    if (page == 'import') {
        $('.manageExp').trigger('click');
    }
    if (page == 'logging') {
        $('.showLogging').trigger('click');
    }
    if (page == 'siteSettings') {
        $('.manageSite').trigger('click');
    }
</script>





<?php
require_once 'footer.php';
?>
