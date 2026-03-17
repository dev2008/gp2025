<?php
// don't delete this line, this must be the first line of your code
if(!defined('custom_page_from_inclusion')) { die(); }
#echo "<p>Slapshot</p>";
$arr = get_defined_vars();
#nz_debug([$myfilearray],"Variables:");
//Work out League / Week / Season
$_cp_etext = preg_split("/[\s,]+/", $myline);
#nz_debug([$_cp_etext],"Variables:");
$_cp_league=$_cp_etext[1];
echo "<p>Found League $_cp_league</p>";
//If League is populated properly then proceed 
$_cdp_validleagues = array("NHL1","NHL2","NHL3","NHL4");
if (in_array($_cp_league, $_cdp_validleagues)) {
	$_cdp_valid=1;
    #echo "<p>$_cp_league found</p>";
    //Work out Season and Week
    $_cp_season=$_cp_etext[5];	
    if ('Postseason'==$_cp_etext[7]){
        $_cp_week=$_cp_etext[8];
        if ('Week'==$_cp_week) {
        $_cp_week=16+$_cp_etext[9];
            }
    } elseif ('Preseason'==$_cp_etext[7]) {	
        $_cp_week=0;
    } else {	
        $_cp_week=$_cp_etext[7];
    }	

    if (21==$_cp_week){
        $roundup='Y';
        $rounduprext='with roundup ';
    }
//Work out Coach name
$myline2=$i+1;
#($myfilearray[$myline2]);
$mycoachtext=explode(" ",$myfilearray[$myline2]);
($mycoachtext);
$mycoachtextr=array_reverse($mycoachtext);
($mycoachtextr);
$i=0;
$mystart=0;
foreach ($mycoachtextr as $mycline){ 
	if (1==startsWithLetter($mycline) AND 0==$mystart AND strlen($mycline)>2 ) {
	#echo "<p>$i - strlen($mycline)</p>";
	$mystart=$i+1;
	$myend=$i;
	#echo "<p>Found start - $mystart !</p>";
}
$i++;
}
$_cp_mycoachname=$mycoachtextr[$mystart];
$_cp_mycoachname.=" ";
$_cp_mycoachname.=$mycoachtextr[$myend];
#echo "<p>Coach - $_cp_mycoachname</p>";    
}    
?>
