<?php
/**
  * This is the statistics page for the LCM. 
  *
**/
require_once 'header.php';

$sql = "select * from {$dbPre}leadSource order by sourceName asc";      // Get Sources
$sourceNames = $db->extQuery($sql);

foreach ($sourceNames as $row => $source) {
    $sql = "select count(*) as 'Total' from {$dbPre}contacts where leadSource='$source->sourceID'";
    $sourceCount = $db->extQueryRowObj($sql);
    $leadSource[$source->sourceName] = $sourceCount->Total;
}
arsort($leadSource);      // sort from largest to lowest

$sql = "select * from {$dbPre}leadType order by typeName asc";         // Get Types
$typeNames = $db->extQuery($sql);

foreach ($typeNames as $row => $type) {
    $sql = "select count(*) as 'Total' from {$dbPre}contacts where leadType='$type->typeID'";
    $typeCount = $db->extQueryRowObj($sql);
    $leadType[$type->typeName] = $typeCount->Total;
}
arsort($leadType);

$sql = "select * from {$dbPre}leadStatus order by statusName asc";     // Get Lead Status
$statusName = $db->extQuery($sql);

foreach ($statusName as $row => $status) {
    $sql = "select count(*) as 'Total' from {$dbPre}contacts where lStatus='$status->id'";
    $statusCount = $db->extQueryRowObj($sql);
    $leadStatus[$status->statusName] = $statusCount->Total;
}
$totalLeadCount = count($leadStatus);

$sql = "SELECT DATE_FORMAT(dateAdded, '%Y') as 'year',"
     . "DATE_FORMAT(dateAdded, '%m') as 'month',"
     . "COUNT(id) as 'total'"
     . "FROM {$dbPre}contacts WHERE (dateAdded) >= CURDATE() - INTERVAL 1 YEAR"
     . " GROUP BY DATE_FORMAT(dateAdded, '%Y%m')";
$lastTwelveCount = $db->extQuery($sql);             // Get the last 12 months of leads added per month
$lastTwelveArray = array();
$lastTwelve = array();
for ($i = 11; $i >= 0; $i--) {                      // Compare months to totals and build new array with amounts
    $pastMonth = strtotime("-$i month");
    $dateCompare = date('Y-m', $pastMonth);
    $dateFormatted = date('M-Y', $pastMonth);
    foreach ($lastTwelveCount as $entry => $month) {
        $dateString = $month->year . '-' . $month->month;
        if ($dateCompare == $dateString) {
            $lastTwelve[$dateFormatted] = $month->total;
            $match = 'yes';
            break;
        } else {
            $match = 'no';
        }
        if ($match == 'no') {
            $lastTwelve[$dateFormatted] = 0;
        }
    }
}
//print_r($lastTwelve);

?>

<!--[if lt IE 9]>
<script type="text/javascript" src="js/FlashCanvas/bin/flashcanvas.js"></script>
<![endif]-->
<script type="text/javascript" src="js/flotr2.min.js"></script>
<script type="text/javascript">
    var leadSources = {};
    leadSources = ( <?php echo json_encode($leadSource);?> );

    var leadTypes = {};
    leadTypes = ( <?php echo json_encode($leadType);?> );

    var leadStatuss = {};
    leadStatuss = ( <?php echo json_encode($leadStatus);?> );

    var totalLeadCount = <?php echo $totalLeadCount; ?>;

    var lastTwelve = {};
    lastTwelve = ( <?php echo json_encode($lastTwelve); ?> );

    var count = 5;       // Total to display for pies
</script>

<div class="outer">
  <div class="statistics">
    <h3 class="modalH">Leads and Contacts Statistics</h3>
    <!-- Sources -->
    <div class="statsSec">
      <h2 class="left">Lead Sources</h2>

      <div id="leadSourceChart"></div>

      <div class="leadList">
        <table class="leadTable">
          <tr><th>Source</th><th>Count</th></tr>
    <?php
    foreach ($leadSource as $name => $count) {
    ?>
          <tr><td><?php echo $name; ?></td><td><?php echo $count; ?></td></tr>
    

    <?php } ?>
        </table>
      </div>
    </div>
    <!-- End Sources -->

        <!-- Types -->
    <div class="statsSec">
      <h2 class="left">Lead Types</h2>
      <div id="leadTypeChart"></div>
    
      <div class="leadList">
        <table class="leadTable">
          <tr><th>Source</th><th>Count</th></tr>
    <?php
    foreach ($leadType as $name => $count) {
    ?>
          <tr><td><?php echo $name; ?></td><td><?php echo $count; ?></td></tr>
    

    <?php } ?>
        </table>
      </div>
    </div>
    <!-- End Types -->

    <!-- Status -->
    <div class="statsSec">
      <h2 class="left">Lead Status Group Count</h2>

      <div id="leadStatusChart"></div>

      <div class="leadList">
        <table class="leadTable">
          <tr><th>Status</th><th>Count</th></tr>
    <?php
    foreach ($leadStatus as $name => $count) {
    ?>
          <tr><td><?php echo $name; ?></td><td><?php echo $count; ?></td></tr>


    <?php } ?>
        </table>
      </div>
    </div>
    <!-- End Status -->

    <!-- New Leads Per Month -->
    <div class="statsSec">
      <h2 class="left">New Leads Last 12 Months</h2>

      <div id="newLeadsMonth"></div>

      <div class="leadList">
        <table class="leadTable">
          <tr><th>Month</th><th>Count</th></tr>
    <?php
        foreach ($lastTwelve as $key => $value) {
    ?>
          <tr><td><?php echo $key; ?></td><td><?php echo $value; ?></td></tr>
    <?php 
        }    
    ?>
        </table>
      </div>
    </div>
    <!-- End New Leads Per Month -->
  </div>
  </div>
  <div class="push"></div>
</div>

<!-- load charts js last -->
<script type="text/javascript" src="js/stats.js"></script>

<?php
require_once 'footer.php';
?>

