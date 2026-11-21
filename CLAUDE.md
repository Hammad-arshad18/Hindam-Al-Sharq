# Hindam Al-Sharq website

Business website for a Libyan garment maker. Plain PHP 8.2, no framework, no build step, no git repo.
Arabic is the default language (RTL); English is the second. Look: dark, cinematic, design comes first.

The folder name ends with a space (`Hindam Al-Sharq /`). Always quote paths.

## Hard rules (from the client)

- No police, security or guard clothing anywhere: copy, alt text, metadata, images, either language. Military wear is allowed.
- No licence, commercial register or chamber numbers, dates or issuing bodies. No partners' names, IDs or capital figures.
- Contact details in `config.php` stay blank until the client supplies real ones. Do not use the catalog's placeholder phone, email or web address.
- Garments first, fabrics second.
- Copy is plain, everyday language. No numbers, delivery times or certificates unless the client gave them.
- Photos: no women or girls (not even partial figures), and nothing British (UK streets, signs, flags, uniforms). People shown should look Libyan / North African / Arab men. Cloth, machine and hands-only shots are safe fallbacks. Check each new photo by eye.
- Photographs are WebP. Save new photos as `.webp` in `assets/img/` and reference `.webp` in PHP, CSS, and JS. Do not add `.jpg` photographs. The tab icon is `favicon.webp`. The Apple home-screen icon stays `favicon.png`.

## Where things are

| File | What it holds |
|---|---|
| `index.php` | Home page: hero, about, range list, loom, shirt scene, dye scene, process, enquiry form |
| `line.php` | One product line's page: `line.php?line=<key>`. Unknown key redirects to `./#range` |
| `includes/bootstrap.php` | Language (session + `?lang=`), helpers `e()`, `words()`, `asset()`, `csrf_token()`, form handling (saves to CSV in `storage/`) |
| `includes/lines.php` | The seven lines: key → cloth scene + home-page photo |
| `includes/make.php`, `includes/dye.php` | SVG scroll scenes (shirt fabrics; weaving, dyeing, printing) |
| `lang/en.php`, `lang/ar.php` | Home page text, `$t[...]` |
| `lang/lines.en.php`, `lang/lines.ar.php` | Line page text: `ui` + `lines[key]` (lead, intro, garments, fabrics, options, questions, alts) |
| `assets/js/cloth.js` | WebGL cloth behind every page. Scenes: silk, cotton, navy, pearl, drill, scrub, slate, olive, custom |
| `assets/js/loom.js` | three.js loom scroll scene |
| `assets/js/site.js` | Loader, Lenis scroll, reveals, cloth scene switching, scroll scenes |
| `assets/css/style.css` | All styles. Tokens: `--ink #0B2545`, `--red #FF6A00`, `--paper #E3E6EB`, `--bone #F5F6F8`, `--accent` |
| `assets/img/` | `g-*.webp` home photos, `l-<key>-a/b/c.webp` line page photos (hero, intro, band), `l-<key>-d..g.webp` the captioned photo strip (`gallery` in `lang/lines.*.php`), logo files |

Line keys: `office`, `workwear`, `medical`, `hospitality`, `school`, `military`, `fabric`.

Any text change must be made in both language files, with the same array shape.

## Running and checking

- Preview server: `hindam-php` in `.claude/launch.json` (`php -S localhost:8137`).
- Line pages set `<html data-quick data-scene="...">`: no loader, cloth stays in that line's colour.
- When the browser pane is hidden, timers and animation frames are throttled, so screenshots can look blank or half-faded. Use `window.__hindam` (`lift()`, `update(true)`, `cloth.frame(t)`) and add `is-in` classes by hand before a screenshot.
- Check both languages and phone width (375) after layout changes.

## Working cheaply

- Read only the file the change touches, and only the part needed. Grep for a class or key first.
- The four `lang/` files are long. Do not read them whole to change one line.
- Do not re-read `cloth.js` or `loom.js` unless the task is about them.

## Still open

- All copy is a draft for the client to confirm (samples, made-to-measure, patterns kept on file).
- Photos: office, workwear, medical, hospitality and school pages and the home range are AI catalogue shots (Gemini, plain navy studio, men/boys only). Military photos stay general stock (client choice); everything else, fabric and factory included, is generated. Prompts for the rest are in `photo-brief/PROMPTS-2.md`; `photo-brief/place.py` crops and files new images. Photo frames use `object-position: 50% 15%` so faces stay in view.
- Not deployed.
