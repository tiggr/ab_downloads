<?php
/**
 * $Id: ext_tables.php 164 2008-07-04 09:39:51Z andreas $
 */

if( !defined ( 'TYPO3_MODE' ) ) die ( 'Access denied.' );

if( TYPO3_MODE=='BE' )	{
	t3lib_extMgm::addModule( 'web', 'txabdownloadsM1', '', t3lib_extMgm::extPath( $_EXTKEY) . 'mod1/' );
}

// Get extension configuration
$configArray = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ab_downloads'] );

$TCA['tx_abdownloads_download'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:ab_downloads/locallang_db.php:tx_abdownloads_download',		
		'label' => 'label',	
		'tstamp' => 'tstamp',
		'thumbnail' => 'image',
		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'languageField' => 'sys_language_uid',
		'dividers2tabs' => $configArray['noTabDividers'] ? false : true,
		'shadowColumnsForNewPlaceholders' => 'sys_language_uid, l18n_parent, starttime, endtime, fe_group',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'editlock' => 'editlock',
		'sortby' => 'sorting',
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',	
		'enablecolumns' => Array (		
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'icon_tx_abdownloads_download.gif',
		'prependAtCopy' => $configArray['prependAtCopy'] ? 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy' : '',
		'mainpalette' => '10',
		'versioning_followPages' => true,
		'origUid' => 't3_origuid',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, fe_group, label, description, sponsored_description, license, language_uid, clicks, rating, votes, status, category, contact, homepage, image, file'
	)
);

$TCA['tx_abdownloads_category'] = Array (
	'ctrl' => Array (
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
		'enablecolumns' => Array (		
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath( $_EXTKEY ) . 'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath( $_EXTKEY ) . 'icon_tx_abdownloads_category.gif',
		'prependAtCopy' => $configArray['prependAtCopy'] ? 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy' : '',
		'mainpalette' => '2, 10',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, fe_group, label, parent_category, image'
	)
);

/**
 * Compatibility with TYPO3 versions lower than 4.0
 */

// Enable workspace versioning only for TYPO3 versions 4.0 and higher
if( t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version ) >= 4000000 )	{
	$TCA['tx_abdownloads_download']['ctrl']['versioningWS'] = true;
} else	{
	$TCA['tx_abdownloads_download']['ctrl']['versioning'] = true;

	// Disable support for nested fe_groups in tx_abdownloads_download records in TYPO3 versions lower than 4.0
	t3lib_div::loadTCA( 'tx_abdownloads_download' );

	$TCA['tx_abdownloads_download']['columns']['fe_group'] = Array(
		'l10n_mode' => 'mergeIfNotBlank',
		'exclude' => 1,
		'label' => 'LLL:EXT:lang/locallang_general.php:LGL.fe_group',
		'config' => Array (
			'type' => 'select',
			'items' => Array (
				Array( '', 0 ),
				Array( 'LLL:EXT:lang/locallang_general.php:LGL.hide_at_login', -1 ),
				Array( 'LLL:EXT:lang/locallang_general.php:LGL.any_login', -2 ),
				Array( 'LLL:EXT:lang/locallang_general.php:LGL.usergroups', '--div--' )
			),
			'foreign_table' => 'fe_groups'
		)
	);

	$TCA['tx_abdownloads_download']['palettes']['1'] = Array( 'showitem' => 'hidden, starttime, endtime, fe_group' );
	$TCA['tx_abdownloads_download']['ctrl']['mainpalette'] = false;

	// Disable support for nested fe_groups in tx_abdownloads_category records in TYPO3 versions lower than 4.0
	t3lib_div::loadTCA( 'tx_abdownloads_category' );

	$TCA['tx_abdownloads_category']['columns']['fe_group'] = Array(
		'l10n_mode' => 'mergeIfNotBlank',
		'exclude' => 1,
		'label' => 'LLL:EXT:lang/locallang_general.php:LGL.fe_group',
		'config' => Array (
			'type' => 'select',
			'items' => Array (
				Array( '', 0 ),
				Array( 'LLL:EXT:lang/locallang_general.php:LGL.hide_at_login', -1 ),
				Array( 'LLL:EXT:lang/locallang_general.php:LGL.any_login', -2 ),
				Array( 'LLL:EXT:lang/locallang_general.php:LGL.usergroups', '--div--' )
			),
			'foreign_table' => 'fe_groups'
		)
	);

	$TCA['tx_abdownloads_category']['palettes']['1'] = Array( 'showitem' => 'hidden, starttime, endtime, fe_group' );
	$TCA['tx_abdownloads_category']['ctrl']['mainpalette'] = '1';
}

