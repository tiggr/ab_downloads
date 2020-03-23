<?php defined('TYPO3_MODE') or die();

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
    'FILE:EXT:ab_downloads/flexform_ds.xml'
);

return ([
    "types" => [
        "list" => [
            'subtypes_excludelist' => [
                "tx_abdownloads_p1" => 'layout,select_key,pages,recursive',
            ],
            'subtypes_addlist'     => [
                "tx_abdownloads_p1" => 'pi_flexform',
            ],
        ],
    ],
]);
