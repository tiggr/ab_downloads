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

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_abdownloads_category');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToInsertRecords('tx_abdownloads_category');

return [
    'ctrl' => [
        'title' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_category',
        'label' => 'label',
        'tstamp' => 'tstamp',
        'thumbnail' => 'image',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'languageField' => 'sys_language_uid',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
        'default_sortby' => 'ORDER BY crdate',
        'treeParentField' => 'parent_category',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group',
        ],
        'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("ab_downloads") . 'tca.php',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath("ab_downloads") . 'icon_tx_abdownloads_category.gif',
        'prependAtCopy' => $configArray['prependAtCopy'] ? 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.prependAtCopy' : '',
        'mainpalette' => '2, 10',
    ],
    'feInterface' => [
        'fe_admin_fieldList' => 'hidden, fe_group, label, parent_category, image',
    ],
    'feInterface' => $GLOBALS['TCA']['tx_abdownloads_category']['feInterface'],
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
                'foreign_table' => 'tx_abdownloads_category',
                'foreign_table_where' => 'AND tx_abdownloads_category.uid=###REC_FIELD_l18n_parent### AND tx_abdownloads_category.sys_language_uid IN (-1,0)',
            ],
        ],
        'l18n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
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
            'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_category.label',
            'config' => [
                'type' => 'input',
                'size' => '40',
                'eval' => 'required',
            ],
        ],
        'description' => [
            'l10n_mode' => $l10n_mode,
            'exclude' => 1,
            'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_category.description',
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
        'parent_category' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_category.parent_category',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectTree',
                'size' => 10,
                'size' => $configArray['categoryTreeHeigth'],
                'minitems' => 0,
                'maxitems' => 2,
                'foreign_table' => 'tx_abdownloads_category',
                'treeConfig' => [
                    'parentField' => 'parent_category',
                    'appearance' => [
                        'showHeader' => true,
                        'expandAll' => true,
                    ],
                ],
                'fieldControl' => ['addRecord' => ['disabled' => false, 'options' => ['title' => 'LLL:EXT:ab_downloads/locallang_tca.php:ab_downloads.createNewCategory', 'table' => 'tx_abdownloads_category', 'pid' => '###CURRENT_PID###', 'setValue' => 'set']], 'editPopup' => ['disabled' => false, 'options' => ['title' => 'LLL:EXT:ab_downloads/locallang_tca.php:ab_downloads.editCategory']], 'listModule' => ['disabled' => false, 'options' => ['title' => 'LLL:EXT:ab_downloads/locallang_tca.php:ab_downloads.listCategories', 'table' => 'tx_abdownloads_category', 'pid' => '###CURRENT_PID###']]],

            ],
        ],
        'image' => [
            'l10n_mode' => $l10n_mode_image,
            'exclude' => 1,
            'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_category.image',
            'config' => [
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
                'max_size' => '30000',
                'uploadfolder' => 'uploads/tx_abdownloads/categoryImages',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
            // TODO check TCA migration by rector
            // 'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('image', ['max_size' => '30000', 'uploadfolder' => 'uploads/tx_abdownloads/categoryImages', 'maxitems' => 1], $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']),
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'label,--palette--;;1,description,--palette--;;2,image,parent_category'],
    ],
    'palettes' => [
        '1' => ['showitem' => 'hidden, starttime, endtime'],
        '10' => ['showitem' => 'fe_group'],
        '2' => ['showitem' => 'l18n_parent, sys_language_uid'],
    ],

];
