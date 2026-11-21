<?php
// How a garment is made: a shirt drawn as pattern pieces that scrolling cuts, sews and finishes.
// Each piece: label key, outline, where it lies on the cutting table (dx, dy, turn), shade, label position.
$makePieces = [
    ['sleeve', 'M246,168 L266,330 L196,566 L122,540 L170,300 Z', -135, -30,  20, 2, 195, 372],
    ['sleeve', 'M554,168 L534,330 L604,566 L678,540 L630,300 Z',  135, -30, -20, 2, 605, 372],
    ['cuff',   'M118,542 L198,570 L190,598 L108,568 Z',            -70, 100, -19, 3, 153, 574],
    ['cuff',   'M682,542 L602,570 L610,598 L692,568 Z',             70, 100,  19, 3, 647, 574],
    ['front',  'M398,205 L342,130 L246,168 Q262,250 266,330 L272,650 L398,650 Z', -95, 25, 0, 1, 330, 470],
    ['front',  'M402,205 L458,130 L554,168 Q538,250 534,330 L528,650 L402,650 Z',  95, 25, 0, 1, 470, 470],
    [null,     'M391,205 H409 V650 H391 Z',                           0,  40,   0, 3, 0, 0],
    [null,     'M350,112 Q400,96 450,112 L400,142 Z',                 0, -95,   0, 4, 0, 0],
    ['collar', 'M400,140 L350,112 L326,150 L378,214 Z',             -62, -80, -24, 2, 358, 160],
    ['collar', 'M400,140 L450,112 L474,150 L422,214 Z',              62, -80,  24, 2, 442, 160],
    ['pocket', 'M292,300 H366 V372 L329,388 L292,372 Z',             22, 190,   0, 3, 329, 344],
    ['pocket', 'M434,300 H508 V372 L471,388 L434,372 Z',            -22, 190,   0, 3, 471, 344],
];
$makeStops = [0, 0.18, 0.38, 0.62, 0.8];
$m = $t['make'];
$makeLines = $m['lines'];   // the finished shirt is shown in each of these fabrics, in this order
?>
  <section class="make" id="make" data-make>
    <div class="loom__stage">
      <div class="make__scene" aria-hidden="true">
        <svg class="shirt" viewBox="-75 40 950 700" data-v="office" data-shirt>
          <defs>
            <!-- Poplin: a fine, smooth shirting with a woven stripe -->
            <pattern id="tx-office" width="12" height="12" patternUnits="userSpaceOnUse">
              <path d="M3,0V12" stroke="#fff" stroke-opacity=".6" stroke-width="1.6"/>
              <path d="M9,0V12" stroke="#1D4F9C" stroke-opacity=".32" stroke-width="1"/>
              <path d="M0,6H12 M0,0H12" stroke="#0B2545" stroke-opacity=".05" stroke-width="1"/>
            </pattern>
            <!-- Drill: a heavy twill, its ribs running on the diagonal -->
            <pattern id="tx-workwear" width="9" height="9" patternUnits="userSpaceOnUse">
              <path d="M-2,2 L2,-2 M0,9 L9,0 M7,11 L11,7" stroke="#3A1500" stroke-opacity=".3" stroke-width="2.4"/>
              <path d="M-2,6.5 L6.5,-2 M2.5,11 L11,2.5" stroke="#fff" stroke-opacity=".16" stroke-width="1"/>
            </pattern>
            <!-- Oxford: a soft basket weave with a small check -->
            <pattern id="tx-school" width="16" height="16" patternUnits="userSpaceOnUse">
              <rect width="8" height="16" fill="#2F6FDE" fill-opacity=".16"/>
              <rect width="16" height="8" fill="#2F6FDE" fill-opacity=".16"/>
              <path d="M0,4H16 M0,12H16 M4,0V16 M12,0V16" stroke="#0B2545" stroke-opacity=".07" stroke-width="1"/>
            </pattern>
            <!-- Ripstop: a reinforcing grid woven through a camouflage print -->
            <pattern id="tx-military" width="120" height="120" patternUnits="userSpaceOnUse">
              <path d="M-10,22 C8,4 34,10 44,26 C56,44 30,58 14,50 C0,44 -16,36 -10,22 Z M110,22 C128,4 154,10 164,26 C176,44 150,58 134,50 C120,44 104,36 110,22 Z M62,70 C80,58 104,66 108,84 C112,104 86,112 70,104 C54,96 48,80 62,70 Z" fill="#3C4627"/>
              <path d="M52,8 C70,0 92,8 90,24 C88,40 64,42 52,32 C44,24 42,14 52,8 Z M4,78 C20,68 40,78 38,96 C36,112 12,116 2,104 C-6,94 -6,84 4,78 Z M124,78 C140,68 160,78 158,96 C156,112 132,116 122,104 C114,94 114,84 124,78 Z" fill="#8C8F5E"/>
              <path d="M30,60 C40,54 54,58 54,68 C54,78 40,82 32,76 C24,70 22,64 30,60 Z M88,40 C98,36 110,42 108,52 C106,62 92,62 86,56 C80,50 80,44 88,40 Z M70,112 C80,106 92,112 90,122 L66,122 Z M70,-8 C80,-14 92,-8 90,2 C88,10 74,10 68,4 Z" fill="#5B4A30"/>
              <?php for ($g = 0; $g < 120; $g += 12): ?><path d="M0,<?= $g ?>H120 M<?= $g ?>,0V120" stroke="#1C220F" stroke-opacity=".28" stroke-width="1"/><?php endfor; ?>
            </pattern>
          </defs>

          <?php foreach ($makePieces as $i => [$label, $d, $dx, $dy, $turn, $shade, $lx, $ly]): ?>
          <g class="pc" data-cut="<?= $dx ?>,<?= $dy ?>,<?= $turn ?>" data-i="<?= $i ?>">
            <path class="pc__fill pc__fill--<?= $shade ?>" d="<?= $d ?>"/>
            <path class="pc__tex" d="<?= $d ?>"/>
            <path class="pc__line" d="<?= $d ?>"/>
            <?php if ($label !== null): ?><text x="<?= $lx ?>" y="<?= $ly ?>"><?= e($m['pieces'][$label]) ?></text><?php endif; ?>
          </g>
          <?php endforeach; ?>

          <g class="shirt__tape">
            <rect x="269" y="430" width="122" height="18"/><rect x="409" y="430" width="122" height="18"/>
            <path d="M172,352 L246,376 L241,393 L167,369 Z"/><path d="M628,352 L554,376 L559,393 L633,369 Z"/>
          </g>
          <g class="shirt__stitch">
            <path d="M277,637 H390 M410,637 H523 M395,210 V646 M405,210 V646 M344,143 L253,179 M456,143 L547,179 M116,554 L194,581 M684,554 L606,581"/>
            <path d="M298,306 H360 V368 L329,381 L298,368 Z M440,306 H502 V368 L471,381 L440,368 Z"/>
          </g>
          <g class="shirt__finish">
            <?php foreach ([248, 328, 408, 488, 568] as $y): ?><circle cx="400" cy="<?= $y ?>" r="5.5"/><?php endforeach; ?>
            <circle cx="153" cy="572" r="4"/><circle cx="647" cy="572" r="4"/>
            <image href="assets/img/logo-mark-360.webp" x="450" y="318" width="42" height="32"/>
          </g>
        </svg>

        <!-- The fabric up close, with the line of clothing it is used for -->
        <div class="make__chip" data-make-chip data-lines="<?= e(json_encode($makeLines, JSON_UNESCAPED_UNICODE)) ?>">
          <svg class="make__swatch" viewBox="0 0 30 30" data-v="office" data-swatch><rect class="pc__fill" width="30" height="30"/><rect class="pc__tex" width="30" height="30"/></svg>
          <p><span><?= e($m['made_for']) ?></span> <b data-make-line><?= e($makeLines[0][0]) ?></b><br><span><?= e($m['fabric']) ?></span> <b data-make-fab><?= e($makeLines[0][1]) ?></b></p>
        </div>
      </div>

      <div class="loom__text">
        <p class="tag"><?= e($ui['make']) ?></p>
        <h2 class="visually-hidden"><?= e($m['title']) ?></h2>
        <ol class="loom__chapters">
          <?php foreach ($m['chapters'] as $i => [$chTitle, $chText]): ?>
          <li class="loom__chapter<?= $i === 0 ? ' is-active' : '' ?>" data-from="<?= e((string) $makeStops[$i]) ?>">
            <h3><?= e($chTitle) ?></h3>
            <p><?= e($chText) ?></p>
          </li>
          <?php endforeach; ?>
        </ol>
        <div class="loom__foot" aria-hidden="true">
          <span class="loom__count"><b data-make-num>1</b> <?= e($m['of']) ?> <?= count($m['chapters']) ?></span>
          <div class="loom__progress"><i></i></div>
        </div>
      </div>
    </div>
  </section>
