<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require 'g_functions.php';

$str="<br /><div class='nz-card'>";
$str.="<header class='w3-container $mycolour6'>";
$str.="<h1>Baseball fixes</h1>";
$str.="</header>";
output($str);

$str="<div class='w3-container w3-teal'>";
output($str);

#pgetpost_print();

if (isset($_POST['reset']) && !empty($_POST['reset'])) {
	$_cp_mychoice=$_POST['reset'];
	if ("Yes"==$_cp_mychoice) {
		$_cp_sql = "UPDATE `bb_franchisehistory` SET `fh_season`=12, `fh_week`=0 WHERE `fh_league`='MLB6' AND `fh_season`=11 AND `fh_week`=21 ";
		nz_pdo($_cp_sql,$conn);
		$_cp_sql = "UPDATE `bb_franchisehistory` SET `fh_season`=18, `fh_week`=0 WHERE `fh_league`='MLB7' AND `fh_season`=17 AND `fh_week`=21 ";
		nz_pdo($_cp_sql,$conn);
		$_cp_sql = "UPDATE `bb_franchisehistory` SET `fh_season`=17, `fh_week`=0 WHERE `fh_league`='MLB21' AND `fh_season`=16 AND `fh_week`=21 ";
		nz_pdo($_cp_sql,$conn);
		$str="<h2>Fixes applied!</h2>";
		output($str);
	} else {
		$str="<h4>Operation cancelled!</h4>";
		output($str);
		
	}
} else {
$str="<h2>Are you sure you wish to apply all fixes?</h2>";
output($str);
echo "<br>";
echo "<form class='w3-container' action='index.php?function=show_static_page&id_static_page=41' method='post'>";
echo "<select class='w3-select' name='reset'>";
echo "<option value='Yes'>&nbsp;Yes</option>";
echo "<option value='No'>&nbsp;No</option>";
echo "</select><br>";
echo "<input type='submit' value='Submit'>";
echo "</form>";
} 

echo "<br />";

require_once 'g_footer.php';
?>
