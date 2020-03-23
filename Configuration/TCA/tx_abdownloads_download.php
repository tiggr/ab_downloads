<?php
$configArray = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ab_downloads']) ?? [];
$l10n_mode = ( $configArray['l10n_mode_prefixLangTitle'] ? 'prefixLangTitle' : '' );
//$l10n_mode_author = ( $configArray['l10n_mode_prefixLangTitle'] ? 'mergeIfNotBlank' : '' );

// l10n_mode for the homepage field
$l10n_mode_homepage = ( $configArray['l10n_mode_homepageExclude'] ? 'exclude' : 'mergeIfNotBlank' );

// l10n_mode for the image field
$l10n_mode_image = ( $configArray['l10n_mode_imageExclude'] ? 'exclude' : 'mergeIfNotBlank' );

// l10n_mode for the file field
$l10n_mode_file = ( $configArray['l10n_mode_fileExclude'] ? 'exclude' : 'mergeIfNotBlank' );

// Hide new localizations
$hideNewLocalizations = ( $configArray['hideNewLocalizations'] ? 'mergeIfNotBlank' : '' );

// Use template references
$useTemplateReferences = ( $configArray['useTemplateReferences'] ? '' : '' );

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages( 'tx_abdownloads_download' );
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToInsertRecords('tx_abdownloads_download');

