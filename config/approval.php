<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Test Mode
    |--------------------------------------------------------------------------
    | When enabled, all emails will be sent to the test email address
    */
    'test_mode' => env('APPROVAL_TEST_MODE', true),
    'test_email' => env('APPROVAL_TEST_EMAIL', 'aris.setyawan@edelweiss.sch.id'),

    /*
    |--------------------------------------------------------------------------
    | Department Coordinators
    |--------------------------------------------------------------------------
    | Mapping of departments to their coordinators
    */
    'coordinators' => [
        'KB/TK' => [
            'name' => 'Armitridesi Shinta Marito',
            'email' => 'armitridesi.marito@edelweiss.sch.id',
        ],
        'SD' => [
            'name' => 'Miske Ferlani',
            'email' => 'miske.ferlani@edelweiss.sch.id',
        ],
        'SMP' => [
            'name' => 'Yudha Hadi Purnama',
            'email' => 'yudha.purnama@edelweiss.sch.id',
        ],
        'PKBM' => [
            'name' => 'Mia Roosmalisa',
            'email' => 'mia.roosmalisa@edelweiss.sch.id',
        ],
        'ICT' => [
            'name' => 'Juarsa Oemardikarta',
            'email' => 'juarsa.oemardikarta@edelweiss.sch.id',
        ],
        'HRD' => [
            'name' => 'Juarsa Oemardikarta',
            'email' => 'juarsa.oemardikarta@edelweiss.sch.id',
        ],
        'Finance & Accounting' => [
            'name' => 'Juarsa Oemardikarta',
            'email' => 'juarsa.oemardikarta@edelweiss.sch.id',
        ],
        'Marketing' => [
            'name' => 'Juarsa Oemardikarta',
            'email' => 'juarsa.oemardikarta@edelweiss.sch.id',
        ],
        'Management' => [
            'name' => 'Ayu Puspitawati',
            'email' => 'ayu.puspitawati@edelweiss.sch.id',
        ],
        'Operator' => [
            'name' => 'Juarsa Oemardikarta',
            'email' => 'juarsa.oemardikarta@edelweiss.sch.id',
        ],
        'Customer Service Officer' => [
            'name' => 'Juarsa Oemardikarta',
            'email' => 'juarsa.oemardikarta@edelweiss.sch.id',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Special Approvers
    |--------------------------------------------------------------------------
    */
    'accounting' => [
        'name' => 'Titis Rahmawati Wijiastuti',
        'email' => 'titis.wijiastuti@edelweiss.sch.id',
    ],

    'chairman' => [
        'name' => 'Juarsa Oemardikarta',
        'email' => 'juarsa.oemardikarta@edelweiss.sch.id',
    ],

    /*
    |--------------------------------------------------------------------------
    | Approval Sequences
    |--------------------------------------------------------------------------
    */
    'pr_sequence' => [
        1 => 'koordinator',
        2 => 'accounting',
        3 => 'ketua_yayasan',
    ],

    'leave_sequence' => [
        1 => 'atasan_langsung',
        2 => 'hrd',
        3 => 'ketua_yayasan',
    ],

    'handover_sequence' => [
        1 => 'ict',
        2 => 'recipient',
    ],

    /*
    |--------------------------------------------------------------------------
    | Departments List
    |--------------------------------------------------------------------------
    */
    'departments' => [
        'KB/TK',
        'SD',
        'SMP',
        'PKBM',
        'ICT',
        'HRD',
        'Finance & Accounting',
        'Marketing',
        'Management',
        'Operator',
        'Customer Service Officer',
    ],
];
