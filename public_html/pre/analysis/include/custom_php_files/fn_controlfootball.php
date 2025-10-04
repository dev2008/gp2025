<?php
if(!defined('custom_page_from_inclusion')) { die(); }

// Strongly prefer explicit path to avoid include_path ambiguity
$__cp_lr_path = __DIR__ . '/fn_leaguereport.php';

$_cp_title      = "League Report Control";
$_cp_action     = isset($_POST['_cp_action']) ? $_POST['_cp_action'] : '';
$_cp_upload_id  = isset($_POST['_cp_upload_id']) ? (int)$_POST['_cp_upload_id'] : 0;

// DaDaBIK exposes $upload_directory — show it so we know it's set
$_cp_files_dir  = isset($upload_directory) ? $upload_directory : null;

echo '<div class="w3-container w3-padding">';
echo '<h3 class="w3-text-black">'.htmlspecialchars($_cp_title).'</h3>';

// always show environment hints
echo '<div class="w3-small w3-text-grey">';
echo 'upload_directory: <code>'.htmlspecialchars((string)$_cp_files_dir).'</code><br>';
echo 'leaguereport path: <code>'.htmlspecialchars($__cp_lr_path).'</code>';
echo '</div>';

if ($_cp_action === 'process' && $_cp_upload_id > 0) {
    if (!is_file($__cp_lr_path)) {
        echo '<div class="w3-panel w3-red"><b>Error:</b> Cannot find fn_leaguereport.php at '
            .htmlspecialchars($__cp_lr_path).'</div></div>';
        return;
    }
    require_once $__cp_lr_path;

    if (!function_exists('_cp_process_league_report')) {
        echo '<div class="w3-panel w3-red"><b>Error:</b> _cp_process_league_report() not found after include.</div></div>';
        return;
    }

    echo '<div class="w3-panel w3-pale-blue w3-border">Processing upload_id '
        .(int)$_cp_upload_id.' …</div>';

    _cp_process_league_report($conn, $_cp_upload_id, $_cp_files_dir);
    echo '</div>';
    return;
}

try {
    // ensure PDO throws exceptions (so catch shows a panel)
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
        SELECT `upload_id`, `filename`, `league`, `season`, `week`, `mytimestamp`, `processed`
        FROM `g_uploads`
        WHERE `filename` LIKE '%NFL%' OR `filename` LIKE '%NCAA%'
        ORDER BY `mytimestamp` DESC, `upload_id` DESC
        LIMIT 200
    ";
    $st = $conn->prepare($sql);
    $ok = $st->execute();

    if (!$ok) {
        echo '<div class="w3-panel w3-red"><b>DB Error:</b> execute() returned false.</div></div>';
        return;
    }

    $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    $count = is_array($rows) ? count($rows) : 0;

    echo '<div class="w3-panel w3-pale-green w3-border">Query returned <b>'.$count.'</b> row(s).</div>';

    if ($count === 0) {
        echo '<div class="w3-panel w3-pale-yellow w3-border">No matching uploads found for LIKE %NFL% or %NCAA%.</div>';
        echo '</div>';
        return;
    }

    echo '<form method="post" class="w3-margin-top">';
    echo '<input type="hidden" name="_cp_action" value="process">';
    echo '<div class="w3-responsive">';
    echo '<table class="w3-table w3-bordered w3-small">';
    echo '<tr class="w3-light-grey"><th></th><th>Upload ID</th><th>Filename</th><th>League</th><th>Season</th><th>Week</th><th>Timestamp</th><th>Processed</th></tr>';

    foreach ($rows as $r) {
        echo '<tr>';
        echo '<td><input class="w3-radio" type="radio" name="_cp_upload_id" value="'.(int)$r['upload_id'].'"></td>';
        echo '<td>'.(int)$r['upload_id'].'</td>';
        echo '<td>'.htmlspecialchars($r['filename']).'</td>';
        echo '<td>'.htmlspecialchars((string)$r['league']).'</td>';
        echo '<td>'.htmlspecialchars((string)$r['season']).'</td>';
        echo '<td>'.htmlspecialchars((string)$r['week']).'</td>';
        echo '<td>'.htmlspecialchars($r['mytimestamp']).'</td>';
        echo '<td>'.htmlspecialchars((string)$r['processed']).'</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';
    echo '<div class="w3-margin-top">';
    echo '<button class="w3-button w3-blue">Process League Report</button>';
    echo '</div>';
    echo '</form>';

} catch (Exception $e) {
    echo '<div class="w3-panel w3-red"><b>DB Exception:</b> '.htmlspecialchars($e->getMessage()).'</div>';
}

echo '</div>';
