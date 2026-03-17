<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require 'g_functions.php';

//Start of content
echo "<div class='w3-panel $mycolour4 nz-card w3-round-xxlarge'>";
echo "<h1>Baseball reset</h1>";
#pgetpost_print();

//Need to update this to not just blow away everything!
if (isset($_POST['reset']) && !empty($_POST['reset'])) {
	$_cp_mychoice=$_POST['reset'];
	if ("Yes"==$_cp_mychoice) {
		$_cp_sql = "SET FOREIGN_KEY_CHECKS=0;";
		nz_pdo($_cp_sql,$conn);
		$_cp_sql = "DELETE FROM `g_turnsummary`";
		nz_pdo($_cp_sql,$conn);
		$_cp_sql = "DELETE FROM `g_turnsfull`";
		nz_pdo($_cp_sql,$conn);
		$_cp_sql = "DELETE FROM `bb_playerstemp`";
		nz_pdo($_cp_sql,$conn);
		$_cp_sql = "DELETE FROM `bb_dplayers`";
		nz_pdo($_cp_sql,$conn);
		$_cp_sql = "DELETE FROM `bb_drafts`";
		nz_pdo($_cp_sql,$conn);
		$_cp_sql = "DELETE FROM `bb_draftpicks`";
		nz_pdo($_cp_sql,$conn);
		#$_cp_sql = "DELETE FROM `bb_franchises`";
		#nz_pdo($_cp_sql,$conn);
		#$_cp_sql = "DELETE FROM `bb_franchisehistory`";
		#nz_pdo($_cp_sql,$conn);
		$_cp_sql = "DELETE FROM `g_uploads`";
		nz_pdo($_cp_sql,$conn);
		$_cp_sql = "SET FOREIGN_KEY_CHECKS=1;";
		nz_pdo($_cp_sql,$conn);
		$str="<h2>Tables truncated!</h2>";
		$str.="<p>**Remember to clear out uploads directory**</p>";
		output($str);
	} else {
		$str="<h4>Operation cancelled!</h4>";
		output($str);
		
	}
} else {
$str="<h2>Are you sure you wish to reset baseball data?</h2>";
output($str);
echo "<br>";
echo "<form class='w3-container' action='index.php?function=show_static_page&id_static_page=30' method='post'>";
echo "<select class='w3-select' name='reset'>";
echo "<option value='Yes'>&nbsp;Yes</option>";
echo "<option value='No'>&nbsp;No</option>";
echo "</select><br>";
echo "<br />";
echo "<input type='submit' value='Submit'>";
echo "</form>";
} 

echo "<br />";

require_once 'g_footer.php';
?>
