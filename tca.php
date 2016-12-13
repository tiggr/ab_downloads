<?php
/**
 * $Id: tca.php 164 2008-07-04 09:39:51Z andreas $
 */

if( !defined ( 'TYPO3_MODE' ) ) die ( 'Access denied.' );

// Get extension configuration
$configArray = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ab_downloads'] );

// l10n_mode for text fields
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

$TCA['tx_abdownloads_download'] = Array(
	'ctrl' => $TCA['tx_abdownloads_download']['ctrl'],
	'interface' => Array(
		'showRecordFieldList' => 'hidden, fe_group, label, description, sponsored_description, license, language_uid, clicks, rating, votes, status, category, contact, homepage, image, file'
	),
	'feInterface' => $TCA['tx_abdownloads_download']['feInterface'],
	'columns' => Array(
		'sys_language_uid' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => array(
                    array(
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ),
                ),
                'default' => 0,
            )
		),
		'l18n_parent' => Array(
		    'displayCond' => 'FIELD:sys_language_uid:>:0',
		    'exclude' => 1,
		    'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
		    'config' => Array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => Array(
                    Array( '', 0 )
                ),
                'foreign_table' => 'tx_abdownloads_download',
                'foreign_table_where' => 'AND tx_abdownloads_download.uid=###REC_FIELD_l18n_parent### AND tx_abdownloads_download.sys_language_uid IN (-1,0)'
		    )
		),
		'l18n_diffsource' => Array(
		    'config' => Array(
			'type' => 'passthrough'
		    )
		),
		'editlock' => Array(
			'l10n_mode' => 'mergeIfNotBlank',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_tca.xml:editlock',
			'config' => Array(
				'type' => 'check'
			)
		),
		'hidden' => Array(
			'l10n_mode' => $hideNewLocalizations,
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array(
				'type' => 'check',
				'default' => '0'
			)
		),
		'starttime' => Array(
			'l10n_mode' => 'mergeIfNotBlank',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.starttime',
			'config' => Array(
				'type' => 'input',
				'size' => '10',
				'max' => '20',
				'eval' => 'datetime',
				'checkbox' => '0',
				'default' => '0'
			)
		),
		'endtime' => Array(
			'l10n_mode' => 'mergeIfNotBlank',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.endtime',
			'config' => Array(
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'datetime',
				'checkbox' => '0',
				'default' => '0',
				'range' => Array(
					'upper' => mktime( 0, 0, 0, 12, 31, 2020 ),
					'lower' => mktime( 0, 0, 0, date( 'm' )-1, date( 'd' ), date( 'Y' ) )
				)
			)
		),
		'fe_group' => Array(
			'l10n_mode' => 'mergeIfNotBlank',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.fe_group',
			'config' => Array(
				'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
				'size' => 5,
				'maxitems' => 20,
				'items' => Array(
					Array( 'LLL:EXT:lang/locallang_general.php:LGL.hide_at_login', -1 ),
					Array( 'LLL:EXT:lang/locallang_general.php:LGL.any_login', -2 ),
					Array( 'LLL:EXT:lang/locallang_general.php:LGL.usergroups', '--div--' )
				),
				'exclusiveKeys' => '-1,-2',
				'foreign_table' => 'fe_groups'
			)
		),
		'label' => Array(
			'l10n_mode' => $l10n_mode,
			'exclude' => 1,
			'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.label',
			'config' => Array(
				'type' => 'input',
				'size' => '40',
				'eval' => 'required'
			)
		),
		't3ver_label' => Array (
			'displayCond' => 'FIELD:t3ver_label:REQ:true',
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.versionLabel',
			'config' => Array (
				'type'=>'none',
				'cols' => 27
			)
		),
		'description' => Array(
			'l10n_mode' => $l10n_mode,
			'exclude' => 1,
			'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.description',
            'defaultExtras' => 'richtext:rte_transform[mode=ts_css]',
			'config' => Array(
				'type' => 'text',
				'cols' => '50',
				'rows' => '5',
                'softref' => 'typolink_tag,images,email[subst],url',
                'wizards' => Array(
                    'RTE' => array(
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext.W.RTE',
                        'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_rte.gif',
                        'module' => array(
                            'name' => 'wizard_rte'
                        )
                    )
                )
			)
		),
		'tags' => Array(
			'l10n_mode' => $l10n_mode,
			'exclude' => 1,
			'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.tags',
			'config' => Array(
				'type' => 'input',
				'size' => '40'
			)
		),
		'sponsored_description' => Array(
			'l10n_mode' => $l10n_mode,
			'exclude' => 1,
			'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.sponsored_description',
            'defaultExtras' => 'richtext:rte_transform[mode=ts_css]',
			'config' => Array(
				'type' => 'text',
				'cols' => '50',
				'rows' => '5',
                'softref' => 'typolink_tag,images,email[subst],url',
                'wizards' => Array(
                    'RTE' => array(
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext.W.RTE',
                        'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_rte.gif',
                        'module' => array(
                            'name' => 'wizard_rte'
                        )
                    )
                )
			)
		),
		'license' => Array(
			'l10n_mode' => $l10n_mode,
			'exclude' => 1,
			'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.license',
			'config' => Array(
				'type' => 'input',
				'size' => '40'
			)
		),
		'language_uid' => Array (
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
			'config' => Array (
				'type' => 'select',
                'renderType' => 'selectSingle',
				'foreign_table' => 'static_languages',
				'foreign_table_where' => '',
				'items' => Array(
					Array( '', '0' )
				)
			)
		),
		'clicks' => Array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.clicks',
			'config' => Array(
				'type' => 'input',
				'size' => '5',
				'max' => '10',
				'eval' => 'int',
				'checkbox' => '0',
				'default' => 0
			)
		),
		'rating' => Array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.rating',
			'config' => Array(
				'type' => 'input',
				'size' => '5',
				'max' => '5',
				'eval' => 'int',
				'checkbox' => '0',
				'range' => Array(
					'upper' => '10',
					'lower' => '0'
				),
				'default' => 0
			)
		),
		'votes' => Array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.votes',
			'config' => Array(
				'type' => 'input',
				'size' => '5',
				'max' => '10',
				'eval' => 'int',
				'checkbox' => '0',
				'default' => 0
			)
		),
		'status' => Array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.status',
			'config' => Array(
				'type' => 'select',
                'renderType' => 'selectSingle',
				'items' => Array(
					Array( 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.status.i.0', '0' ),
					Array( 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.status.i.1', '1' ),
					Array( 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.status.i.2', '2' ),
					Array( 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.status.i.3', '3' )
				)
			)
		),
		'category' => Array (
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.category',
			'config' => Array (
				'type' => 'select',
				'renderType' => 'selectTree',
				'foreign_table' => 'tx_abdownloads_category',
				'size' => 10,
				'autoSizeMax' => $configArray['categoryTreeHeigth'],
				'minitems' => 0,
				'maxitems' => 500,
				'MM' => 'tx_abdownloads_category_mm',
				'treeConfig' => [
					'parentField' => 'parent_category',
					'appearance' => [
                        'expandAll' => TRUE,
                        'showHeader' => TRUE,
					]
				],
				'wizards' => Array(
					'add' => Array(
						'type' => 'script',
						'title' => 'LLL:EXT:ab_downloads/locallang_tca.php:ab_downloads.createNewCategory',
						'icon' => 'actions-add',
						'params' => Array(
							'table'=>'tx_abdownloads_category',
							'pid' => '###CURRENT_PID###',
							'setValue' => 'set'
						),
						'module' => [
							'name' => 'wizard_add',
						],
					),
					'edit' => Array(
						'type' => 'popup',
						'title' => 'LLL:EXT:ab_downloads/locallang_tca.php:ab_downloads.editCategory',
						'module' => [
							'name' => 'wizard_edit',
                        ],
						'popup_onlyOpenIfSelected' => 1,
						'icon' => 'actions-open',
						'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
					'list' => Array(
						'type' => 'script',
						'title' => 'LLL:EXT:ab_downloads/locallang_tca.php:ab_downloads.listCategories',
						'icon' => 'actions-system-list-open',
						'params' => Array(
							'table'=>'tx_abdownloads_category',
							'pid' => '###CURRENT_PID###',
						),
						'module' => [
                            'name' => 'wizard_list',
						],
					)
				)
			)
		),
		'contact' => Array(
			'l10n_mode' => $l10n_mode,
			'exclude' => 1,
			'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.contact',
			'config' => Array(
				'type' => 'input',
				'size' => '40'
			)
		),
		'homepage' => Array(
			'l10n_mode' => $l10n_mode_homepage,
			'exclude' => 1,
			'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.homepage',
			'config' => Array(
				'type' => 'input',
				'size' => '40',
				'max' => '255',
				'checkbox' => '',
				'eval' => 'trim',
				'wizards' => Array(
					'_PADDING' => 2,
					'link' => Array(
						'type' => 'popup',
						'title' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_link_formlabel',
                        'icon' => 'actions-wizard-link',
                        'module' => [
                            'name' => 'wizard_link',
                        ],
                        'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
                        #'params' => [
                        #    'blindLinkOptions' => 'folder',
                        #    'blindLinkFields' => 'class, target',
                        #    'allowedExtensions' => 'jpg',
                        #],
					)
				)
			)
		),
		'image' => Array(
			'l10n_mode' => $l10n_mode_image,
			'exclude' => 1,
			'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.image',
			'config' => Array(
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'max_size' => '30000',
				'uploadfolder' => 'uploads/tx_abdownloads/downloadImages',
				'show_thumbs' => '1',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1
			)
		),
		'file' => Array(
			'l10n_mode' => $l10n_mode_file,
			'exclude' => 1,
			'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.file',
			'config' => Array(
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => '',
				'disallowed' => 'php, php3',
				'max_size' => '500000',
				'uploadfolder' => 'uploads/tx_abdownloads/files',
				'show_thumbs' => '0',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1
			)
		),
		'crdate' => Array(
			'l10n_mode' => 'mergeIfNotBlank',
			'exclude' => 1,
			'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.crdate',
			'config' => Array(
				'type' => 'input',
				'size' => '10',
				'max' => '20',
				'eval' => 'datetime',
				'default' => mktime( date( 'H' ), date( 'i' ), date( 's' ), date( 'm' ), date( 'd' ), date( 'Y' ) )
				)
		),
		'sponsored' => Array(
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download.sponsored',
			'config' => Array(
				'type' => 'check',
				'default' => 0
			)
		),
	),
	'types' => Array(
		'0' => Array( 'showitem' => 'label;;1;;, description;;2;richtext:rte_transform[flag=rte_enabled|mode=ts];, tags, license, language_uid, crdate;;3;;, status, --div--;Relations, contact, homepage, category, image, file, --div--;Sponsorship, sponsored, sponsored_description;;2;richtext:rte_transform[flag=rte_enabled|mode=ts];, --div--;Statistics, clicks, rating, votes' )
	),
	'palettes' => Array(
		'1' => Array( 'showitem' => 'hidden, starttime, endtime, editlock' ),
		'10' => Array( 'showitem' => 'fe_group' ),
		'2' => Array( 'showitem' => 'l18n_parent, sys_language_uid' ),
		'3' => Array( 'showitem' => 't3ver_label' ),
	)
);

$TCA['tx_abdownloads_category'] = Array(
	'ctrl' => $TCA['tx_abdownloads_category']['ctrl'],
	'interface' => Array(
		'showRecordFieldList' => 'hidden, fe_group, label, parent_category, image'
	),
	'feInterface' => $TCA['tx_abdownloads_category']['feInterface'],
	'columns' => Array(
		'sys_language_uid' => Array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => array(
                    array(
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ),
                ),
                'default' => 0,
            )
		),
		'l18n_parent' => Array(
		    'displayCond' => 'FIELD:sys_language_uid:>:0',
		    'exclude' => 1,
		    'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
		    'config' => Array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => Array(
                    Array( '', 0 )
                ),
                'foreign_table' => 'tx_abdownloads_category',
                'foreign_table_where' => 'AND tx_abdownloads_category.uid=###REC_FIELD_l18n_parent### AND tx_abdownloads_category.sys_language_uid IN (-1,0)'
		    )
		),
		'l18n_diffsource' => Array(
		    'config' => Array(
			'type' => 'passthrough'
		    )
		),
		'hidden' => Array(
			'l10n_mode' => $hideNewLocalizations,
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array(
				'type' => 'check',
				'default' => '0'
			)
		),
		'starttime' => Array(
			'l10n_mode' => 'mergeIfNotBlank',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.starttime',
			'config' => Array(
				'type' => 'input',
				'size' => '10',
				'max' => '20',
				'eval' => 'datetime',
				'checkbox' => '0',
				'default' => '0'
			)
		),
		'endtime' => Array(
			'l10n_mode' => 'mergeIfNotBlank',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.endtime',
			'config' => Array(
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'datetime',
				'checkbox' => '0',
				'default' => '0',
				'range' => Array(
					'upper' => mktime( 0, 0, 0, 12, 31, 2020 ),
					'lower' => mktime( 0, 0, 0, date( 'm' )-1, date( 'd' ), date( 'Y' ) )
				)
			)
		),
		'fe_group' => Array(
			'l10n_mode' => 'mergeIfNotBlank',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.fe_group',
			'config' => Array(
				'type' => 'select',
				'size' => 5,
				'maxitems' => 20,
				'items' => Array(
					Array( 'LLL:EXT:lang/locallang_general.php:LGL.hide_at_login', -1 ),
					Array( 'LLL:EXT:lang/locallang_general.php:LGL.any_login', -2 ),
					Array( 'LLL:EXT:lang/locallang_general.php:LGL.usergroups', '--div--' )
				),
				'exclusiveKeys' => '-1,-2',
				'foreign_table' => 'fe_groups'
			)
		),
		'label' => Array(
			'l10n_mode' => $l10n_mode,
			'exclude' => 1,
			'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_category.label',
			'config' => Array(
				'type' => 'input',
				'size' => '40',
				'eval' => 'required'
			)
		),
		'description' => Array(
			'l10n_mode' => $l10n_mode,
			'exclude' => 1,
			'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_category.description',
            'defaultExtras' => 'richtext:rte_transform[mode=ts_css]',
			'config' => Array(
				'type' => 'text',
				'cols' => '50',
				'rows' => '5',
                'softref' => 'typolink_tag,images,email[subst],url',
                'wizards' => Array(
                    'RTE' => array(
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext.W.RTE',
                        'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_rte.gif',
                        'module' => array(
                            'name' => 'wizard_rte'
                        )
                    )
                )
			)
		),
		'parent_category' => Array (
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_category.parent_category',
			'config' => Array (
				'type' => 'select',
                'renderType' => 'selectTree',
				'size' => 10,
				'autoSizeMax' => $configArray['categoryTreeHeigth'],
				'minitems' => 0,
				'maxitems' => 2,
				'foreign_table' => 'tx_abdownloads_category',
                'treeConfig' => array(
                    'parentField' => 'parent_category',
                    'appearance' => array(
                        'showHeader' => TRUE,
                        'expandAll' => true,
                    ),
                ),
                'wizards' => Array(
                    'add' => Array(
                        'type' => 'script',
                        'title' => 'LLL:EXT:ab_downloads/locallang_tca.php:ab_downloads.createNewCategory',
                        'icon' => 'actions-add',
                        'params' => Array(
                            'table'=>'tx_abdownloads_category',
                            'pid' => '###CURRENT_PID###',
                            'setValue' => 'set'
                        ),
                        'module' => [
                            'name' => 'wizard_add',
                        ],
                    ),
                    'edit' => Array(
                        'type' => 'popup',
                        'title' => 'LLL:EXT:ab_downloads/locallang_tca.php:ab_downloads.editCategory',
                        'module' => [
                            'name' => 'wizard_edit',
                        ],
                        'popup_onlyOpenIfSelected' => 1,
                        'icon' => 'actions-open',
                        'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
                    ),
                    'list' => Array(
                        'type' => 'script',
                        'title' => 'LLL:EXT:ab_downloads/locallang_tca.php:ab_downloads.listCategories',
                        'icon' => 'actions-system-list-open',
                        'params' => Array(
                            'table'=>'tx_abdownloads_category',
                            'pid' => '###CURRENT_PID###',
                        ),
                        'module' => [
                            'name' => 'wizard_list',
                        ],
                    )
                )

			)
		),
		'image' => Array(
			'l10n_mode' => $l10n_mode_image,
			'exclude' => 1,
			'label' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_category.image',
			'config' => Array(
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'max_size' => '30000',
				'uploadfolder' => 'uploads/tx_abdownloads/categoryImages',
				'show_thumbs' => '1',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1
			)
		)
	),
	'types' => Array(
		'0' => Array( 'showitem' => 'label;;1;;, description;;2;richtext:rte_transform[flag=rte_enabled|mode=ts];, image, parent_category' )
	),
	'palettes' => Array(
		'1' => Array( 'showitem' => 'hidden, starttime, endtime' ),
		'10' => Array( 'showitem' => 'fe_group' ),
		'2' => Array( 'showitem' => 'l18n_parent, sys_language_uid' ),
	)
);
?>
