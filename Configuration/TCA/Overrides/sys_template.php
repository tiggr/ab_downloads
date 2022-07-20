<?php

defined('TYPO3') or die();

// Initialize static extension templates
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'ab_downloads',
    'static/table_based/',
    'Table-based template'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('ab_downloads', 'static/css_based/', 'CSS-based template');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('ab_downloads', 'static/css/', 'Default CSS-styles');
