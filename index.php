<?php
require __DIR__ . '/includes/bootstrap.php';

// Each product line has its own cloth and photo; each also has its own page (line.php).
$lines  = require __DIR__ . '/includes/lines.php';
$more   = (require __DIR__ . "/lang/lines.{$lang}.php")['ui']['more'];
// Arriving from a line's page (?interest=office) preselects that line in the enquiry form.
$wanted = (string) ($_GET['interest'] ?? '');
$stepPhotos = ['g-fit', 'rolls', 'g-pattern', 'g-cut', 'g-line', 'g-press'];
// Where each chapter of the 3D scene begins, as a share of the scroll. Matches the timeline in assets/js/loom.js.
$loomStops = [0, 0.13, 0.30, 0.47, 0.66, 0.88];

$c  = $t['contact'];
$ui = $t['ui'];

// The company's logo mark, cut from the supplied logo.
$mark = '<img class="mark" src="assets/img/logo-mark-360.webp" width="360" height="276" alt="">';
?>
<!doctype html>
<html lang="<?= e($lang) ?>" dir="<?= e($t['dir']) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($t['meta_title']) ?></title>
<meta name="description" content="<?= e($t['meta_desc']) ?>">
<meta name="theme-color" content="#0B2545">
<meta property="og:title" content="<?= e($t['meta_title']) ?>">
<meta property="og:description" content="<?= e($t['meta_desc']) ?>">
<meta property="og:type" content="website">
<link rel="alternate" hreflang="ar" href="?lang=ar">
<link rel="alternate" hreflang="en" href="?lang=en">
<link rel="icon" href="assets/img/favicon.webp" type="image/webp">
<link rel="apple-touch-icon" href="assets/img/favicon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@100..900&family=Archivo:wdth,wght@62..125,100..900&family=Readex+Pro:wght@200..600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
<script>document.documentElement.classList.add('js')</script>
<script type="importmap">{"imports":{"three":"https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.min.js","three/addons/":"https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/"}}</script>
</head>
<body>
<a class="skip" href="#main"><?= e($t['skip']) ?></a>

<!-- Loading sequence -->
<div class="loader" aria-hidden="true">
  <div class="loader__mark"><?= $mark ?></div>
  <span class="loader__count" data-count>0</span>
  <span class="loader__name"><?= e($t['name']) ?></span>
</div>

<!-- The cloth: one live piece of fabric behind the whole page -->
<canvas id="cloth" aria-hidden="true"></canvas>
<div class="cursor" aria-hidden="true"></div>

<header class="bar" data-bar>
  <a class="logo" href="#top" aria-label="<?= e($t['name']) ?>"><?= $mark ?><span><?= e($t['name']) ?></span></a>

  <nav class="bar__nav" id="nav" aria-label="<?= e($t['nav']['menu']) ?>">
    <a href="#about"><?= e($t['nav']['about']) ?></a>
    <a href="#range"><?= e($t['nav']['range']) ?></a>
    <a href="#process"><?= e($t['nav']['process']) ?></a>
    <a href="#contact"><?= e($t['nav']['contact']) ?></a>
    <a class="bar__lang" href="?lang=<?= e($otherLang) ?>" lang="<?= e($otherLang) ?>" hreflang="<?= e($otherLang) ?>"><?= e($t['nav']['switch']) ?></a>
  </nav>

  <button class="bar__menu" type="button" aria-controls="nav" aria-expanded="false"
          data-open="<?= e($t['nav']['menu']) ?>" data-close="<?= e($t['nav']['close']) ?>"><?= e($t['nav']['menu']) ?></button>
</header>

