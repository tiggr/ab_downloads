<?php

defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
    [
        'LLL:EXT:ab_downloads/locallang_db.php:tt_content.list_type',
        'ab_downloads_pi1',
    ],
    'list_type',
    'ab_downloads'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'ab_downloads_pi1',
    'FILE:EXT:ab_downloads/Configuration/FlexForms/flexform_ds.xml'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['ab_downloads_pi1'] = 'layout,select_key,pages,recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['ab_downloads_pi1'] = 'pi_flexform';

//return ([
//    "types" => [
//        "list" => [
//            'subtypes_excludelist' => [
//                "tx_abdownloads_p1" => 'layout,select_key,pages,recursive',
//            ],
//            'subtypes_addlist'     => [
//                "tx_abdownloads_p1" => 'pi_flexform',
//            ],
//        ],
//    ],
//]);
