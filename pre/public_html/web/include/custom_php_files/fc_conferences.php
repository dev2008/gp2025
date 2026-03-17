<?php
//Version 11.4.2 
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
$time_start = microtime(true);
require_once 'g_functions.php';
$_cp_leaguetype="NCAA%";

//Create basic layout
$str="";
$str.= "<div class='w3-panel w3-theme-d5 w3-text-white w3-round-xxlarge '>";
$str.="<h1>";
$str.="<img  class='w3-right-align' src=\"images\NCAA_football.png\" alt=\"NCAA logo\" width='160' height='95' >";
$str.="&nbsp&nbsp&nbsp&nbsp&nbsp&nbspGameplan College Football Conference Page</h1>";
$str.= "<div class='w3-panel w3-theme w3-round-large '>";
output($str);

#Main content
$str="";
$str="<br />";
//Check for League choice 
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
	$_cp_sql = "SELECT MAX(`season`) FROM `f_games` WHERE `League` ='$_cp_myleague'  ";
	$result = $conn->prepare($_cp_sql); 
	$result->execute(); 
	//Loop through results
	while($row = fetch_row_db($result)){
		$_cp_myseason =  $row[0];
	   }  
	$_cp_myselected = ' - ';
}
//League selector
	#Loop through all leagues and select
	$_cp_sql = "SELECT DISTINCT `league`, `season` FROM `fc_confgames` WHERE `league` LIKE '$_cp_myleague' ORDER BY `league` ASC, `season`  DESC";        
	$res = execute_db($_cp_sql, $conn);
	$str.="<form action='index.php?function=show_static_page&id_static_page=17' method='post'>\n";	
	$str.="<select name='_cp_mychoice'  onchange='this.form.submit()'>\n";
        while($row = fetch_row_db($res)){
			$str.="<option value='$row[0]-$row[1]'";
			$_cp_mychoice = $row[0];
			$_cp_mychoice .= '-';
			$_cp_mychoice .= $row[1];
			$x = ($_cp_mychoice == $_cp_myselected) ? " selected ": "";
			$str.="$x>$row[0] - $row[1]</option>\n";
        } 
	$str.="</select>\n";	
	$str.="</form>\n";	
	$str.="</script>\n";
	$str.="<br />\n";
	$str.="<hr />\n";

	//Select Conferences
	$_cp_sql = "SELECT `conf_id`, `conf_name`, `conf_logo`, `conf_alt` FROM `fc_conferences` WHERE `conf_league`='$_cp_myleague' ORDER BY `conf_name`";
	$result = $conn->prepare($_cp_sql); 
	$result->execute(); 
	$number_of_rows = $result->rowCount() ; 
	$i=1;
	//Loop through Conferences
	while($row = fetch_row_db($result)){
			$_cp_myconf = $row[1];
			$_cp_myconflogo = $row[2];
			$_cp_myconfalt = $row[2];
			$str.="<p><img src='images\\$_cp_myconflogo' alt='$_cp_myconfalt' height='86' width='80'></p>\n";
			$str.="<b>$_cp_myconf Conference</b></h1>";
			//Start table
			$str.="<table style='width:40%' class='w3-table w3-striped w3-bordered w3-mobile'>\n";
			$str.="<tr class='w3-theme-d5'><th>School&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</th><th>Record</th><th class='w3-right-align'>For</th><th class='w3-right-align'>Against</th></th>\n";
			$_cp_sql2 = "SELECT a.`myconf`, b.`team`, a.`wins`, a.`losses`, a.`ties`, a.`pfor`, a.`pagn`, a.`pts`, a.`diff`, 
                    (a.`wins` + a.`ties` * 0.5) AS total_wins
             FROM `fc_confgames` a
             INNER JOIN `fc_franchises` b ON a.`franchise` = b.`franchise`
             WHERE a.`league` = '$_cp_myleague' 
               AND a.`season` = $_cp_myseason 
               AND a.`myconf` = '$_cp_myconf'
             GROUP BY a.`myconf`, a.`franchise`, b.`team`, a.`wins`, a.`losses`, a.`ties`, a.`pfor`, a.`pagn`, a.`pts`, a.`diff`
             ORDER BY total_wins DESC, a.`ties` DESC, a.`diff` DESC, a.`pfor` DESC";

			$result2 = $conn->prepare($_cp_sql2); 
			$result2->execute(); 

			// Loop through Conference Standings using standard PDO fetch method
			$i = 0; // Initialize counter if not set before
			while ($row2 = $result2->fetch(PDO::FETCH_NUM)) {
				// Check for any ties, then create record
				if (0 == $row2[4]) {  // If there are no ties
					$_cp_myrecord = "$row2[2] - $row2[3]"; // Wins - Losses
				} else {  // If there are ties
					$_cp_myrecord = "$row2[2] - $row2[3] ($row2[4]t)"; // Wins - Losses (Ties)
				}

				// Alternate row color based on even or odd index
				$row_class = ($i % 2 == 1) ? $gpcolour5 : $gpcolour6;

				// Output the row with data
				$str.= "<tr class='$row_class w3-right-align'>
						<td>{$row2[1]}</td>  <!-- Team name -->
						<td>$_cp_myrecord</td>  <!-- Wins - Losses (Ties) -->
						<td class='w3-right-align'>{$row2[5]}</td>  <!-- Points For -->
						<td class='w3-right-align'>{$row2[6]}</td>  <!-- Points Against -->
					  </tr>";

				$i++; // Increment the counter
			}

//End of loop through Conferences
$str.= "</table>\n";

					//All time Conference records
					#$str.= "<br />\n";
					$str.="<h3>All time wins:-</h3>";
					$str.="<p><em>";
					$_cp_sql22="SELECT `team`, `ConfWins` FROM `fc_franchises` WHERE `conference` = '$_cp_myconf' AND `league` LIKE '$_cp_myleague' ORDER BY `ConfWins` DESC";
					#$str.="<p>$_cp_sql22</p>";
					$result22 = $conn->prepare($_cp_sql22); 
					$result22->execute();
					$j=0;
					while($row22 = fetch_row_db($result22)){
					if ($j<3){
					$str.="$row22[0] ($row22[1]), ";
					} else {
					$str.="$row22[0] ($row22[1])";	
					}
					$j++;
					}
					$str.="</em></p>";	
					$str.="<br />\n";
					$str.="<hr />\n";
}


#End of main content
output($str);



//End of page
$str="</div>";
output($str);

//Start of footer
require_once 'g_footer.php';
?>
