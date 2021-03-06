<?php
return [
    'doctrine' => [
        'driver' => [
            'odm_default' => [
                'drivers' => [
                    'ValuFileStorage\Model' => 'ValuFileStorage'
                ]
            ],
            'ValuFileStorage' => [
                'class' => 'Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver',
                'paths' => [
                    __DIR__ . '/../src/ValuFileStorage/Model'
                ]
            ]
        ]
    ],
    'valu_so' => [
        'services' => [
            'ValuFileStorageMongoFile' => [
                'name' => 'FileStorage.File',
                'factory' => 'ValuFileStorage\\Service\\MongoFileServiceFactory',
                'options' => [
                    'url_scheme' => 'mongofs',
                ],
            ],
            'ValuFileStorageLocalFile' => [
                'name' => 'FileStorage.File',
                'factory' => 'ValuFileStorage\\Service\\LocalFileServiceFactory',
                'options' => [
                    'url_scheme' => 'file',
                    'hashed_dir_levels' => 0,
                    'paths' => [
                        'tmp' => 'data/filestorage/tmp',
                        'files' => 'data/filestorage/files',
                    ]
                ],
            ],
            'ValuFileStorageSetup' => [
                'name' => 'ValuFileStorage.Setup',
                'class' => 'ValuFileStorage\\Service\\SetupService'
            ],
        ],
    ],
    'array_adapter' => [
        'model_listener' => [
            'namespaces' => [
                'ValuFileStorage' => 'ValuFileStorage\\Model\\'
            ]
        ]
    ],
    'file_storage' => [
        'whitelist' => [
            'tmp' => 'file://.*' . sys_get_temp_dir(),
            'dataurl' => 'data:.*'
        ]
    ],
];
