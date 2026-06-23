<?php
/*
 * Gameplan Situation Analyser
 * Analyses offensive play sequences per team/coach grouped by formation and down
 * to infer gameplan situations and reaction types with confidence scores.
 *
 * Reaction types tested (in priority order):
 *   S - Sequence: fixed cycle A->B->C->D->A, resets each game
 *   R - Rotate: repeat until fail, then next in list
 *   H - Highest: always call current highest-weighted play (one play dominates)
 *   A - Alternate: rotate then sequence alternating (max 2 repeats before moving on)
 *   E - Even: all plays start equal weight, drifts with results
 *
 * Numeric reactions (0-4) deferred - require weight modelling.
 *
 * A play is GOOD (success) if:
 *   - First down gained, OR
 *   - Touchdown scored, OR
 *   - Gained >= 4 yards with no turnover and did not set up 4th down
 *
 * DaDaBIK custom page - must be included via DaDaBIK, not called directly.
 */

if(!defined('custom_page_from_inclusion')) { die(); }

// ============================================================
// CONFIGURATION
// ============================================================
define('GP_TABLE',       'n_playbyplay');
define('GP_OFFENCE',     'a_off');
define('GP_OFF_COACH',   'n_offcoach');
define('GP_SEASON',      'a_season');
define('GP_WEEK',        'a_week');
define('GP_MINUTES',     'a_minutes');
define('GP_SECONDS',     'a_seconds');
define('GP_DOWN',        'a_down');
define('GP_DISTANCE',    'a_distance');
define('GP_FIELD',       'a_field');
define('GP_FORMATION',   'a_form');
define('GP_PLAY',        'a_ocall');
define('GP_PLAY_TYPE',   'a_playtype');
define('GP_LEAGUE',      'a_league');
define('GP_YARDS',       'a_yards');
define('GP_FIRST',       'a_first');
define('GP_TD',          'a_td');
define('GP_FUMBLE',      'a_fumble');
define('GP_INT',         'a_intercept');
define('GP_DEF_CALL',    'a_dcall');

// Minimum plays before attempting analysis
define('GP_MIN_PLAYS', 10);

// ============================================================
// INPUT HANDLING
// ============================================================
$_cp_team       = isset($_GET['_cp_team'])       ? trim($_GET['_cp_team'])       : '';
$_cp_league     = isset($_GET['_cp_league'])     ? trim($_GET['_cp_league'])     : '';
$_cp_season_from = isset($_GET['_cp_season_from']) ? (int)$_GET['_cp_season_from'] : 2018;
$_cp_min_conf   = isset($_GET['_cp_min_conf'])   ? (int)$_GET['_cp_min_conf']   : 80;

// ============================================================
// SUCCESS / FAILURE
// ============================================================

/**
 * Determine if a play was "good" per Gameplan rules:
 *   good = first down OR touchdown OR (>=4 yards AND no turnover AND not set up 4th down)
 */
function _cp_is_good($row) {
    if ((int)$row[GP_FIRST]  === 1) return true;
    if ((int)$row[GP_TD]     === 1) return true;
    if ((int)$row[GP_FUMBLE] === 1) return false;
    if ((int)$row[GP_INT]    === 1) return false;
    // Setting up 4th down = on 3rd and didn't convert
    if ((int)$row[GP_DOWN] === 3 && (int)$row[GP_YARDS] < (int)$row[GP_DISTANCE]) return false;
    return (int)$row[GP_YARDS] >= 4;
}

// ============================================================
// CYCLE EXTRACTION
// ============================================================

/**
 * Find shortest repeating cycle (length 1-4) explaining >= 90% of a play sequence.
 * Falls back to unique plays in order of first appearance.
 *
 * Note: the same play can legitimately appear multiple times in a Gameplan
 * situation list (e.g. OP, SC, LO, SC is a valid 4-play situation where SC
 * appears twice). Maximum 4 plays per situation per Gameplan rules.
 */
