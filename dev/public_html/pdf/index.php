<?php
/*
 * Gameplan PBM Report -> PDF Converter  (standalone)
 * ====================================================
 * Place fpdf.php (from http://www.fpdf.org/) and the font/ folder
 * in the same directory as this file, then open in a browser.
 *
 * MARKUP UNDERSTOOD:
 *   <B>          bold on (heading block -- persists across lines until <C> on its own line)
 *   <C>          bold + underline off
 *   <Z>          within-line bold/highlight (resets at end of each line)
 *   <U>          underline on
 *   <UC>         underline off
 *   <T>          tab / column separator
 *   <L>          newline (single)
 *   <L.n.1>      end-of-line with spacing hint:
 *                  n < 10  -> small gap (0.5 lines extra)
 *                  n = 30  -> continuation indent (no extra gap)
 *                  n = 33  -> normal line ending (no extra gap)
 *                  n >= 42 -> after heading (1 extra blank line)
 *   <P>  <P.n>   page break
 *   <D>  <E>     section separator (page break)
 *   <DU>         ignored
 *   <BK.name>    block start -> page break + ruled section heading
 *   <ST.n...>    tab stop definitions (ignored)
 *   <STARTREP>   start of report body (everything before this is stripped)
 */

$_gpdf_fpdf = __DIR__ . '/fpdf.php';

// ===============================================================================
//  PDF GENERATION  (POST request)
// ===============================================================================
if (isset($_POST['_gpdf_action']) && $_POST['_gpdf_action'] === 'go') {

    $raw = '';
    if (!empty($_FILES['_gpdf_file']['tmp_name'])) {
        $raw = file_get_contents($_FILES['_gpdf_file']['tmp_name']);
    } elseif (!empty($_POST['_gpdf_text'])) {
        $raw = $_POST['_gpdf_text'];
    }

    $orient = ($_POST['_gpdf_orient'] ?? 'P') === 'L' ? 'L' : 'P';
    $fsize  = max(5, min(10, (int)($_POST['_gpdf_fsize'] ?? 7)));

    if (trim($raw) === '') {
        $err = 'Please paste report text or upload a file.';
    } elseif (!file_exists($_gpdf_fpdf)) {
        $err = 'fpdf.php not found -- see setup instructions below.';
    } else {
        require_once $_gpdf_fpdf;

        // -- Parse -------------------------------------------------------------
        $parsed   = gpdf_parse($raw);
        $title    = gpdf_extract_title($parsed);
        $filename = gpdf_make_filename($raw);
        $lh       = round($fsize * 0.52, 2); // line height in mm

        // -- FPDF subclass -----------------------------------------------------
        class GPDF extends FPDF {
            public $hdr = '';
            function Header() {
                if ($this->hdr === '') return;
                $this->SetFont('Courier', '', 6);
                $this->SetTextColor(140, 140, 140);
                $this->Cell(0, 4, $this->hdr . '    p.' . $this->PageNo(), 0, 1, 'R');
                $this->SetTextColor(0, 0, 0);
                $this->SetFont('Courier', '', 7);
            }
            function Footer() {}
        }

        $pdf = new GPDF($orient, 'mm', 'A4');
        $pdf->hdr = $title;
        $pdf->SetTopMargin(10);
        $pdf->SetLeftMargin(7);
        $pdf->SetRightMargin(7);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();
        $pdf->SetFont('Courier', '', $fsize);

        // -- Render ------------------------------------------------------------
        foreach ($parsed as $item) {
            // Page break
            if ($item['type'] === 'page') {
                if ($pdf->GetY() > $pdf->GetPageHeight() * 0.25) $pdf->AddPage();
                continue;
            }
            // Section heading (from <BK.name>)
            if ($item['type'] === 'section') {
                if ($pdf->GetY() > $pdf->GetPageHeight() * 0.25) $pdf->AddPage();
                else $pdf->Ln($lh * 1.5);
                $bar = str_repeat('-', $orient === 'L' ? 130 : 90);
                $pdf->SetFont('Courier', '', $fsize - 1);
                $pdf->SetTextColor(100, 100, 100);
                $pdf->Cell(0, $lh, $bar, 0, 1);
                $pdf->SetFont('Courier', 'B', $fsize);
                $pdf->SetTextColor(0, 30, 120);
                $pdf->Cell(0, $lh * 1.4, '  ' . strtoupper($item['label']), 0, 1);
                $pdf->SetFont('Courier', '', $fsize - 1);
                $pdf->SetTextColor(100, 100, 100);
                $pdf->Cell(0, $lh, $bar, 0, 1);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Ln($lh * 0.5);
                continue;
            }
            // Extra blank lines
            if ($item['type'] === 'gap') {
                $pdf->Ln($lh * $item['lines']);
                continue;
            }
            // Normal blank line
            if ($item['type'] === 'blank') {
                $pdf->Ln($lh);
                continue;
            }
            // Text line: array of segments
            if ($item['type'] === 'line') {
                foreach ($item['segs'] as $s) {
                    $sty = ($s['bold'] ? 'B' : '') . ($s['under'] ? 'U' : '');
                    $pdf->SetFont('Courier', $sty, $fsize);
                    switch ($s['color']) {
                        case 'score':
                            $pdf->SetTextColor(180, 0, 0); break;
                        case 'head':
                            $pdf->SetTextColor(0, 30, 120); break;
                        default:
                            $pdf->SetTextColor(20, 20, 20); break;
                    }
                    $pdf->Write($lh, $s['text']);
                }
                $pdf->Ln($lh);
                // Extra spacing from <L.n.1>
                if (!empty($item['gap'])) $pdf->Ln($lh * $item['gap']);
            }
        }

        // Clear any buffered output before streaming PDF
        while (ob_get_level()) ob_end_clean();

        // Stream download
        $pdf->Output('D', $filename);
        exit;
    }
}

