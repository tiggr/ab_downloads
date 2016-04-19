<?php
/**
 * $Id: ext_localconf.php 123 2007-07-16 19:42:26Z andreas $
 */

if(!defined ('TYPO3_MODE') ) die ('Access denied.');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig( 'options.saveDocNew.tx_abdownloads_category=1' );
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig( 'options.saveDocNew.tx_abdownloads_download=1' );

## Extending TypoScript from static template uid=43 to set up userdefined tag:
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript( $_EXTKEY, 'editorcfg', "tt_content.CSS_editor.ch.tx_abdownloads_pi1 =< plugin.tx_abdownloads_pi1.CSS_editor", 43 );

// Add PlugIn to Static Template #43 and create USER cObject
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43( $_EXTKEY, 'pi1/class.tx_abdownloads_pi1.php', "_pi1", 'list_type', 1 );

// Define the fields of category records to show in the backend page module
$TYPO3_CONF_VARS['EXTCONF']['cms']['db_layout']['addTables']['tx_abdownloads_category'][0] = array(
	'fList' => 'label,parent_category',
	'icon' => TRUE
);

// Define the fields of download records to show in the backend page module
$TYPO3_CONF_VARS['EXTCONF']['cms']['db_layout']['addTables']['tx_abdownloads_download'][0] = array(
	'fList' => 'label,file,category',
	'icon' => TRUE
);

/**
 * Register hooks in TCEmain:
 */

// This hook is used to prevent saving of category or download records which have categories assigned that are not allowed for the current BE user.
// The list of allowed categories can be set with 'tx_abdownloads_category.allowedItems' in user/group TSconfig.
// This check will be disabled until 'options.useListOfAllowedItems' (user/group TSconfig) is set to a value.
$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:ab_downloads/lib/class.tx_abdownloads_tcemain.php:tx_abdownloads_tcemain';

// This hook is used to prevent saving of a download record that has non-allowed categories assigned when a command is executed (modify, copy, move, delete...).
// It checks if the record has an editlock. If true, nothing will be saved.
$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'EXT:ab_downloads/lib/class.tx_abdownloads_tcemain.php:tx_abdownloads_tcemain_cmdmap';

?>
