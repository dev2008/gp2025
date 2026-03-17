<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';
#$array=get_defined_vars();
#nz_debug($array,"All variables:");
if ($_cp_processed<6) {
$str="<h2>Processing draft stage 2.</h3>";
output($str); 

			$_cp_sql3 = "SELECT `dp_id`, `dp_type`, `dp_hand`, `dp_pos`, `dp_level`, `dp_best`, `dp_pot`, `dp_val`, `dp_no` , `dp_skill1`, `dp_skill2`, `dp_skill3`, `dp_skill4`
						 FROM `bb_dplayers` 
						 WHERE `srate` IS NULL 
						 ORDER BY `dp_id` ASC ";
			#echo "$_cp_sql3";
			$result3 = $conn->prepare($_cp_sql3); 
			$result3->execute(); 
			$number_of_rows = number_format($result3->rowCount() ); 
			$str="<p>Found $number_of_rows draft records for $_cp_league Season $_cp_season</p>";
			output($str);
			while($row3 = fetch_row_db($result3)){
				$_cp_pid=$row3[0];
				$_cp_ptype=$row3[1];
				$_cp_phand=$row3[2];
				$_cp_ppos=$row3[3];
				$_cp_plevel=$row3[4];
				$_cp_pbest=$row3[5];
				$_cp_ppot=$row3[6];
				$_cp_pval=$row3[7];
				$_cp_pno=$row3[8];
				$_cp_skill1=$row3[9];
				$_cp_skill2=$row3[10];
				$_cp_skill3=$row3[11];
				$_cp_skill4=$row3[12];
				
				//Standard rating
				if ("Pit"==$_cp_ptype AND "L"==$_cp_phand) { 
					$_cp_srate=5*(($_cp_plevel-1)+($_cp_ppot/2));
					#$str="<p>#$_cp_pid dinged for being left handed pitcher ($_cp_ptype - $_cp_phand)</p>";
					#output($str);										
				} else {
					$_cp_srate=5*($_cp_plevel+($_cp_ppot/2));	
				}
				
				//Alan's rating
				$_cp_arate=draft_asm($_cp_pid,$_cp_plevel, $_cp_ppot,$_cp_phand,$_cp_ppos,$_cp_pbest,$_cp_pval);
				#echo "<p>$_cp_arate</p>";
				

	

$_cp_sql4 = "UPDATE `bb_dplayers` SET `srate`='$_cp_srate', `arate`='$_cp_arate' WHERE `dp_id`=$_cp_pid";
nz_pdo($_cp_sql4,$conn);
//End of update loop
}	

$str="<h2>Draft stage 2 processed without errors.</h3>";
output($str); 

} else {
	$str="<h2>Draft stage 2 already processed!!</h3>";
	output($str); 	
}
?>
