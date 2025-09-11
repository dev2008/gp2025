<?php

/*
 * Function to calculate the ASM rating of a draftee.
 */
function ih_prateg($_cp_plevel, $_cp_exp, $_cp_ppot)
	{
		if ('R'==$_cp_exp){
			$_cp_exp=0;
		}
		
		$_cp_plevelp=$_cp_plevel+round(($_cp_ppot/3));
		
		switch ($_cp_plevelp) {
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
							$_cdp_levelboost=0;
						break;
			case 6:
							$_cdp_levelboost=2;
			break;
			case 7:
							$_cdp_levelboost=7;
			break;
			case 8:
							$_cdp_levelboost=12;
			break;
			case 9:
							$_cdp_levelboost=15;
			break;
			case 10:
							$_cdp_levelboost=18;
			break;
			case 11:
							$_cdp_levelboost=22;
			break;
			default:
							$_cdp_levelboost=25;
					}
					
					
				
					$_cp_prateg=(($_cdp_levelboost*3)+($_cp_exp*2));

					return $_cp_prateg;
	}

	function ih_prate($_cp_plevel, $_cp_exp, $_cp_ppot, $_cp_pagg)
	{
		if ('R'==$_cp_exp){
			$_cp_exp=0;
		}
		
		#echo "<p>Experience is $_cp_exp</p>";

		$_cp_plevelp=$_cp_plevel+round(($_cp_ppot/3));
		
		switch ($_cp_plevelp) {
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
				$_cdp_levelboost=0;
			break;
			case 6:
				$_cdp_levelboost=2;
			break;
			case 7:
				$_cdp_levelboost=7;
			break;
			case 8:
				$_cdp_levelboost=12;
			break;
			case 9:
				$_cdp_levelboost=15;
			break;
			case 10:
				$_cdp_levelboost=18;
			break;
			case 11:
				$_cdp_levelboost=22;
			break;
			default:
				$_cdp_levelboost=25;
			}
					
			$_cp_prate=(($_cdp_levelboost*2.5)+($_cp_exp*1.75)+($_cp_pagg*2));

			return $_cp_prate;
	}

	function ih_arateg($_cp_plevel, $_cp_exp, $_cp_ppot,$_cp_skill1, $_cp_skill2, $_cp_skill3, $_cp_skill4)
	{
		if ('R'==$_cp_exp){
			$_cp_exp=0;
		}
		
		$_cp_plevelp=$_cp_plevel+round(($_cp_ppot/3));
		
        switch ($_cp_plevelp) {
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
							$_cdp_levelboost=0;
						break;
			case 6:
							$_cdp_levelboost=2;
			break;
			case 7:
							$_cdp_levelboost=7;
			break;
			case 8:
							$_cdp_levelboost=12;
			break;
			case 9:
							$_cdp_levelboost=15;
			break;
			case 10:
							$_cdp_levelboost=18;
			break;
			case 11:
							$_cdp_levelboost=22;
			break;
			default:
							$_cdp_levelboost=25;
	}
					
	switch ($_cp_skill1) {
						case "Po":
							$_cp_s1val=0;
						  break;
							case "Fa":
							$_cp_s1val=3;
						  break;
							case "Av":
							$_cp_s1val=6;
						  break;
							case "Go":
							$_cp_s1val=9;
						  break;										
							case "Ex":
							$_cp_s1val=12;
						  break;										
							case "WC":
							$_cp_s1val=15;
						  break;			
						default:
						$_cp_s1val="0";
				  }		
				  switch ($_cp_skill2) {
						case "Po":
						$_cp_s2val=0;
					  break;
						case "Fa":
						$_cp_s2val=3;
					  break;
						case "Av":
						$_cp_s2val=6;
					  break;
						case "Go":
						$_cp_s2val=9;
					  break;										
						case "Ex":
						$_cp_s2val=12;
					  break;										
						case "WC":
						$_cp_s2val=15;
					  break;			
						default:
						$_cp_s2val="0";
				  }
				  switch ($_cp_skill3) {
					case "Po":
						$_cp_s3val=0;
					  break;
						case "Fa":
						$_cp_s3val=3;
					  break;
						case "Av":
						$_cp_s3val=6;
					  break;
						case "Go":
						$_cp_s3val=9;
					  break;										
						case "Ex":
						$_cp_s3val=12;
					  break;										
						case "WC":
						$_cp_s3val=15;
						default:
						$_cp_s3val="0";
				  }
				  switch ($_cp_skill4) {
					case "Po":
						$_cp_s4val=0;
					  break;
						case "Fa":
						$_cp_s4val=3;
					  break;
						case "Av":
						$_cp_s4val=6;
					  break;
						case "Go":
						$_cp_s4val=9;
					  break;										
						case "Ex":
						$_cp_s4val=12;
					  break;										
						case "WC":
						$_cp_s4val=15;
						default:
						$_cp_s4val="0";
				  }
			  
				
        $_cp_arateg=(($_cdp_levelboost)+($_cp_exp)+$_cp_s1val+$_cp_s2val+$_cp_s3val+$_cp_s4val);

    return $_cp_arateg;
    }

	function ih_arateC($_cp_plevel, $_cp_exp, $_cp_ppot,$_cp_skill1, $_cp_skill2, $_cp_skill3, $_cp_skill4, $_cp_skill5, $_cp_skill6, $_cp_skill7, $_cp_skill8)
	{
		if ('R'==$_cp_exp){
			$_cp_exp=0;
		}
		
		$_cp_plevelp=$_cp_plevel+round(($_cp_ppot/3));
		
        switch ($_cp_plevelp) {
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
				$_cdp_levelboost=0;
			break;
			case 6:
				$_cdp_levelboost=2;
			break;
			case 7:
				$_cdp_levelboost=7;
			break;
			case 8:
				$_cdp_levelboost=12;
			break;
			case 9:
				$_cdp_levelboost=15;
			break;
			case 10:
				$_cdp_levelboost=18;
			break;
			case 11:
				$_cdp_levelboost=22;
			break;
			default:
				$_cdp_levelboost=25;
	}
					
	switch ($_cp_skill1) {
						case "Po":
							$_cp_s1val=0;
						  break;
							case "Fa":
							$_cp_s1val=3;
						  break;
							case "Av":
							$_cp_s1val=6;
						  break;
							case "Go":
							$_cp_s1val=9;
						  break;										
							case "Ex":
							$_cp_s1val=12;
						  break;										
							case "WC":
							$_cp_s1val=15;
						  break;			
						default:
						$_cp_s1val="0";
				  }		
				  switch ($_cp_skill2) {
						case "Po":
						$_cp_s2val=0;
					  break;
						case "Fa":
						$_cp_s2val=6;
					  break;
						case "Av":
						$_cp_s2val=12;
					  break;
						case "Go":
						$_cp_s2val=18;
					  break;										
						case "Ex":
						$_cp_s2val=24;
					  break;										
						case "WC":
						$_cp_s2val=30;
					  break;			
						default:
						$_cp_s2val="0";
				  }
				  switch ($_cp_skill3) {
					case "Po":
						$_cp_s3val=0;
					  break;
						case "Fa":
						$_cp_s3val=3;
					  break;
						case "Av":
						$_cp_s3val=6;
					  break;
						case "Go":
						$_cp_s3val=9;
					  break;										
						case "Ex":
						$_cp_s3val=12;
					  break;										
						case "WC":
						$_cp_s3val=15;
						default:
						$_cp_s3val="0";
				  }
				  switch ($_cp_skill4) {
					case "Po":
						$_cp_s4val=0;
					  break;
						case "Fa":
						$_cp_s4val=3;
					  break;
						case "Av":
						$_cp_s4val=6;
					  break;
						case "Go":
						$_cp_s4val=9;
					  break;										
						case "Ex":
						$_cp_s4val=12;
					  break;										
						case "WC":
						$_cp_s4val=15;
						default:
						$_cp_s4val="0";
				  }
				  switch ($_cp_skill5) {
					case "Po":
						$_cp_s5val=0;
					  break;
						case "Fa":
						$_cp_s5val=3;
					  break;
						case "Av":
						$_cp_s5val=6;
					  break;
						case "Go":
						$_cp_s5val=9;
					  break;										
						case "Ex":
						$_cp_s5val=12;
					  break;										
						case "WC":
						$_cp_s5val=15;
						default:
						$_cp_s5val="0";
				  }
				  switch ($_cp_skill6) {
					case "Po":
						$_cp_s6val=0;
					  break;
						case "Fa":
						$_cp_s6val=3;
					  break;
						case "Av":
						$_cp_s6val=6;
					  break;
						case "Go":
						$_cp_s6val=9;
					  break;										
						case "Ex":
						$_cp_s6val=12;
					  break;										
						case "WC":
						$_cp_s6val=15;
						default:
						$_cp_s6val="0";
				  }
				  switch ($_cp_skill7) {
					case "Po":
						$_cp_s7val=0;
					  break;
						case "Fa":
						$_cp_s7val=6;
					  break;
						case "Av":
						$_cp_s7val=12;
					  break;
						case "Go":
						$_cp_s7val=18;
					  break;										
						case "Ex":
						$_cp_s7val=24;
					  break;										
						case "WC":
						$_cp_s7val=30;
						default:
						$_cp_s7val="0";
				  }
				  switch ($_cp_skill8) {
					case "Po":
						$_cp_s8val=0;
					  break;
						case "Fa":
						$_cp_s8val=3;
					  break;
						case "Av":
						$_cp_s8val=6;
					  break;
						case "Go":
						$_cp_s8val=9;
					  break;										
						case "Ex":
						$_cp_s8val=12;
					  break;										
						case "WC":
						$_cp_s8val=15;
						default:
						$_cp_s8val="0";
				  }
				
        $_cp_aratec=(($_cdp_levelboost)+($_cp_exp)+(($_cp_s1val+$_cp_s2val+$_cp_s3val+$_cp_s4val+$_cp_s5val+$_cp_s6val+$_cp_s7val+$_cp_s8val)*0.5));

    return $_cp_aratec;
    }

	function ih_arateW($_cp_plevel, $_cp_exp, $_cp_ppot,$_cp_skill1, $_cp_skill2, $_cp_skill3, $_cp_skill4, $_cp_skill5, $_cp_skill6, $_cp_skill7, $_cp_skill8)
	{
		if ('R'==$_cp_exp){
			$_cp_exp=0;
		}
		
		$_cp_plevelp=$_cp_plevel+round(($_cp_ppot/3));
		
        switch ($_cp_plevelp) {
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
				$_cdp_levelboost=0;
			break;
			case 6:
				$_cdp_levelboost=2;
			break;
			case 7:
				$_cdp_levelboost=7;
			break;
			case 8:
				$_cdp_levelboost=12;
			break;
			case 9:
				$_cdp_levelboost=15;
			break;
			case 10:
				$_cdp_levelboost=18;
			break;
			case 11:
				$_cdp_levelboost=22;
			break;
			default:
				$_cdp_levelboost=25;
	}
					
	switch ($_cp_skill1) {
						case "Po":
							$_cp_s1val=0;
						  break;
							case "Fa":
							$_cp_s1val=3;
						  break;
							case "Av":
							$_cp_s1val=6;
						  break;
							case "Go":
							$_cp_s1val=9;
						  break;										
							case "Ex":
							$_cp_s1val=12;
						  break;										
							case "WC":
							$_cp_s1val=15;
						  break;			
						default:
						$_cp_s1val="0";
				  }		
				  switch ($_cp_skill2) {
						case "Po":
						$_cp_s2val=0;
					  break;
						case "Fa":
						$_cp_s2val=6;
					  break;
						case "Av":
						$_cp_s2val=12;
					  break;
						case "Go":
						$_cp_s2val=18;
					  break;										
						case "Ex":
						$_cp_s2val=24;
					  break;										
						case "WC":
						$_cp_s2val=30;
					  break;			
						default:
						$_cp_s2val="0";
				  }
				  switch ($_cp_skill3) {
					case "Po":
						$_cp_s3val=0;
					  break;
						case "Fa":
						$_cp_s3val=6;
					  break;
						case "Av":
						$_cp_s3val=12;
					  break;
						case "Go":
						$_cp_s3val=18;
					  break;										
						case "Ex":
						$_cp_s3val=24;
					  break;										
						case "WC":
						$_cp_s3val=30;
						default:
						$_cp_s3val="0";
				  }
				  switch ($_cp_skill4) {
					case "Po":
						$_cp_s4val=0;
					  break;
						case "Fa":
						$_cp_s4val=3;
					  break;
						case "Av":
						$_cp_s4val=6;
					  break;
						case "Go":
						$_cp_s4val=9;
					  break;										
						case "Ex":
						$_cp_s4val=12;
					  break;										
						case "WC":
						$_cp_s4val=15;
						default:
						$_cp_s4val="0";
				  }
				  switch ($_cp_skill5) {
					case "Po":
						$_cp_s5val=0;
					  break;
						case "Fa":
						$_cp_s5val=3;
					  break;
						case "Av":
						$_cp_s5val=6;
					  break;
						case "Go":
						$_cp_s5val=9;
					  break;										
						case "Ex":
						$_cp_s5val=12;
					  break;										
						case "WC":
						$_cp_s5val=15;
						default:
						$_cp_s5val="0";
				  }
				  switch ($_cp_skill6) {
					case "Po":
						$_cp_s6val=0;
					  break;
						case "Fa":
						$_cp_s6val=3;
					  break;
						case "Av":
						$_cp_s6val=6;
					  break;
						case "Go":
						$_cp_s6val=9;
					  break;										
						case "Ex":
						$_cp_s6val=12;
					  break;										
						case "WC":
						$_cp_s6val=15;
						default:
						$_cp_s6val="0";
				  }
				  switch ($_cp_skill7) {
					case "Po":
						$_cp_s7val=0;
					  break;
						case "Fa":
						$_cp_s7val=3;
					  break;
						case "Av":
						$_cp_s7val=6;
					  break;
						case "Go":
						$_cp_s7val=9;
					  break;										
						case "Ex":
						$_cp_s7val=12;
					  break;										
						case "WC":
						$_cp_s7val=15;
						default:
						$_cp_s7val="0";
				  }
				  switch ($_cp_skill8) {
					case "Po":
						$_cp_s8val=0;
					  break;
						case "Fa":
						$_cp_s8val=3;
					  break;
						case "Av":
						$_cp_s8val=6;
					  break;
						case "Go":
						$_cp_s8val=9;
					  break;										
						case "Ex":
						$_cp_s8val=12;
					  break;										
						case "WC":
						$_cp_s8val=15;
						default:
						$_cp_s8val="0";
				  }
				
        $_cp_aratew=(($_cdp_levelboost)+($_cp_exp)+(($_cp_s1val+$_cp_s2val+$_cp_s3val+$_cp_s4val+$_cp_s5val+$_cp_s6val+$_cp_s7val+$_cp_s8val)*0.5));

    return $_cp_aratew;
    }
	
	function ih_arateD($_cp_plevel, $_cp_exp, $_cp_ppot,$_cp_skill1, $_cp_skill2, $_cp_skill3, $_cp_skill4, $_cp_skill5, $_cp_skill6, $_cp_skill7, $_cp_skill8)
	{
		if ('R'==$_cp_exp){
			$_cp_exp=0;
		}
		
		$_cp_plevelp=$_cp_plevel+round(($_cp_ppot/3));
		
        switch ($_cp_plevelp) {
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
				$_cdp_levelboost=0;
			break;
			case 6:
				$_cdp_levelboost=2;
			break;
			case 7:
				$_cdp_levelboost=7;
			break;
			case 8:
				$_cdp_levelboost=12;
			break;
			case 9:
				$_cdp_levelboost=15;
			break;
			case 10:
				$_cdp_levelboost=18;
			break;
			case 11:
				$_cdp_levelboost=22;
			break;
			default:
				$_cdp_levelboost=25;
	}
					
	switch ($_cp_skill1) {
						case "Po":
							$_cp_s1val=0;
						  break;
							case "Fa":
							$_cp_s1val=3;
						  break;
							case "Av":
							$_cp_s1val=6;
						  break;
							case "Go":
							$_cp_s1val=9;
						  break;										
							case "Ex":
							$_cp_s1val=12;
						  break;										
							case "WC":
							$_cp_s1val=15;
						  break;			
						default:
						$_cp_s1val="0";
				  }		
				  switch ($_cp_skill2) {
						case "Po":
						$_cp_s2val=0;
					  break;
						case "Fa":
						$_cp_s2val=3;
					  break;
						case "Av":
						$_cp_s2val=6;
					  break;
						case "Go":
						$_cp_s2val=9;
					  break;										
						case "Ex":
						$_cp_s2val=12;
					  break;										
						case "WC":
						$_cp_s2val=15;
					  break;			
						default:
						$_cp_s2val="0";
				  }
				  switch ($_cp_skill3) {
					case "Po":
						$_cp_s3val=0;
					  break;
						case "Fa":
						$_cp_s3val=3;
					  break;
						case "Av":
						$_cp_s3val=6;
					  break;
						case "Go":
						$_cp_s3val=9;
					  break;										
						case "Ex":
						$_cp_s3val=12;
					  break;										
						case "WC":
						$_cp_s3val=15;
						default:
						$_cp_s3val="0";
				  }
				  switch ($_cp_skill4) {
					case "Po":
						$_cp_s4val=0;
					  break;
						case "Fa":
						$_cp_s4val=3;
					  break;
						case "Av":
						$_cp_s4val=6;
					  break;
						case "Go":
						$_cp_s4val=9;
					  break;										
						case "Ex":
						$_cp_s4val=12;
					  break;										
						case "WC":
						$_cp_s4val=15;
						default:
						$_cp_s4val="0";
				  }
				  switch ($_cp_skill5) {
					case "Po":
						$_cp_s5val=0;
					  break;
						case "Fa":
						$_cp_s5val=3;
					  break;
						case "Av":
						$_cp_s5val=6;
					  break;
						case "Go":
						$_cp_s5val=9;
					  break;										
						case "Ex":
						$_cp_s5val=12;
					  break;										
						case "WC":
						$_cp_s5val=15;
						default:
						$_cp_s5val="0";
				  }
				  switch ($_cp_skill6) {
					case "Po":
						$_cp_s6val=0;
					  break;
						case "Fa":
						$_cp_s6val=6;
					  break;
						case "Av":
						$_cp_s6val=12;
					  break;
						case "Go":
						$_cp_s6val=18;
					  break;										
						case "Ex":
						$_cp_s6val=24;
					  break;										
						case "WC":
						$_cp_s6val=30;
						default:
						$_cp_s6val="0";
				  }
				  switch ($_cp_skill7) {
					case "Po":
						$_cp_s7val=0;
					  break;
						case "Fa":
						$_cp_s7val=6;
					  break;
						case "Av":
						$_cp_s7val=12;
					  break;
						case "Go":
						$_cp_s7val=18;
					  break;										
						case "Ex":
						$_cp_s7val=24;
					  break;										
						case "WC":
						$_cp_s7val=30;
						default:
						$_cp_s7val="0";
				  }
				  switch ($_cp_skill8) {
					case "Po":
						$_cp_s8val=0;
					  break;
						case "Fa":
						$_cp_s8val=3;
					  break;
						case "Av":
						$_cp_s8val=6;
					  break;
						case "Go":
						$_cp_s8val=9;
					  break;										
						case "Ex":
						$_cp_s8val=12;
					  break;										
						case "WC":
						$_cp_s8val=15;
						default:
						$_cp_s8val="0";
				  }
				
        $_cp_arated=(($_cdp_levelboost)+($_cp_exp)+(($_cp_s1val+$_cp_s2val+$_cp_s3val+$_cp_s4val+$_cp_s5val+$_cp_s6val+$_cp_s7val+$_cp_s8val)*0.5));

    return $_cp_arated;
    }	
?>
