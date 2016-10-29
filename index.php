<?php
/**
  * This is the main page for the LCM. 
  *
**/
require_once 'header.php';
require_once 'classes/paging.php';

// If the user can only access their own leads
$ownLeadsQ = '';
$ownLeadsQS = '';
$ownLeads = false;
if ($access == 2) { // user
    if ($_SESSION['ownLeadsOnly'] == 1) { //own Leads
        $ownLeadsQS = " and assignedTo='{$_SESSION['userID']}'";
        $ownLeadsQ = " and c.assignedTo='{$_SESSION['userID']}'";
        $ownLeads = true;
    }
}

// Advanced Search
$advSearch = false;
$advCheck = '';
if (isset($_GET['advSearch']) && $_GET['advSearch'] == 'true') {
    if (isset($_POST['firstNameStr'])) {
        // Clean and convert post vars for html
        foreach ($_POST as $key => $value) {
            $key = trim(htmlentities($key, ENT_QUOTES, "UTF-8"));
            $value = trim(htmlentities($value, ENT_QUOTES, "UTF-8"));  // prior to 5.4 default is iso-8859-1
            $post[$key] = $value;
        }
		if (isset($post['leadOwner']) && $post['leadOwner'] != '') {
			$ownerString = " and c.assignedTo='{$post['leadOwner']}'";
		} else {
			$ownerString = " and c.assignedTo LIKE '%{$post['leadOwner']}%'";
		}
        $searchString = " and c.firstName LIKE '%{$post['firstNameStr']}%' and c.lastName LIKE '%{$post['lastNameStr']}%'"
                  . " and c.Address LIKE '%{$post['addressStr']}%' and c.City LIKE '%{$post['cityStr']}%'"
                  . " and c.State LIKE '%{$post['stateStr']}%' and c.Country LIKE '%{$post['countryStr']}%'"
                  . " and c.Zip LIKE '%{$post['zipStr']}%' and c.Phone LIKE '%{$post['phoneStr']}%'"
                  . " and c.secondaryPhone LIKE '%{$post['secondPhoneStr']}%' and c.Fax LIKE '%{$post['faxStr']}%'"
                  . " and c.customField LIKE '%{$post['customStr']}%' and c.customField2 LIKE '%{$post['custom2Str']}%'"
                  . " and c.customField3 LIKE '%{$post['custom3Str']}%' and c.dateAdded LIKE '%{$post['dateAddedStr']}%'"
                  . " and c.leadSource LIKE '%{$post['leadSource']}%' and c.leadType LIKE '%{$post['leadType']}%'"
                  //. " and c.assignedTo LIKE '%{$post['leadOwner']}%'"
				  . $ownerString
                  . " and c.Email LIKE '%{$post['emailStr']}%' $ownLeadsQ";

    } elseif (isset($_SESSION['advSearchRet']) && $_SESSION['advSearchRet'] == true) {
        $searchString = $_SESSION['searchString'];
    }
    $advSearch = true;
    $searchVal = '';
    $searchCol = '';
    $secondaryEmails = false;
    $advCheck = 'checked="checked"';

// Normal Search
} elseif ( isset($_GET['search'])
    && $_GET['search'] != '') {
    if ($_GET['searchCol'] == 'secondaryEmails') {          // Secondary Emails
        $searchVal = trim(htmlentities($_GET['search'], ENT_QUOTES, "UTF-8"));
        $searchCol = trim(htmlentities($_GET['searchCol'], ENT_QUOTES, "UTF-8"));
        $secondaryEmails = true;

    } elseif ($_GET['searchCol'] == 'leadSource'            // Cannot do like clauses on ids, get extra matches
        || $_GET['searchCol'] == 'leadType'
		|| $_GET['searchCol'] == 'assignedTo' ) {
        $searchCol = trim(htmlentities($_GET['searchCol'], ENT_QUOTES, "UTF-8"));
        $searchVal = trim(htmlentities($_GET['search'], ENT_QUOTES, "UTF-8"));
        $search = "and c.$searchCol='$searchVal'";
        $searchCount = "and $searchCol='$searchVal'";
        $secondaryEmails = false;

    } else {
        $searchCol = trim(htmlentities($_GET['searchCol'], ENT_QUOTES, "UTF-8"));
        $searchVal = trim(htmlentities($_GET['search'], ENT_QUOTES, "UTF-8"));
        $search = "and c.$searchCol LIKE '%$searchVal%'";
        $searchCount = "and $searchCol LIKE '%$searchVal%'";
        $secondaryEmails = false;
    }
} else {
    $search = '';
    $searchCount = '';
    $searchCol = '';
    $searchVal = '';
    $secondaryEmails = false;
}


