<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';
#$array=get_defined_vars();
#nz_debug($array,"All variables:");

//Start of draft processing
$k=0;
$mydraftline=0;
$mydraftstart=0;
$_cp_sql="SELECT `tf_id`, `tf_seq`, `tf_line` FROM `g_turnsfull` WHERE `up_id`=$_cp_turnid;";
$myfilearray=nz_pdo_array($_cp_sql,$conn);
//now loop through turn line by line
foreach ($myfilearray as $mylines){ 
$myline=$mylines['tf_line'];
#nz_debug($myline,"$k:");
$pattern = "/Draft Order/";
	if(1==preg_match($pattern, $myline)) {
		if (0==$mydraftstart) {
		$mydraftstart=$k+2;
		}
	}
$k++;
}
echo "<p>Draft order is on line: $mydraftstart</p>";

$mydraftorder=$myfilearray[$mydraftstart]['tf_line'];
$_cp_val=1;
$_cp_sql="SELECT `d_id` FROM `bb_drafts` WHERE `d_league`='$_cp_league' AND `d_season`=$_cp_season AND 1=?";
$mydraftid=nz_pdo_single($_cp_sql,$_cp_val,$conn);
echo "<p>Draft order for draft #$mydraftid is: $mydraftorder</p>";
$separator=" ";
$_cp_tempdraftorder=explode($separator, $mydraftorder);
#nz_debug($_cp_tempdraftorder,"Temporary Draft Order:");


$i=1;
$j=25;
$k=49;

//TODO replace with proper query
#$mydraftteam=10600;

echo"<p>";
foreach ($_cp_tempdraftorder as $myabbr){ 
	#KLUDGE - 25th element is a 2 ch string that doesn't show!
	if (2==strlen($myabbr) AND is_string($myabbr) AND $i<25) {
		#nz_debug($myabbr,"Draft Order Debug:");
		$_cp_sql="SELECT `f_id` FROM `bb_franchises` WHERE `f_league`='$_cp_league' AND `f_abbr`=?";
		#nz_debug($_cp_sql,"Draft Order Debug:");
		$row = nz_pdo_row($_cp_sql,$myabbr,$conn);
		#nz_debug($row,"Draft Order Debug:");
		$mydraftteam=$row[0];
		#nz_debug($mydraftteam,"Draft Order Debug:");
		echo "$i - $_cp_val - $mydraftteam - $myabbr<br>";
		if ($i<24) {
			#echo "&nbsp;&nbsp;/&nbsp;&nbsp;"; 
		} 
		
		#Insert draft order into draft picks table i+24 i+48
		$_cp_sql="INSERT INTO `bb_draftpicks` (`pick_id`, `d_id`, `round`, `pick`, `f_id`, `dp_ID`) VALUES (NULL, $mydraftid, 1, $i, $mydraftteam, NULL)";
		#echo "<p>$_cp_sql</p>";
		$asm=nz_pdo_insert($_cp_sql,$conn);
		$_cp_sql="INSERT INTO `bb_draftpicks` (`pick_id`, `d_id`, `round`, `pick`, `f_id`, `dp_ID`) VALUES (NULL, $mydraftid, 2, $j, $mydraftteam, NULL)";
		#echo "<p>$_cp_sql</p>";
		$asm=nz_pdo_insert($_cp_sql,$conn);
		$_cp_sql="INSERT INTO `bb_draftpicks` (`pick_id`, `d_id`, `round`, `pick`, `f_id`, `dp_ID`) VALUES (NULL, $mydraftid, 3, $k, $mydraftteam, NULL)";
		#echo "<p>$_cp_sql</p>";
		$asm=nz_pdo_insert($_cp_sql,$conn);	
		$i++;
		$j++;
		$k++;
	}

}
echo"<p>";
#nz_debug($mydraftorder,"Draft Order:");

	$_cp_sql="UPDATE `a_uploads` SET `processed`=`processed`+16 WHERE `upload_id`=$_cp_turnid";
	#echo $_cp_sql; 
	nz_pdo($_cp_sql,$conn);
	
?>
