<?php
/**
 * Site-wide settings. Edit this file to change company details.
 * Empty contact values are simply not shown on the site.
 *
 * The mailbox password lives in config.mail.php (kept off the public pages and out of git).
 */
$mail = [];
$mailFile = __DIR__ . '/config.mail.php';
if (is_file($mailFile)) {
    $loaded = require $mailFile;
    if (is_array($loaded)) {
        $mail = $loaded;
    }
}

return [
    'default_lang' => 'ar',
    'languages'    => ['ar', 'en'],

    // Contact details — fill these in; blank ones stay hidden.
    'email'    => 'info@hindamalsharq.com',
    'phone'    => '',   // e.g. +218 9X XXX XXXX
    'whatsapp' => '',   // digits only, e.g. 2189XXXXXXXX

    // SMTP for the enquiry form. Empty when config.mail.php is missing.
    'mail' => $mail,

    // Enquiries are always appended to this file, and mailed when SMTP is set.
    // It is a CSV behind a PHP guard line, so it cannot be downloaded from the web on any server.
    'enquiries_file' => __DIR__ . '/storage/enquiries.csv.php',
];
