<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
require_once 'bb_functions.php';
require_once 'g_functions.php';
require_once 'mydatabase.php';

$str="<h3>Processing $_cp_league Season $_cp_season Week $_cp_week at stage $_cp_processed</h3>";
output($str); 

if (19==$_cp_week){
	$_cp_dround=1; 
} elseif (20==$_cp_week) {
	$_cp_dround=2;  
} elseif (21==$_cp_week) {
	$_cp_dround=3;  
} else 	{
	$_cp_dround=0;  
}	

#Roundup processing	
#Stats processing		
#Stage 2
if ("Y"==$_cp_roundup) {
				$str="<p>$_cp_league Season $_cp_season Week $_cp_week is a roundup turn.&nbsp;&nbsp;&nbsp;&nbsp;";
				output($str); 
				#p2
				#require_once 'g_process_baseballfranchises.php';
				#p3
				#require_once 'g_process_baseballstatsb.php';
				#p4
				#require_once 'g_process_baseballstatsp.php';
}		

#Standings processing
#Stage 3

#Draft processing
switch ($_cp_week) {
					case "15":
						$str="<p>$_cp_league Season $_cp_season Week $_cp_week is a draft turn.</p>";
						output($str); 
						#p5
						require 'g_process_baseballdraft15a.php';
						#p6
						require 'g_process_baseballdraft15b.php';
					break;
					case "18":
						//Draft order released
						$str="<h3>$_cp_league Season $_cp_season Week $_cp_week is a draft turn.</h3>";
						output($str);
						#p7 
						require 'g_process_baseballdraft18.php';
					break;
					case "19":
					  //Draft round 1
						$str="<h3>$_cp_league Season $_cp_season Week $_cp_week is a draft turn.</h3>";
						output($str); 
						#p7
						require 'g_process_baseballdraft192021.php';
					break;
					case "20":
						//Draft round 2
						$str="<h3>$_cp_league Season $_cp_season Week $_cp_week is a draft turn.</h3>";
						output($str);
						#p8 
						require 'g_process_baseballdraft192021.php';
					break;
					case "21":
						//Draft round 3
						$str="<p>$_cp_league Season $_cp_season Week $_cp_week is a draft and roundup turn.</p>";
						#p9
						require 'g_process_baseballdraft192021.php';
						#p10
						#require_once 'g_process_baseballfranchises.php';
						#p11
						#require 'g_process_baseballrosters.php';
						output($str); 
					break;																			
					default:
					  //Nothing to do
				}

#My Roster Processing 
#p14
#require 'g_process_baseballmyteam.php';

#Special Actions Processing 
#p13
require_once 'g_process_baseballactions.php';



