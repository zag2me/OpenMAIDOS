<?php
	require_once('authentication.php');
	if (file_exists('maint.now')) {
		$semaphore = true;
		$fh = fopen('maint.now', 'r');
		while (!feof($fh))
			$maint_reason .= fread($fh, 1024);
		fclose($fh);
		}
	else $semaphore = false;
	$currentUser = Authenticate();
	if (!IsAdmin($currentUser) && $semaphore == true ) {
		//show header
		include('header.php');

		//show maintenance message
		echo "<div id=\"menudiv\">";
		echo "<b><a href=\"$sys_url\">OpenMAID</a> > Directory</b>";
		echo "</div></td></tr><tr>";
		echo "<td id=\"contentarea\">";
		echo "<div id=\"widebar\">";
		echo "<center><h2><b>Temporarily Offline For Maintenance</b></h2></center>";
		echo "<br><center><b>Sorry, OpenMAID is down for maintenance.<br>Please check back again later...</b></center><br>";
		if ($maint_reason != "") echo "<br><br><br><center><b>Reason: $maint_reason</b></center><br>";
		echo "</div>";

		//show footer
		include('footer.php');

		//exit
		exit();
		}
?>