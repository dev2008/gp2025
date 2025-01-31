<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }

//Define colours
$mycolour1="w3-red";
$mycolour2="w3-deep-purple";
$mycolour3="w3-purple";
$mycolour4="w3-teal";
$mycolour5="w3-light-grey";
$mycolour6="w3-light-blue";
$mycolour7="w3-orange";
$mycolour8="w3-text-deep-purple";
$mycolour9="w3-text-indigo";
$mycolour10="w3-purple";
$mycolour11="w3-text-black";
$mycolour12="w3-text-deep-purple";
$mycolour13="w3-amber";
$mycolour14="w3-yellow";
$mycolour15="w3-dark-grey";
$mycolour16="w3-grey";
$mycolour17="black";
$mycolour18="w3-text-white";

$gpcolour1="w3-text-yellow";
$gpcolour2="w3-text-orange";
$gpcolour3="w3-text-black";
$gpcolour4="w3-orange";
$gpcolour5="w3-theme-l3";
$gpcolour6="w3-theme-l4";

function pgetpost_print()
	{
					echo "<pre><code>\n";
					echo "<h2>Printing Globals</h3>\n";
					print_r($_POST);
					print_r($_GET);
					echo "\n</code></pre>\n";
					return;
	}

function getDir4TxtR($directory) {
  if ($handle = opendir($directory)) {
    while (false !== ($entry = readdir($handle))) {
      if($entry != "." && $entry != "..") {

        $str1 = "$directory/$entry";

        if(preg_match("/\.txt$/i", $entry)) {
          echo $str1 . "<br />\n"; 
          getGPTurnDetails($str1);         
        } else {
          if(is_dir($str1)) {
             getDir4TxtR($str1);
          }
        }       
      }
    }
    closedir($handle);
  }
}

//Function to extract League season and Week from file
function getGPTurnDetails($bb_filename)
{
        
        $bb_turn_data = file_get_contents($bb_filename);
       
        // split up the turn data using the <BK. string which signifies the beginning of each block
        $bb_blocks = explode("<BK.", $bb_turn_data);
		foreach ($bb_blocks AS $bb_block) {
            //Find Team Report Block
            if(strncmp("Team Report",$bb_block,11) == 0) {
                $myblock = $bb_block;
                            // Extract the league name and id
            if(preg_match_all('~BASEBALL\s*(MLB[0-9]*)~', $myblock, $bb_extract_league_name, PREG_SET_ORDER))
            {
                $this->bb_league_name = $bb_extract_league_name[0][1];
                $this->bb_league_id = substr($bb_extract_league_name[0][1],3);

            }
                
                
                
                debug_print("TURN DATA BLOCKS",$myblock);
            }
		}



		return;
}

function output($str) {
    echo $str;
	if(ob_get_length() > 0) {
		ob_end_flush();
	}
	if(ob_get_length() > 0) {
		ob_flush();
	}	
    flush();
    ob_start();
}

function array_to_table($data,$caption,$args=false) {
	if (!is_array($args)) { $args = array(); }
	foreach (array('class','column_widths','custom_headers','format_functions','nowrap_head','nowrap_body','capitalize_headers') as $key) {
		if (array_key_exists($key,$args)) { $$key = $args[$key]; } else { $$key = false; }
	}
	if ($class) { $class = ' class="'.$class.'"'; } else { $class = ''; }
	if (!is_array($column_widths)) { $column_widths = array(); }

	//get rid of headers row, if it exists (headers should exist as keys)
	if (array_key_exists('headers',$data)) { unset($data['headers']); }

	#$t="<table style='width:40%'  class='w3-table w3-striped w3-bordered'>";
	#$t="<table style='width:40%'  class='results'>";
	#$t="<table id='table_id' class='display'>";
	$t="<table style='width:80%'  class='w3-table w3-striped w3-bordered'>";
	 
	
	$t .= $caption;
	
	$i = 0;
	foreach ($data as $row) {
		$i++;
		//display headers
		if ($i == 1) { 
			foreach ($row as $key => $value) {
				if (array_key_exists($key,$column_widths)) { $style = ' style="width:'.$column_widths[$key].'px;"'; } else { $style = ''; }
				$t .= '<col'.$style.' />';
			}
			$t .= '<thead><tr>';
			foreach ($row as $key => $value) {
				if (is_array($custom_headers) && array_key_exists($key,$custom_headers) && ($custom_headers[$key])) { $header = $custom_headers[$key]; }
				elseif ($capitalize_headers) { $header = ucwords($key); }
				else { $header = $key; }
				if ($nowrap_head) { $nowrap = ' nowrap'; } else { $nowrap = ''; }
				$t .= '<td'.$nowrap.'>'.$header.'</td>';
			}
			$t .= '</tr></thead>';
		}

		//display values
		if ($i == 1) { $t .= '<tbody>'; }
		$t .= '<tr>';
		foreach ($row as $key =>  $value) {
			if (is_array($format_functions) && array_key_exists($key,$format_functions) && ($format_functions[$key])) {
				$function = $format_functions[$key];
				if (!function_exists($function)) { custom_die('Data format function does not exist: '.htmlspecialchars($function)); }
				$value = $function($value);
			}
			if ($nowrap_body) { $nowrap = ' nowrap'; } else { $nowrap = ''; }
			$t .= '<td'.$nowrap.'>'.$value.'</td>';
		}
		$t .= '</tr>';
	}
	$t .= '</tbody>';
	$t .= '</table>';
	return $t;
}