function _cp_extract_cycle($plays) {
    $n = count($plays);
    if ($n === 0) return [];

    for ($len = 1; $len <= 4; $len++) {
        $candidate = array_slice($plays, 0, $len);
        $matches   = 0;
        for ($i = 0; $i < $n; $i++) {
            if ($plays[$i] === $candidate[$i % $len]) $matches++;
        }
        if (($matches / $n) >= 0.90) return $candidate;
    }

    // Fallback: unique plays in first-seen order, capped at 4
    $seen = $result = [];
    foreach ($plays as $p) {
        if (!in_array($p, $seen)) {
            $seen[]   = $p;
            $result[] = $p;
            if (count($result) === 4) break;
        }
    }
    return $result;
}

// ============================================================
// REACTION TESTS
// Each returns ['reaction', 'plays', 'confidence', 'total_plays', 'notes'] or null.
// game_plays  = [ game_key => [play, play, ...] ]           (play strings only)
// game_result = [ game_key => [['play'=>, 'good'=>], ...] ] (with success flag)
// ============================================================

/**
 * S - Sequence: A->B->C->D->A, hard reset to A at start of each game.
 */
function _cp_test_S($game_plays) {
    $first     = reset($game_plays);
    $candidate = _cp_extract_cycle($first);
    if (empty($candidate)) return null;

    $total = $correct = 0;
    $notes = [];

    foreach ($game_plays as $gkey => $plays) {
        $pos = 0;
        foreach ($plays as $play) {
            $expected = $candidate[$pos % count($candidate)];
            if ($play === $expected) {
                $correct++;
            } else {
                $notes[] = "$gkey pos$pos: expected $expected got $play";
                $found   = array_search($play, $candidate);
                if ($found !== false) $pos = $found;
            }
            $pos++;
            $total++;
        }
    }

    return [
        'reaction'    => 'S',
        'plays'       => $candidate,
        'confidence'  => $total > 0 ? round($correct / $total * 100) : 0,
        'total_plays' => $total,
        'notes'       => $notes,
    ];
}

/**
 * R - Rotate: repeat current play until it fails, then advance to next in list.
 * Resets to first play at start of each game.
 */
function _cp_test_R($game_results) {
    $all_plays = [];
    foreach ($game_results as $rows) {
        foreach ($rows as $row) $all_plays[] = $row['play'];
    }
    $candidate = _cp_extract_cycle($all_plays);
    if (count($candidate) < 2) return null;

    $total = $correct = 0;
    $notes = [];

    foreach ($game_results as $gkey => $rows) {
        $pos = 0;
        foreach ($rows as $row) {
            $expected = $candidate[$pos % count($candidate)];
            $actual   = $row['play'];
            if ($actual === $expected) {
                $correct++;
                // Only rotate on failure
                if (!$row['good']) $pos++;
            } else {
                $notes[] = "$gkey: expected $expected got $actual";
                $found   = array_search($actual, $candidate);
                if ($found !== false) $pos = $found + ($row['good'] ? 0 : 1);
            }
            $total++;
        }
    }

    return [
        'reaction'    => 'R',
        'plays'       => $candidate,
        'confidence'  => $total > 0 ? round($correct / $total * 100) : 0,
        'total_plays' => $total,
        'notes'       => $notes,
    ];
}

/**
 * H - Highest: always call the play with the highest current weight.
 * Signature: one play appears in >= 60% of calls.
 */
function _cp_test_H($game_plays) {
    $counts = [];
    $total  = 0;
    foreach ($game_plays as $plays) {
        foreach ($plays as $p) {
            $counts[$p] = ($counts[$p] ?? 0) + 1;
            $total++;
        }
    }
    if ($total === 0) return null;

    arsort($counts);
    $top_play  = array_key_first($counts);
    $top_count = $counts[$top_play];
    $dominance = round($top_count / $total * 100);

    if ($dominance < 60) return null;

    return [
        'reaction'    => 'H',
        'plays'       => array_slice(array_keys($counts), 0, 4),
        'confidence'  => $dominance,
        'total_plays' => $total,
        'notes'       => ["Dominant play: $top_play ($top_count/$total calls)"],
    ];
}

/**
 * A - Alternate: sequence step then rotate step alternating.
 * Play called once (seq), then repeated once if successful (rotate), then next play.
 * Maximum 2 consecutive identical plays before forced advance.
 */
