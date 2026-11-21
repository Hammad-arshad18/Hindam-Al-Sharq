<?php
// One entry per product line, in the order they are shown.
// cloth: the colour and weave the live fabric changes into (see assets/js/cloth.js).
// photo: the picture shown in the list on the home page. The line's own page uses l-<key>-a/b/c.webp.
return [
    'office'      => ['cloth' => 'navy',   'photo' => 'g-office'],
    'workwear'    => ['cloth' => 'drill',  'photo' => 'g-workwear'],
    'medical'     => ['cloth' => 'scrub',  'photo' => 'g-medical'],
    'hospitality' => ['cloth' => 'pearl',  'photo' => 'g-chef'],
    'school'      => ['cloth' => 'slate',  'photo' => 'g-school'],
    'military'    => ['cloth' => 'olive',  'photo' => 'g-military'],
    'fabric'      => ['cloth' => 'custom', 'photo' => 'rolls'],
];
