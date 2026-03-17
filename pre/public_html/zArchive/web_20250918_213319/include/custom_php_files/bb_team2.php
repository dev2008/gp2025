<?php
//Retrieve Franchise details
$_cp_sql = "SELECT 	`f_league`, `f_conference`, `f_division`, `f_team`, `f_city`, `f_nickname`, `f_coach` 
			FROM 	`bb_franchises`
			WHERE 	`f_id`= $_cp_myfranchise";     

	$res = execute_db($_cp_sql, $conn);
        while($row = fetch_row_db($res)){
			$_cp_myleague = $row[0];
			$_cp_myconference = $row[1];
			$_cp_mydivision = $row[2];
			$_cp_myteam = $row[3];
			$_cp_mycoach = $row[6];
        } 

//Show Franchise details in a table
echo "<br />";
echo "<table class='w3-table w3-striped w3-bordered'>";
echo "<caption text-align: left;>$_cp_myleague $_cp_myteam (Coach:&nbsp;$_cp_mycoach)</caption>\n";
echo "<tr class='$mycolour6'>";
echo "<th>World Series Winners: </th>";
echo "<td></td>";
echo "</tr>";
echo "</table>";
echo "</p>";
echo "<hr />";

#Season by Season records
echo "<table class='w3-table w3-striped w3-bordered'>";
echo "<caption text-align: left;>Season by Season Records</caption>\n";
echo "<tr class='$mycolour7'><th>Season</th><th>Team</th><th>Coach</th><th>Stadium</th><th>Record</th><th>Runs F</th><th>Runs A</th></tr>";
$_cp_sql3 = "SELECT `fh_season`,`fh_team`, `fh_coach`, CONCAT(`fh_size`,', ' ,`fh_surface`,', ', `fh_type`) as `Stadium` FROM `bb_franchisehistory` WHERE `f_id`=$_cp_myfranchise ORDER BY `fh_season` DESC;";
$res3 = execute_db($_cp_sql3, $conn);
$i=1;
while($row3 = fetch_row_db($res3)){
		if (($i % 2) <> 1) {		
		echo "<tr class='$mycolour5'><td>$row3[0]</td><td>$row3[1]</td><td>$row3[2]</td><td>$row3[3]</td><td></td><td></td><td></td>";
		} else {
		echo "<tr class='$mycolour6'><td>$row3[0]</td><td>$row3[1]</td><td>$row3[2]</td><td>$row3[3]</td><td></td><td></td><td></td>";
		}
$i++;
}
echo "</table>";
echo "<br />";


?>
