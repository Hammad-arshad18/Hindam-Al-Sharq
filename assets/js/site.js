/* Hindam Al-Sharq — page behaviour: loading sequence, scroll scenes, pointer details. */
(() => {
  'use strict';

  const html = document.documentElement;
  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];
  const clamp = (n, lo = 0, hi = 1) => Math.min(hi, Math.max(lo, n));
  const reduceMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const finePointer = matchMedia('(hover: hover) and (pointer: fine)').matches;
  const wide = matchMedia('(min-width: 981px)');
  const rtl = html.dir === 'rtl';

  const debug = (window.__hindam = {});

  /* ---------- The cloth ---------- */
  const cloth = window.Cloth ? window.Cloth.init($('#cloth')) : null;
  if (cloth) html.classList.add('has-cloth');
  else html.classList.add('no-cloth');

  /* ---------- Loading sequence ---------- */
  const counter = $('[data-count]');
  let ready = false;
  function lift() {
    if (ready) return;
    ready = true;
    if (counter) counter.textContent = '100';
    html.classList.add('is-ready');
    setTimeout(() => { const el = $('.loader'); if (el) el.remove(); }, 1800);
  }
  if (reduceMotion) {
    lift();
  } else {
    const began = performance.now();
    const span = 'quick' in html.dataset ? 150 : 1250;      // inner pages have no loading sequence to wait for
    (function count(now) {
      if (ready) return;
      const k = clamp((now - began) / span);
      if (counter) counter.textContent = String(Math.round((1 - Math.pow(1 - k, 3)) * 100));
      if (k < 1) requestAnimationFrame(count);
    })(began);
    const fonts = document.fonts ? document.fonts.ready : Promise.resolve();
    // Line pages are marked quick: lift on the short timer. Waiting on the webfonts
    // held the photographs behind the loader for as long as the font file took.
    const gate = 'quick' in html.dataset
      ? new Promise((r) => setTimeout(r, span))
      : Promise.all([fonts, new Promise((r) => setTimeout(r, span + 150))]);
    gate.then(lift);
    setTimeout(lift, 4000);
  }

  /* ---------- Smooth scrolling ---------- */
  let lenis = null;
  if (window.Lenis && !reduceMotion) {
    try { lenis = new window.Lenis({ lerp: 0.095, wheelMultiplier: 1 }); } catch (err) { lenis = null; }
  }
  document.addEventListener('click', (ev) => {
    const link = ev.target.closest('a[href^="#"]');
    if (!link) return;
    const target = $(link.getAttribute('href'));
    if (!target) return;
    ev.preventDefault();
    if (lenis) lenis.scrollTo(target, { duration: 1.6 });
    else target.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth' });
    history.replaceState(null, '', link.getAttribute('href'));
  });

  /* ---------- Entrances ---------- */
  const entering = $$('[data-in], [data-fade]');
  const reveal = (el) => el.classList.add('is-in');
  // The hero is on screen from the start: it enters as soon as the loader lifts, without waiting to be observed.
  $$('.hero [data-in], .hero [data-fade], .lhero [data-in], .lhero [data-fade]').forEach(reveal);
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach((en) => {
        if (!en.isIntersecting) return;
        io.unobserve(en.target);
        reveal(en.target);
      });
    }, { rootMargin: '0px 0px -10% 0px', threshold: 0.12 });
    entering.forEach((el) => io.observe(el));
  } else {
    entering.forEach(reveal);
  }

  // Is any open (cloth-showing) section on screen? If not, the cloth is not drawn at all.
  let clothOnScreen = true;
  if (cloth && 'IntersectionObserver' in window) {
    const seen = new Set();
    const io = new IntersectionObserver((entries) => {
      entries.forEach((en) => (en.isIntersecting ? seen.add(en.target) : seen.delete(en.target)));
      clothOnScreen = seen.size > 0;
    });
    $$('[data-cloth]').forEach((el) => io.observe(el));
  }

  /* ---------- Scroll scenes ---------- */
  const bar = $('[data-bar]');
  const nav = $('#nav');
  const scrubs = $$('[data-scrub]').map((el) => ({ el, words: $$('.sw', el), lit: -1 }));
  const parallax = reduceMotion ? [] : $$('[data-par]');

  const range = $('[data-range]');
  const rangeItems = range ? $$('.range__item', range) : [];
  const rangeDetails = range ? $$('.range__detail', range) : [];
  const rangeChips = range ? $$('.range__chip', range) : [];
  const rangePhotos = range ? $$('.range__photo', range) : [];
  const rangeNum = $('[data-range-num]');
  const rangeBar = range ? $('.range__progress', range) : null;
  let rangeIndex = 0;

  const loadPhoto = (img) => {
    if (img && img.dataset.src && !img.getAttribute('src')) img.src = img.dataset.src;
  };

  function showRange(i) {
    if (i === rangeIndex) return;
    rangeIndex = i;
    [rangeItems, rangeDetails, rangeChips, rangePhotos].forEach((list) => list.forEach((el, k) => el.classList.toggle('is-active', k === i)));
    if (rangeNum) rangeNum.textContent = String(i + 1);
    loadPhoto(rangePhotos[i]);
  }

  // The other lines' photos stay out of the first download. Pull them in together
  // once the list is close, and always have the one on screen ready.
  if (range && 'IntersectionObserver' in window) {
    const soon = new IntersectionObserver((entries) => {
      if (!entries.some((en) => en.isIntersecting)) return;
      rangePhotos.forEach(loadPhoto);
      soon.disconnect();
    }, { rootMargin: '500px 0px' });
    soon.observe(range);
  } else rangePhotos.forEach(loadPhoto);

  /* The garment: a shirt drawn as pattern pieces. Scrolling cuts it, sews it and finishes it. */
  const makeEl = $('[data-make]');
  const shirt = makeEl ? $('[data-shirt]', makeEl) : null;
  const makeStage = makeEl ? $('.loom__stage', makeEl) : null;
  const makeChapters = makeEl ? $$('.loom__chapter', makeEl) : [];
  const makeStops = makeChapters.map((el) => parseFloat(el.dataset.from));
  const makeNum = $('[data-make-num]');
  const makeChip = $('[data-make-chip]');
  const makeLines = makeChip ? JSON.parse(makeChip.dataset.lines) : [];
  const makeKeys = ['office', 'workwear', 'school', 'military'];
  const swatch = $('[data-swatch]');
  const pieces = shirt ? $$('.pc', shirt).map((el) => ({ el, cut: el.dataset.cut.split(',').map(Number) })) : [];
  let makeIndex = 0;
  let makeLine = 0;
  const span01 = (v, a, b) => Math.min(1, Math.max(0, (v - a) / (b - a)));
  const smooth = (v) => v * v * (3 - 2 * v);
  const updateMake = (vh) => {
    const r = makeEl.getBoundingClientRect();
    if (r.bottom < -vh || r.top > vh * 2) return;
    const p = span01(-r.top / Math.max(1, r.height - vh), 0, 1);
    let i = 0;
    makeStops.forEach((stop, k) => { if (p >= stop) i = k; });
    if (i !== makeIndex) {
      makeIndex = i;
      makeChapters.forEach((el, k) => el.classList.toggle('is-active', k === i));
      if (makeNum) makeNum.textContent = String(i + 1);
    }
    const fill = smooth(span01(p, 0.16, 0.26));
    pieces.forEach(({ el, cut }, n) => {
      const k = 1 - smooth(span01(p, 0.38 + n * 0.011, 0.5 + n * 0.011));
      el.style.transform = k > 0.001 ? `translate(${(cut[0] * k).toFixed(1)}px, ${(cut[1] * k).toFixed(1)}px) rotate(${(cut[2] * k).toFixed(2)}deg)` : '';
    });
    shirt.style.setProperty('--fill', fill.toFixed(3));
    shirt.style.setProperty('--lab', (1 - span01(p, 0.37, 0.43)).toFixed(3));
    shirt.style.setProperty('--st', smooth(span01(p, 0.56, 0.63)).toFixed(3));
    shirt.style.setProperty('--fin', smooth(span01(p, 0.64, 0.72)).toFixed(3));
    shirt.classList.toggle('is-cut', fill > 0.5);
    const line = p < 0.8 ? 0 : Math.min(makeKeys.length - 1, Math.floor((p - 0.8) / 0.05));
    if (line !== makeLine) {
      makeLine = line;
      shirt.dataset.v = makeKeys[line];
      if (swatch) swatch.dataset.v = makeKeys[line];
      if (makeChip && makeLines[line]) {
        $('[data-make-line]', makeChip).textContent = makeLines[line][0];
        $('[data-make-fab]', makeChip).textContent = makeLines[line][1];
      }
    }
    if (makeChip) makeChip.classList.toggle('is-on', p >= 0.8);
    makeStage.style.setProperty('--p', p.toFixed(4));
  };

  /* The fabric: rows are woven close up, then the cloth runs through the dye bath and the dryer. */
  const dyeEl = $('[data-dye]');
  const dyeLine = dyeEl ? $('[data-dyeline]', dyeEl) : null;
  const dyeScene = dyeEl ? $('[data-dye-scene]', dyeEl) : null;
  const dyeStage = dyeEl ? $('.loom__stage', dyeEl) : null;
  const dyeChapters = dyeEl ? $$('.loom__chapter', dyeEl) : [];
  const dyeStops = dyeChapters.map((el) => parseFloat(el.dataset.from));
  const dyeNum = $('[data-dye-num]');
  const dyeRows = dyeEl ? $$('[data-row]', dyeEl) : [];
  const dyeWeave = dyeEl ? $('[data-weave]', dyeEl) : null;
  const dyeRun = dyeEl ? $('[data-run]', dyeEl) : null;
  const rollA = dyeEl ? $('[data-roll-a]', dyeEl) : null;
  const rollB = dyeEl ? $('[data-roll-b]', dyeEl) : null;
  const dyeDots = dyeEl ? $('.dye__dots', dyeEl) : null;
  const printClips = dyeEl ? $$('[data-print-clip]', dyeEl) : [];
  const press = dyeEl ? $('[data-press]', dyeEl) : null;
  let dyeIndex = 0;
  let dyeColour = -1;
  const updateDye = (vh) => {
    const r = dyeEl.getBoundingClientRect();
    if (r.bottom < -vh || r.top > vh * 2) return;
    const p = span01(-r.top / Math.max(1, r.height - vh), 0, 1);
    let i = 0;
    dyeStops.forEach((stop, k) => { if (p >= stop) i = k; });
    if (i !== dyeIndex) {
      dyeIndex = i;
      dyeChapters.forEach((el, k) => el.classList.toggle('is-active', k === i));
      if (dyeNum) dyeNum.textContent = String(i + 1);
    }
    const q = Math.min(1, p / 0.76);
    dyeRows.forEach((el, n) => { el.style.transform = `scaleX(${span01(q, 0.02 + n * 0.017, 0.06 + n * 0.017).toFixed(3)})`; });
    const k = smooth(span01(q, 0.22, 0.31));
    dyeWeave.style.transform = `translate(${(-340 * k).toFixed(1)}px, ${(-110 * k).toFixed(1)}px) scale(${(1 - 0.82 * k).toFixed(3)})`;
    const set = (name, v) => dyeLine.style.setProperty(name, v.toFixed(3));
    set('--weave', 1 - span01(q, 0.26, 0.31));
    set('--line', span01(q, 0.24, 0.32));
    set('--t1', span01(q, 0.31, 0.46));
    set('--t2', span01(q, 0.46, 0.68));
    set('--run', span01(q, 0.68, 0.72));
    set('--steam', span01(q, 0.6, 0.68));
    dyeLine.style.setProperty('--rot', `${(q * 2600).toFixed(1)}deg`);
    dyeRun.style.strokeDashoffset = (-q * 1100).toFixed(1);
    const u = span01(q, 0.46, 0.98);
    rollA.setAttribute('r', (62 - 30 * u).toFixed(1));
    rollB.setAttribute('r', (24 + 38 * u).toFixed(1));
    const c = q < 0.86 ? 0 : Math.min(3, Math.floor((q - 0.86) / 0.035));
    if (c !== dyeColour) {
      dyeColour = c;
      dyeScene.dataset.c = String(c);
      if (dyeDots) $$('i', dyeDots).forEach((el, n) => el.classList.toggle('is-on', n === c));
    }
    if (dyeDots) dyeDots.classList.toggle('is-on', q >= 0.84 && p < 0.775);
    // Printing: the roller crosses the cloth three times, each pass laying a new design over the last.
    set('--print', smooth(span01(p, 0.775, 0.815)));
    let pressAt = 0;
    printClips.forEach((el, n) => {
      const s = span01(p, 0.83 + n * 0.055, 0.875 + n * 0.055);
      el.setAttribute('width', (600 * s).toFixed(1));
      if (s > 0 && s < 1) pressAt = s;
      else if (s >= 1 && n === printClips.length - 1) pressAt = 1;
    });
    if (press) press.style.transform = `translateX(${(150 + 600 * pressAt).toFixed(1)}px)`;
    dyeStage.style.setProperty('--p', p.toFixed(4));
  };

  /* The loom: a 3D scene that is only downloaded and built when the visitor gets near it. */
  const loomEl = $('[data-loom]');
  const loomStage = loomEl ? $('.loom__stage', loomEl) : null;
  const loomChapters = loomEl ? $$('.loom__chapter', loomEl) : [];
  const loomStops = loomChapters.map((el) => parseFloat(el.dataset.from));
  const loomNum = $('[data-loom-num]');
  let loom = null;
  let loomP = 0;
  let loomIndex = 0;
  let loomSeen = false;
  let loomVeil = 1;
  if (loomEl && 'IntersectionObserver' in window) {
    new IntersectionObserver((entries) => { loomSeen = entries[0].isIntersecting; }).observe(loomEl);
    const near = new IntersectionObserver((entries) => {
      if (!entries.some((en) => en.isIntersecting)) return;
      near.disconnect();
      import(new URL(loomEl.dataset.src, document.baseURI).href)
        .then((mod) => {
          loom = mod.createLoom({ canvas: $('.loom__canvas', loomEl), labels: $$('.loom__label', loomEl), rtl, reduceMotion });
          if (!loom) return;
          html.classList.add('has-loom');
          loomEl.classList.add('is-live');
          debug.loom = loom;
        })
        .catch((err) => console.warn('3D scene unavailable:', err.message));
    }, { rootMargin: '150% 0px' });
    near.observe(loomEl);
  }

  const process = $('[data-process]');
  const track = $('[data-track]');
  const line = $('[data-line]');
  let travel = 0;
  function measure() {
    if (process && track) {
      if (wide.matches) {
        travel = Math.max(0, track.scrollWidth - innerWidth);
        process.style.height = `${innerHeight + travel}px`;
      } else {
        travel = 0;
        process.style.height = '';
        track.style.transform = '';
      }
    }
    // Fit the footer wordmark to the full width of the page.
    $$('[data-fit]').forEach((el) => {
      const box = el.parentElement;
      el.style.fontSize = '100px';
      const w = el.getBoundingClientRect().width;
      if (w > 0) el.style.fontSize = `${(100 * (box.clientWidth * 0.94)) / w}px`;
    });
  }

  let lastY = -1;
  let lastDir = 0;
  function update(force) {
    const y = scrollY;
    if (!force && y === lastY) return;
    const vh = innerHeight;
    const dir = y > lastY ? 1 : -1;

    // Top bar: glass once scrolled, steps aside while reading down.
    if (bar) {
      bar.classList.toggle('is-float', y > 60);
      if (dir !== lastDir || y < 200) {
        bar.classList.toggle('is-away', dir === 1 && y > 320 && !(nav && nav.classList.contains('is-open')));
        lastDir = dir;
      }
    }
    lastY = y;

    // Paragraph lit word by word.
    scrubs.forEach((s) => {
      const r = s.el.getBoundingClientRect();
      const p = clamp((vh * 0.82 - r.top) / (r.height + vh * 0.28));
      const lit = reduceMotion ? s.words.length : Math.round(p * s.words.length);
      if (lit === s.lit) return;
      s.words.forEach((w, k) => w.classList.toggle('is-lit', k < lit));
      s.lit = lit;
    });

    parallax.forEach((img) => {
      const r = img.parentElement.getBoundingClientRect();
      if (r.bottom < -80 || r.top > vh + 80) return;
      const p = clamp((vh - r.top) / (vh + r.height));
      img.style.transform = `translate3d(0, ${(-p * 13.6).toFixed(2)}%, 0)`;
    });

    // The loom: scroll position is the scene's timeline. At the very end the scene lifts away and
    // the page's own cloth is what is left.
    if (dyeEl) updateDye(vh);
    if (makeEl) updateMake(vh);
    if (loomEl) {
      const r = loomEl.getBoundingClientRect();
      loomP = clamp(-r.top / Math.max(1, r.height - vh));
      let i = 0;
      loomStops.forEach((stop, k) => { if (loomP >= stop) i = k; });
      if (i !== loomIndex) {
        loomIndex = i;
        loomChapters.forEach((el, k) => el.classList.toggle('is-active', k === i));
        if (loomNum) loomNum.textContent = String(i + 1);
      }
      const k = cloth ? clamp((loomP - 0.9) / 0.06) : 0;
      loomVeil = 1 - k * k * (3 - 2 * k);
      loomStage.style.setProperty('--veil', loomVeil.toFixed(3));
      loomStage.style.setProperty('--p', loomP.toFixed(4));
    }

    // Range: scroll position picks the product line, and the cloth becomes it.
    let scene = html.dataset.scene || 'silk';      // a line's page keeps the cloth in that line's colour
    if (range) {
      const r = range.getBoundingClientRect();
      const p = clamp(-r.top / Math.max(1, r.height - vh));
      showRange(Math.min(rangeItems.length - 1, Math.floor(p * rangeItems.length)));
      if (rangeBar) rangeBar.style.setProperty('--p', p.toFixed(4));
      if (r.top < vh * 0.6 && r.bottom > vh * 0.4) scene = rangeItems[rangeIndex].dataset.scene;
    }
    if (cloth) { cloth.setScene(scene); cloth.setScroll(y); }

    // Process: vertical scrolling carries the six steps sideways.
    if (process && track && travel > 0) {
      const r = process.getBoundingClientRect();
      const p = clamp(-r.top / Math.max(1, r.height - vh));
      track.style.transform = `translate3d(${((rtl ? 1 : -1) * p * travel).toFixed(1)}px, 0, 0)`;
      if (line) line.style.setProperty('--p', p.toFixed(4));
    }
  }

  /* ---------- Pointer: cursor dot and magnetic buttons ---------- */
  const cursor = $('.cursor');
  const pointer = { x: 0, y: 0, cx: 0, cy: 0, seen: false };
  if (finePointer && cursor) {
    addEventListener('pointermove', (ev) => {
      pointer.x = ev.clientX; pointer.y = ev.clientY;
      if (!pointer.seen) { pointer.seen = true; pointer.cx = pointer.x; pointer.cy = pointer.y; cursor.classList.add('is-on'); }
      cursor.classList.toggle('is-big', !!ev.target.closest('a, button'));
    }, { passive: true });
    document.addEventListener('pointerleave', () => { cursor.classList.remove('is-on'); pointer.seen = false; });

    if (!reduceMotion) {
      $$('[data-magnetic]').forEach((el) => {
        el.addEventListener('pointermove', (ev) => {
          const r = el.getBoundingClientRect();
          el.style.setProperty('--mx', `${((ev.clientX - (r.left + r.width / 2)) * 0.32).toFixed(1)}px`);
          el.style.setProperty('--my', `${((ev.clientY - (r.top + r.height / 2)) * 0.32).toFixed(1)}px`);
        });
        el.addEventListener('pointerleave', () => { el.style.setProperty('--mx', '0px'); el.style.setProperty('--my', '0px'); });
      });
    }
  }

  /* ---------- One loop drives everything ---------- */
  let accentTick = 0;
  function tick(now) {
    if (lenis) lenis.raf(now);
    update(false);

    if (pointer.seen) {
      pointer.cx += (pointer.x - pointer.cx) * 0.22;
      pointer.cy += (pointer.y - pointer.cy) * 0.22;
      cursor.style.transform = `translate3d(${pointer.cx.toFixed(1)}px, ${pointer.cy.toFixed(1)}px, 0)`;
    }

    if (loom && loomSeen && loomVeil > 0.002 && !document.hidden) loom.frame(now, loomP);

    if (cloth && (clothOnScreen || (loomSeen && loomVeil < 1)) && !document.hidden) {
      cloth.frame(now);
      if (++accentTick % 6 === 0) {
        const c = cloth.colour();
        html.style.setProperty('--accent', `rgb(${Math.round(c[0] * 255)} ${Math.round(c[1] * 255)} ${Math.round(c[2] * 255)})`);
      }
    }
    requestAnimationFrame(tick);
  }

  measure();
  update(true);
  requestAnimationFrame(tick);
  addEventListener('resize', () => { measure(); update(true); });
  addEventListener('load', () => {
    measure(); update(true);
    // Arriving from another page with #contact: the scroll scenes have only now taken their full height, so go there again.
    if (location.hash.length > 1) {
      const target = document.getElementById(location.hash.slice(1));
      if (target) { if (lenis) lenis.scrollTo(target, { immediate: true }); else target.scrollIntoView(); }
    }
  });
  if (document.fonts) {
    // Widths change when a font arrives, and fonts can arrive late: re-measure every time one does.
    document.fonts.ready.then(() => { measure(); update(true); });
    document.fonts.addEventListener('loadingdone', () => { measure(); update(true); });
  }

  /* ---------- Benghazi time ---------- */
  const clocks = $$('[data-clock]');
  if (clocks.length) {
    const fmt = new Intl.DateTimeFormat('en-GB', { timeZone: 'Africa/Tripoli', hour: '2-digit', minute: '2-digit' });
    const setClock = () => { const t = fmt.format(new Date()); clocks.forEach((el) => { el.textContent = t; }); };
    setClock();
    setInterval(setClock, 20000);
  }

  /* ---------- Menu (small screens) ---------- */
  const menuBtn = $('.bar__menu');
  if (menuBtn && nav) {
    const setOpen = (open) => {
      nav.classList.toggle('is-open', open);
      menuBtn.setAttribute('aria-expanded', String(open));
      menuBtn.textContent = open ? menuBtn.dataset.close : menuBtn.dataset.open;
      if (lenis) (open ? lenis.stop() : lenis.start());
      document.body.style.overflow = open ? 'hidden' : '';
    };
    menuBtn.addEventListener('click', () => setOpen(!nav.classList.contains('is-open')));
    nav.addEventListener('click', (ev) => { if (ev.target.closest('a')) setOpen(false); });
    addEventListener('keydown', (ev) => { if (ev.key === 'Escape') setOpen(false); });
  }

  /* ---------- After a form submit, land on the result ---------- */
  const feedback = $('[data-focus]');
  if (feedback) {
    feedback.closest('[data-fade]')?.classList.add('is-in');
    feedback.focus();
  }

  // Debug handle: lets the cloth and scenes be stepped by hand when the page is in a background tab.
  Object.assign(debug, { cloth, update, lift });
})();
