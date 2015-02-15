<?php

return [
    'covers' => [
      'noCover' => 'no-cover.jpg'
    ],
    'paths' => [
        'covers' => storage_path('covers'),
        'epubs' => storage_path('epubs'),
        'epubsSeeds' => base_path('database' . DIRECTORY_SEPARATOR . 'seeds' . DIRECTORY_SEPARATOR . 'epubs')
    ]
];