function _cp_test_A($game_results) {
    $all_plays = [];
    foreach ($game_results as $rows) {
        foreach ($rows as $row) $all_plays[] = $row['play'];
    }
    $candidate = _cp_extract_cycle($all_plays);
    if (count($candidate) < 2) return null;

    $total = $correct = 0;
    $notes = [];

    foreach ($game_results as $gkey => $rows) {
        $pos      = 0;
        $seq_step = true; // true=sequence step, false=rotate step

        foreach ($rows as $row) {
            $expected = $candidate[$pos % count($candidate)];
            $actual   = $row['play'];
            $good     = $row['good'];

            if ($actual === $expected) {
                $correct++;
                if ($seq_step) {
                    // After sequence step: rotate (repeat) only if good
                    $seq_step = $good ? false : true;
                    if (!$good) $pos++;
                } else {
                    // After rotate step: always advance
                    $pos++;
                    $seq_step = true;
                }
            } else {
                $notes[] = "$gkey: expected $expected got $actual";
                $found   = array_search($actual, $candidate);
                if ($found !== false) { $pos = $found; $seq_step = true; }
            }
            $total++;
        }
    }

    return [
        'reaction'    => 'A',
        'plays'       => $candidate,
        'confidence'  => $total > 0 ? round($correct / $total * 100) : 0,
        'total_plays' => $total,
        'notes'       => $notes,
    ];
}

/**
 * E - Even: all plays start with equal weight.
 * Signature: play distribution is roughly equal across all plays in the situation.
 */
function _cp_test_E($game_plays) {
    $counts = [];
    $total  = 0;
    foreach ($game_plays as $plays) {
        foreach ($plays as $p) {
            $counts[$p] = ($counts[$p] ?? 0) + 1;
            $total++;
        }
    }
    if ($total === 0 || count($counts) < 2) return null;

    // Only consider top 4 plays — Gameplan situations hold a maximum of 4
    arsort($counts);
    $counts   = array_slice($counts, 0, 4, true);
    $n_plays  = count($counts);
    $expected = $total / $n_plays;
    $deviation = 0;
    foreach ($counts as $c) {
        $deviation += abs($c - $expected) / $expected;
    }
    $evenness = max(0, round((1 - ($deviation / $n_plays)) * 100));

    if ($evenness < 60) return null;

    return [
        'reaction'    => 'E',
        'plays'       => array_keys($counts),
        'confidence'  => $evenness,
        'total_plays' => $total,
        'notes'       => ['Play distribution: ' . implode(', ', array_map(
            fn($p, $c) => "$p:$c",
            array_keys($counts),
            array_values($counts)
        ))],
    ];
}

// ============================================================
// DATABASE FUNCTIONS
// ============================================================

function _cp_get_leagues($conn) {
    $stmt = $conn->prepare('SELECT DISTINCT ' . GP_LEAGUE . ' FROM ' . GP_TABLE . ' ORDER BY ' . GP_LEAGUE);
    $stmt->execute();
    return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), GP_LEAGUE);
}

