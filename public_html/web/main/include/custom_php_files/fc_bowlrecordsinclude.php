<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
include_once('error_handler.php');

//start of content
echo "<br />";
echo "<div class='w3-col' style='width:5%'><p></p></div>\n";
echo "<div class='w3-col w3-panel $mycolour3 w3-card-4 w3-round-xxlarge w3-centred'  style='width:90%;'>\n";
echo "<br />";
				//Title
				echo "<div class='w3-panel w3-blue'>";
				echo "<h1 class='w3-text-orange' style='text-shadow:1px 1px 0 #444'>";
				echo "<b>All Bowl Game Records</b></h1>";
				echo "</div>";

echo "<table class='w3-table w3-striped w3-bordered w3-mobile'>\n";

//PDO Example with row count
$_cp_sql = "SELECT `franchise`,`team` FROM `fc_franchises` WHERE `league`='$_cp_myleague'";
$result = $conn->prepare($_cp_sql); 
$result->execute(); 
#$number_of_rows = $result->rowCount() ; 
#echo "<p>Rows - $number_of_rows</p>";
$mycell = "";
$mycell1 = "";
$mycell2 = "";
$mycell3 = "";
$mycell4 = "";
$mycell5 = "";
//Loop through results
while($row = fetch_row_db($result)){
	$_cp_myfranchise = $row[0];
	$_cp_myteam = $row[1];
		//Start of Loop for specific Bowl Game
		$_cp_sql = "SELECT IFNULL (SUM(`win`),0),IFNULL (SUM(`lose`),0) 
					FROM `f_games` 
					WHERE `franchise`=$_cp_myfranchise AND `league`='$_cp_myleague' AND `gametype`=8";
		$result2 = $conn->prepare($_cp_sql); 
		$result2->execute(); 

		//Loop through results
		while($row = fetch_row_db($result2)){
			$_cp_mywins = $row[0];
			$_cp_mylosses = $row[1];
			$mycell.="$_cp_myteam $_cp_mywins-$_cp_mylosses, ";
		   }
		//End of Loop for specific Bowl Game
		//Start of Loop for specific Bowl Game
		$_cp_sql = "SELECT IFNULL (SUM(`win`),0),IFNULL (SUM(`lose`),0) 
					FROM `f_games` 
					WHERE `franchise`=$_cp_myfranchise AND `league`='$_cp_myleague' AND `gametype`=9";
		$result2 = $conn->prepare($_cp_sql); 
		$result2->execute(); 

		//Loop through results
		while($row = fetch_row_db($result2)){
			$_cp_mywins = $row[0];
			$_cp_mylosses = $row[1];
			$mycell1.="$_cp_myteam $_cp_mywins-$_cp_mylosses, ";
		   }
		//End of Loop for specific Bowl Game
		//Start of Loop for specific Bowl Game
		$_cp_sql = "SELECT IFNULL (SUM(`win`),0),IFNULL (SUM(`lose`),0) 
					FROM `f_games` 
					WHERE `franchise`=$_cp_myfranchise AND `league`='$_cp_myleague' AND `gametype`=10";
		$result2 = $conn->prepare($_cp_sql); 
		$result2->execute(); 

		//Loop through results
		while($row = fetch_row_db($result2)){
			$_cp_mywins = $row[0];
			$_cp_mylosses = $row[1];
			$mycell2.="$_cp_myteam $_cp_mywins-$_cp_mylosses, ";
		   }
		//End of Loop for specific Bowl Game
		//Start of Loop for specific Bowl Game
		$_cp_sql = "SELECT IFNULL (SUM(`win`),0),IFNULL (SUM(`lose`),0) 
					FROM `f_games` 
					WHERE `franchise`=$_cp_myfranchise AND `league`='$_cp_myleague' AND `gametype`=11";
		$result2 = $conn->prepare($_cp_sql); 
		$result2->execute(); 

		//Loop through results
		while($row = fetch_row_db($result2)){
			$_cp_mywins = $row[0];
			$_cp_mylosses = $row[1];
			$mycell3.="$_cp_myteam $_cp_mywins-$_cp_mylosses, ";
		   }
		//End of Loop for specific Bowl Game		
		//Start of Loop for specific Bowl Game
		$_cp_sql = "SELECT IFNULL (SUM(`win`),0),IFNULL (SUM(`lose`),0) 
					FROM `f_games` 
					WHERE `franchise`=$_cp_myfranchise AND `league`='$_cp_myleague' AND `gametype`=12";
		$result2 = $conn->prepare($_cp_sql); 
		$result2->execute(); 

		//Loop through results
		while($row = fetch_row_db($result2)){
			$_cp_mywins = $row[0];
			$_cp_mylosses = $row[1];
			$mycell4.="$_cp_myteam $_cp_mywins-$_cp_mylosses, ";
		   }
		//End of Loop for specific Bowl Game		
		//Start of Loop for specific Bowl Game
		$_cp_sql = "SELECT IFNULL (SUM(`win`),0),IFNULL (SUM(`lose`),0) 
					FROM `f_games` 
					WHERE `franchise`=$_cp_myfranchise AND `league`='$_cp_myleague' AND `gametype`=13";
		$result2 = $conn->prepare($_cp_sql); 
		$result2->execute(); 

		//Loop through results
		while($row = fetch_row_db($result2)){
			$_cp_mywins = $row[0];
			$_cp_mylosses = $row[1];
			$mycell5.="$_cp_myteam $_cp_mywins-$_cp_mylosses, ";
		   }
		//End of Loop for specific Bowl Game		
   }     
