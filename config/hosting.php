<?php

/**
 * Hosting Configuration for ISO Digital
 * 
 * Gunakan file ini sebagai referensi untuk konfigurasi production.
 * Copy nilai-nilai ini ke file .env di hosting.
 */

return [
    'database' => [
        'connection' => 'mysql',
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'eliteac1_ISO_DIGITAL',
        'username' => 'eliteac1_iso_digital',
        'password' => '123Q@zaqw',
    ],

    'app' => [
        'name' => 'ISO Digital',
        'env' => 'production',
        'debug' => false,
        'url' => 'https://iso-digital.eliteacademia.id',
    ],
];
