<?php
return [
    'backend' => [
        'frontName' => 'admin'
    ],
    'install' => [
        'date' => 'Thu, 04 Jun 2020 06:10:03 +0000'
    ],
    'crypt' => [
        'key' => 'JYq6xxX3yiIg2AWUXSWXEBEhrUN2Qr0b'
    ],
    'session' => [
        'save' => 'files'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'localhost',
                'dbname' => 'uxethwwvet',
                'username' => 'uxethwwvet',
                'password' => 'wtTZBe3Jp5',
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;',
                'active' => '1'
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'developer',
    'cache_types' => [
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'eav' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'full_page' => 0,
        'translate' => 1,
        'config_webservice' => 1,
        'compiled_config' => 1,
        'customer_notification' => 1,
        'vertex' => 1
    ],
    'cache' => [
        'frontend' => [
            'default' => [
                'id_prefix' => 'uxethwwvet_'
            ],
            'page_cache' => [
                'id_prefix' => 'uxethwwvet_'
            ]
        ]
    ],
    'downloadable_domains' => [
        'www.varsidhi.com'
    ]
];
