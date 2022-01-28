<?php

defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('options.saveDocNew.tx_abdownloads_category=1');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('options.saveDocNew.tx_abdownloads_download=1');

// Add PlugIn to Static Template #43 and create USER cObject
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43(
    'ab_downloads',
    'pi1/class.tx_abdownloads_pi1.php',
    "_pi1",
    'list_type',
    1
);

// Define the fields of category records to show in the backend page module
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cms']['db_layout']['addTables']['tx_abdownloads_category'][0] = [
    'fList' => 'label,parent_category',
    'icon' => true,
];

// Define the fields of download records to show in the backend page module
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cms']['db_layout']['addTables']['tx_abdownloads_download'][0] = [
    'fList' => 'label,file,category',
    'icon' => true,
];

/**
 * Register hooks in TCEmain:
 */

// This hook is used to prevent saving of category or download records which have categories assigned that are not allowed for the current BE user.
// The list of allowed categories can be set with 'tx_abdownloads_category.allowedItems' in user/group TSconfig.
// This check will be disabled until 'options.useListOfAllowedItems' (user/group TSconfig) is set to a value.
$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'tx_abdownloads_tcemain';

// This hook is used to prevent saving of a download record that has non-allowed categories assigned when a command is executed (modify, copy, move, delete...).
// It checks if the record has an editlock. If true, nothing will be saved.
$GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'tx_abdownloads_tcemain_cmdmap';