function _cp_get_teams($conn, $league_filter) {
    $sql    = 'SELECT DISTINCT ' . GP_OFFENCE . ', ' . GP_LEAGUE . ', ' . GP_OFF_COACH . ' FROM ' . GP_TABLE;
    $params = [];
    if ($league_filter !== '') { $sql .= ' WHERE ' . GP_LEAGUE . ' = ?'; $params[] = $league_filter; }
    $sql .= ' ORDER BY ' . GP_LEAGUE . ', ' . GP_OFFENCE;
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function _cp_get_plays($conn, $team, $league_filter, $season_from) {
    $sql = 'SELECT '
         . GP_SEASON . ', ' . GP_WEEK     . ', ' . GP_MINUTES  . ', ' . GP_SECONDS . ', '
         . GP_DOWN   . ', ' . GP_DISTANCE . ', ' . GP_FIELD    . ', ' . GP_FORMATION . ', '
         . GP_PLAY   . ', ' . GP_PLAY_TYPE . ', ' . GP_YARDS   . ', ' . GP_FIRST . ', '
         . GP_TD     . ', ' . GP_FUMBLE   . ', ' . GP_INT      . ', ' . GP_DEF_CALL
         . ' FROM '  . GP_TABLE . ' WHERE ' . GP_OFFENCE . ' = ?';
    $params = [$team];
    if ($league_filter !== '') { $sql .= ' AND ' . GP_LEAGUE . ' = ?'; $params[] = $league_filter; }

    // Season greater than or equal filter
    $sql .= ' AND ' . GP_SEASON . ' >= ?';
    $params[] = $season_from;

    // Exclude special teams formations - P (Punt), F (Field Goal), X (Kickoff)
    $sql .= ' AND ' . GP_FORMATION . ' NOT IN (?,?,?)';
    $params[] = 'P';
    $params[] = 'F';
    $params[] = 'X';

    // Exclude game engine overrides - these are not gameplan plays:
    //   1. Late game last-ditch/clock situations: defence always forced to WC at minute 59+
    //   2. End of half long bomb: DL called with WC defence at minute 29 or 59
    $sql .= ' AND NOT (' . GP_DEF_CALL . ' = ? AND ' . GP_MINUTES . ' >= ?)';
    $params[] = 'WC';
    $params[] = 59;
    $sql .= ' AND NOT (' . GP_PLAY . ' = ? AND ' . GP_DEF_CALL . ' = ? AND ' . GP_MINUTES . ' IN (29,59))';
    $params[] = 'DL';
    $params[] = 'WC';

    $sql .= ' ORDER BY ' . GP_SEASON . ', ' . GP_WEEK . ', ' . GP_MINUTES . ', ' . GP_SECONDS;
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ============================================================
// MAIN ANALYSIS
// ============================================================

function _cp_analyse($plays) {
    // Build parallel structures per formation+down group
    $groups_plays  = []; // game_key => [play, ...]
    $groups_result = []; // game_key => [{play, good}, ...]

    foreach ($plays as $row) {
        $fm   = $row[GP_FORMATION];
        $down = $row[GP_DOWN];
        $gkey = $row[GP_SEASON] . '_W' . $row[GP_WEEK];

        $groups_plays[$fm][$down][$gkey][]  = $row[GP_PLAY];
        $groups_result[$fm][$down][$gkey][] = [
            'play' => $row[GP_PLAY],
            'good' => _cp_is_good($row),
        ];
    }

    $results = [];

    foreach ($groups_plays as $fm => $downs) {
        foreach ($downs as $down => $game_plays) {
            $total = array_sum(array_map('count', $game_plays));
            if ($total < GP_MIN_PLAYS) continue;

            $game_results = $groups_result[$fm][$down];

            // Test all reaction types, pick highest confidence
            $candidates = array_filter([
                _cp_test_S($game_plays),
                _cp_test_R($game_results),
                _cp_test_H($game_plays),
                _cp_test_A($game_results),
                _cp_test_E($game_plays),
            ]);

            if (empty($candidates)) continue;

            usort($candidates, fn($a, $b) => $b['confidence'] <=> $a['confidence']);
            $best = $candidates[0];

            $results[] = [
                'formation'   => $fm,
                'down'        => $down,
                'reaction'    => $best['reaction'],
                'plays'       => $best['plays'],
                'confidence'  => $best['confidence'],
                'total_plays' => $best['total_plays'],
                'games'       => count($game_plays),
                'notes'       => $best['notes'],
                'all_scores'  => array_map(
                    fn($c) => $c['reaction'] . ':' . $c['confidence'] . '%',
                    $candidates
                ),
            ];
        }
    }

    usort($results, function($a, $b) {
        if ($a['formation'] !== $b['formation']) return strcmp($a['formation'], $b['formation']);
        return $a['down'] <=> $b['down'];
    });

    return $results;
}

// ============================================================
// DISPLAY HELPERS
// ============================================================

function _cp_conf_class($conf) {
    if ($conf >= 95) return 'w3-green';
    if ($conf >= 80) return 'w3-lime';
    if ($conf >= 60) return 'w3-yellow';
    return 'w3-orange';
}

function _cp_defence_hint($reaction, $conf) {
    if ($reaction === 'S' && $conf >= 95)
        return ['w3-text-green', 'Mirror with S reaction defence — play predicted exactly'];
    if ($reaction === 'R' && $conf >= 90)
        return ['w3-text-green', 'R confirmed — key first play, rotate your defence after a stuff'];
    if ($reaction === 'H' && $conf >= 80)
        return ['w3-text-orange', 'H reaction — key heavily on the dominant play'];
    if ($reaction === 'A' && $conf >= 80)
        return ['w3-text-orange', 'A reaction — expect pairs of same play before rotating'];
    if ($reaction === 'E' && $conf >= 70)
        return ['w3-text-grey', 'E reaction — no strong prediction, cover most dangerous play'];
    if ($conf >= 80)
        return ['w3-text-orange', 'Strong tendency — key accordingly'];
    return ['w3-text-grey', 'Observed tendency — use with caution'];
}

// ============================================================
// PAGE
// ============================================================
$_cp_leagues = _cp_get_leagues($conn);
$_cp_teams   = _cp_get_teams($conn, $_cp_league);
$_cp_results = [];
$_cp_coach   = '';

if ($_cp_team !== '') {
    $_cp_raw = _cp_get_plays($conn, $_cp_team, $_cp_league, $_cp_season_from);
    if (!empty($_cp_raw)) {
        foreach ($_cp_teams as $t) {
            if ($t[GP_OFFENCE] === $_cp_team) { $_cp_coach = $t[GP_OFF_COACH]; break; }
        }
        $_cp_results = array_filter(
            _cp_analyse($_cp_raw),
            fn($r) => $r['confidence'] >= $_cp_min_conf
        );
    }
}

$_cp_down_labels = ['1' => '1st', '2' => '2nd', '3' => '3rd', '4' => '4th'];
?>

<div class="w3-container">
  <h2 class="w3-text-deep-purple">Gameplan Situation Analyser</h2>

  <div class="w3-card w3-padding w3-margin-bottom">
    <form method="get" action="index.php">
      <!-- Preserve DaDaBIK page context -->
      <input type="hidden" name="function"       value="show_static_page">
      <input type="hidden" name="id_static_page" value="52">
      <div class="w3-row-padding">

        <div class="w3-col m3">
          <label class="w3-text-grey"><b>League</b></label>
          <select name="_cp_league" class="w3-select w3-border" onchange="this.form.submit()">
            <option value="">— All leagues —</option>
            <?php foreach ($_cp_leagues as $l): ?>
              <option value="<?= htmlspecialchars($l) ?>" <?= $_cp_league === $l ? 'selected' : '' ?>>
                <?= htmlspecialchars($l) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="w3-col m3">
          <label class="w3-text-grey"><b>Team</b></label>
          <select name="_cp_team" class="w3-select w3-border">
            <option value="">— Select team —</option>
            <?php foreach ($_cp_teams as $t): ?>
              <option value="<?= htmlspecialchars($t[GP_OFFENCE]) ?>"
                      <?= $_cp_team === $t[GP_OFFENCE] ? 'selected' : '' ?>>
                <?= htmlspecialchars($t[GP_OFFENCE]) ?> (<?= htmlspecialchars($t[GP_OFF_COACH]) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="w3-col m2">
          <label class="w3-text-grey"><b>Season from</b></label>
          <input type="number" name="_cp_season_from" class="w3-input w3-border"
                 placeholder="2018" value="<?= (int)$_cp_season_from ?>">
        </div>

        <div class="w3-col m2">
          <label class="w3-text-grey"><b>Min confidence %</b></label>
          <input type="number" name="_cp_min_conf" class="w3-input w3-border"
                 min="0" max="100" value="<?= (int)$_cp_min_conf ?>">
        </div>

        <div class="w3-col m2" style="padding-top:1.5em">
          <button type="submit" class="w3-button w3-deep-purple w3-block">Analyse</button>
        </div>

      </div>
    </form>
  </div>

  <?php if ($_cp_team !== '' && empty($_cp_results)): ?>
    <div class="w3-panel w3-yellow">
      <p>No situations found for <b><?= htmlspecialchars($_cp_team) ?></b>
         at &ge;<?= (int)$_cp_min_conf ?>% confidence. Try lowering the threshold.</p>
    </div>

  <?php elseif (!empty($_cp_results)): ?>

    <h3 class="w3-text-deep-purple">
      <?= htmlspecialchars($_cp_team) ?>
      <?php if ($_cp_coach): ?>&mdash; <?= htmlspecialchars($_cp_coach) ?><?php endif; ?>
      (Season &ge; <?= (int)$_cp_season_from ?>)
    </h3>
    <p class="w3-text-grey">
      Situations with &ge;<?= (int)$_cp_min_conf ?>% confidence, ordered by formation then down.
    </p>

    <table class="w3-table w3-striped w3-bordered w3-hoverable" style="width:100%">
      <thead class="w3-deep-purple w3-text-white">
        <tr>
          <th>Fm</th>
          <th>Down</th>
          <th>Inferred situation</th>
          <th>Reaction</th>
          <th>Plays</th>
          <th>Games</th>
          <th>Confidence</th>
          <th>Defensive action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($_cp_results as $r):
          $down_label = $_cp_down_labels[$r['down']] ?? $r['down'] . 'th';
          // Build play slots - pad to 4 with -- for empty slots, reaction at end
          $slots = array_pad($r['plays'], 4, '--');
          $sit_str = $r['formation'] . '  ' . implode(' ', $slots) . '  ' . $r['reaction'];
          [$hint_class, $hint_text] = _cp_defence_hint($r['reaction'], $r['confidence']);
        ?>
        <tr>
          <td><b><?= htmlspecialchars($r['formation']) ?></b></td>
          <td><?= htmlspecialchars($down_label) ?></td>
          <td><code><?= htmlspecialchars($sit_str) ?></code></td>
          <td><b><?= htmlspecialchars($r['reaction']) ?></b></td>
          <td><?= (int)$r['total_plays'] ?></td>
          <td><?= (int)$r['games'] ?></td>
          <td>
            <span class="w3-tag <?= _cp_conf_class($r['confidence']) ?> w3-round">
              <?= (int)$r['confidence'] ?>%
            </span>
          </td>
          <td class="<?= $hint_class ?> w3-small"><?= htmlspecialchars($hint_text) ?></td>
        </tr>
        <tr class="w3-light-grey">
          <td colspan="8" style="font-size:0.8em;color:#555;padding-left:2em">
            All scores: <?= htmlspecialchars(implode(' &nbsp;|&nbsp; ', $r['all_scores'])) ?>
            <?php if (!empty($r['notes'])): ?>
              &nbsp;&mdash;&nbsp;<?= (int)count($r['notes']) ?> anomaly/anomalies:
              <?= htmlspecialchars(implode(' | ', array_slice($r['notes'], 0, 2))) ?>
              <?= count($r['notes']) > 2 ? '&hellip;' : '' ?>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="w3-panel w3-light-grey w3-small w3-margin-top">
      <p>
        <b>S</b> Sequence &mdash; fixed cycle, resets each game. Mirror with S defence for exact prediction.<br>
        <b>R</b> Rotate &mdash; repeats until failure, then next play. Key on first play, rotate after a stuff.<br>
        <b>H</b> Highest &mdash; always calls the current best play. Key heavily on the dominant call.<br>
        <b>A</b> Alternate &mdash; up to two repeats before rotating. Expect pairs before the play changes.<br>
        <b>E</b> Even &mdash; equal starting weights. Cover the most dangerous play.<br>
        <b>Confidence</b> = % of plays consistent with the inferred reaction model across all games.<br>
        <b>Filtered out:</b> game engine overrides — late game last-ditch plays (defence WC, minute &ge;59)
        and end-of-half long bombs (DL with WC defence at minutes 29 or 59) are excluded as they are not gameplan calls.
      </p>
    </div>

  <?php else: ?>
    <div class="w3-panel w3-light-blue">
      <p>Select a league and team above to begin analysis.</p>
    </div>
  <?php endif; ?>

</div>
