<?php
$time_end = microtime(true);
$time=$time_end - $time_start;
$_cp_sql1 = "SELECT `updated_when` FROM `g_updated` ORDER BY `updated_id` DESC LIMIT 1";
$res1 = execute_db($_cp_sql1, $conn);
while($row = fetch_row_db($res1)){
	$_cp_updated = $row[0];
}
$str="<hr>";
$str.="<div class='w3-round-medium w3-center w3-yellow' style='text-shadow:1px 1px 0 #444'>Site was last updated on ";
$changeDate = date("l jS \of F Y", strtotime($_cp_updated));
$str.= "$changeDate";
$str.= " at ";
$changeTime = date("H:i", strtotime($_cp_updated));
$str.= "$changeTime";
$str.="</div>\n";


output($str);
?>
