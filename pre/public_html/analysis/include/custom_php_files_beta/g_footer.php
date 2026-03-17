<?php
$time_end = microtime(true);
$time=$time_end - $time_start;
$str="<hr>";
$str.="<div class='w3-left-align w3-yellow' style='text-shadow:1px 1px 0 #444'>Processing completed in ";
$str.=round($time,2) . "s";
$str.="</div>\n";
$str.="<br />";
output($str);

//Start of footer
#Close Main
$str="</div>\n";
#$str="<br />";
#Close Header
$str.="</div>\n";
#$str.="<hr>\n";
$str.="<footer class='w3-container $mycolour6'>\n";
output($str);

$_cp_sql1 = "SELECT `updated_when` FROM `g_updated` ORDER BY `updated_id` DESC LIMIT 1";
$res1 = execute_db($_cp_sql1, $conn);
while($row = fetch_row_db($res1)){
	$_cp_updated = $row[0];
}

$str="<div class='$mycolour12 w3-right-align'>Site was last updated on ";
#$changeDate = date("js-M-Y H:i", strtotime($_cp_updated));
$changeDate = date("l jS \of F Y", strtotime($_cp_updated));
$str.= "$changeDate";
$str.= " at ";
$changeTime = date("H:i", strtotime($_cp_updated));
$str.= "$changeTime";

$str.= "</div>\n";
$str.="</footer>\n";
$str.="</div>\n";        

output($str);
?>