return [
    'ctrl'        => [
        'title'                           => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download',
        'label'                           => 'label',
        'tstamp'                          => 'tstamp',
        'thumbnail'                       => 'image',
        'transOrigPointerField'           => 'l18n_parent',
        'transOrigDiffSourceField'        => 'l18n_diffsource',
        'languageField'                   => 'sys_language_uid',
        'dividers2tabs'                   => $configArray['noTabDividers'] ? false : true,
        'shadowColumnsForNewPlaceholders' => 'sys_language_uid, l18n_parent, starttime, endtime, fe_group',
        'crdate'                          => 'crdate',
        'cruser_id'                       => 'cruser_id',
        'editlock'                        => 'editlock',
        'sortby'                          => 'sorting',
        'default_sortby'                  => 'ORDER BY crdate',
        'delete'                          => 'deleted',
        'enablecolumns'                   => [
            'disabled'  => 'hidden',
            'starttime' => 'starttime',
            'endtime'   => 'endtime',
            'fe_group'  => 'fe_group',
        ],
        'dynamicConfigFile'               => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("ab_downloads") . 'tca.php',
        'iconfile'                        => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("ab_downloads") . 'icon_tx_abdownloads_download.gif',
        'prependAtCopy'                   => $configArray['prependAtCopy'] ? 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.prependAtCopy' : '',
        'mainpalette'                     => '10',
        'versioning_followPages'          => true,
        'origUid'                         => 't3_origuid',
    ],
    'feInterface' => [
        'fe_admin_fieldList' => 'hidden, fe_group, label, description, sponsored_description, license, language_uid, clicks, rating, votes, status, category, contact, homepage, image, file',
    ],
    'interface'   => [
        'showRecordFieldList' => 'hidden, fe_group, label, description, sponsored_description, license, language_uid, clicks, rating, votes, status, category, contact, homepage, image, file',
    ],
    'feInterface' => $GLOBALS['TCA']['tx_abdownloads_download']['feInterface'],
    'columns'     => [
        'sys_language_uid'      => [
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config'  => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'special'    => 'languages',
                'items'      => [
                    [
                        'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple',
                    ],
                ],
                'default'    => 0,
            ],
        ],
        'l18n_parent'           => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude'     => 1,
            'label'       => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config'      => [
                'type'                => 'select',
                'renderType'          => 'selectSingle',
                'items'               => [
                    ['', 0],
                ],
                'foreign_table'       => 'tx_abdownloads_download',
                'foreign_table_where' => 'AND tx_abdownloads_download.uid=###REC_FIELD_l18n_parent### AND tx_abdownloads_download.sys_language_uid IN (-1,0)',
            ],
        ],
        'l18n_diffsource'       => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'editlock'              => [
            'l10n_mode' => 'mergeIfNotBlank',
            'exclude'   => 1,
            'label'     => 'LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:editlock',
            'config'    => [
                'type' => 'check',
            ],
        ],
        'hidden'                => [
            'l10n_mode' => $hideNewLocalizations,
            'exclude'   => 1,
            'label'     => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config'    => [
                'type'    => 'check',
                'default' => '0',
            ],
        ],
        'starttime'             => [
            'l10n_mode' => 'mergeIfNotBlank',
            'exclude'   => 1,
            'label'     => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config'    => [
                'type'     => 'input',
                'size'     => '10',
                'max'      => '20',
                'eval'     => 'datetime',
                'checkbox' => '0',
                'default'  => '0',
            ],
        ],
        'endtime'               => [
            'l10n_mode' => 'mergeIfNotBlank',
            'exclude'   => 1,
            'label'     => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config'    => [
                'type'     => 'input',
                'size'     => '8',
                'max'      => '20',
                'eval'     => 'datetime',
                'checkbox' => '0',
                'default'  => '0',
                'range'    => [
                    'upper' => mktime(0, 0, 0, 12, 31, 2020),
                    'lower' => mktime(0, 0, 0, date('m') - 1, date('d'), date('Y')),
                ],
            ],
        ],
        'fe_group'              => [
            'l10n_mode' => 'mergeIfNotBlank',
            'exclude'   => 1,
            'label'     => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
            'config'    => [
                'type'          => 'select',
                'renderType'    => 'selectMultipleSideBySide',
                'size'          => 5,
                'maxitems'      => 20,
                'items'         => [
                    ['LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hide_at_login', -1],
                    ['LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.any_login', -2],
                    ['LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.usergroups', '--div--'],
                ],
                'exclusiveKeys' => '-1,-2',
                'foreign_table' => 'fe_groups',
            ],
        ],
        'label'                 => [
            'l10n_mode' => $l10n_mode,
            'exclude'   => 1,
            'label'     => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.label',
            'config'    => [
                'type' => 'input',
                'size' => '40',
                'eval' => 'required',
            ],
        ],
        't3ver_label'           => [
            'displayCond' => 'FIELD:t3ver_label:REQ:true',
            'label'       => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config'      => [
                'type' => 'none',
                'cols' => 27,
            ],
        ],
        'description'           => [
            'l10n_mode'     => $l10n_mode,
            'exclude'       => 1,
            'label'         => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.description',
            'defaultExtras' => 'richtext:rte_transform[mode=ts_css]',
            'config'        => [
                'type'    => 'text',
                'cols'    => '50',
                'rows'    => '5',
                'softref' => 'typolink_tag,images,email[subst],url',
                'wizards' => [
                    'RTE' => [
                        'notNewRecords' => 1,
                        'RTEonly'       => 1,
                        'type'          => 'script',
                        'title'         => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext.W.RTE',
                        'icon'          => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_rte.gif',
                        'module'        => [
                            'name' => 'wizard_rte',
                        ],
                    ],
                ],
            ],
        ],
        'tags'                  => [
            'l10n_mode' => $l10n_mode,
            'exclude'   => 1,
            'label'     => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.tags',
            'config'    => [
                'type' => 'input',
                'size' => '40',
            ],
        ],
        'sponsored_description' => [
            'l10n_mode'     => $l10n_mode,
            'exclude'       => 1,
            'label'         => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.sponsored_description',
            'defaultExtras' => 'richtext:rte_transform[mode=ts_css]',
            'config'        => [
                'type'    => 'text',
                'cols'    => '50',
                'rows'    => '5',
                'softref' => 'typolink_tag,images,email[subst],url',
                'wizards' => [
                    'RTE' => [
                        'notNewRecords' => 1,
                        'RTEonly'       => 1,
                        'type'          => 'script',
                        'title'         => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext.W.RTE',
                        'icon'          => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_rte.gif',
                        'module'        => [
                            'name' => 'wizard_rte',
                        ],
                    ],
                ],
            ],
        ],
        'license'               => [
            'l10n_mode' => $l10n_mode,
            'exclude'   => 1,
            'label'     => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.license',
            'config'    => [
                'type' => 'input',
                'size' => '40',
            ],
        ],
        'language_uid'          => [
            'l10n_mode' => 'exclude',
            'exclude'   => 1,
            'label'     => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config'    => [
                'type'                => 'select',
                'renderType'          => 'selectSingle',
                'foreign_table'       => 'static_languages',
                'foreign_table_where' => '',
                'items'               => [
                    ['', '0'],
                ],
            ],
        ],
        'clicks'                => [
            'l10n_mode' => 'exclude',
            'exclude'   => 1,
            'label'     => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.clicks',
            'config'    => [
                'type'     => 'input',
                'size'     => '5',
                'max'      => '10',
                'eval'     => 'int',
                'checkbox' => '0',
                'default'  => 0,
            ],
        ],
        'rating'                => [
            'l10n_mode' => 'exclude',
            'exclude'   => 1,
            'label'     => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.rating',
            'config'    => [
                'type'     => 'input',
                'size'     => '5',
                'max'      => '5',
                'eval'     => 'int',
                'checkbox' => '0',
                'range'    => [
                    'upper' => '10',
                    'lower' => '0',
                ],
                'default'  => 0,
            ],
        ],
        'votes'                 => [
            'l10n_mode' => 'exclude',
            'exclude'   => 1,
            'label'     => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.votes',
            'config'    => [
                'type'     => 'input',
                'size'     => '5',
                'max'      => '10',
                'eval'     => 'int',
                'checkbox' => '0',
                'default'  => 0,
            ],
        ],
        'status'                => [
            'l10n_mode' => 'exclude',
            'exclude'   => 1,
            'label'     => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.status',
            'config'    => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => [
                    ['LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.status.i.0', '0'],
                    ['LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.status.i.1', '1'],
                    ['LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.status.i.2', '2'],
                    ['LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.status.i.3', '3'],
                ],
            ],
        ],
        'category'              => [
            'l10n_mode' => 'exclude',
            'exclude'   => 1,
            'label'     => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.category',
            'config'    => [
                'type'          => 'select',
                'renderType'    => 'selectTree',
                'foreign_table' => 'tx_abdownloads_category',
                'size'          => 10,
                'autoSizeMax'   => $configArray['categoryTreeHeigth'],
                'minitems'      => 0,
                'maxitems'      => 500,
                'MM'            => 'tx_abdownloads_category_mm',
                'treeConfig'    => [
                    'parentField' => 'parent_category',
                    'appearance'  => [
                        'expandAll'  => true,
                        'showHeader' => true,
                    ],
                ],
                'wizards'       => [
                    'add'  => [
                        'type'   => 'script',
                        'title'  => 'LLL:EXT:ab_downloads/locallang_tca.php:ab_downloads.createNewCategory',
                        'icon'   => 'actions-add',
                        'params' => [
                            'table'    => 'tx_abdownloads_category',
                            'pid'      => '###CURRENT_PID###',
                            'setValue' => 'set',
                        ],
                        'module' => [
                            'name' => 'wizard_add',
                        ],
                    ],
                    'edit' => [
                        'type'                     => 'popup',
                        'title'                    => 'LLL:EXT:ab_downloads/locallang_tca.php:ab_downloads.editCategory',
                        'module'                   => [
                            'name' => 'wizard_edit',
                        ],
                        'popup_onlyOpenIfSelected' => 1,
                        'icon'                     => 'actions-open',
                        'JSopenParams'             => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
                    ],
                    'list' => [
                        'type'   => 'script',
                        'title'  => 'LLL:EXT:ab_downloads/locallang_tca.php:ab_downloads.listCategories',
                        'icon'   => 'actions-system-list-open',
                        'params' => [
                            'table' => 'tx_abdownloads_category',
                            'pid'   => '###CURRENT_PID###',
                        ],
                        'module' => [
                            'name' => 'wizard_list',
                        ],
                    ],
                ],
            ],
        ],
        'contact'               => [
            'l10n_mode' => $l10n_mode,
            'exclude'   => 1,
            'label'     => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.contact',
            'config'    => [
                'type' => 'input',
                'size' => '40',
            ],
        ],
        'homepage'              => [
            'l10n_mode' => $l10n_mode_homepage,
            'exclude'   => 1,
            'label'     => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.homepage',
            'config'    => [
                'type'     => 'input',
                'size'     => '40',
                'max'      => '255',
                'checkbox' => '',
                'eval'     => 'trim',
                'wizards'  => [
                    '_PADDING' => 2,
                    'link'     => [
                        'type'         => 'popup',
                        'title'        => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_link_formlabel',
                        'icon'         => 'actions-wizard-link',
                        'module'       => [
                            'name' => 'wizard_link',
                        ],
                        'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
                        #'params' => [
                        #    'blindLinkOptions' => 'folder',
                        #    'blindLinkFields' => 'class, target',
                        #    'allowedExtensions' => 'jpg',
                        #],
                    ],
                ],
            ],
        ],
        'image'                 => [
            'l10n_mode' => $l10n_mode_image,
            'exclude'   => 1,
            'label'     => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.image',
            'config'    => [
                'type'          => 'group',
                'internal_type' => 'file',
                'allowed'       => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
                'max_size'      => '30000',
                'uploadfolder'  => 'uploads/tx_abdownloads/downloadImages',
                'show_thumbs'   => '1',
                'size'          => 1,
                'minitems'      => 0,
                'maxitems'      => 1,
            ],
        ],
        'file'                  => [
            'l10n_mode' => $l10n_mode_file,
            'exclude'   => 1,
            'label'     => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.file',
            'config'    => [
                'type'          => 'group',
                'internal_type' => 'file',
                'allowed'       => '',
                'disallowed'    => 'php, php3',
                'max_size'      => '500000',
                'uploadfolder'  => 'uploads/tx_abdownloads/files',
                'show_thumbs'   => '0',
                'size'          => 1,
                'minitems'      => 0,
                'maxitems'      => 1,
            ],
        ],
        'crdate'                => [
            'l10n_mode' => 'mergeIfNotBlank',
            'exclude'   => 1,
            'label'     => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.crdate',
            'config'    => [
                'type'    => 'input',
                'size'    => '10',
                'max'     => '20',
                'eval'    => 'datetime',
                'default' => mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('Y')),
            ],
        ],
        'sponsored'             => [
            'l10n_mode' => 'exclude',
            'exclude'   => 1,
            'label'     => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.sponsored',
            'config'    => [
                'type'    => 'check',
                'default' => 0,
            ],
        ],
    ],
    'types'       => [
        '0' => ['showitem' => 'label;;1;;, description;;2;richtext:rte_transform[flag=rte_enabled|mode=ts];, tags, license, language_uid, crdate;;3;;, status, --div--;Relations, contact, homepage, category, image, file, --div--;Sponsorship, sponsored, sponsored_description;;2;richtext:rte_transform[flag=rte_enabled|mode=ts];, --div--;Statistics, clicks, rating, votes'],
    ],
    'palettes'    => [
        '1'  => ['showitem' => 'hidden, starttime, endtime, editlock'],
        '10' => ['showitem' => 'fe_group'],
        '2'  => ['showitem' => 'l18n_parent, sys_language_uid'],
        '3'  => ['showitem' => 't3ver_label'],
    ],
];
