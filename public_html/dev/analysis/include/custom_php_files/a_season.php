<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once('error_handler.php');
require_once('g_functions.php');

#echo '<pre>'; var_dump($_POST, $_GET); echo '</pre>';



if (isset($_POST['_cp_mychoice']) && !empty($_POST['_cp_mychoice'])) {
	$_cp_myoldchoice =  $_POST['_cp_mychoice'];
	$_cp_myarray=(explode("~",$_cp_myoldchoice));
	$_cp_myoldleague=$_cp_myarray[1];
	$_cp_myoldteam=$_cp_myarray[2];
	$_cp_myoldseason=$_cp_myarray[3];
	$_cp_myoldabbr=$_cp_myarray[4];
	$_cp_myformation=$_POST['formation'];
	$_cp_mysideofball=$_POST['sideofball'];

} else {
	$_cp_myoldchoice='';
	$_cp_myoldleague='NFLAR';
	$_cp_myoldteam='';
	$_cp_myoldseason='2027';
	$_cp_myformation='No';
	$_cp_mysideofball='Offence';
	$_cp_myoldabbr='MV';
}

echo "<h1>Plays	 by Season</h1>";

#Drop down list for League and Team and Season
#Use current teams table
#Selector for include Formation or not
	#Select active teams
	$_cp_sql2 = "SELECT `o_team` AS 'myabbr',CONCAT('~',`o_league`,'~', `o_longname`) AS 'myteam', `o_league` as 'myleague' FROM `n_off-season` WHERE 1 ORDER BY `o_league` DESC, `o_team` ASC";  
	//Start the form
	echo "<form action='index.php?function=show_static_page&id_static_page=4' method='post'>\n";	
	echo "<fieldset>";
	echo "<select name='_cp_mychoice'>\n";
    
	$res2 = execute_db($_cp_sql2, $conn);
        while($row2 = fetch_row_db($res2)){
			$_cp_myabbr = $row2[0];
			$_cp_myteam = $row2[1];
			$_cp_myleague = $row2[2];
			#Now find relevant seasons for these teams 
		$_cp_sql = "SELECT DISTINCT `a_season` FROM `n_playbyplay` WHERE `a_off`='$_cp_myabbr' AND `a_league`='$_cp_myleague' ORDER BY `a_season` DESC LIMIT 2";
		$res = execute_db($_cp_sql, $conn);	
		echo "$_cp_sql";
		//Populate the drop down list	
			while($row = fetch_row_db($res)){
				$_cp_myseason = $row[0];
				$_cp_mychoice = $_cp_myteam;
				$_cp_mychoice.= '~';
				$_cp_mychoice.= $_cp_myseason;
				$_cp_mychoice.= '~';
				$_cp_mychoice.= $_cp_myabbr;
				$_cp_mychoicename=str_replace("~"," ","$_cp_mychoice");
				$_cp_mychoicename=substr($_cp_mychoicename, 0, -3);

				if ($_cp_mychoice==$_cp_myoldchoice){
					echo "<option value='$_cp_mychoice' selected >$_cp_mychoicename</option>\n";
				} else {
					echo "<option value='$_cp_mychoice' >$_cp_mychoicename</option>\n";

				}

			} 

		
		}
		echo "</select>\n";	    
				
	
	echo "<p>Include Formations?\n<br />";
	if('Yes'==$_cp_myformation) {	
	echo "<input type='radio' name='formation' value='Yes' checked>Yes";
	echo "<input type='radio' name='formation' value='No'>No";
		} else {
		echo "<input type='radio' name='formation' value='Yes'>Yes";
		echo "<input type='radio' name='formation' value='No' checked>No";
	}

	
	echo "</p>";
	echo "<p>";	
	echo "<label for='sideofball'>Offence or Defence?</label>";
	echo "<select id='sideofball' name='sideofball'>";
	if('Offence'==$_cp_mysideofball) {
		echo "<option value='Offence' selected>Offence</option>";
		echo "<option value='Defence'>Defence</option>";
	} else {
		echo "<option value='Offence'>Offence</option>";
		echo "<option value='Defence' selected>Defence</option>";

	} 	
	echo "</select>";
	echo "</p>";
	echo "<input type='submit' />";
	echo "</form>\n";	
	echo "</fieldset>";
	echo "<br />\n";




#Show table with Formation, Play call, Number, Avge, Std Dev, PenO, PenD, Turnovers
#PenO, PenD, Turnovers excluded from average + Std Dev
if (strlen($_cp_myoldleague)>0){				
    
#Build the query based on user choices
$a= "SELECT ";
#Formation check
if('Yes'==$_cp_myformation) {
$a.= " `a_form` as 'Formation', ";
}
#Off v Def check
if('Offence'==$_cp_mysideofball) {
$a.= " `a_ocall` as 'Play' ,COUNT(`a_ocall`) as 'Number', ";
} else {
$a.= " `a_dcall` as 'Play' ,COUNT(`a_dcall`) as 'Number', ";
}
#Average yards
$a.= " ROUND(AVG(`a_yards`),2) as 'Average' ";

#FROM
$b= "FROM `n_playbyplay` ";
#WHERE
if('Offence'==$_cp_mysideofball) {
$c= "WHERE `a_league`=? AND `a_off`=? AND `a_season`=? AND `a_form` NOT IN ('P','X','F') AND `a_penalty`<>'1' AND `a_intercept`<>1 AND `a_fumble`<>1 ";
} else {
$c= "WHERE `a_league`=? AND `a_def`=? AND `a_season`=? AND `a_form` NOT IN ('P','X','F') AND `a_penalty`<>'1' AND `a_intercept`<>1 AND `a_fumble`<>1 ";	
}
#GROUP BY
$d='GROUP BY  ';
if('Yes'==$_cp_myformation) {
$d.= " `a_form`, "; 
}
if('Offence'==$_cp_mysideofball) {
$d.= " `a_ocall` "; 
} else {
$d.= " `a_dcall` "; 
}

#ORDER BY
if('Offence'==$_cp_mysideofball) {
$e= "ORDER BY AVG(`a_yards`) DESC";
} else {
$e= "ORDER BY AVG(`a_yards`) ASC";	
}

$sql3=$a.$b.$c.$d.$e; 
echo "<p>$sql3</p>";

$stmt = $conn->prepare("$sql3");
$stmt->execute([$_cp_myoldleague,$_cp_myoldabbr,$_cp_myoldseason]); 
$res3 = $stmt->fetchAll(PDO::FETCH_ASSOC);

#Need to add Penalties, Turnovers and exclude from totals



$_cp_caption="<caption><span style=\"font-weight: 900\">$_cp_myoldleague $_cp_myoldteam s$_cp_myoldseason $_cp_mysideofball (Formation: $_cp_myformation)</span></caption>";

echo array_to_table($res3,$_cp_caption);


}



?>