/**
 * 
 * @function recursive_scan
 * @description Recursively scans a folder and its child folders
 * @param $path :: Path of the folder/file
 * 
 * */
function recursive_scan($path){
    global $file_info;
    $path = rtrim($path, '/');
    if(!is_dir($path)) $file_info[] = $path;
        else {
            $files = scandir($path);
            foreach($files as $file) if($file != '.' && $file != '..') recursive_scan($path . '/' . $file);
        }
}

function nz_pdo($_cp_sql,$conn){
	try {
		$result = $conn->prepare($_cp_sql); 
		$result->execute(); 
		$last_id = $conn->lastInsertId();
		#echo "<p>Processed SQL - last record:$last_id</p>";	
	} catch (PDOException $e) {
		echo "DataBase Error:<br>".$e->getMessage();
		echo "<pre></pre>$_cp_sql</pre>";
		exit ("<h1>****Warning - processing stopped on database error****</h1>");
	} catch (Exception $e) {
		echo "General Error:<br>".$e->getMessage();
		exit ("<h1>****Warning - processing stopped on general error****</h1>");
	}
	return ($last_id);
}	

function nz_pdo_array($_cp_sql,$conn){
	try {
		$result = $conn->prepare($_cp_sql); 
		$result->execute(); 
		$data = $result->fetchAll(PDO::FETCH_ASSOC);
		#var_export($data);
		$last_id = $conn->lastInsertId();
	} catch (PDOException $e) {
		echo "DataBase Error:<br>".$e->getMessage();
		echo "<pre></pre>$_cp_sql</pre>";
		exit ("<h1>****Warning - processing stopped on database error****</h1>");
	} catch (Exception $e) {
		echo "General Error:<br>".$e->getMessage();
		exit ("<h1>****Warning - processing stopped on general error****</h1>");
	}
	return ($data);
}

function nz_pdo_single($_cp_sql,$_cp_val,$conn){
	try {
		$stmt = $conn->prepare($_cp_sql);
		$stmt->execute([$_cp_val]);
		$value = $stmt->fetchColumn();
	} catch (PDOException $e) {
		echo "DataBase Error:<br>".$e->getMessage();
		echo "<pre></pre>$_cp_sql</pre>";
		exit ("<h1>****Warning - processing stopped on database error****</h1>");
	} catch (Exception $e) {
		echo "General Error:<br>".$e->getMessage();
		exit ("<h1>****Warning - processing stopped on general error****</h1>");
	}
	return ($value);
}

	
function remove_sp_chr($str)
{
    $result = str_replace(array("<C>", "<Z>", "<L>", "<T>", "<ST.28.88.105.110>", "<L.48.1>", "<P.70>", "<L.6.1>", "<L.33.1>", "<L.36.1>", "<L.39.1>", "<L.1.1>", "<L.3.1>", "<L.1.1>", "<L.3.1>"), ' ', $str);
    $result = str_replace(array("<BK.Draft List>", "<BK.DraftED List>"), 'Draft List', $result);
    $result = str_replace(array(" <BK.Turnsheet><P.70><L>", "<BK.TurnED List>"), 'Turnsheet', $result);   
    $result = str_replace(array("#", "'", ";", "<", ">"), '`', $result);
	return ($result);
}

function remove_sp_chr2($str)
{
    $result = str_replace(array("`U`", "`UC`", "`L.42.1`", "`ST.29.66.95`", "`L.42.1`", "(", ")"), ' ', $str);
	//Fix Baseball specific issues 
    $result = str_replace(array("Devil "), ' ', $result);
    $result = str_replace(array("Open."), 'Open', $result);
    $result = str_replace(array("Dome."), 'Dome', $result);
    $result = str_replace(array("C.Bethancourt"), 'Christian Bethancourt', $result);
	return ($result);
}

//Dump form variables
function asm_dumpform() {
    print '<pre> POST - '  . print_r($_POST, true) . '</pre>'; 
    print '<pre> GET - '  . print_r($_GET, true) . '</pre>'; 
}


?>


