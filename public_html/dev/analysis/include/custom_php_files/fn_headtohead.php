<?php
if(!defined('custom_page_from_inclusion')) { die(); }
require_once __DIR__ . '/fn_sections_bootstrap.php';

function _cp_process_head_to_head(PDO $conn, int $upload_id, string $files_dir) : void {
    echo '<div class="w3-card w3-padding w3-white w3-margin-top"><div class="w3-text-indigo"><b>Head to Head</b></div>';
    [$urow, $filename, $raw] = _cp_load_upload_and_file($conn, $upload_id, $files_dir);
    if ($raw === null) { echo '</div>'; return; }

    $section = _cp_extract_section($raw, '<E><ST.66><L>');
    if ($section === null) { echo '<div class="w3-panel w3-amber w3-border">Head to Head section not found.</div></div>'; return; }

    // TODO: parse & write…

    [$league, $season, $week] = _cp_parse_lsw_from_filename($filename);
    _cp_mark_processed_flag($conn, $upload_id, _CP_FLAG_H2H, $league, $season, $week);
    echo '<div class="w3-panel w3-pale-green w3-border">Head to Head processed.</div></div>';
}
