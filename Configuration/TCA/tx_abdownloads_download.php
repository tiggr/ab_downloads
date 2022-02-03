<?php

$configArray = unserialize(\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('ab_downloads')) ?? [];
$l10n_mode = ($configArray['l10n_mode_prefixLangTitle'] ? 'prefixLangTitle' : '');
//$l10n_mode_author = ( $configArray['l10n_mode_prefixLangTitle'] ? 'mergeIfNotBlank' : '' );

// l10n_mode for the homepage field
$l10n_mode_homepage = ($configArray['l10n_mode_homepageExclude'] ? 'exclude' : 'mergeIfNotBlank');

// l10n_mode for the image field
$l10n_mode_image = ($configArray['l10n_mode_imageExclude'] ? 'exclude' : 'mergeIfNotBlank');

// l10n_mode for the file field
$l10n_mode_file = ($configArray['l10n_mode_fileExclude'] ? 'exclude' : 'mergeIfNotBlank');

// Hide new localizations
$hideNewLocalizations = ($configArray['hideNewLocalizations'] ? 'mergeIfNotBlank' : '');

// Use template references
$useTemplateReferences = ($configArray['useTemplateReferences'] ? '' : '');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_abdownloads_download');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToInsertRecords('tx_abdownloads_download');

return [
    'ctrl' => [
        'title' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download',
        'label' => 'label',
        'tstamp' => 'tstamp',
        'thumbnail' => 'image',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'languageField' => 'sys_language_uid',
        'shadowColumnsForNewPlaceholders' => 'sys_language_uid, l18n_parent, starttime, endtime, fe_group',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'editlock' => 'editlock',
        'sortby' => 'sorting',
        'default_sortby' => 'ORDER BY crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group',
        ],
        'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("ab_downloads") . 'tca.php',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("ab_downloads") . 'icon_tx_abdownloads_download.gif',
        'prependAtCopy' => $configArray['prependAtCopy'] ? 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.prependAtCopy' : '',
        'mainpalette' => '10',
        'origUid' => 't3_origuid',
    ],
    'feInterface' => [
        'fe_admin_fieldList' => 'hidden, fe_group, label, description, sponsored_description, license, language_uid, clicks, rating, votes, status, category, contact, homepage, image, file',
    ],
    'feInterface' => $GLOBALS['TCA']['tx_abdownloads_download']['feInterface'],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple',
                    ],
                ],
                'default' => 0,
            ],
        ],
        'l18n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_abdownloads_download',
                'foreign_table_where' => 'AND tx_abdownloads_download.uid=###REC_FIELD_l18n_parent### AND tx_abdownloads_download.sys_language_uid IN (-1,0)',
            ],
        ],
        'l18n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'editlock' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:editlock',
            'config' => [
                'type' => 'check', ['behaviour' => ['allowLanguageSynchronization' => true]],
            ],
        ],
        'hidden' => [
            'l10n_mode' => $hideNewLocalizations,
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],
        'starttime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => '10',
                'eval' => 'datetime',
                'checkbox' => '0',
                'default' => '0',
                'renderType' => 'inputDateTime',
                ['behaviour' => ['allowLanguageSynchronization' => true]],
            ],
        ],
        'endtime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => '8',
                'eval' => 'datetime',
                'checkbox' => '0',
                'default' => '0',
                'range' => [
                    'upper' => mktime(0, 0, 0, 12, 31, 2020),
                    'lower' => mktime(0, 0, 0, date('m') - 1, date('d'), date('Y')),
                ],
                'renderType' => 'inputDateTime',
                ['behaviour' => ['allowLanguageSynchronization' => true]],
            ],
        ],
        'fe_group' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'size' => 5,
                'maxitems' => 20,
                'items' => [
                    ['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hide_at_login', -1],
                    ['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.any_login', -2],
                    ['LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.usergroups', '--div--'],
                ],
                'exclusiveKeys' => '-1,-2',
                'foreign_table' => 'fe_groups',
                ['behaviour' => ['allowLanguageSynchronization' => true]],
            ],
        ],
        'label' => [
            'l10n_mode' => $l10n_mode,
            'exclude' => 1,
            'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.label',
            'config' => [
                'type' => 'input',
                'size' => '40',
                'eval' => 'required',
            ],
        ],
        't3ver_label' => [
            'displayCond' => 'FIELD:t3ver_label:REQ:true',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'none',
                'cols' => 27,
            ],
        ],
        'description' => [
            'l10n_mode' => $l10n_mode,
            'exclude' => 1,
            'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.description',
            'config' => [
                'type' => 'text',
                'cols' => '50',
                'rows' => '5',
                'softref' => 'typolink_tag,images,email[subst],url',
                'enableRichtext' => true,
                'richtextConfiguration' => 'default',
                'fieldControl' => ['fullScreenRichtext' => ['disabled' => false, 'options' => ['title' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext.W.RTE']]],
            ],
        ],
        'tags' => [
            'l10n_mode' => $l10n_mode,
            'exclude' => 1,
            'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.tags',
            'config' => [
                'type' => 'input',
                'size' => '40',
            ],
        ],
        'sponsored_description' => [
            'l10n_mode' => $l10n_mode,
            'exclude' => 1,
            'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.sponsored_description',
            'config' => [
                'type' => 'text',
                'cols' => '50',
                'rows' => '5',
                'softref' => 'typolink_tag,images,email[subst],url',
                'enableRichtext' => true,
                'richtextConfiguration' => 'default',
                'fieldControl' => ['fullScreenRichtext' => ['disabled' => false, 'options' => ['title' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext.W.RTE']]],
            ],
        ],
        'license' => [
            'l10n_mode' => $l10n_mode,
            'exclude' => 1,
            'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.license',
            'config' => [
                'type' => 'input',
                'size' => '40',
            ],
        ],
        'language_uid' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'static_languages',
                'foreign_table_where' => '',
                'items' => [
                    ['', '0'],
                ],
            ],
        ],
        'clicks' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.clicks',
            'config' => [
                'type' => 'input',
                'size' => '5',
                'max' => '10',
                'eval' => 'int',
                'checkbox' => '0',
                'default' => 0,
            ],
        ],
        'rating' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.rating',
            'config' => [
                'type' => 'input',
                'size' => '5',
                'max' => '5',
                'eval' => 'int',
                'checkbox' => '0',
                'range' => [
                    'upper' => '10',
                    'lower' => '0',
                ],
                'default' => 0,
            ],
        ],
        'votes' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.votes',
            'config' => [
                'type' => 'input',
                'size' => '5',
                'max' => '10',
                'eval' => 'int',
                'checkbox' => '0',
                'default' => 0,
            ],
        ],
        'status' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.status',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.status.i.0', '0'],
                    ['LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.status.i.1', '1'],
                    ['LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.status.i.2', '2'],
                    ['LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.status.i.3', '3'],
                ],
            ],
        ],
        'category' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.category',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectTree',
                'foreign_table' => 'tx_abdownloads_category',
                'size' => 10,
                'size' => $configArray['categoryTreeHeigth'],
                'minitems' => 0,
                'maxitems' => 500,
                'MM' => 'tx_abdownloads_category_mm',
                'treeConfig' => [
                    'parentField' => 'parent_category',
                    'appearance' => [
                        'expandAll' => true,
                        'showHeader' => true,
                    ],
                ],
                'fieldControl' => ['addRecord' => ['disabled' => false, 'options' => ['title' => 'LLL:EXT:ab_downloads/locallang_tca.php:ab_downloads.createNewCategory', 'table' => 'tx_abdownloads_category', 'pid' => '###CURRENT_PID###', 'setValue' => 'set']], 'editPopup' => ['disabled' => false, 'options' => ['title' => 'LLL:EXT:ab_downloads/locallang_tca.php:ab_downloads.editCategory']], 'listModule' => ['disabled' => false, 'options' => ['title' => 'LLL:EXT:ab_downloads/locallang_tca.php:ab_downloads.listCategories', 'table' => 'tx_abdownloads_category', 'pid' => '###CURRENT_PID###']]],
            ],
        ],
        'contact' => [
            'l10n_mode' => $l10n_mode,
            'exclude' => 1,
            'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.contact',
            'config' => [
                'type' => 'input',
                'size' => '40',
            ],
        ],
        'homepage' => [
            'l10n_mode' => $l10n_mode_homepage,
            'exclude' => 1,
            'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.homepage',
            'config' => [
                'type' => 'input',
                'size' => '40',
                'max' => '255',
                'checkbox' => '',
                'eval' => 'trim',
                'renderType' => 'inputLink',
                'fieldControl' => ['linkPopup' => ['options' => ['title' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_link_formlabel']]],
            ],
        ],
        'image' => [
            'l10n_mode' => $l10n_mode_image,
            'exclude' => 1,
            'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.image',
            'config' => [
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
                'max_size' => '30000',
                'uploadfolder' => 'uploads/tx_abdownloads/downloadImages',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
            // TODO check TCA migration by rector
            // 'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('image', ['max_size' => '30000', 'uploadfolder' => 'uploads/tx_abdownloads/downloadImages', 'maxitems' => 1], $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']),
        ],
        'file' => [
            'l10n_mode' => $l10n_mode_file,
            'exclude' => 1,
            'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.file',
            'config' => [
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => '',
                'disallowed' => 'php, php3',
                'max_size' => '500000',
                'uploadfolder' => 'uploads/tx_abdownloads/files',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'fieldWizard' => ['fileThumbnails' => ['disabled' => true]],
            ],
            // TODO check TCA migration by rector
            // 'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('file', ['max_size' => '500000', 'uploadfolder' => 'uploads/tx_abdownloads/files', 'maxitems' => 1], ''),
        ],
        'crdate' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.crdate',
            'config' => [
                'type' => 'input',
                'size' => '10',
                'eval' => 'datetime',
                'default' => mktime(date('H'), date('i'), date('s'), date('m'), date('d'), date('Y')),
                'renderType' => 'inputDateTime',
                ['behaviour' => ['allowLanguageSynchronization' => true]],
            ],
        ],
        'sponsored' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.sponsored',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'label,--palette--;;1,description,--palette--;;2,tags,license,language_uid,crdate,--palette--;;3,status,--div--;Relations,contact,homepage,category,image,file,--div--;Sponsorship,sponsored,sponsored_description,--palette--;;2,--div--;Statistics,clicks,rating,votes'],
    ],
    'palettes' => [
        '1' => ['showitem' => 'hidden, starttime, endtime, editlock'],
        '10' => ['showitem' => 'fe_group'],
        '2' => ['showitem' => 'l18n_parent, sys_language_uid'],
        '3' => ['showitem' => 't3ver_label'],
    ],
];
