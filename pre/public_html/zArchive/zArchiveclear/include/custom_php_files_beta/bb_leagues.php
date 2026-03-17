<?php

// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');

//Set the League
if (isset($_POST['_cp_myleague']) && !empty($_POST['_cp_myleague'])) {
	$_cp_myleague =  $_POST['_cp_myleague'];
	#include_once('f_teamdetails.php');
} elseif (isset($_GET['_cp_myleague']) && !empty($_GET['_cp_myleague'])) { 
	$_cp_myleague =  $_GET['_cp_myleague'];
	#include_once('f_teamdetails.php');	
} else {
	$_cp_myleague = 'MLB8';
}

	echo "<br />\n";

#Show League Selector
	#Loop through all leagues and select
	$_cp_sql = "SELECT DISTINCT `league_franchise` FROM `bb_franchises` ORDER BY `league_franchise` ASC";        
	$res = execute_db($_cp_sql, $conn);
	//Start the form
	echo "<form action='index.php?function=show_static_page&id_static_page=28' method='post'>\n";	
	echo "<select name='_cp_myleague'  onchange='this.form.submit()'>\n";
	//Populate the drop down list	
        while($row = fetch_row_db($res)){
			echo "<option value='$row[0]'";
			$x = ($_cp_myleague == $row[0]) ? " selected ": "";
			echo "$x>$row[0]</option>\n";
        } 
	echo "</select>\n";	

	echo "</form>\n";	
	echo "</script>\n";
#	echo "<br />\n";

echo "<h1>League Summary - $_cp_myleague </h1>";

    
	//Display current World Series winners
    $sql = "
		SELECT a.`season_series`, b.`city_franchise`, b.`nickname_franchise`, a.`cwin_series`, b.`conference_franchise`
		FROM `bb_series` a
			INNER JOIN `bb_franchises` b ON a.`fwin_series`=b.id_franchise
		WHERE a.`league_series`=:league 
		ORDER BY a.`season_series` DESC 
		LIMIT 1";
	
	#echo "<p>$sql</p>";
	
    $res_prepare = prepare_db($conn, $sql);

    $res_bind = bind_param_db($res_prepare, ':league', $_cp_myleague);
    
    $res = execute_prepared_db($res_prepare,0);
    
    $row = fetch_row_db($res_prepare);

	echo "<p>The last completed Season in <u>$_cp_myleague</u> is <b>$row[0]</b> and the winners were the <b>$row[1] $row[2]</b> of the <b>$row[4]</b> coached by <b>$row[3]</b>.</p>";
	//End current World Series winners

//Picture - add league qualifier
$_cp_myimage=$_cp_myleague;
$_cp_myimage.='_champions.jpg';
#echo "<p>$_cp_myimage</p>";
echo "<p style='text-align: center;'><img src='images/$_cp_myimage' alt='' width='40%' height='40%' /></p>";
	
	//Display ALvsNL WS total
    $sql = "SELECT count(*) FROM `bb_series` WHERE `alwin_series`='*' AND `league_series`=:league";
	
    $res_prepare = prepare_db($conn, $sql);

    $res_bind = bind_param_db($res_prepare, ':league', $_cp_myleague);
    
    $res = execute_prepared_db($res_prepare,0);
    
    $row = fetch_row_db($res_prepare);

	$_cp_alw=$row[0];
	
    $sql = "SELECT count(*) FROM `bb_series` WHERE `nlwin_series`='*' AND `league_series`=:league";
	
    $res_prepare = prepare_db($conn, $sql);

    $res_bind = bind_param_db($res_prepare, ':league', $_cp_myleague);
    
    $res = execute_prepared_db($res_prepare,0);
    
    $row = fetch_row_db($res_prepare);

	$_cp_nlw=$row[0];
	
	$_cp_diff=$_cp_nlw-$_cp_alw;
	#echo "<p>$_cp_diff</p>";
	
	if ($_cp_diff >0){
		#Link needs to be to generic WS table
			echo "<p>The <b>NL</b> leads <a href='index.php?function=search&tablename=bb_series'>World Series History</a> with <b>$_cp_nlw</b> victories to $_cp_alw for the AL.</p>"; 
		} else if ($_cp_diff <0){
			echo "<p>The <b>AL</b> leads <a href='index.php?function=search&tablename=bb_series'>World Series History</a> with <b>$_cp_alw</b> victories to $_cp_nlw for the NL.</p>"; 	
		} else if ($_cp_diff ==0){
			echo "<p>The AL and NL are tied in <a href='index.php?function=search&tablename=bb_series'>World Series </a> victories at $_cp_nlw each.</p>"; 
		}					

	//End ALvsNL WS total

	//Display ALvsNL ASG total
    $sql = "SELECT count(*) FROM `bb_asgames` WHERE `alwin_asgame`='*' AND `league_asgame`=:league";
	
    $res_prepare = prepare_db($conn, $sql);

    $res_bind = bind_param_db($res_prepare, ':league', $_cp_myleague);
    
    $res = execute_prepared_db($res_prepare,0);
    
    $row = fetch_row_db($res_prepare);

	$_cp_alw=$row[0];
	
    $sql = "SELECT count(*) FROM `bb_asgames` WHERE `nlwin_asgame`='*' AND `league_asgame`=:league";
	
    $res_prepare = prepare_db($conn, $sql);

    $res_bind = bind_param_db($res_prepare, ':league', $_cp_myleague);
    
    $res = execute_prepared_db($res_prepare,0);
    
    $row = fetch_row_db($res_prepare);

	$_cp_nlw=$row[0];
	
	$_cp_diff=$_cp_nlw-$_cp_alw;
	#echo "<p>$_cp_diff</p>";
	
	if ($_cp_diff >0){
			echo "<p>The <b>NL</b> leads <a href='index.php?function=search&tablename=v_MLB8_ASG'>All Star Game History</a> with <b>$_cp_nlw</b> victories to $_cp_alw for the AL.</p>"; 
		} else if ($_cp_diff <0){
			echo "<p>The <b>AL</b> leads <a href='index.php?function=search&tablename=v_MLB8_ASG'>All Star Game History</a> with <b>$_cp_alw</b> victories to $_cp_nlw for the NL.</p>"; 	
		} else if ($_cp_diff ==0){
			echo "<p>The AL and NL are tied in <a href='index.php?function=search&tablename=v_MLB8_ASG'>All Star Game </a> victories at $_cp_nlw each.</p>"; 
		}					

	//End ALvsNL ASG total


//Move this to table
//Perfect games
echo "<p>There have been <b>8</b> <a href='index.php?function=search&tablename=bb_perfect'>Perfect Games</a> in $_cp_myleague history, 1 in the Play-offs (Josh Hancock for the Blue Jays in Season 5), 6 in the regular season and 1 in a Consolation series. The <em>Rockies</em> and <em>Marlins</em> have both thrown a Perfect Game and had one thrown against them. The <em>Braves</em> are the only Club to have had multiple Pitchers throw Perfect Games (John Burkett in Season 9 and Randall Delgado in Season 17).</p>";


//Picture
echo "<p style='text-align: center;'><img src='images/blue-jays-celebrate.jpg' alt='' width='40%' height='40%' /></p>";


?>