echo "<tr class='$mycolour6'>";
echo "<th>School records in <a href='index.php?function=show_static_page&id_static_page=8'>National Championship</a> games: </th>";
echo "<td>";
$mycell=substr($mycell,0,-2);
echo "$mycell</td>";
echo "</tr>";     
echo "<tr class='$mycolour5'>";
echo "<th>School records in <a href='index.php?function=show_static_page&id_static_page=12'>Rose Bowl</a> games: </th>";
echo "<td>";
$mycell=substr($mycell1,0,-2);
echo "$mycell</td>";
echo "</tr>";     
echo "<tr class='$mycolour6'>";
echo "<th>School records in <a href='index.php?function=show_static_page&id_static_page=13'>Cotton Bowl</a> games: </th>";
echo "<td>";
$mycell=substr($mycell2,0,-2);
echo "$mycell</td>";
echo "</tr>";     
echo "<tr class='$mycolour5'>";
echo "<th>School records in <a href='index.php?function=show_static_page&id_static_page=14'>Orange Bowl</a> games: </th>";
echo "<td>";
$mycell=substr($mycell3,0,-2);
echo "$mycell</td>";
echo "</tr>";     
echo "<tr class='$mycolour6'>";
echo "<th>School records in <a href='index.php?function=show_static_page&id_static_page=15'>Hawaii Bowl</a> games: </th>";
echo "<td>";
$mycell=substr($mycell4,0,-2);
echo "$mycell</td>";
echo "</tr>";     
echo "<tr class='$mycolour5'>";
echo "<th>School records in <a href='index.php?function=show_static_page&id_static_page=16'>Motor City Bowl</a> games: </th>";
echo "<td>";
$mycell=substr($mycell5,0,-2);
echo "$mycell</td>";
echo "</tr>";     
//End of Table
echo "</table>";
echo "<br /><br />";

//Title
echo "<div class='w3-panel w3-blue'>";
echo "<h1 class='w3-text-orange' style='text-shadow:1px 1px 0 #444'>";
echo "<b>Records in any Bowl Game</b></h1>";
echo "</div>";

echo "<table class='w3-table w3-striped w3-bordered w3-mobile'>\n";

//Start of overall Bowl Records
//Most points
$_cp_sql = "SELECT a.`season`, b.`team`, a.`score`,a.`opp_score`,c.`team`, d.`gametype` 
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise` = b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise` = c.`franchise`
    INNER JOIN `f_gametypes` d ON a.`gametype` = d.`id`