// ===============================================================================
//  PARSER
// ===============================================================================

/**
 * Parse Gameplan markup into a flat list of render items:
 *   ['type'=>'page']
 *   ['type'=>'section', 'label'=>string]
 *   ['type'=>'blank']
 *   ['type'=>'gap', 'lines'=>float]
 *   ['type'=>'line', 'segs'=>[...], 'gap'=>float]
 *
 * Each segment: ['text'=>string, 'bold'=>bool, 'under'=>bool, 'color'=>string]
 */
function gpdf_parse(string $raw): array
{
    $raw = str_replace(["\r\n", "\r"], "\n", $raw);

    // Sanitise to Latin-1: replace any non-Latin1 characters with safe ASCII equivalents.
    // FPDF built-in fonts only cover Latin-1 (ISO-8859-1); anything outside that range
    // will render as garbled bytes in the PDF.
    $raw = mb_convert_encoding($raw, 'ISO-8859-1', 'UTF-8');

    // Strip email preamble before <STARTREP>
    $sp = strpos($raw, '<STARTREP>');
    if ($sp !== false) $raw = substr($raw, $sp + 10);

    $src_lines = explode("\n", $raw);
    $out  = [];
    $bold = false;  // persistent bold (from <B> heading block)
    $under = false;

    foreach ($src_lines as $src) {

        // -- <BK.label> -> section break ----------------------------------------
        if (preg_match('/<BK\.([^>]*)>/i', $src, $m)) {
            $label = trim($m[1]);
            if ($label !== '') {
                $out[] = ['type'=>'section','label'=>$label];
            } else {
                $out[] = ['type'=>'page'];
            }
            $src = preg_replace('/<BK\.[^>]*>/i', '', $src);
            // If nothing left on line, skip further processing
            if (trim(preg_replace('/<[^>]+>/', '', $src)) === '') continue;
        }

        // -- Check for <P> / <P.n> / <D> page-break lines ---------------------
        // A line that is *only* control tags (no visible text) and contains a page signal
        $text_only = trim(preg_replace('/<[^>]+>/', '', $src));
        if ($text_only === '') {
            if (preg_match('/<P\.?\d*>|<D>/i', $src)) {
                $out[] = ['type'=>'page'];
            } elseif (preg_match('/<L>/i', $src)) {
                $out[] = ['type'=>'blank'];
            } elseif (preg_match('/<L\.(\d+)\.\d+>/i', $src, $lm)) {
                $gap = gpdf_l_gap((int)$lm[1]);
                if ($gap > 0) $out[] = ['type'=>'gap','lines'=>$gap];
                else          $out[] = ['type'=>'blank'];
            } else {
                // Pure control line with no visible text -- emit blank
                $out[] = ['type'=>'blank'];
            }
            continue;
        }

        // -- Extract trailing <L.n.1> or <L> spacing ----------------------------
        // The LAST spacing tag on the line controls the gap after this line.
        $trailing_gap = 0.0;
        // Find all L tags at end of line
        if (preg_match_all('/<L\.(\d+)\.\d+>/i', $src, $lms)) {
            // Use the last one (it's always at the end in practice)
            $n = (int)end($lms[1]);
            $trailing_gap = gpdf_l_gap($n);
        } elseif (preg_match('/<L>/', $src)) {
            // plain <L> = single newline, no extra gap
            $trailing_gap = 0.0;
        }

        // -- Strip all formatting tags we don't tokenise ------------------------
        $ln = $src;
        $ln = preg_replace('/<ST\.[0-9.]+>/i',   '', $ln); // tab stops
        $ln = preg_replace('/<L\.[0-9.]+>/i',    '', $ln); // spacing hints (already captured)
        $ln = preg_replace('/<L>/i',              '', $ln); // plain newlines
        $ln = preg_replace('/<P\.?\d*>/i',        '', $ln); // page breaks
        $ln = preg_replace('/<D>|<E>|<DU>/i',     '', $ln); // separators
        $ln = preg_replace('/<STARTREP>/i',        '', $ln);
        $ln = preg_replace('/<BK\.[^>]*>/i',       '', $ln);

        // -- Tokenise remaining tags inline -------------------------------------
        $segs      = [];
        $cur       = '';
        $line_bold = $bold;   // snapshot: does this line start bold?
        $z_bold    = false;   // within-line <Z> bold (resets at end of line)
        $i = 0; $len = strlen($ln);

        while ($i < $len) {
            if ($ln[$i] !== '<') { $cur .= $ln[$i++]; continue; }
            $cl = strpos($ln, '>', $i);
            if ($cl === false) { $cur .= $ln[$i++]; continue; }
            $tag = substr($ln, $i, $cl - $i + 1);

            // flush buffered text
            if ($cur !== '') {
                $segs[] = [
                    'text'  => $cur,
                    'bold'  => $bold || $z_bold,
                    'under' => $under,
                    'color' => ($bold && !$z_bold) ? 'head' : 'normal',
                ];
                $cur = '';
            }

            switch (strtoupper($tag)) {
                case '<B>':
                    $bold   = true;
                    $z_bold = false;
                    break;
                case '<C>':
                    // <C> turns off both bold types and underline
                    $z_bold = false;
                    $under  = false;
                    // Only turn off persistent bold if this <C> is on its own
                    // (i.e. after visible text has been emitted on this line, or at line start)
                    // Simplest rule: <C> always turns off bold within the line;
                    // persistent bold ($bold) is only set by <B> and cleared by a bare <C> line.
                    if ($cur === '' && empty($segs)) {
                        $bold = false; // <C> at start of line = clear heading bold
                    } else {
                        $bold   = false; // also clear; headings use full-line <B>
                    }
                    break;
                case '<Z>':
                    $z_bold = true;
                    break;
                case '<U>':
                    $under = true;
                    break;
                case '<UC>':
                    $under = false;
                    break;
                case '<T>':
                    $cur .= '  '; // tab -> 2 spaces
                    break;
                // everything else: silently ignore
            }
            $i = $cl + 1;
        }
        // flush tail
        if ($cur !== '') {
            $segs[] = [
                'text'  => $cur,
                'bold'  => $bold || $z_bold,
                'under' => $under,
                'color' => ($bold && !$z_bold) ? 'head' : 'normal',
            ];
        }

        // Reset within-line <Z> bold at end of every line
        $z_bold = false;
        // Reset persistent bold unless the *source* line opened a <B> block
        // A <B> heading block is typically a single line starting with <B>.
        // We detect it: if the source line contained <B> and is a heading line,
        // bold stays on for the next line only if that line had no <C>.
        // Simplest robust rule: reset $bold after every line.
        // The content that needs bold across lines (e.g. <B>HEADING) is a single line.
        $bold  = false;
        $under = false;

        // -- Highlight score patterns -------------------------------------------
        // Baseball: "PH 7-2", "MM 2-10"  (TEAM score-score)
        // Football: "7-14 PE", "0-7 PE"  (score-score TEAM) -- both directions
        $final = [];
        foreach ($segs as $seg) {
            $parts = preg_split(
                '/(\b[A-Z]{2,4}\s+\d{1,3}-\d{1,3}\b|\b\d{1,3}-\d{1,3}\s+[A-Z]{2,4}\b)/',
                $seg['text'], -1, PREG_SPLIT_DELIM_CAPTURE
            );
            if (count($parts) === 1) {
                $final[] = $seg;
            } else {
                foreach ($parts as $pi => $pt) {
                    if ($pt === '') continue;
                    $is_score = ($pi % 2 === 1);
                    $final[] = [
                        'text'  => $pt,
                        'bold'  => $seg['bold'] || $is_score,
                        'under' => $seg['under'],
                        'color' => $is_score ? 'score' : $seg['color'],
                    ];
                }
            }
        }

        // Emit
        $all = implode('', array_column($final, 'text'));
        if (trim($all) === '') {
            $out[] = ['type'=>'blank'];
        } else {
            $out[] = ['type'=>'line','segs'=>$final,'gap'=>$trailing_gap];
        }
    }

    return $out;
}

