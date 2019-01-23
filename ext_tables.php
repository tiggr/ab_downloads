<?php

if( !defined ( 'TYPO3_MODE' ) ) die ( 'Access denied.' );


// Sets the transformation mode for the RTE to "ts_css" if the extension css_styled_content is installed (default: "ts")
if( \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded( 'css_styled_content' ) )	{
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig( '
# RTE mode in table "tx_abdownloads_download"
RTE.config.tx_abdownloads_download.description.proc.overruleMode=ts_css' );

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig( '
# RTE mode in table "tx_abdownloads_download"
RTE.config.tx_abdownloads_download.sponsored_description.proc.overruleMode=ts_css' );

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig( '
# RTE mode in table "tx_abdownloads_category"
RTE.config.tx_abdownloads_category.description.proc.overruleMode=ts_css' );
}

// Initialize static extension templates
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile( $_EXTKEY, 'static/table_based/', 'Table-based template' );
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile( $_EXTKEY, 'static/css_based/', 'CSS-based template' );
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile( $_EXTKEY, 'static/css/', 'Default CSS-styles' );

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(Array( 'LLL:EXT:ab_downloads/locallang_db.php:tt_content.list_type', $_EXTKEY. '_pi1' ), 'list_type' );
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue( $_EXTKEY . '_pi1', 'FILE:EXT:ab_downloads/flexform_ds.xml' );

// class for displaying the category tree in BE forms.
include_once( \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath( $_EXTKEY ) . 'lib/class.tx_abdownloads_treeview.php' );

// class that uses hooks in class.t3lib_tcemain.php (processDatamapClass and processCmdmapClass) to prevent not allowed "commands" (copy,delete,...) for a certain BE usergroup
include_once( \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath( $_EXTKEY ) . 'lib/class.tx_abdownloads_tcemain.php' );


if( TYPO3_MODE == 'BE' )	{
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_abdownloads_pi1_wizicon'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath( $_EXTKEY ) . 'pi1/class.tx_abdownloads_pi1_wizicon.php';
}