<main id="main">

  <!-- ============ Hero ============ -->
  <section class="hero" id="top" data-cloth>
    <div class="hero__meta" data-fade>
      <span><?= e($ui['tag']) ?></span>
      <span dir="ltr">32°07′ N &nbsp; 20°04′ E</span>
      <span><?= e($ui['city']) ?> <time data-clock dir="ltr">--:--</time></span>
    </div>

    <h1 class="hero__title" data-in><?= words($t['hero']['title']) ?></h1>

    <div class="hero__foot">
      <p class="hero__lead" data-fade><?= e($t['hero']['lead']) ?></p>
      <a class="orb" href="#contact" data-magnetic data-fade><span><?= e($t['hero']['cta']) ?></span></a>
    </div>
  </section>

  <!-- ============ The name ============ -->
  <section class="name paper" id="about">
    <p class="tag"><?= e($ui['about']) ?></p>
    <p class="name__text" data-scrub><?= scrub($t['about']['title'] . ' ' . $t['about']['p1']) ?></p>
  </section>

  <!-- ============ The mill ============ -->
  <section class="mill paper">
    <figure class="mill__a rv" data-in><img src="assets/img/g-floor.webp" alt="" decoding="async" fetchpriority="low" data-par="0.12"></figure>

    <div class="mill__text">
      <p class="tag"><?= e($ui['mill']) ?></p>
      <h2 class="mill__title" data-in><?= words($t['statement']) ?></h2>
      <p data-fade><?= e($t['about']['p2']) ?></p>
    </div>

    <figure class="mill__b rv" data-in><img src="assets/img/g-sew.webp" alt="" decoding="async" fetchpriority="low" data-par="0.2"></figure>

    <div class="record">
      <p class="tag"><?= e($ui['record']) ?></p>
      <dl class="record__list">
        <?php foreach ($t['about']['facts'] as $term => $value): ?>
        <div data-fade>
          <dt><?= e($term) ?></dt>
          <dd><?= e($value) ?></dd>
        </div>
        <?php endforeach; ?>
      </dl>
    </div>
  </section>

  <!-- ============ How a garment is made: pattern pieces, scrubbed by scrolling ============ -->
<?php (static function () use ($t, $ui) { require __DIR__ . '/includes/dye.php'; })();   // own scope: the scene's loop variables stay out of the page's ?>

