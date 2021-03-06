<?php
return [
    'doctrine' => [
        'connection' => [
            'odm_default' => [
                'server' => getenv('MONGO_SERVER'),
                'port' => getenv('MONGO_PORT')
            ]
        ],
        'configuration' => [
            'odm_default' => [
                'default_db' => 'valu_file_storage_test'
            ]
        ]
    ],
    'valu_so' => [
        // Ensure that proxy classes are re-generated when they change
        'proxy_auto_create_strategy' => 'mtime',
        'services' => [
            'ValuFileStorageLocalFile' => [
                'options' => [
                    'paths' => [
                        'tmp' => __DIR__ .'/../tests/data/tmp',
                        'files' => __DIR__ .'/../tests/data/tmp',
                    ]
                ],
            ],
        ],
    ],
    'file_storage' => [
        'whitelist' => [
            'tmp' => '^file:///tmp',
            'tmp2' => '^file:///var/tmp',
            'tests' => '^file://.*/valufilestorage/tests/resources/.*',
            'dataurl' => '^data:',
        ]
    ],
];
