<?php

//Resolves plays script couldn't work out
function nz_999($_cp_yards) {
    echo "<p>999</p>";
    global $conn;

	//Sacks
    $sql = "UPDATE `n_playbyplay`
            SET `a_yards` = :yards, `a_sack` = 1
            WHERE `a_text` LIKE '%sacked%'";

    $stmt = prepare_db($conn, $sql);

    bind_param_db($stmt, ':yards', (int)$_cp_yards, PDO::PARAM_INT);

    $res = execute_prepared_db($stmt, 0);

	//Offensive penalties
    $sql = "UPDATE `n_playbyplay`
            SET `a_yards` = :yards, `a_peno` = 1
            WHERE `a_text` LIKE '%against off%'";

    $stmt = prepare_db($conn, $sql);

    bind_param_db($stmt, ':yards', (int)$_cp_yards, PDO::PARAM_INT);

    $res = execute_prepared_db($stmt, 0);

	//Defensive penalties
    $sql = "UPDATE `n_playbyplay`
            SET `a_yards` = :yards, `a_pend` = 1
            WHERE `a_text` LIKE '%against def%'";

    $stmt = prepare_db($conn, $sql);

    bind_param_db($stmt, ':yards', (int)$_cp_yards, PDO::PARAM_INT);

    $res = execute_prepared_db($stmt, 0);

	//Incompletions
    $sql = "UPDATE `n_playbyplay`
            SET `a_yards` = :yards, `a_incomplete` = 1
            WHERE `a_text` LIKE '%incomplete%'";

    $stmt = prepare_db($conn, $sql);

    bind_param_db($stmt, ':yards', (int)$_cp_yards, PDO::PARAM_INT);

    $res = execute_prepared_db($stmt, 0);

	//TDs
    $sql = "UPDATE `n_playbyplay`
            SET `a_yards` = `a_field`, `a_intercept` = :yards, `a_td` = 0
            WHERE `a_text` LIKE '%touchdown%'";

    $stmt = prepare_db($conn, $sql);

    bind_param_db($stmt, ':yards', (int)$_cp_yards, PDO::PARAM_INT);

    $res = execute_prepared_db($stmt, 0);

	//Fumbles
    $sql = "UPDATE `n_playbyplay`
            SET `a_yards` = :yards, `a_fumble` = 1, `a_td` = 0
            WHERE `a_text` LIKE '%fumble%'";

    $stmt = prepare_db($conn, $sql);

    bind_param_db($stmt, ':yards', (int)$_cp_yards, PDO::PARAM_INT);

    $res = execute_prepared_db($stmt, 0);

	//Interceptions
    $sql = "UPDATE `n_playbyplay`
            SET `a_yards` = :yards, `a_intercept` = 1, `a_td` = 0
            WHERE `a_text` LIKE '%intercept%'";

    $stmt = prepare_db($conn, $sql);

    bind_param_db($stmt, ':yards', (int)$_cp_yards, PDO::PARAM_INT);

    $res = execute_prepared_db($stmt, 0);

	//Safeties
    $sql = "UPDATE `n_playbyplay`
            SET `a_yards` = :yards, `a_safety` = 1, `a_td` = 0
            WHERE `a_text` LIKE '%safety%'";

    $stmt = prepare_db($conn, $sql);

    bind_param_db($stmt, ':yards', (int)$_cp_yards, PDO::PARAM_INT);

    $res = execute_prepared_db($stmt, 0);

}


?>
