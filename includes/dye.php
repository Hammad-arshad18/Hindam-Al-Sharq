<?php
// From yarn to dyed fabric: threads are woven into cloth, then the cloth runs through a dye line.
// Scrolling weaves the rows, threads the cloth through the bath and the dryer, and winds the dyed roll.
$d = $t['dye'];
$dyeStops = [0, 0.17, 0.34, 0.51, 0.67, 0.79];
$prints = [['#FF6A00', 'stripes'], ['#17A398', 'stars'], ['#2F6FDE', 'checks']];   // cloth colour, design printed on it
$dyeColours = ['#FF6A00', '#2F6FDE', '#17A398', '#6B7446'];
$cell = 36; $n = 10; $ox = 270; $oy = 130;
$greige = 'M110,200 L250,140 L330,140 L368,432 A22,22 0 0 0 412,432';
$dyed   = 'M412,432 L455,215 L455,128 L580,128 L580,250 L630,250 L630,128 L680,128 L680,250 L770,250 L800,400';
?>
  <section class="make dye" id="fabric" data-dye>
    <div class="loom__stage">
      <div class="make__scene dye__scene" aria-hidden="true" data-c="0" data-dye-scene>
        <svg class="dyeline" viewBox="0 0 900 620" data-dyeline>
          <!-- 1. The weave, close up: warp threads standing, weft threads passing over and under -->
          <g class="dye__weave" data-weave>
            <?php for ($c = 0; $c < $n; $c++): ?><rect class="dye__warp" x="<?= $ox + $cell * $c + 7 ?>" y="<?= $oy - 10 ?>" width="22" height="<?= $cell * $n + 20 ?>" rx="5"/><?php endfor; ?>

            <?php for ($r = 0; $r < $n; $r++): ?><rect class="dye__weft" data-row x="<?= $ox - 10 ?>" y="<?= $oy + $cell * $r + 7 ?>" width="<?= $cell * $n + 20 ?>" height="22" rx="5"/><?php endfor; ?>

            <?php for ($r = 0; $r < $n; $r++): for ($c = 0; $c < $n; $c++): if (($r + $c) % 2 === 0): ?><rect class="dye__over" x="<?= $ox + $cell * $c + 7 ?>" y="<?= $oy + $cell * $r + 2 ?>" width="22" height="32" rx="4"/><?php endif; endfor; endfor; ?>

          </g>

          <!-- 2. The dye line, seen from the side -->
          <g class="dye__line">
            <path class="dye__vat" d="M290,330 V482 H490 V330"/>
            <svg x="292" y="352" width="196" height="128" viewBox="0 0 196 128" preserveAspectRatio="none">
              <path class="dye__liquid" d="M-80,14 q10,-9 20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 t20,0 V128 H-80 Z"/>
            </svg>
            <rect class="dye__box" x="540" y="96" width="200" height="186" rx="10"/>
            <g class="dye__steam">
              <path d="M590,84 q-10,-12 0,-24 t0,-24"/><path d="M640,84 q-10,-12 0,-24 t0,-24"/><path d="M690,84 q-10,-12 0,-24 t0,-24"/>
            </g>

            <path class="dye__cloth dye__cloth--greige" pathLength="1" d="<?= $greige ?>"/>
            <path class="dye__cloth dye__cloth--dyed" pathLength="1" d="<?= $dyed ?>"/>
            <path class="dye__run" data-run d="<?= $greige ?> <?= substr($dyed, 9) ?>"/>
            <rect class="dye__liquid dye__liquid--front" x="292" y="372" width="196" height="108"/>

            <?php foreach ([[250, 154, 14], [330, 154, 14], [390, 432, 22], [437, 215, 18], [473, 215, 18], [468, 141, 13], [580, 139, 9], [580, 239, 9], [630, 239, 9], [630, 139, 9], [680, 139, 9], [680, 239, 9], [770, 262, 12]] as [$x, $y, $rad]): ?>
            <g class="dye__roller"><circle cx="<?= $x ?>" cy="<?= $y ?>" r="<?= $rad ?>"/><path class="dye__spin" d="M<?= $x - $rad + 3 ?>,<?= $y ?>H<?= $x + $rad - 3 ?>"/></g>
            <?php endforeach; ?>

            <circle class="dye__roll dye__roll--greige" cx="110" cy="200" r="62" data-roll-a/>
            <circle class="dye__core" cx="110" cy="200" r="9"/>
            <circle class="dye__roll dye__roll--dyed" cx="800" cy="400" r="62" data-roll-b/>
            <circle class="dye__core" cx="800" cy="400" r="9"/>

            <g class="dye__labels">
              <text x="110" y="296"><?= e($d['labels']['greige']) ?></text>
              <text x="390" y="512"><?= e($d['labels']['bath']) ?></text>
              <text x="425" y="104"><?= e($d['labels']['squeeze']) ?></text>
              <text x="640" y="312"><?= e($d['labels']['dryer']) ?></text>
              <text x="800" y="496"><?= e($d['labels']['dyed']) ?></text>
            </g>
          </g>

          <!-- 3. The print table, seen from above: an engraved roller lays a design across the dyed cloth -->
          <defs>
            <pattern id="pr-stripes" width="44" height="44" patternUnits="userSpaceOnUse" patternTransform="rotate(-24)">
              <rect width="14" height="44" fill="#fff"/><rect x="22" width="4" height="44" fill="#0B2545"/>
            </pattern>
            <pattern id="pr-stars" width="84" height="84" patternUnits="userSpaceOnUse">
              <path id="pr-star" d="M0,-17 L4.6,-5.6 L16.8,-5 L7.2,2.6 L10.4,14.4 L0,7.6 L-10.4,14.4 L-7.2,2.6 L-16.8,-5 L-4.6,-5.6 Z" fill="#fff" transform="translate(22,24)"/>
              <use href="#pr-star" transform="translate(42,42) scale(.62) translate(-22,-24) translate(22,24)" x="0" y="0"/>
            </pattern>
            <pattern id="pr-checks" width="56" height="56" patternUnits="userSpaceOnUse">
              <rect width="22" height="56" fill="#fff" fill-opacity=".55"/><rect width="56" height="22" fill="#fff" fill-opacity=".55"/>
              <path d="M39,0V56 M0,39H56" stroke="#0B2545" stroke-opacity=".7" stroke-width="3"/>
            </pattern>
            <?php foreach ($prints as $k => $_): ?><clipPath id="pr-clip-<?= $k ?>"><rect data-print-clip x="150" y="150" width="0" height="340"/></clipPath><?php endforeach; ?>

          </defs>
          <g class="dye__print">
            <rect class="dye__table" x="120" y="126" width="660" height="388" rx="10"/>
            <rect x="150" y="150" width="600" height="340" fill="<?= $prints[0][0] ?>"/>
            <?php foreach ($prints as $k => [$hex, $design]): ?>
            <g clip-path="url(#pr-clip-<?= $k ?>)"><rect x="150" y="150" width="600" height="340" fill="<?= $hex ?>"/><rect x="150" y="150" width="600" height="340" fill="url(#pr-<?= $design ?>)"/></g>
            <?php endforeach; ?>
            <g class="dye__press" data-press>
              <rect x="-17" y="118" width="34" height="404" rx="17"/>
              <path d="M-6,136 V504 M6,136 V504"/>
              <text x="0" y="100"><?= e($d['labels']['print']) ?></text>
            </g>
          </g>
        </svg>
        <p class="dye__dots"><?php foreach ($dyeColours as $i => $hex): ?><i style="background: <?= $hex ?>"></i><?php endforeach; ?></p>
      </div>

      <div class="loom__text">
        <p class="tag"><?= e($ui['dye']) ?></p>
        <h2 class="visually-hidden"><?= e($d['title']) ?></h2>
        <ol class="loom__chapters">
          <?php foreach ($d['chapters'] as $i => [$chTitle, $chText]): ?>
          <li class="loom__chapter<?= $i === 0 ? ' is-active' : '' ?>" data-from="<?= e((string) $dyeStops[$i]) ?>">
            <h3><?= e($chTitle) ?></h3>
            <p><?= e($chText) ?></p>
          </li>
          <?php endforeach; ?>
        </ol>
        <div class="loom__foot" aria-hidden="true">
          <span class="loom__count"><b data-dye-num>1</b> <?= e($d['of']) ?> <?= count($d['chapters']) ?></span>
          <div class="loom__progress"><i></i></div>
        </div>
      </div>
    </div>
  </section>