/**
 * Convert <L.n.1> n-value into extra blank lines to add AFTER the line.
 * n=1  -> 0      (tight spacing, no gap)
 * n<10 -> 0.5    (small gap -- e.g. between roster sub-groups)
 * n=30 -> 0      (continuation / wrapped line -- no gap)
 * n=33 -> 0      (normal line spacing -- no gap)
 * n=36 -> 0.3
 * n=42 -> 0.6
 * n=48 -> 1.0    (after section heading)
 * n=54 -> 1.2
 */
function gpdf_l_gap(int $n): float
{
    if ($n <= 1)  return 0.0;
    if ($n <= 9)  return 0.5;
    if ($n <= 18) return 0.0;
    if ($n <= 30) return 0.0;  // continuation lines
    if ($n <= 33) return 0.0;  // normal
    if ($n <= 36) return 0.3;
    if ($n <= 42) return 0.6;
    if ($n <= 48) return 1.0;
    return 1.2; // 54+
}

/**
 * Extract a display title from the first bold heading line.
 */
function gpdf_extract_title(array $parsed): string
{
    foreach ($parsed as $item) {
        if ($item['type'] !== 'line' || empty($item['segs'])) continue;
        foreach ($item['segs'] as $s) {
            if ($s['bold'] && strlen(trim($s['text'])) > 10) {
                return trim(preg_replace('/\s+/', ' ', $s['text']));
            }
        }
    }
    return 'Gameplan Report';
}