WHERE a.`gametype` BETWEEN 8 AND 13
ORDER BY `score` DESC 
LIMIT 5";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[1]  $row[2]</span> - $row[3] $row[4] <em>($row[0] $row[5]</em>)<br />";
			$mynone++;
        }          
echo "<tr class='$mycolour6'>";
echo "<th>Most points scored:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";

//Biggest Margin
$_cp_sql = "SELECT a.`season`, b.`team`, a.`score`,a.`opp_score`,c.`team`, (a.`score`-a.`opp_score`) AS `margin`, d.`gametype`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise` = b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise` = c.`franchise`
    INNER JOIN `f_gametypes` d ON a.`gametype` = d.`id`
WHERE a.`gametype` BETWEEN 8 AND 13
ORDER BY `margin` DESC 
LIMIT 5";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[5] : $row[1]  $row[2]</span> - $row[3] $row[4] <em>($row[0]  $row[6]</em>)<br />";
			$mynone++;
        }          
echo "<tr class='$mycolour5'>";
echo "<th>Biggest margin of victory:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";

//Smallest Margin
$_cp_sql = "SELECT a.`season`, b.`team`, a.`score`,a.`opp_score`,c.`team`, (a.`score`-a.`opp_score`) AS `margin`, d.`gametype`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise` = b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise` = c.`franchise`
    INNER JOIN `f_gametypes` d ON a.`gametype` = d.`id`
WHERE a.`gametype` BETWEEN 8 AND 13 and `win`=1
ORDER BY `margin` ASC  
LIMIT 5";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[5] : $row[1]  $row[2]</span> - $row[3] $row[4] <em>($row[0] $row[6]</em>)<br />";
			$mynone++;
        }          
echo "<tr class='$mycolour6'>";
echo "<th>Smallest margin of victory:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";

//Total Points
$_cp_sql = "SELECT a.`season`, b.`team`, a.`score`,a.`opp_score`,c.`team`, (a.`score`+a.`opp_score`) AS `totalpnts`, d.`gametype`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise` = b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise` = c.`franchise`
    INNER JOIN `f_gametypes` d ON a.`gametype` = d.`id`    
WHERE a.`gametype` BETWEEN 8 AND 13 AND `win`=1
ORDER BY `totalpnts` DESC  
LIMIT 5";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[5] : $row[1]  $row[2]</span> - $row[3] $row[4] <em>($row[0] $row[6]</em>)<br />";
			$mynone++;
        }          
echo "<tr class='$mycolour5'>";
echo "<th>Most total points:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";

//Total Points
$_cp_sql = "SELECT a.`season`, b.`team`, a.`score`,a.`opp_score`,c.`team`, d.`gametype`
FROM `f_games` a
	INNER JOIN `fc_franchises` b ON a.`franchise` = b.`franchise`
    INNER JOIN `fc_franchises` c ON a.`opp_franchise` = c.`franchise`
    INNER JOIN `f_gametypes` d ON a.`gametype` = d.`id`    
WHERE a.`gametype` BETWEEN 8 AND 13 AND `win`=0
ORDER BY a.`score` DESC  
LIMIT 5";
#echo "<p>$_cp_sql</p>";
$myrow = "";
$mynone=0;
$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$myrow .= "<span style=\"font-weight: 900\">$row[1] $row[2]  </span> - $row[3] $row[4] <em>($row[0] $row[5]</em>)<br />";
			$mynone++;
        }          
echo "<tr class='$mycolour6'>";
echo "<th>Most points in defeat:</th>";
echo "<td>";
//$myrow=substr($myrow,0,-2);
echo "$myrow</td>";
echo "</tr>";
 



//End of Table
echo "</table>";
echo "<br />";
echo "<br />\n";

#Close box - 2
echo "<br />";
echo "</div>";
echo "</div>";
?>
