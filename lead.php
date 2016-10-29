<?php
/**
  * This is the Individual lead view. 
  *
**/
require_once 'header.php';
$leadID = intval($_GET['lead']);
$sql = "select c.*, ls.sourceName, lt.typeName, lst.statusName from {$dbPre}contacts c, {$dbPre}leadStatus lst"
     . ", {$dbPre}leadSource ls, {$dbPre}leadType lt where c.id='$leadID' and c.lStatus=lst.id"
     . " and c.leadSource=sourceID and c.leadType=typeID limit 1";
$lead = $db->extQueryRowObj($sql);

$sql = "select * from {$dbPre}otherEmails where contact='$leadID'";
$otherEmails = $db->extQuery($sql);

$sql = "select * from {$dbPre}leadNotes where leadID='$leadID' order by dateAdded desc";
$notes = $db->extQuery($sql);

$sql = "select * from {$dbPre}leadSource order by sourceName asc";
$leadSource = $db->extQuery($sql);
$sql = "select * from {$dbPre}leadType order by typeName asc";
$leadType = $db->extQuery($sql);
$sql = "select * from {$dbPre}leadStatus order by statusName asc";
$leadStatus = $db->extQuery($sql);

$sql = "select * from {$dbPre}users where id='$lead->lastModifiedBy'";
$lastModified = $db->extQueryRowObj($sql);

$sql = "select * from {$dbPre}siteSettings";
$siteSettings = $db->extQueryRowObj($sql);

$ownLeads = false;
if ($access == 2) { // user
    if ($_SESSION['ownLeadsOnly'] == 1) { //own Leads
        $ownLeads = true;
    }
}

if ($ownLeads == true) {
    $sql = "select * from {$dbPre}users where id='{$_SESSION['userID']}'";
	// Send them to access denied
	if ($lead->assignedTo != $_SESSION['userID']) {
		header('Location: access-denied.php');	
		die();
	}
} else {
    $sql = "select * from {$dbPre}users order by last asc";
}
$Owners = $db->extQuery($sql);

?>
<script>
var token = "<?php echo $token; ?>";
var access = "<?php echo $access; ?>";
var leadSources = {};
leadSources = ( <?php echo json_encode($leadSource);?> );

var leadTypes = {};
leadTypes = ( <?php echo json_encode($leadType);?> );

var leadStatuss = {};
leadStatuss = ( <?php echo json_encode($leadStatus);?> );

var siteSettings = {};
siteSettings = ( <?php echo json_encode($siteSettings);?> );

var Owners = {};
Owners = ( <?php echo json_encode($Owners);?> );

</script>
<!-- Lead specific js file -->
<script type="text/javascript" src="js/lead.js"></script>