<?php require __DIR__ . '/includes/make.php'; ?>

  <!-- ============ Range: the cloth becomes each product ============ -->
  <section class="range" id="range" data-range data-cloth style="--n: <?= count($t['range']['items']) ?>">
    <div class="range__stage">
      <div class="range__panel">
        <div class="range__head">
          <p class="tag"><?= e($ui['range']) ?></p>
          <p class="range__lead"><?= e($t['range']['lead']) ?></p>
        </div>

        <h2 class="visually-hidden"><?= e($t['range']['title']) ?></h2>
        <ol class="range__list">
          <?php foreach ($t['range']['items'] as $i => $item): ?>
          <li><a class="range__item<?= $i === 0 ? ' is-active' : '' ?>" href="line.php?line=<?= e($item['key']) ?>" data-scene="<?= e($lines[$item['key']]['cloth']) ?>"><?= e($item['title']) ?></a></li>
          <?php endforeach; ?>
        </ol>

        <div class="range__foot">
          <div class="range__details">
            <?php foreach ($t['range']['items'] as $i => $item): ?>
            <div class="range__detail<?= $i === 0 ? ' is-active' : '' ?>">
              <p><?= e($item['text']) ?></p>
              <a class="range__more" href="line.php?line=<?= e($item['key']) ?>"><?= e($more) ?><span class="visually-hidden">: <?= e($item['title']) ?></span></a>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="range__progress" aria-hidden="true"><i></i></div>
        </div>
      </div>

      <div class="range__window">
        <?php foreach ($t['range']['items'] as $i => $item): ?>
        <img class="range__photo<?= $i === 0 ? ' is-active' : '' ?>" <?= $i === 0 ? 'src' : 'data-src' ?>="assets/img/<?= e($lines[$item['key']]['photo']) ?>.webp" alt="" decoding="async"<?= $i === 0 ? ' loading="lazy"' : '' ?>>
        <?php endforeach; ?>
        <span class="range__num" aria-hidden="true" data-range-num>1</span>
        <div class="range__chips">
          <?php foreach ($t['range']['items'] as $i => $item): ?>
          <span class="range__chip<?= $i === 0 ? ' is-active' : '' ?>"><?= e($t['range']['weave_label']) ?> <b><?= e($item['weave_name']) ?></b></span>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ Process: six steps, travelled sideways ============ -->
  <section class="process" id="process" data-process>
    <div class="process__stage">
      <header class="process__head">
        <div>
          <p class="tag"><?= e($ui['process']) ?></p>
          <h2 class="process__title" data-in><?= words($t['process']['title']) ?></h2>
        </div>
        <p class="process__lead" data-fade><?= e($t['process']['lead']) ?></p>
      </header>

      <ol class="process__track" data-track>
        <?php foreach ($t['process']['steps'] as $i => [$stepTitle, $stepText]): ?>
        <li class="pcard">
          <figure class="pcard__img">
            <img src="assets/img/<?= e($stepPhotos[$i]) ?>.webp" alt="" decoding="async" loading="lazy">
            <span class="pcard__num" aria-hidden="true"><?= $i + 1 ?></span>
          </figure>
          <div class="pcard__text">
            <h3><?= e($stepTitle) ?></h3>
            <p><?= e($stepText) ?></p>
          </div>
        </li>
        <?php endforeach; ?>
        <li class="pcard pcard--end">
          <a class="orb orb--red" href="#contact" data-magnetic><span><?= e($t['hero']['cta']) ?></span></a>
        </li>
      </ol>

      <div class="process__line" aria-hidden="true"><i data-line></i></div>
    </div>
  </section>

  <!-- ============ The cloth behind it: one 3D scene, scrubbed by scrolling ============ -->
  <section class="loom" id="loom" data-loom data-src="<?= e(asset('js/loom.js')) ?>">
    <div class="loom__stage">
      <div class="loom__scene">
        <img class="loom__photo" src="assets/img/loom.webp" alt="" decoding="async" loading="lazy">
        <canvas class="loom__canvas" aria-hidden="true"></canvas>
      </div>

      <!-- Part names that sit on the parts while the loom is apart; what each one does is listed in that chapter. -->
      <ul class="loom__labels" aria-hidden="true">
        <?php foreach ($t['loom']['parts'] as $key => [$partName, $partRole]): ?>
        <li class="loom__label" data-part="<?= e($key) ?>"><b><?= e($partName) ?></b></li>
        <?php endforeach; ?>
      </ul>

      <div class="loom__text">
        <p class="tag"><?= e($ui['loom']) ?></p>
        <h2 class="visually-hidden"><?= e($t['loom']['title']) ?></h2>
        <ol class="loom__chapters">
          <?php foreach ($t['loom']['chapters'] as $i => [$chTitle, $chText]): ?>
          <li class="loom__chapter<?= $i === 0 ? ' is-active' : '' ?>" data-from="<?= e((string) $loomStops[$i]) ?>">
            <h3><?= e($chTitle) ?></h3>
            <p><?= e($chText) ?></p>
            <?php if ($i === 3): ?>
            <dl class="loom__parts">
              <?php foreach ($t['loom']['parts'] as [$partName, $partRole]): ?>
              <div><dt><?= e($partName) ?></dt><dd><?= e($partRole) ?></dd></div>
              <?php endforeach; ?>
            </dl>
            <?php endif; ?>
          </li>
          <?php endforeach; ?>
        </ol>
        <div class="loom__foot" aria-hidden="true">
          <span class="loom__count"><b data-loom-num>1</b> <?= e($t['loom']['of']) ?> <?= count($t['loom']['chapters']) ?></span>
          <div class="loom__progress"><i></i></div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ Contact ============ -->
  <section class="contact" id="contact" data-cloth>
    <p class="tag"><?= e($ui['contact']) ?></p>
    <h2 class="contact__title" data-in><?= words($c['title']) ?></h2>

    <div class="contact__grid">
      <div class="contact__intro">
        <p class="contact__lead" data-fade><?= e($c['lead']) ?></p>
        <dl class="reach" data-fade>
          <div>
            <dt><?= e($c['where_title']) ?></dt>
            <dd><?= e($c['where']) ?></dd>
          </div>
          <?php if ($config['phone'] !== ''): ?>
          <div>
            <dt><?= e($c['phone']) ?></dt>
            <dd><a href="tel:<?= e(preg_replace('/[^\d+]/', '', $config['phone'])) ?>" dir="ltr"><?= e($config['phone']) ?></a></dd>
          </div>
          <?php endif; ?>
          <?php if ($config['whatsapp'] !== ''): ?>
          <div>
            <dt><?= e($c['whatsapp']) ?></dt>
            <dd><a href="https://wa.me/<?= e($config['whatsapp']) ?>" dir="ltr" rel="noopener">+<?= e($config['whatsapp']) ?></a></dd>
          </div>
          <?php endif; ?>
          <?php if ($config['email'] !== ''): ?>
          <div>
            <dt><?= e($c['email']) ?></dt>
            <dd><a href="mailto:<?= e($config['email']) ?>" dir="ltr"><?= e($config['email']) ?></a></dd>
          </div>
          <?php endif; ?>
        </dl>
      </div>

      <div class="sheet" data-fade>
        <?php if ($sent): ?>
        <div class="notice notice--ok" role="status" tabindex="-1" data-focus>
          <h3><?= e($c['sent_title']) ?></h3>
          <p><?= e($c['sent']) ?></p>
        </div>
        <?php else: ?>
        <form method="post" action="?lang=<?= e($lang) ?>#contact" novalidate>
          <?php if ($errors): ?>
          <div class="notice notice--err" role="alert" tabindex="-1" data-focus>
            <h3><?= e($c['err_title']) ?></h3>
            <ul>
              <?php foreach ($errors as $message): ?><li><?= e($message) ?></li><?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>

          <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
          <div class="hp" aria-hidden="true">
            <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
          </div>

          <div class="field<?= isset($errors['name']) ? ' field--err' : '' ?>">
            <label for="f-name"><?= e($c['f_name']) ?></label>
            <input id="f-name" name="name" type="text" autocomplete="name" required maxlength="120" value="<?= e($old['name']) ?>"
                   <?= isset($errors['name']) ? 'aria-invalid="true" aria-describedby="e-name"' : '' ?>>
            <?php if (isset($errors['name'])): ?><p class="field__err" id="e-name"><?= e($errors['name']) ?></p><?php endif; ?>
          </div>

          <div class="field">
            <label for="f-company"><?= e($c['f_company']) ?> <small><?= e($c['f_optional']) ?></small></label>
            <input id="f-company" name="company" type="text" autocomplete="organization" maxlength="160" value="<?= e($old['company']) ?>">
          </div>

          <div class="field<?= isset($errors['reach']) ? ' field--err' : '' ?>">
            <label for="f-reach"><?= e($c['f_reach']) ?></label>
            <input id="f-reach" name="reach" type="text" required maxlength="160" dir="auto" value="<?= e($old['reach']) ?>"
                   <?= isset($errors['reach']) ? 'aria-invalid="true" aria-describedby="e-reach"' : '' ?>>
            <?php if (isset($errors['reach'])): ?><p class="field__err" id="e-reach"><?= e($errors['reach']) ?></p><?php endif; ?>
          </div>

          <div class="field">
            <label for="f-interest"><?= e($c['f_interest']) ?></label>
            <select id="f-interest" name="interest">
              <?php foreach ($t['range']['items'] as $item): ?>
              <option<?= ($old['interest'] === $item['title'] || ($old['interest'] === '' && $wanted === $item['key'])) ? ' selected' : '' ?>><?= e($item['title']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="field field--wide<?= isset($errors['message']) ? ' field--err' : '' ?>">
            <label for="f-message"><?= e($c['f_message']) ?></label>
            <textarea id="f-message" name="message" rows="4" required maxlength="4000"
                      aria-describedby="h-message<?= isset($errors['message']) ? ' e-message' : '' ?>"
                      <?= isset($errors['message']) ? 'aria-invalid="true"' : '' ?>><?= e($old['message']) ?></textarea>
            <p class="field__hint" id="h-message"><?= e($c['f_message_hint']) ?></p>
            <?php if (isset($errors['message'])): ?><p class="field__err" id="e-message"><?= e($errors['message']) ?></p><?php endif; ?>
          </div>

          <button class="send" type="submit"><span><?= e($c['f_send']) ?></span></button>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>

<footer class="foot" data-cloth>
  <!-- The wordmark is cut out of the page: the cloth shows through the letters. -->
  <div class="foot__mark" aria-hidden="true"><span data-fit><?= e($ui['wordmark']) ?></span></div>
  <div class="foot__row">
    <a class="logo" href="#top" aria-label="<?= e($t['name']) ?>"><?= $mark ?><span><?= e($t['name']) ?></span></a>
    <p><?= e($t['legal_name']) ?></p>
    <p><?= e($ui['city']) ?> <time data-clock dir="ltr">--:--</time></p>
    <p>© <?= date('Y') ?> <?= e($t['footer']['rights']) ?></p>
    <a class="foot__top" href="#top"><?= e($t['footer']['top']) ?></a>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/lenis@1.1.13/dist/lenis.min.js" defer></script>
<script src="<?= e(asset('js/cloth.js')) ?>" defer></script>
<script src="<?= e(asset('js/site.js')) ?>" defer></script>
</body>
</html>
