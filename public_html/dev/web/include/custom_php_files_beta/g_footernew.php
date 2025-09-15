<?php
//Start of footer
$str="</div>\n";
$str.="</div>\n";
$str.="<footer class='w3-container w3-theme-l2'>\n";
output($str);


$_cp_sql1 = "SELECT `modification_time` FROM `f_games` WHERE 1 ORDER BY `modification_time` DESC LIMIT 1";
$res1 = execute_db($_cp_sql1, $conn);
while($row = fetch_row_db($res1)){
	$_cp_updated = $row[0];
}

$str="<div class='$mycolour19 w3-cursive w3-right-align' style='text-shadow:1px 1px 0 #444'><em>Site was last updated on ";
#$changeDate = date("js-M-Y H:i", strtotime($_cp_updated));
$changeDate = date("l jS \of F Y", strtotime($_cp_updated));
$str.= "$changeDate";
$str.= " at ";
$changeTime = date("H:i:s", strtotime($_cp_updated));
$str.= "$changeTime";

$str.= "</em></div>\n";
$str.="</footer>\n";
$str.="</div>\n";        

output($str);
?>
