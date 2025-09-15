<?php
//Start of footer
$str="</div>\n";
$str.="</div>\n";
$str.="<footer class='w3-container $mycolour1'>\n";
output($str);


$_cp_sql1 = "SELECT `updated_when` FROM `g_updated` WHERE 1=? ORDER BY `updated_id` DESC LIMIT 1";
$_cp_val=1;
$_cp_updated=nz_pdo_single($_cp_sql1,$_cp_val,$conn);

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
