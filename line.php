<?php
// The page of one product line: line.php?line=office
require __DIR__ . '/includes/bootstrap.php';

$lines = require __DIR__ . '/includes/lines.php';
$items = $t['range']['items'];
$key   = (string) ($_GET['line'] ?? '');
$at    = array_search($key, array_column($items, 'key'), true);

if ($at === false || !isset($lines[$key])) {
    header('Location: ./#range', true, 302);
    exit;
}

$pages = require __DIR__ . "/lang/lines.{$lang}.php";
$lu    = $pages['ui'];
$p     = $pages['lines'][$key];
$item  = $items[$at];
$next  = $items[($at + 1) % count($items)];
$ui    = $t['ui'];
$self  = '?line=' . rawurlencode($key);
$title = $item['title'];
// "Ask about office wear": the line's name runs on inside the sentence.
$askTitle = $lu['ask'] . ' ' . ($lang === 'en' ? mb_strtolower($title) : $title);

$mark = '<img class="mark" src="assets/img/logo-mark-360.webp" width="360" height="276" alt="">';
$img  = static fn(string $slot): string => 'assets/img/l-' . $key . '-' . $slot . '.webp';
?>
<!doctype html>
<html lang="<?= e($lang) ?>" dir="<?= e($t['dir']) ?>" data-quick data-scene="<?= e($lines[$key]['cloth']) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title . ' — ' . $t['name']) ?></title>
<meta name="description" content="<?= e($p['lead']) ?>">
<meta name="theme-color" content="#0B2545">
<meta property="og:title" content="<?= e($title . ' — ' . $t['name']) ?>">
<meta property="og:description" content="<?= e($p['lead']) ?>">
<meta property="og:type" content="website">
<meta property="og:image" content="<?= e($img('a')) ?>">
<link rel="alternate" hreflang="ar" href="<?= e($self) ?>&amp;lang=ar">
<link rel="alternate" hreflang="en" href="<?= e($self) ?>&amp;lang=en">
<link rel="preload" as="image" href="<?= e($img('a')) ?>" fetchpriority="high">
<link rel="icon" href="assets/img/favicon.webp" type="image/webp">
<link rel="apple-touch-icon" href="assets/img/favicon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@100..900&family=Archivo:wdth,wght@62..125,100..900&family=Readex+Pro:wght@200..600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
<script>document.documentElement.classList.add('js')</script>
</head>
<body>
<a class="skip" href="#main"><?= e($t['skip']) ?></a>

<!-- The cloth: one live piece of fabric behind the page, in this line's colour and weave -->
<canvas id="cloth" aria-hidden="true"></canvas>
<div class="cursor" aria-hidden="true"></div>

<header class="bar" data-bar>
  <a class="logo" href="./" aria-label="<?= e($t['name']) ?>"><?= $mark ?><span><?= e($t['name']) ?></span></a>

  <nav class="bar__nav" id="nav" aria-label="<?= e($t['nav']['menu']) ?>">
    <a href="./#about"><?= e($t['nav']['about']) ?></a>
    <a href="./#range" aria-current="true"><?= e($t['nav']['range']) ?></a>
    <a href="./#process"><?= e($t['nav']['process']) ?></a>
    <a href="./#contact"><?= e($t['nav']['contact']) ?></a>
    <a class="bar__lang" href="<?= e($self) ?>&amp;lang=<?= e($otherLang) ?>" lang="<?= e($otherLang) ?>" hreflang="<?= e($otherLang) ?>"><?= e($t['nav']['switch']) ?></a>
  </nav>

  <button class="bar__menu" type="button" aria-controls="nav" aria-expanded="false"
          data-open="<?= e($t['nav']['menu']) ?>" data-close="<?= e($t['nav']['close']) ?>"><?= e($t['nav']['menu']) ?></button>
</header>

