<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');

echo "<h1>Gameplan College Football Rivalries Page</h1>";



if (isset($_POST['_cp_mychoice']) && !empty($_POST['_cp_mychoice'])) {
	$_cp_myleague =  substr($_POST['_cp_mychoice'],0,5);
	$_cp_myseason =  substr($_POST['_cp_mychoice'],-4);
	$_cp_myselected = $_POST['_cp_mychoice'];
} elseif (isset($_GET['_cp_mychoice']) && !empty($_GET['_cp_mychoice'])) { 
	$_cp_myleague =  substr($_GET['_cp_mychoice'],0,5);
	$_cp_myseason =  substr($_GET['_cp_mychoice'],-4);
	$_cp_myselected = $_GET['_cp_mychoice'];
} else {
	$_cp_myleague = 'NCAA5';
	//Select maximum season for this league
	//PDO Example with row count
	$_cp_sql = "SELECT MAX(`season`) FROM `f_games` WHERE `League` ='$_cp_myleague'  ";
	$result = $conn->prepare($_cp_sql); 
	$result->execute(); 
	//Loop through results
	while($row = fetch_row_db($result)){
		$_cp_myseason =  $row[0];
	   }  
	$_cp_myselected = ' - ';
}


#Show League Selector
	#Loop through all leagues and select
	$_cp_sql = "SELECT DISTINCT `league`, `season` FROM `fc_confgames` WHERE `league` LIKE '$_cp_myleague' ORDER BY `league` ASC, `season`  DESC";        
	$res = execute_db($_cp_sql, $conn);
	//Start the form
	echo "<form action='index.php?function=show_static_page&id_static_page=30' method='post'>\n";	
	echo "<select name='_cp_mychoice'  onchange='this.form.submit()'>\n";
	//Populate the drop down list	
        while($row = fetch_row_db($res)){
			echo "<option value='$row[0]-$row[1]'";
			$_cp_mychoice = $row[0];
			$_cp_mychoice .= '-';
			$_cp_mychoice .= $row[1];
			$x = ($_cp_mychoice == $_cp_myselected) ? " selected ": "";
			echo "$x>$row[0] - $row[1]</option>\n";
			#echo "$_cp_mychoice / $_cp_myselected";
        } 
	echo "</select>\n";	
	echo "</form>\n";	
	echo "<br />\n";


$_cp_sql12 = "SELECT DISTINCT `id`,`Name` FROM fc_rivalries WHERE `league` LIKE '$_cp_myleague' ORDER BY `id` ASC";
	#echo "<p>$_cp_sql2</p>";
	$result12 = $conn->prepare($_cp_sql12); 
	$result12->execute(); 
	//Loop through results
	while($row12 = fetch_row_db($result12)){
		$_cp_myrivalryid =  $row12[0];
		$_cp_myrivalryname =  $row12[1];
		echo "<h1 class='asm2' >$_cp_myrivalryname</h1>\n";
		$_cp_mylogo="images\\".$_cp_myrivalryid."_logo.jpg";
		echo '<img src="' . $_cp_mylogo . '" alt="Rivalry logo" >';

		#Show all time totals
		echo "<p><em>All time wins:- ";
		$_cp_sql22="SELECT `team`, `RivalryWins` FROM `fc_franchises` WHERE `Rivalry`= $_cp_myrivalryid AND `league` LIKE '$_cp_myleague' ORDER BY `RivalryWins` DESC";
		$result22 = $conn->prepare($_cp_sql22); 
		$result22->execute();
		$i=0;
		while($row22 = fetch_row_db($result22)){
		if ($i<1){
		echo "$row22[0] ($row22[1]), ";
		} else {
		echo "$row22[0] ($row22[1])\n";	
		}
		$i++;
		}
		echo "</em></p>";
		#Now show this years results
		$_cp_sql32="SELECT b.`team`, a.`wins`, a.`losses`, a.`ties`, a.`pfor`, a.`pagn`, a.`pts`, a.`diff`, c.`team` 
					FROM `fc_rivalrygames` a
						INNER JOIN `fc_franchises` b ON a.`franchise`=b.`franchise`
						INNER JOIN `fc_franchises` c ON a.`opp_franchise`=c.`franchise`
					WHERE a.`league`='$_cp_myleague' AND a.`season`=$_cp_myseason AND a.`Rivalry`=$_cp_myrivalryid 
					ORDER BY a.`pts` DESC, a.`diff` DESC, a.`pfor` DESC
					LIMIT 1";
		#echo "<p>$_cp_sql32</p>";			
		$result32 = $conn->prepare($_cp_sql32); 
		$result32->execute();
		while($row32 = fetch_row_db($result32)){
			echo "<p>$row32[0] $row32[4] - $row32[8] $row32[5]</p>";
	   } 
	   echo "<br />"; 
}
		

?>
