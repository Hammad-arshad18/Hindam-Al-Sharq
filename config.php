<?php
/**
 * Site-wide settings. Edit this file to change company details.
 * Empty contact values are simply not shown on the site.
 */
return [
    'default_lang' => 'ar',
    'languages'    => ['ar', 'en'],

    // Contact details — fill these in; blank ones stay hidden.
    'email'    => '',   // also the address enquiries are mailed to
    'phone'    => '',   // e.g. +218 9X XXX XXXX
    'whatsapp' => '',   // digits only, e.g. 2189XXXXXXXX

    // Enquiries are always appended to this file, and mailed too if 'email' is set.
    // It is a CSV behind a PHP guard line, so it cannot be downloaded from the web on any server.
    'enquiries_file' => __DIR__ . '/storage/enquiries.csv.php',
];
