<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
//Bring in required files
require_once 'g_functions.php';
require_once 'bb_functions.php';
require_once 'mydatabase.php';

$str="<br /><div class='nz-card'>";
$str.="<header class='w3-container w3-blue-gray'>";
$str.="<h1>Turn analysis</h1>";
$str.="</header>";
output($str);


$str="<div class='w3-container w3-pale-blue'>";
output($str);
//Text for middle box
$str="<h2>About to analyse a turn!</h2>";
output($str);

$str="<div class='w3-container w3-teal'>\n";
output($str);

# Get Turn ID
//Look for turns with Roundup!
$_cp_val=0;
$_cp_sql="SELECT `turn_id` FROM `g_turnsummary` WHERE `processed`=? AND `roundup`='Y' ORDER BY `turn_id` ASC LIMIT 1";
$_cp_turnid=nz_pdo_single($_cp_sql,$_cp_val,$conn);


if (''<>$_cp_turnid) {
$str="<p>Found turn with ID $_cp_turnid</p>";
output($str);
//Find line with AL East
$_cp_val=$_cp_turnid;
$_cp_sql="SELECT `tf_id` FROM `g_turnsfull` WHERE `ts_id`=? AND `tf_line` LIKE '%Roundup%' AND `tf_line` LIKE '%AL East%';";
$_cp_turnid1=nz_pdo_single($_cp_sql,$_cp_val,$conn);
#$str="Found row with ID $_cp_turnid1 &nbsp;/&nbsp;&nbsp;&nbsp;";
#output($str);

echo "<p>";

//Update for AL East
$_cp_data = [
    'id' => $_cp_turnid,
    'id1' => $_cp_turnid1
];
$_cp_mysql="UPDATE `g_turnsummary` SET `roundup1`=:id1 WHERE `turn_id`=:id";
$_cp_updated=nz_pdo_update($_cp_mysql,$_cp_data,$conn);
echo "Found AL East Roundup and updated $_cp_updated row &nbsp;/&nbsp;&nbsp;&nbsp;";

//Find line with AL Central
$_cp_val=$_cp_turnid;
$_cp_sql="SELECT `tf_id` FROM `g_turnsfull` WHERE `ts_id`=? AND `tf_line` LIKE '%Roundup%' AND `tf_line` LIKE '%AL Central%';";
$_cp_turnid1=nz_pdo_single($_cp_sql,$_cp_val,$conn);
#$str="Found row with ID $_cp_turnid1 &nbsp;/&nbsp;&nbsp;&nbsp;";
#output($str);

//Update for AL Central
$_cp_data = [
    'id' => $_cp_turnid,
    'id1' => $_cp_turnid1
];
$_cp_mysql="UPDATE `g_turnsummary` SET `roundup2`=:id1 WHERE `turn_id`=:id";
$_cp_updated=nz_pdo_update($_cp_mysql,$_cp_data,$conn);
echo "Found AL Central Roundup and updated $_cp_updated row &nbsp;/&nbsp;&nbsp;&nbsp;";

//Find line with AL West
$_cp_val=$_cp_turnid;
$_cp_sql="SELECT `tf_id` FROM `g_turnsfull` WHERE `ts_id`=? AND `tf_line` LIKE '%Roundup%' AND `tf_line` LIKE '%AL West%';";
$_cp_turnid1=nz_pdo_single($_cp_sql,$_cp_val,$conn);
#$str="Found row with ID $_cp_turnid1 &nbsp;/&nbsp;&nbsp;&nbsp;";
#output($str);

//Update for AL East
$_cp_data = [
    'id' => $_cp_turnid,
    'id1' => $_cp_turnid1
];
$_cp_mysql="UPDATE `g_turnsummary` SET `roundup3`=:id1 WHERE `turn_id`=:id";
$_cp_updated=nz_pdo_update($_cp_mysql,$_cp_data,$conn);
echo "Found AL West Roundup and updated $_cp_updated row &nbsp;/&nbsp;&nbsp;&nbsp;";

//Find line with NL East
$_cp_val=$_cp_turnid;
$_cp_sql="SELECT `tf_id` FROM `g_turnsfull` WHERE `ts_id`=? AND `tf_line` LIKE '%Roundup%' AND `tf_line` LIKE '%NL East%';";
$_cp_turnid1=nz_pdo_single($_cp_sql,$_cp_val,$conn);
#$str="Found row with ID $_cp_turnid &nbsp;/&nbsp;&nbsp;&nbsp;";
#output($str);

//Update for NL East
$_cp_data = [
    'id' => $_cp_turnid,
    'id1' => $_cp_turnid1
];
$_cp_mysql="UPDATE `g_turnsummary` SET `roundup4`=:id1 WHERE `turn_id`=:id";
$_cp_updated=nz_pdo_update($_cp_mysql,$_cp_data,$conn);
echo "Found NL East Roundup and updated $_cp_updated row &nbsp;/&nbsp;&nbsp;&nbsp;";

//Find line with NL Central
$_cp_val=$_cp_turnid;
$_cp_sql="SELECT `tf_id` FROM `g_turnsfull` WHERE `ts_id`=? AND `tf_line` LIKE '%Roundup%' AND `tf_line` LIKE '%NL Central%';";
$_cp_turnid1=nz_pdo_single($_cp_sql,$_cp_val,$conn);
#$str="Found row with ID $_cp_turnid5 &nbsp;/&nbsp;&nbsp;&nbsp;";
#output($str);

//Update for NL Central
$_cp_data = [
    'id' => $_cp_turnid,
    'id1' => $_cp_turnid1
];
$_cp_mysql="UPDATE `g_turnsummary` SET `roundup5`=:id1 WHERE `turn_id`=:id";
$_cp_updated=nz_pdo_update($_cp_mysql,$_cp_data,$conn);
echo "Found NL Central Roundup and updated $_cp_updated row &nbsp;/&nbsp;&nbsp;&nbsp;";

//Find line with NL West
$_cp_val=$_cp_turnid;
$_cp_sql="SELECT `tf_id` FROM `g_turnsfull` WHERE `ts_id`=? AND `tf_line` LIKE '%Roundup%' AND `tf_line` LIKE '%NL West%';";
$_cp_turnid1=nz_pdo_single($_cp_sql,$_cp_val,$conn);
#$str="Found row with ID $_cp_turnid1 &nbsp;/&nbsp;&nbsp;&nbsp;";
#output($str);

//Update for NL East
$_cp_data = [
    'id' => $_cp_turnid,
    'id1' => $_cp_turnid1
];
$_cp_mysql="UPDATE `g_turnsummary` SET `roundup6`=:id1 WHERE `turn_id`=:id";
$_cp_updated=nz_pdo_update($_cp_mysql,$_cp_data,$conn);
echo "Found NL West Roundup and updated $_cp_updated row &nbsp;/&nbsp;&nbsp;&nbsp;";

echo "</p>";

echo "<p>";

//Find Batting Stats pages
$_cp_sql="SELECT `tf_seq`,`tf_line`  FROM `g_turnsfull` WHERE `ts_id`=$_cp_turnid AND `tf_line` LIKE '% Batting Leaders%';";
$_cp_mybstats=nz_pdo_array($_cp_sql,$conn);
$x=1;
foreach  ($_cp_mybstats as $_cp_mybstat)
{
    $_cp_myval='batstat';
    $_cp_myval.=$x;
	if (isset($_cp_mybstat['tf_seq'])) {
		$_cp_data = [
		'id' => $_cp_turnid,
		'id1' => $_cp_mybstat['tf_seq']
		];
		}
    $_cp_data = [
    'id' => $_cp_turnid,
    'id1' => 9999
    ];
    $_cp_data = [
    'id' => $_cp_turnid,
    'id1' => $_cp_mybstat['tf_seq']
    ];

	$_cp_mysql="UPDATE `g_turnsummary` SET `$_cp_myval`=:id1 WHERE `turn_id`=:id";
	$_cp_updated=nz_pdo_update($_cp_mysql,$_cp_data,$conn);
	echo "Found Batting Stats #$x and updated $_cp_updated row &nbsp;/&nbsp;&nbsp;&nbsp;";    
    $x++;
}

//TODO - remove this kludge

    $_cp_data = [
    'id' => $_cp_turnid,
    'id1' => 2150
    ];
$_cp_mysql="UPDATE `g_turnsummary` SET `batstat8`=:id1  WHERE `turn_id`=:id";
$_cp_updated=nz_pdo_update($_cp_mysql,$_cp_data,$conn);

echo "</p>";
echo "<p>";

//Find Pitching Stats pages
$_cp_sql="SELECT `tf_seq`,`tf_line`  FROM `g_turnsfull` WHERE `ts_id`=$_cp_turnid AND `tf_line` LIKE '% Pitching Leaders%';";
$_cp_mypstats=nz_pdo_array($_cp_sql,$conn);
$x=1;
foreach  ($_cp_mypstats as $_cp_mypstat)
{
    $_cp_myval='pitstat';
    $_cp_myval.=$x;
    $_cp_data = [
    'id' => $_cp_turnid,
    'id1' => $_cp_mypstat['tf_seq']
    ];
	$_cp_mysql="UPDATE `g_turnsummary` SET `$_cp_myval`=:id1 WHERE `turn_id`=:id";
	$_cp_updated=nz_pdo_update($_cp_mysql,$_cp_data,$conn);
	echo "Found Pitching Stats #$x and updated $_cp_updated row &nbsp;/&nbsp;&nbsp;&nbsp;";    
    $x++;
}

//TODO - remove this kludge

    $_cp_data = [
    'id' => $_cp_turnid,
    'id1' => 2050
    ];
$_cp_mysql="UPDATE `g_turnsummary` SET `batstat7`=:id1  WHERE `turn_id`=:id";
$_cp_updated=nz_pdo_update($_cp_mysql,$_cp_data,$conn);
$_cp_mysql="UPDATE `g_turnsummary` SET `batstat8`=:id1  WHERE `turn_id`=:id";
$_cp_updated=nz_pdo_update($_cp_mysql,$_cp_data,$conn);

    $_cp_data = [
    'id' => $_cp_turnid,
    'id1' => 2550
    ];
$_cp_mysql="UPDATE `g_turnsummary` SET `pitstat8`=:id1  WHERE `turn_id`=:id";
$_cp_updated=nz_pdo_update($_cp_mysql,$_cp_data,$conn);


echo "</p>"; 

$_cp_sql = "UPDATE `g_turnsummary` SET `processed`= 1 WHERE `turn_id`=$_cp_turnid";
myDB::query("$_cp_sql");

} else {
	echo "<p>No Roundup turns found</p>";    
}




echo "<br>";

require_once 'g_footer.php';
?>
