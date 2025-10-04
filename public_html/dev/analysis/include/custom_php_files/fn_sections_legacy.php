<?php
if(!defined('custom_page_from_inclusion')) { die(); }

/**
 * fn_sections.php — batch stubs for additional sections
 * Each function:
 *   - loads the selected upload
 *   - extracts the indicated section
 *   - (TODO) parse & write to DB
 *   - updates g_uploads.processed to a given code
 *
 * Use with: _cp_process_team_report($conn, $upload_id, $files_dir); etc.
 */

//
// ----------------------------- Shared helpers -----------------------------
//

/** Load upload row + file text */
function _cp_load_upload_and_file(PDO $conn, int $upload_id, string $files_dir) : array {
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $st = $conn->prepare("SELECT * FROM `g_uploads` WHERE `upload_id`=:id LIMIT 1");
    $st->execute([':id'=>$upload_id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        echo '<div class="w3-panel w3-red">Upload not found.</div>';
        return [null, null, null];
    }

    $filename = $row['filename'];
    $fullpath = rtrim($files_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;
    if (!is_readable($fullpath)) {
        echo '<div class="w3-panel w3-red"><b>File not readable:</b> '.htmlspecialchars($fullpath).'</div>';
        return [$row, $filename, null];
    }

    $raw = file_get_contents($fullpath);
    if ($raw === false || $raw === '') {
        echo '<div class="w3-panel w3-red"><b>Error reading file.</b></div>';
        return [$row, $filename, null];
    }

    // Normalize EOLs
    $raw = str_replace(["\r\n", "\r"], "\n", $raw);
    return [$row, $filename, $raw];
}

/** ---------------- Bitmask flags (powers of two) ---------------- */
define('_CP_FLAG_LEAGUE',     1);   // League Report
define('_CP_FLAG_TEAM',       2);   // Team Report
define('_CP_FLAG_ROSTER',     4);   // Roster
define('_CP_FLAG_PBP',        8);   // Play by Play
define('_CP_FLAG_H2H',       16);   // Head to Head
define('_CP_FLAG_STANDINGS', 32);   // Standings
define('_CP_FLAG_SCOUT',     64);   // Scouting Report

/** Has this flag already been set? */
function _cp_has_flag(int $processed, int $flag): bool {
    return (($processed & $flag) === $flag);
}

/** OR-in a flag, optionally also setting league/season/week */
function _cp_mark_processed_flag(PDO $conn, int $upload_id, int $flag, ?string $league=null, ?int $season=null, ?int $week=null) : void {
    if ($league !== null && $season !== null && $week !== null) {
        $st = $conn->prepare("
            UPDATE g_uploads
               SET league = :l, season = :s, week = :w,
                   processed = COALESCE(processed,0) | :f
             WHERE upload_id = :id
             LIMIT 1
        ");
        $st->execute([':l'=>$league, ':s'=>$season, ':w'=>$week, ':f'=>$flag, ':id'=>$upload_id]);
    } else {
        $st = $conn->prepare("
            UPDATE g_uploads
               SET processed = COALESCE(processed,0) | :f
             WHERE upload_id = :id
             LIMIT 1
        ");
        $st->execute([':f'=>$flag, ':id'=>$upload_id]);
    }
}


/** Extract a section by start marker and (default) end marker <E><P> */
function _cp_extract_section(string $raw, string $startMarker, string $endMarker = '<E><P>') : ?string {
    $pos = strpos($raw, $startMarker);
    if ($pos === false) return null;
    $pos += strlen($startMarker);
    $end = strpos($raw, $endMarker, $pos);
    if ($end === false) $end = strlen($raw);
    return substr($raw, $pos, $end - $pos);
}

/** (Optional) parse league/season/week from filenames like NFLZZ-PE_s2033_w14_vs_*.txt */
function _cp_parse_lsw_from_filename(string $filename) : array {
    $league = $season = $week = null;
    if (preg_match('/^(?P<league>[A-Z]+)-[A-Z]+_s(?P<season>\d{4})_w(?P<week>\d{1,2})_/i', $filename, $m)) {
        $league = strtoupper($m['league']);
        $season = (int)$m['season'];
        $week   = (int)$m['week'];
    }
    return [$league, $season, $week];
}

/** Mark processed with a specific code (and optionally set league/season/week) */
function _cp_mark_processed(PDO $conn, int $upload_id, int $code, ?string $league=null, ?int $season=null, ?int $week=null) : void {
    if ($league !== null && $season !== null && $week !== null) {
        $st = $conn->prepare("UPDATE `g_uploads` SET `league`=:l, `season`=:s, `week`=:w, `processed`=:p WHERE `upload_id`=:id LIMIT 1");
        $st->execute([':l'=>$league, ':s'=>$season, ':w'=>$week, ':p'=>$code, ':id'=>$upload_id]);
    } else {
        $st = $conn->prepare("UPDATE `g_uploads` SET `processed`=:p WHERE `upload_id`=:id LIMIT 1");
        $st->execute([':p'=>$code, ':id'=>$upload_id]);
    }
}

//
// ----------------------------- STUB SECTIONS ------------------------------
//

// Team Report — <BK.Team Report><L>
function _cp_process_team_report(PDO $conn, int $upload_id, string $files_dir) : void {
    echo '<div class="w3-card w3-padding w3-white w3-margin-top"><div class="w3-text-indigo"><b>Team Report</b></div>';

    [$urow, $filename, $raw] = _cp_load_upload_and_file($conn, $upload_id, $files_dir);
    if ($raw === null) { echo '</div>'; return; }

    $section = _cp_extract_section($raw, '<BK.Team Report><L>');
    if ($section === null) { echo '<div class="w3-panel w3-amber w3-border">Team Report section not found.</div></div>'; return; }

    // TODO: parse & write…

    [$league, $season, $week] = _cp_parse_lsw_from_filename($filename);
    _cp_mark_processed_flag($conn, $upload_id, _CP_FLAG_TEAM, $league, $season, $week);

    echo '<div class="w3-panel w3-pale-green w3-border">Team Report processed (flag 2).</div></div>';
}

// Roster — <BK.Roster><L>
function _cp_process_roster(PDO $conn, int $upload_id, string $files_dir) : void {
    echo '<div class="w3-card w3-padding w3-white w3-margin-top"><div class="w3-text-indigo"><b>Roster</b></div>';

    [$urow, $filename, $raw] = _cp_load_upload_and_file($conn, $upload_id, $files_dir);
    if ($raw === null) { echo '</div>'; return; }

    $section = _cp_extract_section($raw, '<BK.Roster><L>');
    if ($section === null) { echo '<div class="w3-panel w3-amber w3-border">Roster section not found.</div></div>'; return; }

    // TODO: parse & write…

    [$league, $season, $week] = _cp_parse_lsw_from_filename($filename);
    _cp_mark_processed_flag($conn, $upload_id, _CP_FLAG_ROSTER, $league, $season, $week);

    echo '<div class="w3-panel w3-pale-green w3-border">Roster processed (flag 4).</div></div>';
}

// Play by Play — <ST.118><C><B>1st Quarter<L.54.1>
function _cp_process_play_by_play(PDO $conn, int $upload_id, string $files_dir) : void {
    echo '<div class="w3-card w3-padding w3-white w3-margin-top"><div class="w3-text-indigo"><b>Play by Play</b></div>';

    [$urow, $filename, $raw] = _cp_load_upload_and_file($conn, $upload_id, $files_dir);
    if ($raw === null) { echo '</div>'; return; }

    $section = _cp_extract_section($raw, '<ST.118><C><B>1st Quarter<L.54.1>');
    if ($section === null) { echo '<div class="w3-panel w3-amber w3-border">Play by Play section not found.</div></div>'; return; }

    // TODO: parse & write…

    [$league, $season, $week] = _cp_parse_lsw_from_filename($filename);
    _cp_mark_processed_flag($conn, $upload_id, _CP_FLAG_PBP, $league, $season, $week);

    echo '<div class="w3-panel w3-pale-green w3-border">Play by Play processed (flag 8).</div></div>';
}

// Head to Head — <E><ST.66><L>
function _cp_process_head_to_head(PDO $conn, int $upload_id, string $files_dir) : void {
    echo '<div class="w3-card w3-padding w3-white w3-margin-top"><div class="w3-text-indigo"><b>Head to Head</b></div>';

    [$urow, $filename, $raw] = _cp_load_upload_and_file($conn, $upload_id, $files_dir);
    if ($raw === null) { echo '</div>'; return; }

    $section = _cp_extract_section($raw, '<E><ST.66><L>');
    if ($section === null) { echo '<div class="w3-panel w3-amber w3-border">Head to Head section not found.</div></div>'; return; }

    // TODO: parse & write…

    [$league, $season, $week] = _cp_parse_lsw_from_filename($filename);
    _cp_mark_processed_flag($conn, $upload_id, _CP_FLAG_H2H, $league, $season, $week);

    echo '<div class="w3-panel w3-pale-green w3-border">Head to Head processed (flag 16).</div></div>';
}

// Standings — <B><BK.Standings><L>
function _cp_process_standings(PDO $conn, int $upload_id, string $files_dir) : void {
    echo '<div class="w3-card w3-padding w3-white w3-margin-top"><div class="w3-text-indigo"><b>Standings</b></div>';

    [$urow, $filename, $raw] = _cp_load_upload_and_file($conn, $upload_id, $files_dir);
    if ($raw === null) { echo '</div>'; return; }

    $section = _cp_extract_section($raw, '<B><BK.Standings><L>');
    if ($section === null) { echo '<div class="w3-panel w3-amber w3-border">Standings section not found.</div></div>'; return; }

    // TODO: parse & write…

    [$league, $season, $week] = _cp_parse_lsw_from_filename($filename);
    _cp_mark_processed_flag($conn, $upload_id, _CP_FLAG_STANDINGS, $league, $season, $week);

    echo '<div class="w3-panel w3-pale-green w3-border">Standings processed (flag 32).</div></div>';
}

// Scouting Report — <BK.Scouting Report><L>
function _cp_process_scouting_report(PDO $conn, int $upload_id, string $files_dir) : void {
    echo '<div class="w3-card w3-padding w3-white w3-margin-top"><div class="w3-text-indigo"><b>Scouting Report</b></div>';

    [$urow, $filename, $raw] = _cp_load_upload_and_file($conn, $upload_id, $files_dir);
    if ($raw === null) { echo '</div>'; return; }

    $section = _cp_extract_section($raw, '<BK.Scouting Report><L>');
    if ($section === null) { echo '<div class="w3-panel w3-amber w3-border">Scouting Report section not found.</div></div>'; return; }

    // TODO: parse & write…

    [$league, $season, $week] = _cp_parse_lsw_from_filename($filename);
    _cp_mark_processed_flag($conn, $upload_id, _CP_FLAG_SCOUT, $league, $season, $week);

    echo '<div class="w3-panel w3-pale-green w3-border">Scouting Report processed (flag 64).</div></div>';
}