// get the page query into a session for returning
if ($advSearch == true) { // remove query for advanced search
    $_SESSION['leadsQ'] = 'advSearch=true';
    $_SESSION['searchString'] = $searchString;
    $_SESSION['advSearchRet'] = true;
} else {
    $_SESSION['leadsQ'] = $_SERVER['QUERY_STRING'];
    unset($_SESSION['advSearchRet']);
    unset($_SESSION['searchString']);
}

if ( isset($_GET['status']) && $_GET['status'] != '' ) {
    $statusSelect =intval($_GET['status']);
    $searchStatus = "and c.lStatus='$statusSelect'";
    $statusCount = "and lStatus='$statusSelect'";
} else {
    $statusSelect = '';
    $searchStatus = '';
    $statusCount = '';
}

// Pagination and sort queries
if ($secondaryEmails == true) {        // Searching Other emails
    $sql = "select count(*) as 'count' from {$dbPre}contacts c, {$dbPre}otherEmails oe"
         . " where oe.contact=c.id and oe.email like '%$searchVal%' $ownLeadsQ group by c.id";
    $tempT = $db->extQuery($sql);
    $clientC = count($tempT);

} elseif ($advSearch == true) {        // Advanced Search
    $sql = "select count(*) as 'count' from {$dbPre}contacts c where 1 $searchString";
    $totalC = $db->extQueryRowObj($sql);
    $clientC = $totalC->count;

} else {
    $sql = "select count(*) as 'count' from {$dbPre}contacts where 1 $searchCount $statusCount $ownLeadsQS";
    $totalC = $db->extQueryRowObj($sql);
    $clientC = $totalC->count;
}
// Get the results per page from siteSettings
$sql = "select * from {$dbPre}siteSettings";
$siteSettings = $db->extQueryRowObj($sql);
$rpp = $siteSettings->pageResults;

if ($advSearch == true) {   // Override for advanced search and send all to screen
    $rpp = $clientC;
}

if ( isset($_GET['sort']) && !empty($_GET['sort']) ) {
    $sort = trim(htmlspecialchars($_GET['sort']));
    if ($sort == 'Source') {        // convert source and type sort selections
        $sort = 'ls.sourceName';
    } elseif ( $sort == 'leadType') {
        $sort = 'lt.typeName';
    }
    $dir = trim(htmlspecialchars($_GET['dir']));
    if ($dir == 'asc') {
        $dirLink = 'desc';
        $arrow = '<img src="img/arrow-down.png" />';
    } else {
        $dirLink = 'asc';
        $arrow = '<img src="img/arrow-up.png" />';
    }
    $orderBy = "order by $sort $dir";
} else {
    $sort = '';
    $dir = '';
    $dirLink = 'asc';  // default
    $arrow = '';
    $orderBy = "order by lastName asc";
}
$reload = "index.php?status=$statusSelect&amp;sort=$sort&amp;dir=$dir&amp;search=$searchVal&amp;searchCol=$searchCol";
if (isset($_GET["pages"])) {
    $pages = intval($_GET["pages"]);
} else {
    $pages = 1;
}
$tpages = ($clientC) ? ceil($clientC/$rpp) : 1;
$adjacents = '3';
$startLimit = ($pages -1) * $rpp;
// End pagination work

