<?php
	require_once 'header.php';
?>

<p class="denied">Access Denied.</p>
<div class="outer">
  	<p class="backLink">
      <a href="index.php?<?php echo $_SESSION['leadsQ']; ?>">
	      <img src='img/subscription.png' alt='Back to Leads' /> Back to Leads (last view)</a>
	</p>
</div>

<?php
require_once 'footer.php';
?>
