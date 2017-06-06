<?php
    return [
        'default' => 'mongodb',
        'connections' => [
            'mongodb' => [ 
                'driver' => 'mongodb',
                'host' => env('DB_HOST', 'localhost'),
                'port' => env('DB_PORT', 27017),
                'database' => env('DB_DATABASE'),
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD')
            ],
            'mongodb1' => [ 
                'driver' => 'mongodb',
                'host' => env('DB_HOST', 'localhost'),
                'port' => env('DB_PORT', 27017),
                'database' => env('DB_DATABASE1'),
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD')
            ],
        ],
        'migrations' => 'migrations',
    ];