// Main Queries
if ($secondaryEmails == true) {  // Searching Other Emails
    $sql = "select oe.email as 'second', c.*, ls.sourceName, lt.typeName, CONCAT(u.first, ' ', u.last) as 'Owner'"
         . " from {$dbPre}otherEmails oe, {$dbPre}contacts c, {$dbPre}leadSource ls, {$dbPre}leadType lt, {$dbPre}users u" 
         . " where oe.contact=c.id and oe.email like '%$searchVal%' and"
         . " c.leadSource=ls.sourceID and"
         . " c.leadType=lt.typeID and"
         . " c.assignedTo=u.id"
         . " $ownLeadsQ group by c.id $orderBy LIMIT $startLimit, $rpp";

} elseif ($advSearch == true) {  // Advanced Search
    $sql = "select c.*, ls.sourceName, lt.typeName, CONCAT(u.first, ' ', u.last) as 'Owner'"
         . " from {$dbPre}contacts c, {$dbPre}leadSource ls, {$dbPre}leadType lt, {$dbPre}users u"
         . " where c.leadSource=ls.sourceID and"
         . " c.leadType=lt.typeID and"
         . " c.assignedTo=u.id"
         . " $searchString $orderBy";

} else {
    $sql = "select c.*, ls.sourceName, lt.typeName, CONCAT(u.first, ' ', u.last) as 'Owner'"
         . " from {$dbPre}contacts c, {$dbPre}leadSource ls, {$dbPre}leadType lt, {$dbPre}users u"
         . " where c.leadSource=ls.sourceID and"
         . " c.leadType=lt.typeID and"
         . " c.assignedTo=u.id $search $searchStatus $ownLeadsQ"
         . " $orderBy LIMIT $startLimit, $rpp";
}
$leads = $db->extQuery($sql);

$sql = "select * from {$dbPre}leadSource order by sourceName asc";
$leadSource = $db->extQuery($sql);
$sql = "select * from {$dbPre}leadType order by typeName asc";
$leadType = $db->extQuery($sql);
$sql = "select * from {$dbPre}leadStatus order by statusName asc";
$leadStatus = $db->extQuery($sql);
$sql = "select * from {$dbPre}sortOrder where used='1' order by orderSet asc";
$sortOrder = $db->extQuery($sql);

if ($ownLeads == true) {
    $sql = "select * from {$dbPre}users where id='{$_SESSION['userID']}'";
} else {
    $sql = "select * from {$dbPre}users order by last asc";
}
$Owners = $db->extQuery($sql);
?>

<script>
// globals
var token = "<?php echo $token; ?>";
var statusSelect = "<?php echo $statusSelect; ?>"; 
var access = "<?php echo $access; ?>";
var leadSources = {};
leadSources = ( <?php echo json_encode($leadSource);?> );

var leadTypes = {};
leadTypes = ( <?php echo json_encode($leadType);?> );

var leadStatuss = {};
leadStatuss = ( <?php echo json_encode($leadStatus);?> );

var Owners = {};
Owners = ( <?php echo json_encode($Owners);?> );

var siteSettings = {};
siteSettings = ( <?php echo json_encode($siteSettings);?> );

</script>
<!-- Leads specific js file -->
<script type="text/javascript" src="js/leads.js"></script>