/**
 * Build a safe filename from the first <B> heading line.
 * e.g. "GAMEPLAN BASEBALL MLB21 Team Report Season 34 Week 16 7/8/25"
 * -> "gameplan_MLB21_s34_w16.pdf"
 */
function gpdf_make_filename(string $raw): string
{
    // Find first <B>...<L> heading line
    if (preg_match('/<B>(.*?)(?:<ST[^>]*>)?<L/s', $raw, $m)) {
        $h = $m[1];
        // Extract league code (e.g. MLB21, NFLAR)
        $league  = '';
        if (preg_match('/\b(MLB\d+|NFLAR|NFL\w*|BB\w*)\b/i', $h, $lm)) {
            $league = strtoupper($lm[1]);
        }
        // Extract season number
        $season = '';
        if (preg_match('/Season\s+(\d+)/i', $h, $sm)) {
            $season = 's' . $sm[1];
        }
        // Extract week number
        $week = '';
        if (preg_match('/Week\s+(\d+)/i', $h, $wm)) {
            $week = 'w' . $wm[1];
        }
        $parts = array_filter([$league, $season, $week]);
        if (!empty($parts)) {
            return 'gameplan_' . implode('_', $parts) . '.pdf';
        }
    }
    return 'gameplan_report.pdf';
}

