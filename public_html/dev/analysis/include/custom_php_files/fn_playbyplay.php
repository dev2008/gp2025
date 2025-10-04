<?php
if(!defined('custom_page_from_inclusion')) { die(); }
require_once __DIR__ . '/fn_sections_bootstrap.php';

function _cp_process_play_by_play(PDO $conn, int $upload_id, string $files_dir) : void {
    echo '<div class="w3-card w3-padding w3-white w3-margin-top"><div class="w3-text-indigo"><b>Play by Play</b></div>';
    [$urow, $filename, $raw] = _cp_load_upload_and_file($conn, $upload_id, $files_dir);
    if ($raw === null) { echo '</div>'; return; }

    $start = '<ST.118><C><B>1st Quarter<L.54.1>';
    $section = _cp_extract_section($raw, $start);
    if ($section === null) { echo '<div class="w3-panel w3-amber w3-border">Play by Play section not found.</div></div>'; return; }

    // TODO: parse & write…

    [$league, $season, $week] = _cp_parse_lsw_from_filename($filename);
    _cp_mark_processed_flag($conn, $upload_id, _CP_FLAG_PBP, $league, $season, $week);
    echo '<div class="w3-panel w3-pale-green w3-border">Play by Play processed.</div></div>';
}