<div class="outer">
  <p class="backLink">
    <a href="index.php?<?php echo $_SESSION['leadsQ']; ?>">
    <img src='img/subscription.png' alt='Back to Leads' /> Back to Leads (last view)</a>
  </p>
  <div class="individualDiv">
    <div class="leadDetails">
      <h2 class="left">Details</h2>
      <p>
      <?php if ($access != 0) { ?>
        <a href="#" class="addEditContact exists lead<?php echo $lead->id; ?>">
          <img src="img/table_edit.png" alt='Edit' />&nbsp;&nbsp;Edit</a>      
      <?php } ?>
      </p>
      <h2 class="modalH"><?php echo $lead->firstName . ' ' . $lead->lastName; ?></h2>
      <hr class="thinLine">
      <table class="individualLead">
        <tr class="trClass0"><td class="leadLabel"><?php echo $siteSettings->Address; ?>:</td>
			 <td class="leadData"><?php echo html_entity_decode($lead->Address); ?></td></tr>
        <tr class="trClass1"><td class="leadLabel"><?php echo $siteSettings->City; ?>:</td>
            <td class="leadData"><?php echo html_entity_decode($lead->City); ?></td></tr>
        <tr class="trClass0"><td class="leadLabel"><?php echo $siteSettings->State; ?>:</td>
            <td class="leadData"><?php echo html_entity_decode($lead->State); ?></td></tr>
        <tr class="trClass1"><td class="leadLabel"><?php echo $siteSettings->Country; ?>: </td>
            <td class="leadData"><?php echo html_entity_decode($lead->Country); ?></td></tr>
        <tr class="trClass1"><td class="leadLabel"><?php echo $siteSettings->Zip; ?>: </td>
            <td class="leadData"><?php echo html_entity_decode($lead->Zip); ?></td></tr>
        <tr class="trClass0"><td class="leadLabel"><?php echo $siteSettings->Phone; ?>: </td>
            <td class="leadData"><?php echo html_entity_decode($lead->Phone); ?></td></tr>
        <tr class="trClass1"><td class="leadLabel"><?php echo $siteSettings->secondaryPhone; ?>: </td>
            <td class="leadData"><?php echo html_entity_decode($lead->secondaryPhone); ?></td></tr>
        <tr class="trClass0"><td class="leadLabel"><?php echo $siteSettings->Fax; ?>: </td>
            <td class="leadData"><?php echo html_entity_decode($lead->Fax); ?></td></tr>
        <tr class="trClass1"><td class="leadLabel">Email: </td><td class="leadData"><?php echo $lead->Email; ?></td></tr>
        <?php 
        $i = 1;
        foreach ($otherEmails as $row => $email) {
        $i++;
        $alt = $i & 1;
        ?>
        <tr class="trClass<?php echo $alt;?>">
          <td class="leadLabel">Secondary Email: </td><td class="leadData"><?php echo $email->email; ?></td>
        </tr>
        <?php } 
        $i++;
        $alt = $i & 1;
        ?>
        <tr class="trClass<?php echo $alt;?>">
          <td class="leadLabel">Lead Type: </td><td class="leadData"><?php echo $lead->typeName; ?></td></tr>
        <?php 
        $i++;
        $alt = $i & 1;
        ?>
        <tr class="trClass<?php echo $alt;?>">
          <td class="leadLabel">Source: </td><td class="leadData"><?php echo $lead->sourceName; ?></td></tr>
        <?php
        $dateAdded = strtotime($lead->dateAdded);
        $dateSearch = date('Y-m-d', $dateAdded);
        $dateAdded = date('m-d-Y', $dateAdded);
        $lastModifiedBy = isset($lastModified->userName) != '' ? $lastModified->userName : 'Removed User';
        if ($lead->customField != '') {
        $i++;
        $alt = $i & 1;
        ?>
        <tr class="trClass<?php echo $alt;?>">
            <td class="leadLabel"><?php echo $siteSettings->customField1; ?>:</td>
            <td class="leadData"><?php echo html_entity_decode($lead->customField); ?></td>
        </tr>
        <?php } 
        if ($lead->customField2 != '') {
        $i++;
        $alt = $i & 1;
        ?>
        <tr class="trClass<?php echo $alt;?>">
          <td class="leadLabel"><?php echo $siteSettings->customField2; ?>:</td>
          <td class="leadData"><?php echo html_entity_decode($lead->customField2); ?></td>
        </tr>
        <?php }
        if ($lead->customField3 != '') {
        $i++;
        $alt = $i & 1;
        ?>
        <tr class="trClass<?php echo $alt;?>">
          <td class="leadLabel"><?php echo $siteSettings->customField3; ?>:</td>
          <td class="leadData"><?php echo html_entity_decode($lead->customField3); ?></td>
        </tr>
        <?php } 
        $i++;
        $alt = $i & 1
        ?>
        <tr class="trClass<?php echo $alt;?>">
          <td class="leadLabel">Status: </td>
          <td class="leadData"><?php echo $lead->statusName; ?></td>
        </tr>
      </table>
      <div class="userData">
        Date Added: 
        <a href="index.php?search=<?php echo $dateSearch;?>&amp;searchCol=dateAdded" title="Go to all Leads added this date.">
          <?php echo $dateAdded;?></a><br />
        Last Modified By: <span class="userHighlight"><b><?php echo $lastModifiedBy; ?></b> 
         on <?php echo $lead->dateModified; ?></span></div>
    </div>
    <div class="leadNotes">
      <h3 class="modalH">Notes</h3>
      <?php if ($access != 0) { ?>
      <p><a href="#" class="addNote <?php echo $leadID; ?>"><img src="img/add.png" alt='Add a Note' />&nbsp;&nbsp;Add a Note</a></p>
      <?php } ?>
      <div class="notesSec">
        <?php
        if (!$notes) {
        ?>
        <div class="noteContent noNotes"><span class="notice">No Notes for this lead at this time.</span></div>
        <hr class="thinLine">
        <?php 
        } else {
            foreach ($notes as $row => $note) {
                $where = array (
                    'id' => $note->creator
                );
                $creator = $db->get_value("{$dbPre}users",'userName', $where);
                $creator = ($creator == '') ? 'Removed User' : $creator;
                $noteDate = strtotime($note->dateAdded);
                $noteDate = date('m-d-Y H:i:s', $noteDate);
        ?>
        <div class="noteContainer <?php echo $note->id; ?>">
          <div class="noteContent"><?php echo $note->Note; ?></div>
          <div class="userData">
            Creator: <span class="userHighlight"><?php echo '<b>' . $creator . '</b> ' . $noteDate;?></span>
            <?php if ($access != 0) { ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="editNote <?php echo $note->id;?>">Edit Note</a>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="removeNote <?php echo $note->id;?>">Remove Note</a>
            <?php } ?>
          </div>
          <hr class="thinLine">
        </div>
        <?php 
            }
        }
        ?>
      </div>


      </div>
    </div>
  </div>
</div>
<div class="push"></div>

<?php
require_once 'footer.php';
?>