// ===============================================================================
//  HTML PAGE
// ===============================================================================
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Gameplan Report -> PDF</title>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<style>
body{background:#f0f2f4;margin:0}
.gp-wrap{max-width:940px;margin:28px auto;padding:0 14px 50px}

.gp-hero{
  background:linear-gradient(135deg,#004D40,#00796B);
  color:#fff;border-radius:10px;padding:22px 28px;margin-bottom:20px
}
.gp-hero h2{margin:0 0 5px;font-size:1.45em}
.gp-hero p{margin:0;opacity:.85;font-size:.9em}

.gp-card{
  background:#fff;border-radius:10px;
  box-shadow:0 2px 10px rgba(0,0,0,.09);
  padding:24px;margin-bottom:18px
}

/* Tabs */
.gp-tabs{display:flex;border-bottom:2px solid #e0e0e0;margin-bottom:20px}
.gp-tab{
  padding:9px 24px;cursor:pointer;border:none;background:none;
  font-size:.93em;color:#666;border-bottom:3px solid transparent;
  margin-bottom:-2px;border-radius:6px 6px 0 0;transition:all .15s
}
.gp-tab.on{color:#00796B;border-bottom-color:#00796B;font-weight:700;background:#f5fffe}
.gp-tab:hover:not(.on){background:#f5f5f5}

/* Textarea */
textarea{
  font-family:'Courier New',monospace;font-size:11.5px;line-height:1.45;
  width:100%;box-sizing:border-box;border:1px solid #ccc;border-radius:6px;
  padding:10px;color:#222;background:#fafafa;resize:vertical
}

/* Drop zone */
.gp-dz{
  border:2px dashed #bbb;border-radius:8px;padding:36px 20px;
  text-align:center;cursor:pointer;background:#fafafa;
  transition:background .2s,border-color .2s
}
.gp-dz.over{background:#e0f2f1;border-color:#00796B}
.gp-dz input{display:none}
.gp-fn{margin-top:10px;font-size:.85em;color:#555;text-align:center;min-height:18px}

/* Options */
.gp-opts{display:flex;gap:18px;flex-wrap:wrap;margin-top:18px}
.gp-opt label{font-size:.82em;font-weight:700;color:#555;display:block;margin-bottom:4px}
.gp-opt select{border:1px solid #ccc;border-radius:5px;padding:6px 10px;font-size:.87em;background:#fafafa}

/* Button row */
.gp-row{margin-top:20px;display:flex;justify-content:flex-end;align-items:center;gap:14px}
.gp-tip{font-size:.82em;color:#999}
.gp-btn{
  background:#00796B;color:#fff;border:none;border-radius:7px;
  padding:11px 28px;font-size:1em;font-weight:700;cursor:pointer;
  box-shadow:0 2px 6px rgba(0,0,0,.2);transition:background .15s;
  display:inline-flex;align-items:center;gap:8px
}
.gp-btn:hover{background:#005F56}
.gp-btn:disabled{background:#aaa;cursor:not-allowed}

/* Warning */
.gp-warn{
  border-left:5px solid #F57C00;background:#FFF8E1;
  border-radius:6px;padding:16px 20px;margin-bottom:18px
}
.gp-warn a{color:#BF360C}

/* Help */
.gp-help{display:grid;grid-template-columns:1fr 1fr;gap:0 24px}
.gp-help ul{margin:6px 0 0;padding-left:18px}
.gp-help li{margin-bottom:5px;font-size:.84em;color:#555}
code{font-family:'Courier New',monospace;background:#f0f0f0;padding:1px 5px;border-radius:3px;font-size:.88em}

@media(max-width:600px){
  .gp-help{grid-template-columns:1fr}
  .gp-opts{flex-direction:column}
}
</style>
</head>
<body>
<div class="gp-wrap">

<div class="gp-hero">
  <h2>📄 Gameplan Report -> PDF</h2>
  <p>Convert any Gameplan PBM report (MLB Baseball, NFL/NCAA Football, etc.) to a formatted PDF.
     Paste the email text or upload the <code style="background:rgba(255,255,255,.2)">.txt</code> file.</p>
</div>

<?php if (!file_exists($_gpdf_fpdf)): ?>
<div class="gp-warn">
  <strong>⚠ One-time setup required -- FPDF library not found</strong>
  <p style="margin:.5em 0 .3em">This page uses the free <strong>FPDF</strong> PHP library (pure PHP, no extensions).</p>
  <ol style="margin:.4em 0;padding-left:20px;font-size:.9em;line-height:1.8">
    <li>Download from <a href="http://www.fpdf.org/" target="_blank">http://www.fpdf.org/</a></li>
    <li>Copy <code>fpdf.php</code> and the <code>font/</code> folder into the same directory as this page</li>
    <li>Reload -- the Generate button will activate</li>
  </ol>
</div>
<?php endif; ?>

<?php if (!empty($err)): ?>
<div class="w3-panel w3-red w3-round"><p><strong>⚠ <?= htmlspecialchars($err) ?></strong></p></div>
<?php endif; ?>

<div class="gp-card">
<form method="post" enctype="multipart/form-data" id="gpf">
<input type="hidden" name="_gpdf_action" value="go">

<div class="gp-tabs">
  <button type="button" class="gp-tab on" id="tb-paste"  onclick="gpTab('paste')">📋 Paste Text</button>
  <button type="button" class="gp-tab"   id="tb-upload" onclick="gpTab('upload')">📁 Upload .txt</button>
</div>

<!-- Paste -->
<div id="pn-paste">
  <p style="margin:0 0 8px;font-size:.88em;color:#555">
    Copy the full Gameplan email text (including all markup codes like
    <code>&lt;B&gt;</code>, <code>&lt;T&gt;</code>, <code>&lt;BK....&gt;</code>) and paste below.
  </p>
  <textarea name="_gpdf_text" id="gp-ta" rows="20"
    placeholder="Paste report here...&#10;&#10;(starts with something like:  Game Report from Ab Initio Games MLB21-PH, Turn 8 ...)"
    wrap="off"><?= isset($_POST['_gpdf_text']) ? htmlspecialchars($_POST['_gpdf_text']) : '' ?></textarea>
</div>

<!-- Upload -->
<div id="pn-upload" style="display:none">
  <p style="margin:0 0 10px;font-size:.88em;color:#555">
    Upload the <code>.txt</code> file received from Gameplan by email. Nothing is stored server-side.
  </p>
  <div class="gp-dz" id="gp-dz" onclick="document.getElementById('gp-fi').click()">
    <div style="font-size:2.5em;line-height:1.2">⬆</div>
    <div style="font-weight:700;margin:.3em 0">Click to select file</div>
    <div style="font-size:.85em;color:#888">or drag &amp; drop a <code>.txt</code> file here</div>
    <input type="file" name="_gpdf_file" id="gp-fi" accept=".txt,text/plain">
  </div>
  <div class="gp-fn" id="gp-fn">No file selected</div>
</div>

<div class="gp-opts">
  <div class="gp-opt">
    <label>Page orientation</label>
    <select name="_gpdf_orient">
      <option value="P" selected>Portrait A4</option>
      <option value="L">Landscape A4 (wider tables)</option>
    </select>
  </div>
  <div class="gp-opt">
    <label>Font size</label>
    <select name="_gpdf_fsize">
      <option value="6">6 pt -- very compact</option>
      <option value="7" selected>7 pt -- recommended</option>
      <option value="8">8 pt -- comfortable</option>
      <option value="9">9 pt -- large</option>
    </select>
  </div>
</div>

<div class="gp-row">
  <span class="gp-tip">PDF downloads automatically</span>
  <button type="submit" class="gp-btn" id="gp-sub"
    <?= !file_exists($_gpdf_fpdf) ? 'disabled title="Install FPDF first"' : '' ?>>
    ⬇&nbsp; Generate PDF
  </button>
</div>
</form>
</div>

<!-- Help -->
<div class="gp-card">
  <h4 style="margin:0 0 14px;color:#004D40;font-size:1.05em">📖 Reference</h4>
  <div class="gp-help">
    <div>
      <strong style="color:#444">Markup codes parsed:</strong>
      <ul>
        <li><code>&lt;B&gt;</code> bold heading · <code>&lt;C&gt;</code> off</li>
        <li><code>&lt;Z&gt;</code> within-line label bold</li>
        <li><code>&lt;U&gt;</code> underline · <code>&lt;UC&gt;</code> off</li>
        <li><code>&lt;T&gt;</code> column tab</li>
        <li><code>&lt;L&gt;</code> newline · <code>&lt;L.n.1&gt;</code> newline + spacing</li>
        <li><code>&lt;BK.Name&gt;</code> -> page break + section heading</li>
        <li><code>&lt;P&gt;</code> <code>&lt;P.n&gt;</code> <code>&lt;D&gt;&lt;E&gt;</code> -> page break</li>
        <li><code>&lt;ST.n...&gt;</code> tab stops -> ignored</li>
      </ul>
    </div>
    <div>
      <strong style="color:#444">PDF features:</strong>
      <ul>
        <li>Monospace Courier -- columns stay aligned</li>
        <li>Bold navy headings (<code>&lt;B&gt;</code> blocks)</li>
        <li>Score highlights in red -- both directions:<br>
            <code>PH 7-2</code> (baseball) &amp; <code>7-14 PE</code> (football)</li>
        <li>Underline for pitcher/player changes in play-by-play</li>
        <li>Running header with title &amp; page number</li>
        <li>Filename includes league, season &amp; week automatically</li>
        <li>Works with MLB Baseball, NFL/NCAA Football, and other Gameplan games</li>
      </ul>
    </div>
  </div>
</div>

</div><!-- /gp-wrap -->
<script>
function gpTab(t) {
    document.getElementById('pn-paste').style.display  = t==='paste'  ? '' : 'none';
    document.getElementById('pn-upload').style.display = t==='upload' ? '' : 'none';
    document.getElementById('tb-paste').classList.toggle('on',  t==='paste');
    document.getElementById('tb-upload').classList.toggle('on', t==='upload');
    if (t==='upload') document.getElementById('gp-ta').value = '';
}

document.getElementById('gp-fi').addEventListener('change', function() {
    document.getElementById('gp-fn').textContent =
        this.files.length ? '✔ ' + this.files[0].name : 'No file selected';
});

// Drag & drop
(function(){
    var dz = document.getElementById('gp-dz');
    dz.addEventListener('dragover',  function(e){ e.preventDefault(); dz.classList.add('over'); });
    dz.addEventListener('dragleave', function()  { dz.classList.remove('over'); });
    dz.addEventListener('drop', function(e){
        e.preventDefault(); dz.classList.remove('over');
        if (e.dataTransfer.files.length) {
            document.getElementById('gp-fi').files = e.dataTransfer.files;
            document.getElementById('gp-fn').textContent = '✔ ' + e.dataTransfer.files[0].name;
        }
    });
})();

// Re-enable button once the PDF download starts (browser fires beforeunload)
// We use a flag + iframe trick: after submit, listen for the download response.
document.getElementById('gpf').addEventListener('submit', function(){
    var btn = document.getElementById('gp-sub');
    btn.disabled = true;
    btn.innerHTML = '⏳&nbsp; Generating&hellip;';
    // Re-enable after a timeout -- the PDF streams as a download so the page
    // never navigates away; we just need to restore the button after a moment.
    setTimeout(function(){
        btn.disabled = false;
        btn.innerHTML = '⬇&nbsp; Generate PDF';
    }, 4000);
});
</script>
</body>
</html>