// Allow downloads and download-category records on normal pages
t3lib_extMgm::allowTableOnStandardPages( 'tx_abdownloads_download' );
t3lib_extMgm::allowTableOnStandardPages( 'tx_abdownloads_category' );

// Add the tx_abdownloads_download and tx_abdownloads_category record to the insert records content element
t3lib_extMgm::addToInsertRecords('tx_abdownloads_download');
t3lib_extMgm::addToInsertRecords('tx_abdownloads_category');

// Load tt_content to $TCA array
t3lib_div::loadTCA( 'tt_content' );

// Remove some fields from the tt_content content element
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] ='layout,select_key,pages,recursive';

// Add FlexForm field to tt_content
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform';

// Sets the transformation mode for the RTE to "ts_css" if the extension css_styled_content is installed (default: "ts")
if( t3lib_extMgm::isLoaded( 'css_styled_content' ) )	{
t3lib_extMgm::addPageTSConfig( '
# RTE mode in table "tx_abdownloads_download"
RTE.config.tx_abdownloads_download.description.proc.overruleMode=ts_css' );

t3lib_extMgm::addPageTSConfig( '
# RTE mode in table "tx_abdownloads_download"
RTE.config.tx_abdownloads_download.sponsored_description.proc.overruleMode=ts_css' );

t3lib_extMgm::addPageTSConfig( '
# RTE mode in table "tx_abdownloads_category"
RTE.config.tx_abdownloads_category.description.proc.overruleMode=ts_css' );
}

// Initialize static extension templates
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/table_based/', 'Table-based template' );
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/css_based/', 'CSS-based template' );
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/css/', 'Default CSS-styles' );

t3lib_extMgm::addPlugin(Array( 'LLL:EXT:ab_downloads/locallang_db.php:tt_content.list_type', $_EXTKEY. '_pi1' ), 'list_type' );
t3lib_extMgm::addPiFlexFormValue( $_EXTKEY . '_pi1', 'FILE:EXT:ab_downloads/flexform_ds.xml' );

// class for displaying the category tree in BE forms.
include_once( t3lib_extMgm::extPath( $_EXTKEY ) . 'lib/class.tx_abdownloads_treeview.php' );

// class that uses hooks in class.t3lib_tcemain.php (processDatamapClass and processCmdmapClass) to prevent not allowed "commands" (copy,delete,...) for a certain BE usergroup
include_once( t3lib_extMgm::extPath( $_EXTKEY ) . 'lib/class.tx_abdownloads_tcemain.php' );

$tempColumns = Array (
		'ab_downloads_categorymounts' => Array (
			'exclude' => 1,
		#	'l10n_mode' => 'exclude', // the localizalion mode will be handled by the userfunction
			'label' => 'LLL:EXT:ab_downloads/locallang_tca.xml:ab_downloads.categoryMounts',
			'config' => Array (
				'type' => 'select',
				'form_type' => 'user',
				'userFunc' => 'tx_abdownloads_treeview->displayCategoryTree',
				'treeView' => 1,
				'foreign_table' => 'tx_abdownloads_category',
				#'foreign_table_where' => $fTableWhere.'ORDER BY tx_abdownloads_category.'.$configArray['category_OrderBy'],
				'size' => 3,
				'autoSizeMax' => $configArray['categoryTreeHeigth'],
				'minitems' => 0,
				'maxitems' => 500,
// 				'MM' => 'tx_abdownloads_category_mm',

			)
		),
// 		'ab_downloads_cmounts_usesubcats' => Array (
// 			'exclude' => 1,
// 			'label' => 'LLL:EXT:ab_downloads/locallang_tca.xml:ab_downloads.cmountsUseSubcats',
// 			'config' => Array (
// 				'type' => 'check'
// 			)
// 		),
);


t3lib_div::loadTCA('be_groups');
t3lib_extMgm::addTCAcolumns('be_groups',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('be_groups','ab_downloads_categorymounts;;;;1-1-1');

$tempColumns['ab_downloads_categorymounts']['displayCond'] = 'FIELD:admin:=:0';
// $tempColumns['ab_downloads_cmounts_usesubcats']['displayCond'] = 'FIELD:admin:=:0';


t3lib_div::loadTCA('be_users');
t3lib_extMgm::addTCAcolumns('be_users',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('be_users','ab_downloads_categorymounts;;;;1-1-1');

if( TYPO3_MODE == 'BE' )	{
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_abdownloads_pi1_wizicon'] = t3lib_extMgm::extPath( $_EXTKEY ) . 'pi1/class.tx_abdownloads_pi1_wizicon.php';
}
?>