<div class="outer">

  <div class="statusSelect">
    <ul>
      <li><a <?php echo ($statusSelect == '') ? 'class="selected"' : '';?> href="index.php">All Status</a></li>
    <?php
    foreach ($leadStatus as $row => $leadStat) {
        if ($leadStat->id == $statusSelect) {
            $selected = 'class="selected"'; 
        } else { 
            $selected = '';
        }
    ?>
      <li>
        <a href="index.php?status=<?php echo $leadStat->id;?>" <?php echo $selected; ?>><?php echo $leadStat->statusName;?></a>
      </li>
    <?php } ?>
    </ul>
  </div>

  <div class="leadsMenu">
    <span class="leadsTitle">Leads and Contacts </span> <span class="smallFont">(<?php echo $clientC; ?> Total)</span>
    <span class="addNewSpan">
      <?php 
      if ($access == 0) { // disable add new
          $disabled = ' disabled="disabled"';
      } else {
          $disabled = '';
      }
      ?>
      <button class="button smallButtons blueButton addEditContact" title="Add a new Lead." <?php echo $disabled;?>>
       <span class="centerImg"><img src="img/add.png" alt="Add" /></span>Add New
      </button>
    </span>
  
    <!-- Pagination links -->
    <div class="paginate">
    <?php 
    $paging = new paging($reload, $pages, $tpages, $adjacents);
    echo $paging->getDiv();
    ?>
    </div>

    <!-- Search -->
    <span class="searchLeads">
      <?php 
      if($searchCol == 'leadType' || $searchCol == 'leadSource' || $searchCol == 'assignedTo') { 
          $hide = 'hidden';
          $text = '';
      } else {
          $hide = '';
          $text = $searchVal;
      }
      ?>
      Search For:  <input type="text" class="searchText <?php echo $hide; ?>" value="<?php echo $text; ?>">
       
      <select class="leadSources <?php echo ($searchCol == 'leadSource') ? '': 'hidden'; ?>">
        <option value="">--Select--</option>
        <?php
        foreach ($leadSource as $row => $source) {
            if ($searchCol == 'leadSource'
               && $searchVal == $source->sourceID ) {
                $selectText = ' selected="selected"';
            } else {
                $selectText = '';
            }
        ?>
            <option value="<?php echo $source->sourceID;?>"<?php echo $selectText; ?>><?php echo $source->sourceName;?></option>
        <?php 
        }
        ?>
      </select>

      <select class="leadTypes <?php echo ($searchCol == 'leadType') ? '': 'hidden'; ?>">
        <option value="">--Select--</option>
        <?php  
        foreach ($leadType as $row => $type) {
            if ($searchCol == 'leadType'
               && $searchVal == $type->typeID ) {
                $selectText = ' selected="selected"';
            } else {
                $selectText = '';
            }
        ?>
            <option value="<?php echo $type->typeID;?>"<?php echo $selectText;?>><?php echo $type->typeName;?></option>
        <?php
        }
        ?>
      </select>

      <select class="Owners <?php echo ($searchCol == 'assignedTo') ? '': 'hidden'; ?>">
        <option value="">--Select--</option>
        <?php
        foreach ($Owners as $row => $own) {
            if ($searchCol == 'assignedTo'
               && $searchVal == $own->id ) {
                $selectText = ' selected="selected"';
            } else {
                $selectText = '';
            }
        ?>
            <option value="<?php echo $own->id;?>"<?php echo $selectText; ?>><?php echo $own->first . ' ' . $own->last?></option>
        <?php 
        }
        ?>
      </select>

        In:  
      <?php $selectText = 'selected="selected"'; ?>
      <select class="searchColumn">
        <option value="firstName" <?php echo ($searchCol == 'firstName') ? $selectText: ''; ?>>First Name</option>
        <option value="lastName" <?php echo ($searchCol == 'lastName') ? $selectText: ''; ?>>Last Name</option>
        <option value="Address" <?php echo ($searchCol == 'Address') ? $selectText: ''; ?>>
        <?php echo $siteSettings->Address; ?></option>
        <option value="City" <?php echo ($searchCol == 'City') ? $selectText: ''; ?>><?php echo $siteSettings->City; ?></option>
        <option value="State" <?php echo ($searchCol == 'State') ? $selectText: ''; ?>><?php echo $siteSettings->State; ?></option>
        <option value="Country" <?php echo ($searchCol == 'Country') ? $selectText: ''; ?>>
        <?php echo $siteSettings->Country; ?></option>
        <option value="Zip" <?php echo ($searchCol == 'Zip') ? $selectText: ''; ?>><?php echo $siteSettings->Zip; ?></option>
        <option value="Phone" <?php echo ($searchCol == 'Phone') ? $selectText: ''; ?>><?php echo $siteSettings->Phone; ?></option>
        <option value="secondaryPhone" <?php echo ($searchCol == 'secondaryPhone') ? $selectText: ''; ?>>
        <?php echo $siteSettings->secondaryPhone; ?></option>
        <option value="Fax" <?php echo ($searchCol == 'Fax') ? $selectText: ''; ?>><?php echo $siteSettings->Fax; ?></option>
        <option value="Email" <?php echo ($searchCol == 'Email') ? $selectText: ''; ?>>Primary Email</option>
        <option value="secondaryEmails" <?php echo ($searchCol == 'secondaryEmails') ? $selectText: ''; ?>>Secondary Email</option>
        <option value="leadSource" <?php echo ($searchCol == 'leadSource') ? $selectText: ''; ?>>Source</option>
        <option value="leadType" <?php echo ($searchCol == 'leadType') ? $selectText: ''; ?>>Type</option>
        <option value="dateAdded" <?php echo ($searchCol == 'dateAdded') ? $selectText: ''; ?>>Date Added</option>
        <option value="customField" <?php echo ($searchCol == 'customField') ? $selectText: ''; ?>>
         <?php echo $siteSettings->customField1; ?></option>
        <option value="customField2" <?php echo ($searchCol == 'customField2') ? $selectText: ''; ?>>
         <?php echo $siteSettings->customField2; ?></option>
        <option value="customField3" <?php echo ($searchCol == 'customField3') ? $selectText: ''; ?>>
         <?php echo $siteSettings->customField3; ?></option>
        <option value="assignedTo" <?php echo ($searchCol == 'assignedTo') ? $selectText: ''; ?>>
         <?php echo $siteSettings->assignedTo; ?></option>
      </select>
      <button class="goSearch smallButtons greenButton">Search</button>
      &nbsp;&nbsp;&nbsp;&nbsp;Advanced Search: <input type="checkbox" class="advSearch" <?php echo $advCheck;?>>
    </span>
  </div>

