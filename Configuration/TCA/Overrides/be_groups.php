<?php declare(strict_types=1);

// Get extension configuration
$configArray = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ab_downloads'] );

$tempColumns = [
    'ab_downloads_categorymounts' => [
        'exclude' => 1,
        #	'l10n_mode' => 'exclude', // the localizalion mode will be handled by the userfunction
        'label'   => 'LLL:EXT:ab_downloads/locallang_tca.xml:ab_downloads.categoryMounts',
        'config'  => [
            'type'          => 'select',
            'form_type'     => 'user',
            'userFunc'      => 'tx_abdownloads_treeview->displayCategoryTree',
            'treeView'      => 1,
            'foreign_table' => 'tx_abdownloads_category',
            #'foreign_table_where' => $fTableWhere.'ORDER BY tx_abdownloads_category.'.$configArray['category_OrderBy'],
            'size'          => 3,
            'autoSizeMax'   => $configArray['categoryTreeHeigth'],
            'minitems'      => 0,
            'maxitems'      => 500,
            // 				'MM' => 'tx_abdownloads_category_mm',

        ],
        'displayCond' => 'FIELD:admin:=:0',
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('be_groups', $tempColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('be_groups', 'ab_downloads_categorymounts;;;;1-1-1');
