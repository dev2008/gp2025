<?php
/*
***********************************************************************************
DaDaBIK (DaDaBIK is a DataBase Interfaces Kreator) https://dadabik.com/
Copyright (C) 2001-2025 Eugenio Tacchini

This program is distributed "as is" and WITHOUT ANY WARRANTY, either expressed or implied, without even the implied warranties of merchantability or fitness for a particular purpose.

This program is distributed under the terms of the DaDaBIK license, which is included in this package (see dadabik_license.txt). For all the details see dadabik_license.txt.

If you are unsure about what you are allowed to do with this license, feel free to contact info@dadabik.com
***********************************************************************************
*/
?>
<?php
$hooks['performances']['insert']['after'] = 'dadabik_strikerate';
$hooks['performances']['update']['after'] = 'dadabik_strikerate';

function dadabik_strikerate($id_performance)
{
    global $conn;

    // Step 1: Fetch score and balls
    $sql = "SELECT score, balls FROM performances WHERE id = :id_performance";
    $res_prepare = prepare_db($conn, $sql);
    bind_param_db($res_prepare, ':id_performance', $id_performance);
    execute_prepared_db($res_prepare, 0);
    $row = fetch_row_db($res_prepare);

    // Step 2: Calculate strike rate safely
   $strikerate = ($row['balls'] == 0) ? 0 : round(($row['score'] / $row['balls']) * 100, 2);

    // Step 3: Update the strike rate
    $sql = "UPDATE performances SET sr = :sr WHERE id = :id_performance";
    $res_prepare = prepare_db($conn, $sql);
    bind_param_db($res_prepare, ':sr', $strikerate);
    bind_param_db($res_prepare, ':id_performance', $id_performance);
    execute_prepared_db($res_prepare, 0);
}



?>