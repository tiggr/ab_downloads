<?php

$EM_CONF[$_EXTKEY] = [
    'title'              => 'Modern Downloads',
    'description'        => 'This is a modern download plugin based on the Modern Linklist (ab_linklist) extension.',
    'category'           => 'plugin',
    'shy'                => 0,
    'version'            => '7.6.5',
    'dependencies'       => '',
    'conflicts'          => '',
    'priority'           => '',
    'loadOrder'          => '',
    'module'             => 'mod1',
    'state'              => 'stable',
    'uploadfolder'       => 1,
    'createDirs'         => 'uploads/tx_abdownloads/categoryImages,uploads/tx_abdownloads/downloadImages,uploads/tx_abdownloads/files',
    'modify_tables'      => 'be_groups,be_users',
    'clearcacheonload'   => 1,
    'lockType'           => '',
    'author'             => 'Andreas Bulling',
    'author_email'       => 'typo3@andreas-bulling.de',
    'author_company'     => '',
    'CGLcompliance'      => null,
    'CGLcompliance_note' => null,
    'constraints'        =>
        [
            'depends'   =>
                [
                    'cms'                => '',
                    'static_info_tables' => '2.0.0',
                    'php'                => '4.1.0-0.0.0',
                    'typo3'              => '3.6.0-0.0.0',
                ],
            'conflicts' =>
                [
                ],
            'suggests'  =>
                [
                ],
        ],
];
