<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

//Start of draft processing
$_cp_sql="SELECT `d_id` FROM `bb_drafts` WHERE `d_league`='$_cp_league' AND `d_season`=$_cp_season AND 1=?";
#echo "<p>$_cp_sql</p>";
$_cp_val=1;
$mydraftid=nz_pdo_single($_cp_sql,$_cp_val,$conn);
echo "<h3>Draft id for $_cp_league s$_cp_season is #$mydraftid, this is round $_cp_dround.</h3>";

#$array=get_defined_vars();
#nz_debug($array,"All variables:");

$_cp_sql="SELECT `tf_seq`,`tf_line` FROM `g_turnsfull` WHERE `up_id`=? AND `tf_line` LIKE '%Draft Selections%';";
$_cp_val=$_cp_mychoice;
$_cp_mydraftno=nz_pdo_single($_cp_sql,$_cp_val,$conn);
$_cp_mydraftno=$_cp_mydraftno+1;
echo "<p>Draft starts on line #$_cp_mydraftno</p>";
$_cp_mydraftmax=12+$_cp_mydraftno;
$_cp_sql="SELECT `tf_seq`,`tf_line` FROM `g_turnsfull` WHERE `up_id`=$_cp_mychoice AND `tf_seq`>=$_cp_mydraftno AND `tf_seq`<$_cp_mydraftmax";
$myfilearraytemp=nz_pdo_array($_cp_sql,$conn);
#nz_debug($myfilearray,"File Text:");
//now loop through turn line by line
$myfilearray=array();
foreach ($myfilearraytemp as $myfilearraytemp2) {
		array_push($myfilearray,$myfilearraytemp2['tf_line']);
}
#nz_debug($myfilearray,"File Text:");
$_cp_sql="SELECT `nickname` FROM `bb_teams` WHERE 1";
$myteamarraytemp=nz_pdo_array($_cp_sql,$conn);
$myteamarray=array();
foreach ($myteamarraytemp as $myteamarraytemp2) {
		array_push($myteamarray,$myteamarraytemp2['nickname']);
}
#nz_debug($myteamarray,"Team Text:");

#Number of selections found
$y=0;
if (1==$_cp_dround){
	$k=1;	
} elseif (2==$_cp_dround){
	$k=25;	
} elseif (3==$_cp_dround){
	$k=49;	
} 

foreach ($myfilearray as $myfilearray2) {
	$separator=" ";
	$myfilearray3=explode($separator, $myfilearray2);
	#nz_debug($myfilearray3,"Pick Text:");
	$myarraysize=sizeof($myfilearray3);
	$x=0;
	while($x <= $myarraysize) {
		if ('select'==$myfilearray3[$x]) {
			$mycurrentteam=$myfilearray3[$x-1];
			$mycurrentpick=$myfilearray3[$x+1];
			$mycurrentpick=str_replace("no.","",$mycurrentpick);
			$_cp_sql="SELECT `dp_id` FROM `bb_dplayers` WHERE `d_id`=? AND `dp_no`=$mycurrentpick;";
			$_cp_val=$mydraftid;
			$mydraftplayer=nz_pdo_single($_cp_sql,$_cp_val,$conn);
			echo "In round $_cp_dround with pick $k the $mycurrentteam picked #$mycurrentpick ($mydraftplayer).<br>";
			$_cp_sql="UPDATE `bb_draftpicks` SET `dp_id`=$mydraftplayer WHERE `d_id`=$mydraftid AND `round`=$_cp_dround AND `pick`=$k";	
			nz_pdo($_cp_sql,$conn);
			#echo "<p>$_cp_sql</p>";
			$y++;
			$k++;
		}
		$x++;
	}
}
echo "Found $y selections <br>";

	$_cp_sql="UPDATE `a_uploads` SET `processed`=`processed`+16 WHERE `upload_id`=$_cp_turnid";
	#echo $_cp_sql; 
	nz_pdo($_cp_sql,$conn);

?>
