<?php
header('Content-Type: application/json; charset=utf-8');

// Static list for now (replace with your real promo images later)
$promos = [
    [
        'src' => '/frontend/assets/images/icon-7797704_1280.png',
        'alt' => 'Promo 1',
        'href' => '#'
    ],
    [
        'src' => '/frontend/assets/images/icon-7797704_1280.png',
        'alt' => 'Promo 2',
        'href' => '#'
    ],
    [
        'src' => '/frontend/assets/images/icon-7797704_1280.png',
        'alt' => 'Promo 3',
        'href' => '#'
    ]
];

echo json_encode($promos);