<div class="allLeadsHolder">
  <table class="allLeads">
    <tr class="topRow">
<?php    
    $extra = "&amp;searchCol=$searchCol";
    if ($advSearch == true) {
        $extra .= "&amp;advSearch=true";
    }
    require_once 'classes/general.php';
    $GEN = new GEN();
    foreach ($sortOrder as $row => $field) {
        $printName = $GEN->nameField($field->setName, $siteSettings);
?>
      <th>
        <a href="<?php echo "?status=$statusSelect&amp;sort=$field->columnName&amp;dir=$dirLink&amp;search=$searchVal" . $extra; ?>"
        class="sort"><?php echo $printName; ?><?php echo ($sort == $field->columnName) ? $arrow : ''; ?></a>
      </th>
<?php } ?>
    </tr>


<?php
$i = 0;
foreach ($leads as $row => $lead) {
    $i++;
    $trClass = 'trClass' . ($i & 1);
    echo "<tr class='$trClass'>";
    foreach ($sortOrder as $row => $field) {
        $columnName = $field->columnName;
        if ($field->setName == 'Name') {
            if ($access != 0) { // All but read only
                $editDelete = "<a href='#' class='addEditContact exists lead$lead->id'><img src='img/table_edit.png'"
                   . "alt='Edit' title='Edit' /></a>&nbsp;&nbsp;"
                   . "<a href='#' class='deleteLead lead$lead->id'><img src='img/delete.png' alt='Delete' title='Delete' /></a>"
                   . "&nbsp;&nbsp;";
            } else {
                $editDelete = '';
            }
            echo "<td>$editDelete"
               . "&nbsp;&nbsp;<a href='lead.php?lead=$lead->id' class='viewLead'"
               . " title='Go view this Contact'>$lead->firstName $lead->lastName</a>"
               . "</td>";
        } elseif ($field->setName == 'Owner') {
            echo "<td>" . $lead->Owner . "</td>";
        } else {
            echo "<td>" . html_entity_decode($lead->{"$columnName"}) . "</td>";
        }
   }
   echo "</tr>";
}
?>
  </table>
</div>

</div>
<div class="push"></div>
</div>

<?php
require_once 'footer.php';
?>

