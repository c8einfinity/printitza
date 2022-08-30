<?php
return [
    'backend' => [
        'frontName' => 'admin'
    ],
    'crypt' => [
        'key' => '41cd4a8d47282968463adec8bd93e609'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'localhost',
                'dbname' => 'onlineprintshop_production_final',
                'username' => 'onlineprintshop',
                'password' => 'zFGyiAHd4AB8bzC',
                'active' => '1',
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;'
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'default',
    'session' => [
        'save' => 'files'
    ],
    'cache_types' => [
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'full_page' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'translate' => 1,
        'config_webservice' => 1,
        'compiled_config' => 1,
        'vertex' => 1,
        'wp_gtm_categories' => 1
    ],
    'install' => [
        'date' => 'Sat, 11 Nov 2017 06:34:28 +0000'
    ],
    'system' => [
        'default' => [
            'dev' => [
                'debug' => [
                    'debug_logging' => '1'
                ]
            ]
        ]
    ],
    'dev' => [
        'debug' => [
            'debug_logging' => 1
        ]
    ],
    'cache' => [
        'frontend' => [
            'default' => [
                'id_prefix' => 'dc5_'
            ],
            'page_cache' => [
                'id_prefix' => 'dc5_'
            ]
        ]
    ]
];
