<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }

//Define colours
$mycolour1="w3-pale-yellow";
$mycolour2="w3-cyan";
$mycolour3="w3-khaki";
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
$mycolour15="w3-dark-gray";
$mycolour16="w3-gray";
$mycolour17="w3-light-gray";

/*
 * Generic function to take an array and print all of the content in a basic format.
 * Used to understand the contents
 */
function nz_debug($array,$title)
	{
					echo "<pre>$title<code>\n";
					print_r($array);
					echo "\n</code></pre>\n";
					return;
	}	
	
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

function output($str) {
    echo $str;
    ob_end_flush();
	if (ob_get_level() > 0) {ob_flush();}
    flush();
    ob_start();
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


	
function remove_sp_chr($str)
{
    $result = str_replace(array("<B>", "<C>", "<Z>", "<L>", "<T>", "<ST.28.88.105.110>", "<L.48.1>", "<P.70>", "<L.6.1>", "<L.33.1>", "<L.36.1>", "<L.39.1>", "<L.1.1>", "<L.3.1>", "<L.1.1>", "<L.3.1>","<ST.66>"), ' ', $str);
    $result = str_replace(array("<BK.Draft List>", "<BK.DraftED List>"), 'Draft List', $result);
    $result = str_replace(array(" <BK.Turnsheet><P.70><L>", "<BK.TurnED List>"), 'Turnsheet', $result);   
    $result = str_replace(array("#", "'", ";", "<", ">"), '`', $result);
	return ($result);
}

function remove_sp_chr2($str)
{
    $result = str_replace(array("`U`", "`UC`", "`L.42.1`", "`ST.29.66.95`", "`L.42.1`","`L.6.1`", "(", ")"), ' ', $str);
	//Fix Baseball specific issues 
    $result = str_replace(array("Open."), 'Open', $result);
    $result = str_replace(array("Dome."), 'Dome', $result);
    $result = str_replace(array("C.Bethancourt"), 'Christian Bethancourt', $result);
    //Fix multi nickname teams
    $result = str_replace(array("Devil "), ' ', $result);
    $result = str_replace(array(" Sox"), '', $result);
    $result = str_replace(array(" Jays"), '', $result);
	return ($result);
}

//Dump form variables
function asm_dumpform() {
    print '<pre> POST - '  . print_r($_POST, true) . '</pre>'; 
    print '<pre> GET - '  . print_r($_GET, true) . '</pre>'; 
}

function startsWithLetter($str)
{
    return preg_match('/^[a-zA-Z]/', $str) === 1;
}

//PDO Functions
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

function nz_pdo_insert($_cp_sql,$conn){
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

function nz_pdo_update($_cp_sql,$_cp_data,$conn){
	try {
		$result = $conn->prepare($_cp_sql); 
		$result->execute($_cp_data); 
		$no=$result->rowCount();
	} catch (PDOException $e) {
		echo "DataBase Error:<br>".$e->getMessage();
		echo "<pre></pre>$_cp_sql</pre>";
		exit ("<h1>****Warning - processing stopped on database error****</h1>");
	} catch (Exception $e) {
		echo "General Error:<br>".$e->getMessage();
		exit ("<h1>****Warning - processing stopped on general error****</h1>");
	}
	return ($no);
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

function nz_pdo_row($_cp_sql,$_cp_val,$conn){
	try {
		$stmt = $conn->prepare($_cp_sql);
		$stmt->execute([$_cp_val]);
		$value = $stmt->fetch();
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

function nz_pdo_new($_cp_sql,$_cp_data,$conn){
	try {
		$result = $conn->prepare($_cp_sql); 
		$result->execute([$_cp_data]); 
		$_cp_ok = "OK";
	} catch (PDOException $e) {
		echo "DataBase Error:<br>".$e->getMessage();
		echo "<pre></pre>$_cp_sql</pre>";
		exit ("<h1>****Warning - processing stopped on database error****</h1>");
	} catch (Exception $e) {
		echo "General Error:<br>".$e->getMessage();
		exit ("<h1>****Warning - processing stopped on general error****</h1>");
	}
	return ($_cp_ok);
}


?>


