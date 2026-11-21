<?php
declare(strict_types=1);

session_start();

$config = require __DIR__ . '/../config.php';

/* ---------- Language ---------- */
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? $config['default_lang'];
if (!in_array($lang, $config['languages'], true)) {
    $lang = $config['default_lang'];
}
$_SESSION['lang'] = $lang;
$otherLang = $lang === 'ar' ? 'en' : 'ar';
$t = require __DIR__ . "/../lang/{$lang}.php";

/* ---------- Helpers ---------- */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Wraps each word so it can rise into place. Words stay whole, so Arabic letters remain joined. */
function words(string $text): string
{
    $out = [];
    foreach (preg_split('/\s+/u', trim($text)) as $i => $word) {
        $out[] = '<span class="w"><span class="wi" style="--i:' . $i . '">' . e($word) . '</span></span>';
    }
    return implode(' ', $out);
}

/** Wraps each word for the scroll-lit paragraph. */
function scrub(string $text): string
{
    $out = [];
    foreach (preg_split('/\s+/u', trim($text)) as $word) {
        $out[] = '<span class="sw">' . e($word) . '</span>';
    }
    return implode(' ', $out);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['csrf'];
}

function asset(string $path): string
{
    $file = __DIR__ . '/../assets/' . $path;
    $v = is_file($file) ? filemtime($file) : time();
    return 'assets/' . $path . '?v=' . $v;
}

/* ---------- Enquiry form (POST → redirect → GET) ---------- */
$errors = [];
$old = ['name' => '', 'company' => '', 'reach' => '', 'interest' => '', 'message' => ''];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    foreach ($old as $key => $_) {
        $old[$key] = trim((string) ($_POST[$key] ?? ''));
    }
    $c = $t['contact'];

    if (!hash_equals($_SESSION['csrf'] ?? '', (string) ($_POST['csrf'] ?? ''))) {
        $errors['form'] = $c['err_token'];
    }
    if ($old['name'] === '' || mb_strlen($old['name']) > 120) {
        $errors['name'] = $c['err_name'];
    }
    if (mb_strlen($old['reach']) < 5 || mb_strlen($old['reach']) > 160) {
        $errors['reach'] = $c['err_reach'];
    }
    if (mb_strlen($old['message']) < 10 || mb_strlen($old['message']) > 4000) {
        $errors['message'] = $c['err_message'];
    }

    // Honeypot: real visitors never fill this field. Pretend success for bots.
    $isBot = trim((string) ($_POST['website'] ?? '')) !== '';

    if (!$errors && !$isBot) {
        $file = $config['enquiries_file'];
        $dir = dirname($file);
        if (!is_dir($dir)) {
            @mkdir($dir, 0750, true);
        }
        // Neutralise spreadsheet formulas before writing to CSV.
        $safe = static fn(string $v): string => preg_match('/^[=+\-@\t\r]/', $v) ? "'" . $v : $v;
        $row = array_map($safe, [
            date('Y-m-d H:i:s'), $lang, $old['name'], $old['company'],
            $old['reach'], $old['interest'], $old['message'],
        ]);
        $isNew = !is_file($file) || filesize($file) === 0;
        $handle = @fopen($file, 'ab');
        if ($handle && flock($handle, LOCK_EX)) {
            if ($isNew) {
                fwrite($handle, "<?php exit; ?>\n");   // guard: the file never outputs its rows
            }
            fputcsv($handle, $row);
            flock($handle, LOCK_UN);
            fclose($handle);
        } else {
            $errors['form'] = $c['err_save'];
        }

        if (!$errors && $config['email'] !== '') {
            $body = "Name: {$old['name']}\nCompany: {$old['company']}\nReach: {$old['reach']}\n"
                  . "Interest: {$old['interest']}\n\n{$old['message']}\n";
            @mail(
                $config['email'],
                '=?UTF-8?B?' . base64_encode('Website enquiry — ' . $old['name']) . '?=',
                $body,
                "Content-Type: text/plain; charset=UTF-8\r\n"
            );
        }
    }

    if (!$errors) {
        unset($_SESSION['csrf']);
        $_SESSION['sent'] = true;
        header('Location: ?lang=' . $lang . '#contact', true, 303);
        exit;
    }
}

$sent = !empty($_SESSION['sent']);
unset($_SESSION['sent']);
