<?php
declare(strict_types=1);

/**
 * SUPER-LITE SSE TEST (no transactions, no prepared statements)
 * ------------------------------------------------------------
 * Params:
 *   league=ABC
 *   season_from=1989
 *   season_to=2033
 *
 * WARNING: This is for quick smoke-testing only. Do NOT use in production.
 */

// ---- 1) DB connection (fill these in) ----
$dsn  = "mysql:host=localhost;dbname=dev-gp;charset=utf8mb4";
$user = "dev-gp";
$pass = "EgpsSmr@m@LaarWd1eWtH2hde";
try {
    $conn = new PDO($dsn, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Throwable $e) {
    header("Content-Type: text/plain");
    http_response_code(500);
    echo "DB connection failed: " . $e->getMessage();
    exit;
}

// ---- 2) SSE headers / buffering ----
if (function_exists('apache_setenv')) { @apache_setenv('no-gzip','1'); }
@ini_set('zlib.output_compression','0');
@ini_set('output_buffering','off');
if (!headers_sent()) {
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('X-Accel-Buffering: no'); // NGINX
}
while (ob_get_level() > 0) { @ob_end_flush(); }
@ob_implicit_flush(true);
function sse(string $msg, string $event='message'): void {
    echo "event: {$event}\n";
    echo "data: " . str_replace("\n", "\\n", $msg) . "\n\n";
    @flush();
}

// ---- 3) Read params (very light validation) ----
$league     = isset($_GET['league']) ? (string)$_GET['league'] : '';
$seasonFrom = isset($_GET['season_from']) ? (int)$_GET['season_from'] : 1989;
$seasonTo   = isset($_GET['season_to'])   ? (int)$_GET['season_to']   : 2033;
if ($league === '') { sse('Missing ?league', 'error'); exit; }
if ($seasonFrom > $seasonTo) { [$seasonFrom, $seasonTo] = [$seasonTo, $seasonFrom]; }

$total = $seasonTo - $seasonFrom + 1;
$count = 0;
sse("Starting league {$league}, {$seasonFrom}–{$seasonTo} ({$total} seasons)", 'start');

// ---- 4) Loop seasons (RAW SQL for smoke test) ----
for ($season = $seasonFrom; $season <= $seasonTo; $season++) {
    try {
        // Find winner (expect 0..1 rows)
        $sqlFind = "SELECT `franchise`,`team` FROM `f_games`
                    WHERE `win`=1 AND `gametype`=36
                      AND `league`='".addslashes($league)."'
                      AND `season`=".$season."
                    LIMIT 1";
        $winnerRow = $conn->query($sqlFind)->fetch(PDO::FETCH_NUM);

        if ($winnerRow) {
            $franchise = (int)$winnerRow[0];
            $team      = (string)$winnerRow[1];
            $yearstr   = $season . ' ';

            // RAW updates (no transactions, no prepared statements)
            $conn->exec("UPDATE `fp_franchises` SET `Winner`=COALESCE(`Winner`,0)+1 WHERE `franchise`={$franchise}");
            $conn->exec("UPDATE `fp_franchises` SET `WinnerYears`=CONCAT(COALESCE(`WinnerYears`,''), '{$yearstr}') WHERE `franchise`={$franchise}");
            $conn->exec("UPDATE `fp_franchises` SET `ConferenceYears`=CONCAT(COALESCE(`ConferenceYears`,''), '{$yearstr}') WHERE `franchise`={$franchise}");
            $conn->exec("UPDATE `fp_franchises` SET `ChampionshipW`=COALESCE(`ChampionshipW`,0)+1 WHERE `franchise`={$franchise}");

            $msg = "Season {$season}: Winner {$team}";
        } else {
            $msg = "Season {$season}: No winner found";
        }

        $count++;
        sse("Processed {$count}/{$total} — {$msg}");
    } catch (Throwable $e) {
        sse("Error in season {$season}: " . $e->getMessage(), 'error');
    }

    // Small delay so you can see the streaming clearly (optional)
    usleep(200000); // 0.2s
}

sse('Done ✅', 'done');