<main id="main">

  <!-- ============ The line ============ -->
  <section class="lhero" id="top" data-cloth>
    <a class="tag lhero__back" href="./#range"><?= e($lu['back']) ?></a>

    <div class="lhero__grid">
      <div class="lhero__text">
        <h1 class="lhero__title" data-in><?= words($title) ?></h1>
        <p class="lhero__lead" data-fade><?= e($p['lead']) ?></p>
        <p class="lhero__chip" data-fade><?= e($t['range']['weave_label']) ?> <b><?= e($item['weave_name']) ?></b></p>
      </div>
      <figure class="lhero__img rv" data-in><img src="<?= e($img('a')) ?>" alt="<?= e($p['alts'][0]) ?>" decoding="async" fetchpriority="high" data-par="0.1"></figure>
    </div>
  </section>

  <!-- ============ Who it is for ============ -->
  <section class="lintro paper">
    <figure class="lintro__img rv" data-in><img src="<?= e($img('b')) ?>" alt="<?= e($p['alts'][1]) ?>" decoding="async" fetchpriority="low" data-par="0.12"></figure>
    <div class="lintro__text">
      <h2 data-in><?= words($p['intro_title']) ?></h2>
      <?php foreach ($p['intro'] as $para): ?>
      <p data-fade><?= e($para) ?></p>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ============ The garments ============ -->
  <section class="lmake">
    <h2 class="tag"><?= e($p['garments_title'] ?? $lu['garments']) ?></h2>
    <ul class="lmake__list">
      <?php foreach ($p['garments'] as [$name, $text]): ?>
      <li data-fade>
        <h3><?= e($name) ?></h3>
        <p><?= e($text) ?></p>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php if (!empty($p['gallery'])): ?>
    <div class="lgal">
      <?php foreach ($p['gallery'] as $i => [$cap, $alt]): ?>
      <figure class="lgal__item" data-fade>
        <div class="lgal__img"><img src="<?= e($img(chr(100 + $i))) ?>" alt="<?= e($alt) ?>" decoding="async" loading="lazy"></div>
        <figcaption><?= e($cap) ?></figcaption>
      </figure>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </section>

  <figure class="lband rv" data-in><img src="<?= e($img('c')) ?>" alt="<?= e($p['alts'][2]) ?>" decoding="async" fetchpriority="low" data-par="0.16"></figure>

  <!-- ============ Fabric and choices ============ -->
  <section class="lspec paper">
    <div class="lspec__col">
      <h2 class="tag"><?= e($p['fabrics_title'] ?? $lu['fabrics']) ?></h2>
      <ul class="lspec__list">
        <?php foreach ($p['fabrics'] as [$name, $text]): ?>
        <li data-fade>
          <h3><?= e($name) ?></h3>
          <p><?= e($text) ?></p>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="lspec__col">
      <h2 class="tag"><?= e($lu['options']) ?></h2>
      <dl class="record__list">
        <?php foreach ($p['options'] as [$term, $text]): ?>
        <div data-fade>
          <dt><?= e($term) ?></dt>
          <dd><?= e($text) ?></dd>
        </div>
        <?php endforeach; ?>
      </dl>
    </div>
  </section>

  <!-- ============ Questions ============ -->
  <section class="lask">
    <h2 class="tag"><?= e($lu['questions']) ?></h2>
    <div class="lask__grid">
      <?php foreach ($p['questions'] as [$question, $answer]): ?>
      <div data-fade>
        <h3><?= e($question) ?></h3>
        <p><?= e($answer) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ============ Ask, or go on to the next line ============ -->
  <section class="lnext" data-cloth>
    <div class="lnext__ask">
      <div>
        <h2 class="lnext__title" data-in><?= words($askTitle) ?></h2>
        <p class="lnext__lead" data-fade><?= e($lu['ask_lead']) ?></p>
      </div>
      <a class="orb orb--red" href="./?interest=<?= e(rawurlencode($key)) ?>#contact" data-magnetic><span><?= e($t['hero']['cta']) ?></span></a>
    </div>

    <div class="lnext__row">
      <a class="lnext__link" href="?line=<?= e(rawurlencode($next['key'])) ?>">
        <span><?= e($lu['next']) ?></span>
        <b><?= e($next['title']) ?></b>
      </a>
      <nav aria-label="<?= e($lu['all']) ?>">
        <ul class="lnext__all">
          <?php foreach ($items as $other): ?>
          <li><a href="?line=<?= e(rawurlencode($other['key'])) ?>"<?= $other['key'] === $key ? ' aria-current="page"' : '' ?>><?= e($other['title']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </nav>
    </div>
  </section>
</main>

<footer class="foot" data-cloth>
  <div class="foot__mark" aria-hidden="true"><span data-fit><?= e($ui['wordmark']) ?></span></div>
  <div class="foot__row">
    <a class="logo" href="./" aria-label="<?= e($t['name']) ?>"><?= $mark ?><span><?= e($t['name']) ?></span></a>
    <p><?= e($t['legal_name']) ?></p>
    <p><?= e($ui['city']) ?></p>
    <p>© <?= date('Y') ?> <?= e($t['footer']['rights']) ?></p>
    <a class="foot__top" href="#top"><?= e($t['footer']['top']) ?></a>
    <p class="foot__note"><?= e($t['footer']['images']) ?></p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/lenis@1.1.13/dist/lenis.min.js" defer></script>
<script src="<?= e(asset('js/cloth.js')) ?>" defer></script>
<script src="<?= e(asset('js/site.js')) ?>" defer></script>
</body>
</html>
