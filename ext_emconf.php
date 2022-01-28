<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Modern Downloads',
    'description' => 'This is a modern download plugin based on the Modern Linklist (ab_linklist) extension.',
    'category' => 'plugin',
    'version' => '10.4.0',
    'state' => 'stable',
    'uploadfolder' => true,
    'createDirs' => 'uploads/tx_abdownloads/categoryImages,uploads/tx_abdownloads/downloadImages,uploads/tx_abdownloads/files',
    'clearCacheOnLoad' => true,
    'author' => 'Andreas Bulling',
    'author_email' => 'typo3@andreas-bulling.de',
    'author_company' => '',
    'constraints' =>
        [
            'depends' =>
                [
                    'static_info_tables' => '2.0.0',
                    'php' => '4.1.0-0.0.0',
                    'typo3' => '9.5.0-10.4.99',
                ],
            'conflicts' =>
                [
                ],
            'suggests' =>
                [
                ],
        ],
];
