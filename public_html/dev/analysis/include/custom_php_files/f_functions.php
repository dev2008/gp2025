<?php

function nz_pbp($league,$season,$week) {
	echo "<p>";
    echo $league,$season,$week;
	echo "</p>p>";
}

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

	//Interceptions
    $sql = "UPDATE `n_playbyplay`
            SET `a_yards` = :yards, `a_intercept` = 1
            WHERE `a_text` LIKE '%intercept%'";

    $stmt = prepare_db($conn, $sql);

    bind_param_db($stmt, ':yards', (int)$_cp_yards, PDO::PARAM_INT);

    $res = execute_prepared_db($stmt, 0);

}



?>
