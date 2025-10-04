<?php
if(!defined('custom_page_from_inclusion')) { die(); }

// Set this to true to enable verbose debug output across all modules
define('_CP_DEBUG', false);

/** -------- Bitmask flags -------- */
define('_CP_FLAG_LEAGUE',     1);   // League Report
define('_CP_FLAG_TEAM',       2);   // Team Report
define('_CP_FLAG_ROSTER',     4);   // Roster
define('_CP_FLAG_PBP',        8);   // Play by Play
define('_CP_FLAG_H2H',       16);   // Head to Head
define('_CP_FLAG_STANDINGS', 32);   // Standings
define('_CP_FLAG_SCOUT',     64);   // Scouting

/** has flag? */
function _cp_has_flag(int $processed, int $flag): bool {
    return (($processed & $flag) === $flag);
}

/** OR-in a flag; optionally set league/season/week */
function _cp_mark_processed_flag(PDO $conn, int $upload_id, int $flag, ?string $league=null, ?int $season=null, ?int $week=null) : void {
    if ($league !== null && $season !== null && $week !== null) {
        $st = $conn->prepare("
            UPDATE g_uploads
               SET league=:l, season=:s, week=:w,
                   processed = COALESCE(processed,0) | :f
             WHERE upload_id=:id
             LIMIT 1
        ");
        $st->execute([':l'=>$league, ':s'=>$season, ':w'=>$week, ':f'=>$flag, ':id'=>$upload_id]);
    } else {
        $st = $conn->prepare("
            UPDATE g_uploads
               SET processed = COALESCE(processed,0) | :f
             WHERE upload_id=:id
             LIMIT 1
        ");
        $st->execute([':f'=>$flag, ':id'=>$upload_id]);
    }
}

/** Load upload row + file text */
function _cp_load_upload_and_file(PDO $conn, int $upload_id, string $files_dir) : array {
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $st = $conn->prepare("SELECT * FROM g_uploads WHERE upload_id=:id LIMIT 1");
    $st->execute([':id'=>$upload_id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if (!$row) { echo '<div class="w3-panel w3-red">Upload not found.</div>'; return [null, null, null]; }

    $filename = $row['filename'];
    $fullpath = rtrim($files_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename;
    if (!is_readable($fullpath)) {
        echo '<div class="w3-panel w3-red"><b>File not readable:</b> '.htmlspecialchars($fullpath).'</div>';
        return [$row, $filename, null];
    }
    $raw = file_get_contents($fullpath);
    if ($raw === false || $raw === '') { echo '<div class="w3-panel w3-red"><b>Error reading file.</b></div>'; return [$row,$filename,null]; }
    $raw = str_replace(["\r\n","\r"], "\n", $raw);
    return [$row, $filename, $raw];
}

/** Extract a section via start marker (ends on <E><P> by default) */
function _cp_extract_section(string $raw, string $startMarker, string $endMarker = '<E><P>') : ?string {
    $pos = strpos($raw, $startMarker);
    if ($pos === false) return null;
    $pos += strlen($startMarker);
    $end = strpos($raw, $endMarker, $pos);
    if ($end === false) $end = strlen($raw);
    return substr($raw, $pos, $end - $pos);
}

/** Parse L/S/W from filenames like NFLZZ-PE_s2033_w14_vs_*.txt */
function _cp_parse_lsw_from_filename(string $filename) : array {
    $league = $season = $week = null;
    if (preg_match('/^(?P<league>[A-Z]+)-[A-Z]+_s(?P<season>\d{4})_w(?P<week>\d{1,2})_/i', $filename, $m)) {
        $league = strtoupper($m['league']); $season=(int)$m['season']; $week=(int)$m['week'];
    }
    return [$league, $season, $week];
}
