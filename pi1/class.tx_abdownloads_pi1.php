<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2005 - 2009 Andreas Bulling <typo3@andreas-bulling.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Service\MarkerBasedTemplateService;

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *  100: class tx_abdownloads_pi1 extends \TYPO3\CMS\Frontend\Plugin\AbstractPlugin
 *
 *              SECTION: Main function
 *  133:     function main( $content, $conf )
 *
 *              SECTION: Basic views
 *  365:     function displayCategory( $categoryUID = 0 )
 *  702:     function displayTree( $categoryUID = 0, $level = 0 )
 *  907:     function displaySearch()
 * 1134:     function displayTop( $categoryUID = 0 )
 * 1305:     function displayCatalog( $categoryUID = 0, $level = 0 )
 *
 *              SECTION: Additional views after user interaction
 * 1675:     function getViewClickedDownload( $uid = null )
 * 1723:     function getViewDetailsForDownload( $uid = null, $categoryUID )
 * 1794:     function getViewAddNewDownload( $categoryUID, $form_errormsg = null )
 * 1905:     function getViewAddNewDownloadResult( $categoryUID = 0 )
 * 2055:     function getViewReportBrokenDownload( $uid = null, $categoryUID )
 * 2120:     function getViewReportBrokenDownloadResult( $uid = null, $categoryUID )
 * 2195:     function getViewRateDownload( $uid = null, $categoryUID )
 * 2266:     function getViewRateDownloadResult( $uid = null, $categoryUID )
 *
 *              SECTION: Helper functions
 * 2345:     function makePageBrowser( $pointerName = 'pointer' )
 * 2400:     function userProcess( $configKey, $variable )
 * 2418:     function initPidList()
 * 2442:     function recursiveDownloadCount( $categoryUID )
 * 2482:     function recursiveCategoryGet( $categoryUID )
 * 2517:     function getCategoryPath( $categoryUID, $showLinks = true )
 * 2593:     function getRecordOverlay( &$resultSet, $databaseTable )
 * 2631:     function getCategorySelect( $categoryUID, $selectedID = 0 )
 * 2681:     function getDownloadRecord( $downloadUID )
 * 2712:     function getCategoryRecords( $field, $categoryUID, $orderBy = '', $limit = '' )
 * 2737:     function getCategoryUID( $recordUID )
 * 2769:     function displayFEHelp( $type )
 * 2786:     function containsBlacklistedWords( $text )
 * 2810:     function checkInputFields( $categoryUID )
 * 2897:     function getImageLink( $record, $field, $type, $view, $categoryUID, $detailedView = false )
 * 2980:     function getFileIcon( $record )
 * 3002:     function getLanguage( $language_uid )
 * 3033:     function returnStarsForRating( $rating )
 * 3078:     function fillMarkerArray( &$array, $record, $localConf, $categoryUID, $pageID = '' )
 *
 * TOTAL FUNCTIONS: 33
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


/**
 * Plugin 'downloads' for the 'ab_downloads' extension.
 *
 * $Id: class.tx_abdownloads_pi1.php 178 2009-07-30 14:34:22Z andreas $
 *
 * @author    Andreas Bulling    <typo3@andreas-bulling.de>
 * @package    TYPO3
 * @subpackage    tx_abdownloads
 *
 * TypoScript setup:
 * @See static/css_based/setup.txt
 * @See static/table_based/setup.txt
 * @See Project homepage: http://typo3.andreas-bulling.de/en/extensions/modern-downloads/
 * @See Bugtracker: http://typo3.andreas-bulling.de/en/bug-tracker/
 * @See Demo: http://typo3.andreas-bulling.de/en/demos/modern-downloads/',
 * @See ab_downloads Manual: http://typo3.org/documentation/document-library/extension-manuals/ab_downloads/current/
 * @See TSref: http://typo3.org/documentation/document-library/references/doc_core_tsref/current/
 */
class tx_abdownloads_pi1 extends \TYPO3\CMS\Frontend\Plugin\AbstractPlugin
{
    var $prefixId = 'tx_abdownloads_pi1';                // Same as class name
    var $scriptRelPath = 'pi1/class.tx_abdownloads_pi1.php';        // Path to this script relative to the extension directory.
    var $extKey = 'ab_downloads';                    // The extension key.
    var $tablePrefix = 'tx_abdownloads_';                // The database table prefix.
    var $originalTemplateCode = null;                // Holds template code.
    var $cObj;                            // Reference to the calling cObj.
    var $markerBasedTemplateService = null;
    var $debug = false;                        // Global debug switch. Change to 'true' for debugging information.
    var $debugDB = false;                        // Database debug switch. Change to 'true' for debugging information.
    var $full_debug = false;                    // Full debug switch. Change to 'true' for full debugging information.
    var $sysfolderList = null;                    // Holds the 'Starting Point' PIDs.
    var $filePath = null;                        // Holds the file path for downloads.
    var $flexform = null;                        // Holds the flexform configuration for the plugin.
    var $pi_checkCHash = true;                    // Enable cHash check.
    var $allowCaching;                        // Holds cache setting.
    var $sys_language_mode;                        // Holds the language mode.
    var $alternatingLayouts;                    // Holds the number of alternating layouts.
    var $versioningEnabled = false;                    // Is the extension 'version' loaded
    var $downloadMode;                        // Holds the download mode.

    /*************************************
     *
     * Main function
     *
     *************************************/

    /**
     * Main function: Decides which of the views to display.
     *
     * @param    string $content Function output is added to this.
     * @param    array $conf Configuration array.
     * @return    string        $content Complete content generated by the tx_abdownloads plugin.
     */
    function main($content, $conf)
    {

        // Check if this plugin instance is the correct target
        if ($this->piVars['cid'] != '' && ($this->piVars['cid'] != $this->cObj->data['uid'])) {
            return;
        }

        $this->markerBasedTemplateService = GeneralUtility::makeInstance(MarkerBasedTemplateService::class);

        // Initialize new cObj object
        $this->local_cObj = GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::class);

        // Initialize new fileFunc object
        $this->fileFunc = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Utility\File\BasicFileUtility::class);

        // Init config for flexform
        $this->pi_initPIflexForm();
        $this->flexform = $this->cObj->data['pi_flexform'];

        // Get selected captcha extension
        $this->captchaExtension = $this->pi_getFFvalue($this->flexform, 'captchaExtension', 'sDEF');

        // Check for extension "sr_freecap"
        if ($this->captchaExtension == 'sr_freecap' && \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('sr_freecap')) {
            require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('sr_freecap') . 'pi2/class.tx_srfreecap_pi2.php');
            $this->freeCap = GeneralUtility::makeInstance('tx_srfreecap_pi2');
        }

        // Check for extension "version"
        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('version')) {
            $this->versioningEnabled = true;
        }

        // Do some configuration
        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();

        // Configure caching
        $this->allowCaching = $this->conf['allowCaching'] ? 1 : 0;
        if (!$this->allowCaching) {
            $GLOBALS['TSFE']->set_no_cache();
        }

        // Get the 'Starting Point' PID(s)
        $this->initPidList();

        // Get the file path for downloads
        $this->filePath = $this->conf['filePath'] ? $this->conf['filePath'] : 'uploads/tx_abdownloads/files/';

        // Extend enable fields
        $this->enableFields = ' AND ' . $this->tablePrefix . 'download.status IN (1,2) AND ' . $this->tablePrefix . 'download.sys_language_uid IN (-1,0) AND ' . $this->tablePrefix . 'download.pid IN (' . $this->sysfolderList . ')' . $this->cObj->enableFields($this->tablePrefix . 'download');
        $this->enableFieldsCategory = ' AND ' . $this->tablePrefix . 'category.sys_language_uid IN (-1,0) AND ' . $this->tablePrefix . 'category.pid IN (' . $this->sysfolderList . ')' . $this->cObj->enableFields($this->tablePrefix . 'category');

        // Set sys_language_mode
        // sys_language_mode == 'strict': If a certain language is requested, select only download records from the default language which have a translation.
        $this->sys_language_mode = $this->conf['sys_language_mode'] ? $this->conf['sys_language_mode'] : $GLOBALS['TSFE']->sys_language_mode;

        // Set the number of alternating layouts (default is 2)
        $alternatingLayouts = intval($this->pi_getFFvalue($this->flexform, 'alternatingLayouts', 's_template'));
        $alternatingLayouts = $alternatingLayouts ? $alternatingLayouts : intval($this->conf['alternatingLayouts']);
        $this->alternatingLayouts = $alternatingLayouts ? $alternatingLayouts : 2;

        // Debugging output
        if ($this->debug) {
            $GLOBALS['TSFE']->set_no_cache();

            $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;

            t3lib_utility_Debug::printArray($conf);

            if ($this->full_debug) {
                t3lib_utility_Debug::printArray(get_object_vars($GLOBALS['TSFE']));
            }

            echo '<br />FLEXFORM CONFIGURATION:<br />';
            t3lib_utility_Debug::printArray($this->flexform);
            echo '<br />';
        }

        if ($this->debugDB) {
            $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
        }

        // Set the template
        $templateFile = $this->pi_getFFvalue($this->flexform, 'templateFile', 's_template');
        $this->originalTemplateCode = file_get_contents(
            PATH_site . ($templateFile ? 'uploads/tx_abdownloads/' . $templateFile : $this->conf['templateFile'])
        );

        // Get category UID from piVars or FlexForm or set to 0
        $startCategoryID = intval($this->pi_getFFvalue($this->flexform, 'startCategoryID', 's_display'));
        $categoryUID = intval($this->piVars['category_uid']) ? intval($this->piVars['category_uid']) : ($startCategoryID ? $startCategoryID : 0);

        // Get code
        $code = $this->pi_getFFvalue($this->flexform, 'whatToDisplay', 'sDEF');
        $this->conf['code'] = $code ? $code : 'CATEGORY';

        // Get the view mode(s)
        $viewModes = GeneralUtility::trimExplode(',', $this->conf['code'], 1);
        if (!count($viewModes)) {
            $viewModes = [];
        }

        // Set download mode for getCategoryPath()
        foreach ($viewModes as $id => $viewMode) {
            switch ((string)$viewMode) {
                case 'TREE':
                    $this->downloadMode = 'TREE';
                    break;

                case 'CATALOG':
                    $this->downloadMode = 'CATALOG';
                    break;

                default:
                    $this->downloadMode = 'CATEGORY';
                    break;
            }
        }

        // Get action
        $action = htmlspecialchars(trim($this->piVars['action']));

        // 'getviewcategory' and 'getviewcatalog' have to be excluded as they're only used in the CATEGORY/CATALOG view
        if ($action != null && $action != 'getviewcategory' && $action != 'getviewcatalog') {
            // Get download UID
            $uid = intval($this->piVars['uid']);

            switch ((string)$action) {
                case 'getviewclickeddownload':
                    $content = $this->getViewClickedDownload($uid);
                    break;

                case 'getviewdetailsfordownload':
                    $content = $this->getViewDetailsForDownload($uid, $categoryUID);
                    break;

                case 'getviewaddnewdownload':
                    if (htmlspecialchars(trim($this->piVars['submit_button'])) != null) {
                        $content = $this->checkInputFields($categoryUID);
                    } elseif (htmlspecialchars(trim($this->piVars['cancel_button'])) != null) {
                        if ($this->downloadMode == 'TREE') {
                            // FIXME: startCategoryID not taken into account
                            $content = $this->displayTree();
                        } elseif ($this->downloadMode == 'CATALOG') {
                            $content = $this->displayCatalog($categoryUID);
                        } else {
                            $content = $this->displayCategory($categoryUID);
                        }
                    } else {
                        $content = $this->getViewAddNewDownload($categoryUID);
                    }
                    break;

                case 'getviewreportbrokendownload':
                    if (htmlspecialchars(trim($this->piVars['submit_button'])) != null) {
                        $content = $this->getViewReportBrokenDownloadResult($uid, $categoryUID);
                    } elseif (htmlspecialchars(trim($this->piVars['cancel_button'])) != null) {
                        if ($this->downloadMode == 'TREE') {
                            // FIXME: startCategoryID not taken into account
                            $content = $this->displayTree();
                        } elseif ($this->downloadMode == 'CATALOG') {
                            $content = $this->displayCatalog($categoryUID);
                        } else {
                            $content = $this->displayCategory($categoryUID);
                        }
                    } else {
                        $content = $this->getViewReportBrokenDownload($uid, $categoryUID);
                    }
                    break;

                case 'getviewratedownload':
                    if (htmlspecialchars(trim($this->piVars['submit_button'])) != null) {
                        $content = $this->getViewRateDownloadResult($uid, $categoryUID);
                    } elseif (htmlspecialchars(trim($this->piVars['cancel_button'])) != null) {
                        if ($this->downloadMode == 'TREE') {
                            // FIXME: startCategoryID not taken into account
                            $content = $this->displayTree();
                        } elseif ($this->downloadMode == 'CATALOG') {
                            $content = $this->displayCatalog($categoryUID);
                        } else {
                            $content = $this->displayCategory($categoryUID);
                        }
                    } else {
                        $content = $this->getViewRateDownload($uid, $categoryUID);
                    }
                    break;
            }

            // Don't display any further content
            return $this->pi_wrapInBaseClass($content);
        }

        // Display main view(s)
        foreach ($viewModes as $id => $view) {
            switch ((string)$view) {
                case 'CATEGORY':
                    $content .= $this->displayCategory($categoryUID);
                    break;

                case 'TREE':
                    $content .= $this->displayTree($categoryUID);
                    break;

                case 'SEARCH':
                    $content .= $this->displaySearch();
                    break;

                case 'TOP':
                    $content .= $this->displayTop($categoryUID);
                    break;

                case 'CATALOG':
                    $content .= $this->displayCatalog($categoryUID);
                    break;
            }
        }

        return $this->pi_wrapInBaseClass($content);
    }

    /*************************************
     *
     * Basic views
     *
     *************************************/

    /**
     * displayCategory( $categoryUID = 0 )
     *
     * Generates the html for the list view of this extension. In this view the current category with its
     * subcategories and downloads is shown.
     *
     * @param    integer $categoryUID UID of the category that shall be shown.
     * @return    string        The generated HTML source for this view.
     */
    function displayCategory($categoryUID = 0)
    {

        // Init some vars
        $action = 'getviewcategory';
        $content = '';
        $templateCode = null;
        $conf['subpartMarker'] = 'VIEW_CATEGORY';
        $subSub_categories = 'CATEGORIES';
        $subSub_downloads = 'DOWNLOADS';
        $subSub_pathmenu = 'PATHMENU';
        $subSub_additional = 'ADDITIONAL';
        $subSubSub_category = 'CATEGORY';
        $subSubSub_download = 'DOWNLOAD';

        // Get the html source between subpart markers from the template file
        $templateCode = $this->markerBasedTemplateService->getSubpart($this->originalTemplateCode, '###' . $conf['subpartMarker'] . '###');

        // Get local config
        $localConf = $this->conf['listView.'];

        // Save original ATagParams
        $originalATagParams = $GLOBALS['TSFE']->ATagParams;

        /**
         * PAGE TITLE
         */

        // Substitute the title of the page with the category label
        if ($this->conf['substitutePageTitle'] && $categoryUID != '0') {
            // Get category record(s)
            $categoryResult = $this->getCategoryRecords('uid', $categoryUID);

            // Get record overlay
            $category = $this->getRecordOverlay($categoryResult, $this->tablePrefix . 'category');

            $GLOBALS['TSFE']->page['title'] .= ' : ' . htmlspecialchars(trim($category[0]['label']));
            $GLOBALS['TSFE']->indexedDocTitle = htmlspecialchars(trim($category[0]['label']));
        }

        /**
         * CATEGORY PATH
         */

        // Create marker array
        $markerArray = [];
        $markerArray['###CATEGORY_PATH###'] = $this->local_cObj->stdWrap($this->getCategoryPath($categoryUID),
            $localConf['categoryPath_stdWrap.']);

        // Substitute the markers in the given sub sub part
        $subpartContent = null;
        $subpartContent = $this->markerBasedTemplateService->substituteMarkerArray($this->markerBasedTemplateService->getSubpart($templateCode,
            '###' . $subSub_pathmenu . '###'), $markerArray);

        // Substitute the template code with the given subpartcontent
        $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_pathmenu . '###',
            $subpartContent);

        /**
         * ADD DOWNLOAD
         */

        // Check if anonymous users are allowed to add downloads
        $allowAddDownloads = $this->pi_getFFvalue($this->flexform, 'allowAddDownloads',
            'sDEF') ? $this->pi_getFFvalue($this->flexform, 'allowAddDownloads',
            'sDEF') : $this->conf['allowAddDownloads'];

        // Check if a frontend user is logged in
        $isLoggedIn = $GLOBALS['TSFE']->loginUser;

        // Create add download
        if ($isLoggedIn == 1 || $allowAddDownloads == 1) {
            // Create marker array
            $markerArray = [];

            $GLOBALS['TSFE']->ATagParams = $GLOBALS['TSFE']->ATagParams . ($localConf['downloadAddNew.']['ATagParams'] ? ' ' . $localConf['downloadAddNew.']['ATagParams'] : '');
            $download = $this->pi_LinkTP(htmlspecialchars(trim($this->pi_getLL('ll_add_download'))), [
                'tx_abdownloads_pi1[action]'       => 'getviewaddnewdownload',
                'tx_abdownloads_pi1[category_uid]' => $categoryUID,
                'tx_abdownloads_pi1[cid]'          => $this->cObj->data['uid'],
            ], $this->allowCaching);
            $markerArray['###DOWNLOAD_ADD_NEW###'] = $this->local_cObj->stdWrap($download,
                $localConf['downloadAddNew_stdWrap.']);
            $GLOBALS['TSFE']->ATagParams = $originalATagParams;

            // Substitute the markers in the given sub sub part
            $subpartContent = null;
            $subpartContent = $this->markerBasedTemplateService->substituteMarkerArray($this->markerBasedTemplateService->getSubpart($templateCode,
                '###' . $subSub_additional . '###'), $markerArray);

            // Substitute the template code with the given subpartcontent
            $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_additional . '###',
                $subpartContent);
        } else {
            $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_additional . '###', '');
        }

        /**
         * CATEGORIES
         */

        // Init total download count
        $categoryDownloadCountTotal = null;

        // Count number of downloads in current category
        if ($categoryUID == 0) {
            // Handle topmost category
            $databaseTable = $this->tablePrefix . 'download';
            $whereClause = 'category=0' . $this->enableFields;
            $groupBy = '';
            $orderBy = '';
            $limit = '';
            $downloadsInCurrentCategoryResults = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $databaseTable,
                $whereClause, $groupBy, $orderBy, $limit);
        } else {
            $databaseTable = $this->tablePrefix . 'download';
            $relationTable = $this->tablePrefix . 'category_mm';
            $foreignTable = $this->tablePrefix . 'category';
            $theField = $foreignTable . '.uid';
            $theValue = $categoryUID;
            $whereClause = $this->enableFields;
            $groupBy = '';
            $orderBy = '';
            $limit = '';
            $downloadsInCurrentCategoryResults = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query($databaseTable . '.*',
                $databaseTable, $relationTable, $foreignTable,
                ' AND ' . $theField . '=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($theValue,
                    $databaseTable) . ' ' . $whereClause, $groupBy, $orderBy, $limit);
        }

        if ($this->debugDB) {
            $GLOBALS['TSFE']->set_no_cache();
            t3lib_utility_Debug::debug($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
        }

        // Get record overlay
        $downloadsInCurrentCategory = $this->getRecordOverlay($downloadsInCurrentCategoryResults,
            $this->tablePrefix . 'download');

        // Get flexform config for displaying of categories
        $categorySortBy = $this->pi_getFFvalue($this->flexform, 'categorySortBy', 's_display');
        $categorySortOrder = $this->pi_getFFvalue($this->flexform, 'categorySortOrder', 's_display');

        if ($categorySortBy == 'random') {
            $categorySortBy = 'RAND()';
            $categorySortOrder = '';
        }

        // Get category record(s)
        $categoryLabelsResults = $this->getCategoryRecords('parent_category', $categoryUID,
            $categorySortBy . ' ' . $categorySortOrder);

        // Get record overlay
        $categoryLabels = $this->getRecordOverlay($categoryLabelsResults, $this->tablePrefix . 'category');

        // Output the labels
        if (is_array($categoryLabels) && count($categoryLabels) > 0) {
            $categoryList = null;
            $subpartContent = null;

            for ($i = 0; $i < count($categoryLabels); $i++) {
                // Start the recursion to count the total number of downloads below the current category
                $categoryDownloadCount = $this->recursiveDownloadCount($categoryLabels[$i]['uid']);
                $categoryDownloadCountTotal += $categoryDownloadCount;

                $GLOBALS['TSFE']->ATagParams = $GLOBALS['TSFE']->ATagParams . ($localConf['categoryLabel.']['ATagParams'] ? ' ' . $localConf['categoryLabel.']['ATagParams'] : '');
                $download = $this->pi_LinkTP(htmlspecialchars(trim($categoryLabels[$i]['label'])), [
                    'tx_abdownloads_pi1[action]'       => $action,
                    'tx_abdownloads_pi1[category_uid]' => $categoryLabels[$i]['uid'],
                    'tx_abdownloads_pi1[cid]'          => $this->cObj->data['uid'],
                ], $this->allowCaching);
                $GLOBALS['TSFE']->ATagParams = $originalATagParams;

                // Create marker array
                $markerArrayCategory = [];

                $markerArrayCategory['###CATEGORY_ICON###'] = $this->getImageLink($categoryLabels[$i], 'image',
                    'category', 'list', $categoryUID);
                $markerArrayCategory['###CATEGORY_LABEL###'] = $this->local_cObj->stdWrap($download,
                    $localConf['categoryLabel_stdWrap.']);
                $markerArrayCategory['###CATEGORY_DOWNLOAD_COUNT###'] = $this->local_cObj->stdWrap($categoryDownloadCount,
                    '');
                $markerArrayCategory['###LL_DOWNLOADS###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_downloads_lower'))),
                    '');
                $markerArrayCategory['###LL_TOTAL###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_total'))),
                    '');

                if ($categoryLabels[$i]['description']) {
                    $markerArrayCategory['###CATEGORY_DESCRIPTION###'] = $this->pi_RTEcssText($this->local_cObj->stdWrap(trim($categoryLabels[$i]['description']),
                        $localConf['categoryDescription_stdWrap.']));
                } else {
                    $markerArrayCategory['###CATEGORY_DESCRIPTION###'] = '';
                }

                // Substitute the markers in the given sub sub part
                $categoryList .= $this->markerBasedTemplateService->substituteMarkerArray($this->markerBasedTemplateService->getSubpart($templateCode,
                    '###' . $subSubSub_category . '###'), $markerArrayCategory);
            }

            // Prepare title array
            $markerArrayTitle = [];
            $markerArrayTitle['###LL_CATS###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_cats'))),
                $localConf['categories_stdWrap.']);
            $markerArrayTitle['###LL_DOWNLOADS###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_downloads_lower'))),
                $localConf['downloads_stdWrap.']);
            $markerArrayTitle['###LL_TOTAL###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_total'))),
                '');
            $markerArrayTitle['###DOWNLOAD_COUNT_TOTAL###'] = $this->local_cObj->stdWrap($categoryDownloadCountTotal + count($downloadsInCurrentCategory),
                '');

            $wrappedSubpartArray = [];
            $subpartArray['###CATEGORY###'] = $categoryList;
            $subpartContent = $this->markerBasedTemplateService->substituteMarkerArrayCached($this->markerBasedTemplateService->getSubpart($templateCode,
                '###' . $subSub_categories . '###'), $markerArrayTitle, $subpartArray, $wrappedSubpartArray);

            $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_categories . '###',
                $subpartContent);
        } else {
            $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_categories . '###', '');
        }

        /**
         * DOWNLOADS
         */

        // Get flexform config for displaying of downloads
        $downloadSortBy = $this->pi_getFFvalue($this->flexform, 'downloadSortBy', 's_display');
        $downloadSortOrder = $this->pi_getFFvalue($this->flexform, 'downloadSortOrder', 's_display');
        $listLimit = intval($this->pi_getFFvalue($this->flexform, 'listLimit',
            's_display') ? $this->pi_getFFvalue($this->flexform, 'listLimit', 's_display') : $this->conf['listLimit']);
        $noSponsoredPreference = $this->pi_getFFvalue($this->flexform, 'noSponsoredPreference', 's_display');

        $limitStart = 0;
        if (intval($this->piVars['pointer']) > 0) {
            $limitStart = intval($this->piVars['pointer']) * $listLimit;
        }

        if ($downloadSortBy == 'random') {
            $downloadSortBy = 'RAND()';
            $downloadSortOrder = '';
        }

        if (!$noSponsoredPreference) {
            $downloadSortBy = 'sponsored DESC,' . $downloadSortBy;
        }

        // Get downloads (only those, which are approved)
        if ($categoryUID == 0) {
            // Handle topmost category
            // Count number of downloads in current category
            $databaseTable = $this->tablePrefix . 'download';
            $whereClause = 'category=0' . $this->enableFields;
            $groupBy = '';
            $orderBy = $downloadSortBy . ' ' . $downloadSortOrder;
            $limit = $limitStart . ', ' . $listLimit;
            $downloadsResults = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $databaseTable, $whereClause, $groupBy,
                $orderBy, $limit);
        } else {
            $databaseTable = $this->tablePrefix . 'download';
            $relationTable = $this->tablePrefix . 'category_mm';
            $foreignTable = $this->tablePrefix . 'category';
            $theField = $foreignTable . '.uid';
            $theValue = $categoryUID;
            $whereClause = $this->enableFields;
            $groupBy = '';
            $orderBy = $downloadSortBy . ' ' . $downloadSortOrder;
            $limit = $limitStart . ', ' . $listLimit;
            $downloadsResults = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query($databaseTable . '.*', $databaseTable,
                $relationTable, $foreignTable, ' AND ' . $theField . '=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($theValue,
                    $databaseTable) . ' ' . $whereClause, $groupBy, $orderBy, $limit);
        }

        if ($this->debugDB) {
            $GLOBALS['TSFE']->set_no_cache();
            t3lib_utility_Debug::debug($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
        }

        // Get record overlay
        $downloads = $this->getRecordOverlay($downloadsResults, $this->tablePrefix . 'download');

        // Display downloads
        if (is_array($downloads) && count($downloads) > 0) {
            $downloadList = null;
            $subpartContent = null;

            for ($i = 0; $i < count($downloads); $i++) {
                // Create marker array
                $markerArrayDownload = [];

                $this->fillMarkerArray($markerArrayDownload, $downloads[$i], $localConf, $categoryUID);

                // Substitute the markers in the given sub sub part
                $downloadList .= $this->markerBasedTemplateService->substituteMarkerArray($this->markerBasedTemplateService->getSubpart($templateCode,
                    '###' . $subSubSub_download . (($i % $this->alternatingLayouts + 1) ? '_' . ($i % $this->alternatingLayouts + 1) : '') . '###'),
                    $markerArrayDownload);
            }

            // Prepare title array
            $markerArrayTitle = [];
            $markerArrayTitle['###LL_DOWNLOADS###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_downloads'))),
                $localConf['downloads_stdWrap.']);

            $wrappedSubpartArray = [];
            $subpartArray['###DOWNLOAD###'] = $downloadList;
            $subpartContent = $this->markerBasedTemplateService->substituteMarkerArrayCached($this->markerBasedTemplateService->getSubpart($templateCode,
                '###' . $subSub_downloads . '###'), $markerArrayTitle, $subpartArray, $wrappedSubpartArray);

            $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_downloads . '###',
                $subpartContent);
        } else {
            $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_downloads . '###', '');
        }

        /**
         * PAGEBROWSER
         */

        // Render the pagebrowser if needed
        $pointerName = $this->pointerName = 'pointer';

        if (count($downloadsInCurrentCategory) > $listLimit && !$this->conf['noPageBrowser'] && !$this->pi_getFFvalue($this->flexform,
                'noPageBrowser', 's_display')) {
            // Configure pagebrowser vars
            $this->internal['res_count'] = count($downloadsInCurrentCategory);
            $this->internal['results_at_a_time'] = $listLimit;
            $this->internal['maxPages'] = $this->conf['pageBrowser.']['maxPages'] > 0 ? $this->conf['pageBrowser.']['maxPages'] : 10;
            $this->internal['action'] = $action;
            $this->internal['category_uid'] = $categoryUID;

            if (!$this->conf['pageBrowser.']['showPBrowserText']) {
                $this->LOCAL_LANG[$this->LLkey]['pi_list_browseresults_page'] = '';
            }

            if ($this->conf['userPageBrowserFunc']) {
                $markerArray = $this->userProcess('userPageBrowserFunc', $markerArray);
            } else {
                if ($this->conf['usePiBasePagebrowser']) {
                    $this->internal['pagefloat'] = $this->conf['pageBrowser.']['pagefloat'];
                    $this->internal['showFirstLast'] = $this->conf['pageBrowser.']['showFirstLast'];
                    $this->internal['showRange'] = $this->conf['pageBrowser.']['showRange'];
                    $this->internal['dontDownloadActivePage'] = $this->conf['pageBrowser.']['dontDownloadActivePage'];

                    $wrapArrFields = GeneralUtility::trimExplode(',',
                        'disabledDownloadWrap,inactiveDownloadWrap,activeDownloadWrap,browseDownloadsWrap,showResultsWrap,showResultsNumbersWrap,browseBoxWrap');
                    $wrapArr = [];
                    foreach ($wrapArrFields as $key) {
                        if ($this->conf['pageBrowser.'][$key]) {
                            $wrapArr[$key] = $this->conf['pageBrowser.'][$key];
                        }
                    }

                    // If there is a GETvar in the URL that is not in this list, caching will be disabled for the pagebrowser downloads
                    $this->pi_isOnlyFields = $pointerName . ',action,category_uid';
                    $this->pi_alwaysPrev = $this->conf['pageBrowser.']['alwaysPrev'];
                    $markerArray['###BROWSE_DOWNLOADS###'] = $this->pi_list_browseresults($this->conf['pageBrowser.']['showResultCount'],
                        $this->conf['pageBrowser.']['tableParams'], $wrapArr, $pointerName,
                        $this->conf['pageBrowser.']['hscText']);
                } else {
                    $markerArray['###BROWSE_DOWNLOADS###'] = $this->makePageBrowser($pointerName);
                }
            }
        } else {
            $markerArray['###BROWSE_DOWNLOADS###'] = '';
        }

        // Finally substitute the marker array
        $templateCode = $this->markerBasedTemplateService->substituteMarkerArray($templateCode, $markerArray);

        /**
         * TOP LISTS
         */

        // Return the generated content
        $content = $templateCode;

        return $content;
    }

    /**
     * displayTree( $categoryUID = 0, $level = 0 )
     *
     * Generates the html for the tree view of this extension. In this view a tree of all downloads is shown.
     *
     * @param    integer $categoryUID UID of the category that shall be shown.
     * @param    integer $level Level in the tree.
     * @return    string        The generated HTML source for this view.
     */
    function displayTree($categoryUID = 0, $level = 0)
    {

        // Init some vars
        $content = '';
        $templateCode = null;
        $conf['subpartMarker'] = 'VIEW_TREE';
        $subSub_treedownload = 'TREE_DOWNLOAD';

        // Get the html source between subpart markers from the template file
        $templateCode = $this->markerBasedTemplateService->getSubpart($this->originalTemplateCode, '###' . $conf['subpartMarker'] . '###');

        // Get local config
        $localConf = $this->conf['treeView.'];

        // Save original ATagParams
        $originalATagParams = $GLOBALS['TSFE']->ATagParams;

        // Check if anonymous users may add downloads
        $allowAddDownloads = $this->pi_getFFvalue($this->flexform, 'allowAddDownloads',
            'sDEF') ? $this->pi_getFFvalue($this->flexform, 'allowAddDownloads',
            'sDEF') : $this->conf['allowAddDownloads'];

        // Check if a fe_user is logged in
        $isLoggedIn = $GLOBALS['TSFE']->loginUser;

        // Calculate indention from the current level
        $indention = $level * $localConf['indentionFactor'];
        $level++;

        /**
         * CATEGORY
         */

        // Get category label
        if ($categoryUID == 0) {
            $category[0]['label'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_cats'))), '');
        } else {
            // Get category record(s)
            $categoryResult = $this->getCategoryRecords('uid', $categoryUID);

            // Get record overlay
            $category = $this->getRecordOverlay($categoryResult, $this->tablePrefix . 'category');
        }

        /**
         * DOWNLOADS
         */

        // Get flexform config for displaying of downloads
        $downloadSortBy = $this->pi_getFFvalue($this->flexform, 'downloadSortBy', 's_display');
        $downloadSortOrder = $this->pi_getFFvalue($this->flexform, 'downloadSortOrder', 's_display');
        $noSponsoredPreference = $this->pi_getFFvalue($this->flexform, 'noSponsoredPreference', 's_display');

        if ($downloadSortBy == 'random') {
            $downloadSortBy = 'RAND()';
            $downloadSortOrder = '';
        }

        if (!$noSponsoredPreference) {
            $downloadSortBy = 'sponsored DESC,' . $downloadSortBy;
        }

        // Get downloads in current category
        if ($categoryUID == 0) {
            // Handle topmost category
            $databaseTable = $this->tablePrefix . 'download';
            $whereClause = 'category=0' . $this->enableFields;
            $groupBy = '';
            $orderBy = $downloadSortBy . ' ' . $downloadSortOrder;
            $limit = '';
            $downloadsResults = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $databaseTable, $whereClause, $groupBy,
                $orderBy, $limit);
        } else {
            $databaseTable = $this->tablePrefix . 'download';
            $relationTable = $this->tablePrefix . 'category_mm';
            $foreignTable = $this->tablePrefix . 'category';
            $theField = $foreignTable . '.uid';
            $theValue = $categoryUID;
            $whereClause = $this->enableFields;
            $groupBy = '';
            $orderBy = $downloadSortBy . ' ' . $downloadSortOrder;
            $limit = '';
            $downloadsResults = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query($databaseTable . '.*', $databaseTable,
                $relationTable, $foreignTable, ' AND ' . $theField . '=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($theValue,
                    $databaseTable) . ' ' . $whereClause, $groupBy, $orderBy, $limit);
        }

        if ($this->debugDB) {
            $GLOBALS['TSFE']->set_no_cache();
            t3lib_utility_Debug::debug($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
        }

        // Get record overlay
        $downloads = $this->getRecordOverlay($downloadsResults, $this->tablePrefix . 'download');

        // Display downloads
        if (is_array($downloads) && count($downloads) > 0) {
            for ($j = 0; $j < count($downloads); $j++) {
                // Create marker array
                $markerArray = [];

                $this->fillMarkerArray($markerArray, $downloads[$j], $localConf, $categoryUID);

                $markerArray['###INDENTION###'] = $indention;
                $markerArray['###LEVEL###'] = $level;

                // Substitute the markers in the given sub sub part
                $downloadList .= $this->markerBasedTemplateService->substituteMarkerArray($this->markerBasedTemplateService->getSubpart($templateCode,
                    '###' . $subSub_treedownload . '###'), $markerArray);
            }

            $markerArrayMessage = [];

            // Start the recursion to count the total number of downloads below the current category
            $categoryDownloadCount = $this->recursiveDownloadCount($category[0]['uid']);

            $markerArrayMessage['###CATEGORY_ANCHOR###'] = 'cat_' . $category[0]['uid'];
            $markerArrayMessage['###CATEGORY_DOWNLOAD_COUNT###'] = $this->local_cObj->stdWrap($categoryDownloadCount,
                '');
            $markerArrayMessage['###INDENTION###'] = $indention;
            $markerArrayMessage['###LEVEL###'] = $level;
            $markerArrayMessage['###CATEGORY_ICON###'] = $this->getImageLink($category[0], 'image', 'category', 'tree',
                $categoryUID);
            $markerArrayMessage['###CATEGORY_LABEL###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($category[0]['label'])),
                $localConf['categoryLabel_stdWrap.']);

            if ($isLoggedIn == 1 || $allowAddDownloads == 1) {
                $GLOBALS['TSFE']->ATagParams = $GLOBALS['TSFE']->ATagParams . ($localConf['downloadAddNew.']['ATagParams'] ? ' ' . $localConf['downloadAddNew.']['ATagParams'] : '');
                $download = $this->pi_LinkTP(htmlspecialchars(trim($this->pi_getLL('ll_add_download'))), [
                    'tx_abdownloads_pi1[action]'       => 'getviewaddnewdownload',
                    'tx_abdownloads_pi1[category_uid]' => $categoryUID,
                    'tx_abdownloads_pi1[cid]'          => $this->cObj->data['uid'],
                ], $this->allowCaching);
                $markerArrayMessage['###DOWNLOAD_ADD_NEW###'] = $this->local_cObj->stdWrap($download,
                    $localConf['downloadAddNew_stdWrap.']);
                $GLOBALS['TSFE']->ATagParams = $originalATagParams;
            } else {
                $markerArrayMessage['###DOWNLOAD_ADD_NEW###'] = '';
            }

            if ($category[0]['description']) {
                $markerArrayMessage['###CATEGORY_DESCRIPTION###'] = $this->pi_RTEcssText($this->local_cObj->stdWrap(trim($category[0]['description']),
                    $localConf['categoryDescription_stdWrap.']));
            } else {
                $markerArrayMessage['###CATEGORY_DESCRIPTION###'] = '';
            }

            $wrappedSubpartArray = [];
            $subpartArray['###TREE_DOWNLOAD###'] = $downloadList;
            $content = $this->markerBasedTemplateService->substituteMarkerArrayCached($templateCode, $markerArrayMessage, $subpartArray,
                $wrappedSubpartArray);
        } else {
            $markerArrayMessage = [];

            // Start the recursion to count the total number of downloads below the current category
            $categoryDownloadCount = $this->recursiveDownloadCount($category[0]['uid']);

            $markerArrayMessage['###CATEGORY_ANCHOR###'] = 'cat_' . $category[0]['uid'];
            $markerArrayMessage['###CATEGORY_DOWNLOAD_COUNT###'] = $this->local_cObj->stdWrap($categoryDownloadCount,
                '');
            $markerArrayMessage['###INDENTION###'] = $indention;
            $markerArrayMessage['###LEVEL###'] = $level;
            $markerArrayMessage['###CATEGORY_ICON###'] = $this->getImageLink($category[0], 'image', 'category', 'tree',
                $categoryUID);
            $markerArrayMessage['###CATEGORY_LABEL###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($category[0]['label'])),
                $localConf['categoryLabel_stdWrap.']);

            if ($isLoggedIn == 1 || $allowAddDownloads == 1) {
                $GLOBALS['TSFE']->ATagParams = $GLOBALS['TSFE']->ATagParams . ($localConf['downloadAddNew.']['ATagParams'] ? ' ' . $localConf['downloadAddNew.']['ATagParams'] : '');
                $download = $this->pi_LinkTP(htmlspecialchars(trim($this->pi_getLL('ll_add_download'))), [
                    'tx_abdownloads_pi1[action]'       => 'getviewaddnewdownload',
                    'tx_abdownloads_pi1[category_uid]' => $categoryUID,
                    'tx_abdownloads_pi1[cid]'          => $this->cObj->data['uid'],
                ], $this->allowCaching);
                $markerArrayMessage['###DOWNLOAD_ADD_NEW###'] = $this->local_cObj->stdWrap($download,
                    $localConf['downloadAddNew_stdWrap.']);
                $GLOBALS['TSFE']->ATagParams = $originalATagParams;
            } else {
                $markerArrayMessage['###DOWNLOAD_ADD_NEW###'] = '';
            }

            if ($category[0]['description']) {
                $markerArrayMessage['###CATEGORY_DESCRIPTION###'] = $this->pi_RTEcssText($this->local_cObj->stdWrap(trim($category[0]['description']),
                    $localConf['categoryDescription_stdWrap.']));
            } else {
                $markerArrayMessage['###CATEGORY_DESCRIPTION###'] = '';
            }

            $wrappedSubpartArray = [];
            $subpartArray['###TREE_DOWNLOAD###'] = '';
            $content = $this->markerBasedTemplateService->substituteMarkerArrayCached($templateCode, $markerArrayMessage, $subpartArray,
                $wrappedSubpartArray);

            // hide empty subtrees
            if ($categoryDownloadCount === 0) {
                $content = '';
            }
        }

        /**
         * SUBCATEGORIES
         */

        // Get flexform config for displaying of categories
        $categorySortBy = $this->pi_getFFvalue($this->flexform, 'categorySortBy', 's_display');
        $categorySortOrder = $this->pi_getFFvalue($this->flexform, 'categorySortOrder', 's_display');

        if ($categorySortBy == 'random') {
            $categorySortBy = 'RAND()';
            $categorySortOrder = '';
        }

        // Get subcategories
        $subcategoriesResults = $this->getCategoryRecords('parent_category', $categoryUID,
            $categorySortBy . ' ' . $categorySortOrder);

        // Get record overlay
        $subcategories = $this->getRecordOverlay($subcategoriesResults, $this->tablePrefix . 'category');
        if (is_array($subcategories) && count($subcategories) > 0) {
            // Do the recursion for all subcategories
            for ($i = 0; $i < count($subcategories); $i++) {
                $content .= $this->displayTree($subcategories[$i]['uid'], $level);
            }
        }

        return $content;
    }

    /**
     * displaySearch()
     *
     * Generates the html for the search view of this extension. In this view a search form and after
     * submitting the search results are shown.
     *
     * @return    string        The generated HTML source for this view.
     */
    function displaySearch()
    {

        // Init some vars
        $content = '';
        $templateCode = null;
        $conf['subpartMarker'] = 'VIEW_SEARCH';
        $subSub_form = 'FORM';
        $subSub_downloads = 'DOWNLOADS';
        $subSub_nodownloads = 'NODOWNLOADS';

        // Get the html source between subpart markers from the template file
        $templateCode = $this->markerBasedTemplateService->getSubpart($this->originalTemplateCode, '###' . $conf['subpartMarker'] . '###');

        // Get local config
        $localConf = $this->conf['searchView.'];

        // Save original ATagParams
        $originalATagParams = $GLOBALS['TSFE']->ATagParams;

        // Extract search words
        $searchWords = htmlspecialchars(strip_tags($this->piVars['sword']));

        // Create marker array
        $markerArrayForm = [];
        $markerArrayForm['###FORM_ACTION###'] = htmlspecialchars(GeneralUtility::getIndpEnv('REQUEST_URI'));
        $markerArrayForm['###FORM_SEARCH_VALUE###'] = $searchWords;
        $markerArrayForm['###FORM_SUBMIT_BUTTON_VALUE###'] = htmlspecialchars(trim($this->pi_getLL('pi_list_searchBox_search', 'Search')));
        $markerArrayForm['###FORM_POINTER_VALUE###'] = '';

        $formContent = $this->markerBasedTemplateService->substituteMarkerArray($this->markerBasedTemplateService->getSubpart($templateCode,
            '###' . $subSub_form . '###'), $markerArrayForm);
        $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_form . '###', $formContent);

        $downloads = [];

        if ($searchWords != null) {
            $searchWordsArray = GeneralUtility::trimExplode(' ', $searchWords);

            // Remove duplicates
            $searchWordsArray = array_unique($searchWordsArray);

            // Construct where clause
            $whereClause = '';
            foreach ($searchWordsArray as $id => $searchFor) {
                if (strtoupper($searchFor) == 'AND' || strtoupper($searchFor) == 'OR' || strtoupper($searchFor) == 'NOT') {
                    $whereClause .= ' ' . strtoupper($searchFor) . ' ';
                } else {
                    $whereClause .= '( label LIKE "%' . $searchFor . '%" OR description LIKE "%' . $searchFor . '%" OR tags LIKE "%' . $searchFor . '%" )';
                }
            }

            // Get flexform config for displaying of downloads
            $downloadSortBy = $this->pi_getFFvalue($this->flexform, 'downloadSortBy', 's_display');
            $downloadSortOrder = $this->pi_getFFvalue($this->flexform, 'downloadSortOrder', 's_display');
            $listLimit = intval($this->pi_getFFvalue($this->flexform, 'listLimit',
                's_display') ? $this->pi_getFFvalue($this->flexform, 'listLimit',
                's_display') : $this->conf['listLimit']);
            $noSponsoredPreference = $this->pi_getFFvalue($this->flexform, 'noSponsoredPreference', 's_display');

            $limitStart = 0;
            if (intval($this->piVars['pointer']) > 0) {
                $limitStart = intval($this->piVars['pointer']) * $listLimit;
            }

            if ($downloadSortBy == 'random') {
                $downloadSortBy = 'RAND()';
                $downloadSortOrder = '';
            }

            if (!$noSponsoredPreference) {
                $downloadSortBy = 'sponsored DESC,' . $downloadSortBy;
            }

            // Get downloads
            $databaseTable = $this->tablePrefix . 'download';
            $groupBy = '';
            $orderBy = $downloadSortBy . ' ' . $downloadSortOrder;
            $limit = $limitStart . ', ' . $listLimit;
            $downloadsResults = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $databaseTable, $whereClause, $groupBy,
                $orderBy, $limit);

            if ($this->debugDB) {
                $GLOBALS['TSFE']->set_no_cache();
                t3lib_utility_Debug::debug($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
            }

            // Get record overlay
            $downloads = $this->getRecordOverlay($downloadsResults, $this->tablePrefix . 'download');

            $downloads = $downloads ?? [];

            for ($i = 0; $i < count($downloads); $i++) {
                $categoryUID = $this->getCategoryUID($downloads[$i]['uid']);
                $download = '';

                // Get category record
                $categoryResult = $this->getCategoryRecords('uid', $categoryUID);

                // Get record overlay
                $category = $this->getRecordOverlay($categoryResult, $this->tablePrefix . 'category');

                if ($categoryUID != '0') {
                    $GLOBALS['TSFE']->ATagParams = $GLOBALS['TSFE']->ATagParams . ($localConf['categoryLabel.']['ATagParams'] ? ' ' . $localConf['categoryLabel.']['ATagParams'] : '');
                    $download = $this->pi_LinkTP(htmlspecialchars(trim($category[0]['label'])), [
                        'tx_abdownloads_pi1[action]'       => 'getviewcategory',
                        'tx_abdownloads_pi1[category_uid]' => $categoryUID,
                        'tx_abdownloads_pi1[cid]'          => $this->cObj->data['uid'],
                    ], $this->allowCaching);
                    $GLOBALS['TSFE']->ATagParams = $originalATagParams;
                }

                // Create marker array
                $markerArray = [];

                $markerArray['###CATEGORY_PATH###'] = $this->local_cObj->stdWrap($this->getCategoryPath($categoryUID),
                    $localConf['categoryPath_stdWrap.']);
                $markerArray['###CATEGORY_ICON###'] = $this->getImageLink($category[0], 'image', 'category', 'search',
                    $categoryUID);
                $markerArray['###CATEGORY_LABEL###'] = $this->local_cObj->stdWrap($download,
                    $localConf['categoryLabel_stdWrap.']);

                if ($category[0]['description']) {
                    $markerArray['###CATEGORY_DESCRIPTION###'] = $this->pi_RTEcssText($this->local_cObj->stdWrap(trim($category[0]['description']),
                        $localConf['categoryDescription_stdWrap.']));
                } else {
                    $markerArray['###CATEGORY_DESCRIPTION###'] = '';
                }

                // Get flexform config for the ID of the page with the CATEGORY plugin
                $pageID = intval($this->pi_getFFvalue($this->flexform, 'pageListPlugin',
                    'sDEF') ? $this->pi_getFFvalue($this->flexform, 'pageListPlugin', 'sDEF') : $GLOBALS['TSFE']->id);

                $this->fillMarkerArray($markerArray, $downloads[$i], $localConf, $categoryUID, $pageID);

                // Substitute the markers in the given sub sub part
                $subpartContent .= $this->markerBasedTemplateService->substituteMarkerArray($this->markerBasedTemplateService->getSubpart($templateCode,
                    '###' . $subSub_downloads . '###'), $markerArray);
            }

            if (count($downloads) != 0) {
                // Substitute the sub sub part markers with the given subpartcontents.
                $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_nodownloads . '###', '');
                $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_downloads . '###',
                    $subpartContent);

                // For next step -> LL VALUES
                $markerArray = [];
                $markerArray['###RESULT_MESSAGE###'] = sprintf($this->local_cObj->stdWrap($this->pi_getLL('ll_result'),
                    $localConf['resultMessage_stdWrap.']), $searchFor, count($downloads));
            } else {
                // Create marker array
                $markerArray = [];
                $markerArray['###RESULT_MESSAGE###'] = '';
                $markerArray['###NO_DOWNLOADS_MESSAGE###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('no_downloads_message'))),
                    $localConf['noDownloadsMessage_stdWrap.']);

                // Substitute the markers in the given sub sub part
                $subpartContent = $this->markerBasedTemplateService->substituteMarkerArray($this->markerBasedTemplateService->getSubpart($templateCode,
                    '###' . $subSub_nodownloads . '###'), $markerArray);

                // Substitute the sub sub part markers with the given subpartcontents.
                $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_nodownloads . '###',
                    $subpartContent);
                $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_downloads . '###', '');
            }
        } else {
            // No searchword(s) defined
            $markerArray = [];
            $markerArray['###NO_DOWNLOADS_MESSAGE###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('no_downloads_message'))),
                $localConf['noDownloadsMessage_stdWrap.']);

            // Substitute the markers in the given sub sub part
            $subpartContent = $this->markerBasedTemplateService->substituteMarkerArray($this->markerBasedTemplateService->getSubpart($templateCode,
                '###' . $subSub_nodownloads . '###'), $markerArray);

            // Substitute the sub sub part markers with the given subpartcontents.
            $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_nodownloads . '###',
                $subpartContent);
            $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_downloads . '###', '');

            // For next step -> LL VALUES
            $markerArray = [];
            $markerArray['###RESULT_MESSAGE###'] = '';
        }

        $markerArray['###LL_DOWNLOADS###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_downloads'))),
            $localConf['downloads_stdWrap.']);

        /**
         * PAGEBROWSER
         */

        // Render a pagebrowser if needed
        $pointerName = $this->pointerName = 'pointer';

        if (count($downloads) > $listLimit && !$this->conf['noPageBrowser'] && !$this->pi_getFFvalue($this->flexform,
                'noPageBrowser', 's_display')) {
            //  configure pagebrowser vars
            $this->internal['res_count'] = count($downloads);
            $this->internal['results_at_a_time'] = $listLimit;
            $this->internal['maxPages'] = $this->conf['pageBrowser.']['maxPages'] > 0 ? $this->conf['pageBrowser.']['maxPages'] : 10;
            $this->internal['action'] = $action;
            $this->internal['category_uid'] = null;

            if (!$this->conf['pageBrowser.']['showPBrowserText']) {
                $this->LOCAL_LANG[$this->LLkey]['pi_list_browseresults_page'] = '';
            }

            if ($this->conf['userPageBrowserFunc']) {
                $markerArray = $this->userProcess('userPageBrowserFunc', $markerArray);
            } else {
                if ($this->conf['usePiBasePagebrowser']) {
                    $this->internal['pagefloat'] = $this->conf['pageBrowser.']['pagefloat'];
                    $this->internal['showFirstLast'] = $this->conf['pageBrowser.']['showFirstLast'];
                    $this->internal['showRange'] = $this->conf['pageBrowser.']['showRange'];
                    $this->internal['dontDownloadActivePage'] = $this->conf['pageBrowser.']['dontDownloadActivePage'];

                    $wrapArrFields = GeneralUtility::trimExplode(',',
                        'disabledDownloadWrap,inactiveDownloadWrap,activeDownloadWrap,browseDownloadsWrap,showResultsWrap,showResultsNumbersWrap,browseBoxWrap');
                    $wrapArr = [];
                    foreach ($wrapArrFields as $key) {
                        if ($this->conf['pageBrowser.'][$key]) {
                            $wrapArr[$key] = $this->conf['pageBrowser.'][$key];
                        }
                    }

                    // If there is a GETvar in the URL that is not in this list, caching will be disabled for the pagebrowser downloads
                    $this->pi_isOnlyFields = $pointerName . ',action,category_uid';
                    $this->pi_alwaysPrev = $this->conf['pageBrowser.']['alwaysPrev'];
                    $markerArray['###BROWSE_DOWNLOADS###'] = $this->pi_list_browseresults($this->conf['pageBrowser.']['showResultCount'],
                        $this->conf['pageBrowser.']['tableParams'], $wrapArr, $pointerName,
                        $this->conf['pageBrowser.']['hscText']);
                } else {
                    $markerArray['###BROWSE_DOWNLOADS###'] = $this->makePageBrowser($pointerName);
                }
            }
        } else {
            $markerArray['###BROWSE_DOWNLOADS###'] = '';
        }

        // Finally substitute the marker array
        $templateCode = $this->markerBasedTemplateService->substituteMarkerArray($templateCode, $markerArray);

        // Return the generated content
        $content .= $templateCode;

        return $content;
    }

    /**
     * displayTop( $categoryUID = 0 )
     *
     * Generates the html for the top view of this extension. In this view the TOPx number of downloads
     * concerning rating, clicks and creation time per category (and below) are shown.
     *
     * @param    integer $categoryUID UID of the category that shall be shown.
     * @return    string        The generated HTML source for this view.
     */
    function displayTop($categoryUID = 0)
    {

        // Init some vars
        $content = '';
        $templateCode = null;
        $conf['subpartMarker'] = 'VIEW_TOP';
        $subSub_topdownload = 'TOP_DOWNLOAD';

        // Get the html source between subpart markers from the template file
        $templateCode = $this->markerBasedTemplateService->getSubpart($this->originalTemplateCode, '###' . $conf['subpartMarker'] . '###');

        // Get local config
        $localConf = $this->conf['topView.'];

        // Save original ATagParams
        $originalATagParams = $GLOBALS['TSFE']->ATagParams;

        // Get flexform settings
        $showTopRated = $this->pi_getFFvalue($this->flexform, 'showTopRated', 's_top');
        $showTopAccessed = $this->pi_getFFvalue($this->flexform, 'showTopAccessed', 's_top');
        $showMostRecent = $this->pi_getFFvalue($this->flexform, 'showMostRecent', 's_top');
        $showRandom = $this->pi_getFFvalue($this->flexform, 'showRandom', 's_top');
        $stickToStartCategoryID = $this->pi_getFFvalue($this->flexform, 'stickToStartCategoryID', 's_top');

        // Reset start category based on stickToStartCategoryID
        if ($stickToStartCategoryID) {
            $startCategoryID = intval($this->pi_getFFvalue($this->flexform, 'startCategoryID', 's_display'));
            $categoryUID = $startCategoryID ? $startCategoryID : 0;
        }

        // Get flexform config for the ID of the page with the CATEGORY plugin
        $pageID = intval($this->pi_getFFvalue($this->flexform, 'pageListPlugin',
            'sDEF') ? $this->pi_getFFvalue($this->flexform, 'pageListPlugin', 'sDEF') : $GLOBALS['TSFE']->id);

        // Check which top list(s) shall be displayed
        if ($showTopRated || $showTopAccessed || $showMostRecent || $showRandom) {
            // Prepare category UID list
            $categoryUIDs = $this->recursiveCategoryGet($categoryUID);
            $categoryUIDsImploded = implode(',', $categoryUIDs);

            // Finally, append current category UID
            $categoryUIDsImploded .= ',' . $categoryUID;

            // Prepare array with fields and limits
            $topLists = [
                'rating' => $showTopRated,
                'clicks' => $showTopAccessed,
                'crdate' => $showMostRecent,
                'random' => $showRandom,
            ];

            // Output top lists
            foreach ($topLists as $field => $downloadLimit) {
                if ($downloadLimit) {
                    $downloadList = null;
                    $selectExtension = '';
                    $whereExtension = '';

                    // Get top downloads
                    $databaseTable = $this->tablePrefix . 'download';
                    $relationTable = $this->tablePrefix . 'category_mm';
                    $foreignTable = $this->tablePrefix . 'category';
                    /*
					if( $categoryUID == 0 ) {
						$selectExtension = 'DISTINCT ';
						$whereExtension = ' OR ' . $databaseTable . '.category=0';
					}
*/
                    $selectExtension = 'DISTINCT ';

                    $whereClause = $foreignTable . '.uid IN (' . $categoryUIDsImploded . ')' . $this->enableFields . $whereExtension;
                    $groupBy = '';
                    $orderBy = $field . ' DESC';
                    $limit = $downloadLimit;

                    if ($field == 'random') {
                        $orderBy = 'RAND()';
                    }

                    $topDownloadsResults = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query($selectExtension . $databaseTable . '.*',
                        $databaseTable, $relationTable, $foreignTable, ' AND ' . $whereClause, $groupBy, $orderBy,
                        $limit);

                    if ($this->debugDB) {
                        $GLOBALS['TSFE']->set_no_cache();
                        t3lib_utility_Debug::debug($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
                    }

                    // Get record overlay
                    $topDownloads = $this->getRecordOverlay($topDownloadsResults, $this->tablePrefix . 'download');
                    $fieldName = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_' . $field))),
                        '');

                    for ($i = 0; $i < count($topDownloads); $i++) {
                        $categoryUID = $this->getCategoryUID($topDownloads[$i]['uid']);

                        // Get category record
                        $categoryResult = $this->getCategoryRecords('uid', $categoryUID);

                        // Get record overlay
                        $category = $this->getRecordOverlay($categoryResult, $this->tablePrefix . 'category');

                        $downloadValue = $topDownloads[$i][$field];

                        // Wrap values if necessary
                        if ($field == 'rating') {
                            $downloadValue = round($downloadValue, 2);
                        }
                        if ($field == 'crdate') {
                            $downloadValue = $this->local_cObj->stdWrap($downloadValue, $this->conf['date_stdWrap.']);
                        }

                        // Create marker array
                        $markerArrayDownload = [];

                        $markerArrayDownload['###CATEGORY_PATH###'] = $this->local_cObj->stdWrap($this->getCategoryPath($categoryUID),
                            $localConf['categoryPath_stdWrap.']);
                        $markerArrayDownload['###CATEGORY_ICON###'] = $this->getImageLink($topDownloads[$i], 'image',
                            'category', 'top', $categoryUID);
                        $markerArrayDownload['###CATEGORY_LABEL###'] = $this->local_cObj->stdWrap($download,
                            $localConf['categoryLabel_stdWrap.']);

                        if ($topDownloads[$i]['description']) {
                            $markerArrayDownload['###CATEGORY_DESCRIPTION###'] = $this->pi_RTEcssText($this->local_cObj->stdWrap(trim($topDownloads[$i]['description']),
                                $localConf['categoryDescription_stdWrap.']));
                        } else {
                            $markerArrayDownload['###CATEGORY_DESCRIPTION###'] = '';
                        }

                        $this->fillMarkerArray($markerArrayDownload, $topDownloads[$i], $localConf, $categoryUID,
                            $pageID);

                        if ($field == 'random') {
                            $markerArrayDownload['###DOWNLOAD_VALUE###'] = '';
                        } else {
                            $markerArrayDownload['###DOWNLOAD_VALUE###'] = '(' . $downloadValue . ')';
                        }

                        // Substitute the markers in the given sub sub part
                        $downloadList .= $this->markerBasedTemplateService->substituteMarkerArray($this->markerBasedTemplateService->getSubpart($templateCode,
                            '###' . $subSub_topdownload . '###'), $markerArrayDownload);
                    }

                    if (count($topDownloads) > 0) {
                        // Prepare top message
                        $markerArrayMessage = [];
                        $markerArrayMessage['###TOP_MESSAGE###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_top') . $downloadLimit . ': ' . $fieldName)),
                            $localConf['topMessage_stdWrap.']);

                        $wrappedSubpartArray = [];
                        $subpartArray['###TOP_DOWNLOAD###'] = $downloadList;
                        $content .= $this->markerBasedTemplateService->substituteMarkerArrayCached($templateCode, $markerArrayMessage,
                            $subpartArray, $wrappedSubpartArray);
                    } else {
                        // Prepare top message
                        $markerArrayMessage = [];
                        $markerArrayMessage['###TOP_MESSAGE###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_top') . $downloadLimit . ': ' . $fieldName)),
                            $localConf['topMessage_stdWrap.']);

                        $wrappedSubpartArray = [];
                        $subpartArray['###TOP_DOWNLOAD###'] = '-';
                        $content .= $this->markerBasedTemplateService->substituteMarkerArrayCached($templateCode, $markerArrayMessage,
                            $subpartArray, $wrappedSubpartArray);
                    }
                }
            }

        } else {
            // Don't display any top list(s)
            $type = 'noTopListSet';
            $content = $this->displayFEHelp($type);
        }

        return $content;
    }

    /**
     * displayCatalog( $categoryUID = 0, $level = 0 )
     *
     * Generates the html for the catalog view of this extension. In this view a catalog of the categories and downloads similar to the google catalog is shown.
     *
     * @param    integer $categoryUID UID of the category that shall be shown.
     * @param    integer $level Level in the catalog.
     * @return    string        The generated HTML source for this view.
     */
    function displayCatalog($categoryUID = 0, $level = 0)
    {

        // Init some vars
        $action = 'getviewcatalog';
        $content = '';
        $templateCode = null;
        $conf['subpartMarker'] = 'VIEW_CATALOG';
        $subSub_pathmenu = 'PATHMENU';
        $subSub_additional = 'ADDITIONAL';
        $subSub_category = 'CATEGORY';
        $subSub_downloads = 'DOWNLOADS';
        $subSubSub_subcategories = 'SUBCATEGORIES';
        $subSubSub_download = 'DOWNLOAD';
        $categoryLabels = [];
        // Get the html source between subpart markers from the template file
        $templateCode = $this->markerBasedTemplateService->getSubpart($this->originalTemplateCode, '###' . $conf['subpartMarker'] . '###');

        // Get local config
        $localConf = $this->conf['catalogView.'];

        // Save original ATagParams
        $originalATagParams = $GLOBALS['TSFE']->ATagParams;

        /**
         * PAGE TITLE
         */

        // Substitute the title of the page with the category label
        if ($this->conf['substitutePageTitle'] && $categoryUID != '0') {
            // Get category record(s)
            $categoryResult = $this->getCategoryRecords('uid', $categoryUID);

            // Get record overlay
            $category = $this->getRecordOverlay($categoryResult, $this->tablePrefix . 'category');

            $GLOBALS['TSFE']->page['title'] .= " : " . htmlspecialchars(trim($category[0]['label']));
            $GLOBALS['TSFE']->indexedDocTitle = htmlspecialchars(trim($category[0]['label']));
        }

        /**
         * CATEGORY PATH
         */

        // Create marker array
        $markerArray = [];
        $markerArray['###CATEGORY_PATH###'] = $this->local_cObj->stdWrap($this->getCategoryPath($categoryUID),
            $localConf['categoryPath_stdWrap.']);

        // Substitute the markers in the given sub sub part
        $subpartContent = null;
        $subpartContent = $this->markerBasedTemplateService->substituteMarkerArray($this->markerBasedTemplateService->getSubpart($templateCode,
            '###' . $subSub_pathmenu . '###'), $markerArray);

        // Substitute the template code with the given subpartcontent
        $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_pathmenu . '###',
            $subpartContent);

        /**
         * ADD DOWNLOAD
         */

        // Check if anonymous users are allowed to add downloads
        $allowAddDownloads = $this->pi_getFFvalue($this->flexform, 'allowAddDownloads',
            'sDEF') ? $this->pi_getFFvalue($this->flexform, 'allowAddDownloads',
            'sDEF') : $this->conf['allowAddDownloads'];

        // Check if a frontend user is logged in
        $isLoggedIn = $GLOBALS['TSFE']->loginUser;

        // Create add download
        if ($isLoggedIn == 1 || $allowAddDownloads == 1) {
            // Create marker array
            $markerArray = [];

            $GLOBALS['TSFE']->ATagParams = $GLOBALS['TSFE']->ATagParams . ($localConf['downloadAddNew.']['ATagParams'] ? ' ' . $localConf['downloadAddNew.']['ATagParams'] : '');
            $download = $this->pi_LinkTP(htmlspecialchars(trim($this->pi_getLL('ll_add_download'))), [
                'tx_abdownloads_pi1[action]'       => 'getviewaddnewdownload',
                'tx_abdownloads_pi1[category_uid]' => $categoryUID,
                'tx_abdownloads_pi1[cid]'          => $this->markerBasedTemplateService->data['uid'],
            ], $this->allowCaching);
            $markerArray['###DOWNLOAD_ADD_NEW###'] = $this->local_cObj->stdWrap($download,
                $localConf['downloadAddNew_stdWrap.']);
            $GLOBALS['TSFE']->ATagParams = $originalATagParams;

            // Substitute the markers in the given sub sub part
            $subpartContent = null;
            $subpartContent = $this->markerBasedTemplateService->substituteMarkerArray($this->markerBasedTemplateService->getSubpart($templateCode,
                '###' . $subSub_additional . '###'), $markerArray);

            // Substitute the template code with the given subpartcontent
            $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_additional . '###',
                $subpartContent);
        } else {
            $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_additional . '###', '');
        }

        /**
         * CATEGORIES
         */

        // Init total download count
        $subcategoryDownloadCountTotal = null;

        // Count number of downloads in current category
        $databaseTable = $this->tablePrefix . 'download';
        $relationTable = $this->tablePrefix . 'category_mm';
        $foreignTable = $this->tablePrefix . 'category';
        $theField = $foreignTable . '.uid';
        $theValue = $categoryUID;
        $whereClause = $this->enableFields;
        $groupBy = '';
        $orderBy = '';
        $limit = '';
        $downloadsInCurrentCategoryResults = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query($databaseTable . '.*',
            $databaseTable, $relationTable, $foreignTable,
            ' AND ' . $theField . '=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($theValue,
                $databaseTable) . ' ' . $whereClause, $groupBy, $orderBy, $limit);

        if ($this->debugDB) {
            $GLOBALS['TSFE']->set_no_cache();
            t3lib_utility_Debug::debug($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
        }

        // Get record overlay
        $downloadsInCurrentCategory = $this->getRecordOverlay($downloadsInCurrentCategoryResults,
            $this->tablePrefix . 'download');

        // Get flexform config for displaying of categories
        $categorySortBy = $this->pi_getFFvalue($this->flexform, 'categorySortBy', 's_display');
        $categorySortOrder = $this->pi_getFFvalue($this->flexform, 'categorySortOrder', 's_display');
        $numberOfColumns = intval($this->pi_getFFvalue($this->flexform, 'numColumns',
            's_catalog') ? $this->pi_getFFvalue($this->flexform, 'numColumns', 's_catalog') : 2);
        $maxNumSubcat = intval($this->pi_getFFvalue($this->flexform, 'maxSubcategories',
            's_catalog') ? $this->pi_getFFvalue($this->flexform, 'maxSubcategories', 's_catalog') : 2);

        if ($categorySortBy == 'random') {
            $categorySortBy = 'RAND()';
            $categorySortOrder = '';
        }

        // Get category record(s)
        $categoryLabelsResults = $this->getCategoryRecords('parent_category', $categoryUID,
            $categorySortBy . ' ' . $categorySortOrder);

        // Get record overlay
        $categoryLabels = $this->getRecordOverlay($categoryLabelsResults, $this->tablePrefix . 'category');

        // Output the category labels
        if (is_array($categoryLabels) && count($categoryLabels) > 0) {
            $subpartContent = null;

            for ($i = 0; $i < count($categoryLabels); $i++) {
                // Prepare title array
                $markerArrayCategory = [];

                $subcategoryList = null;

                // Start new row
                if ($i > 0 && $i % $numberOfColumns == 0) {
                    $subpartContent .= $this->markerBasedTemplateService->getSubpart($templateCode, '###NEXT_ROW###');
                }

                // Start the recursion to count the total number of downloads below the current category
                $categoryDownloadCount = $this->recursiveDownloadCount($categoryLabels[$i]['uid']);

                $GLOBALS['TSFE']->ATagParams = $GLOBALS['TSFE']->ATagParams . ($localConf['categoryLabel.']['ATagParams'] ? ' ' . $localConf['categoryLabel.']['ATagParams'] : '');
                $download = $this->pi_LinkTP(htmlspecialchars(trim($categoryLabels[$i]['label'])), [
                    'tx_abdownloads_pi1[action]'       => $action,
                    'tx_abdownloads_pi1[category_uid]' => $categoryLabels[$i]['uid'],
                    'tx_abdownloads_pi1[cid]'          => $this->cObj->data['uid'],
                ], $this->allowCaching);
                $GLOBALS['TSFE']->ATagParams = $originalATagParams;

                $markerArrayCategory['###CATEGORY_ICON###'] = $this->getImageLink($categoryLabels[$i], 'image',
                    'category', 'catalog', $categoryUID);
                $markerArrayCategory['###CATEGORY_LABEL###'] = $this->local_cObj->stdWrap($download,
                    $localConf['categoryLabel_stdWrap.']);
                $markerArrayCategory['###CATEGORY_DOWNLOAD_COUNT###'] = $this->local_cObj->stdWrap($categoryDownloadCount,
                    '');
                $markerArrayCategory['###LL_DOWNLOADS###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_downloads_lower'))),
                    '');
                $markerArrayCategory['###LL_TOTAL###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_total'))),
                    '');

                if ($categoryLabels[$i]['description']) {
                    $markerArrayCategory['###CATEGORY_DESCRIPTION###'] = $this->pi_RTEcssText($this->local_cObj->stdWrap(trim($categoryLabels[$i]['description']),
                        $localConf['categoryDescription_stdWrap.']));
                } else {
                    $markerArrayCategory['###CATEGORY_DESCRIPTION###'] = '';
                }

                // Get subcategories
                $subcategoryLabelsResults = $this->getCategoryRecords('parent_category', $categoryLabels[$i]['uid'],
                    $categorySortBy . ' ' . $categorySortOrder, $maxNumSubcat);

                // Get record overlay
                $subcategoryLabels = $this->getRecordOverlay($subcategoryLabelsResults,
                    $this->tablePrefix . 'category');

                if (count($subcategoryLabels) > 0) {
                    for ($j = 0; $j < count($subcategoryLabels); $j++) {
                        // Start the recursion to count the total number of downloads below the current subcategory
                        $subcategoryDownloadCount = $this->recursiveDownloadCount($subcategoryLabels[$j]['uid']);
                        $subcategoryDownloadCountTotal += $subcategoryDownloadCount;

                        // Create marker array
                        $markerArraySubcategory = [];

                        $GLOBALS['TSFE']->ATagParams = $GLOBALS['TSFE']->ATagParams . ($localConf['subcategoryLabel.']['ATagParams'] ? ' ' . $localConf['subcategoryLabel.']['ATagParams'] : '');
                        $download = $this->pi_LinkTP(htmlspecialchars(trim($subcategoryLabels[$j]['label'])), [
                            'tx_abdownloads_pi1[action]'       => $action,
                            'tx_abdownloads_pi1[category_uid]' => $subcategoryLabels[$j]['uid'],
                            'tx_abdownloads_pi1[cid]'          => $this->cObj->data['uid'],
                        ], $this->allowCaching);
                        $GLOBALS['TSFE']->ATagParams = $originalATagParams;

                        $markerArraySubcategory['###SUBCATEGORY_ICON###'] = $this->getImageLink($subcategoryLabels[$j],
                            'image', 'category', 'catalog', $categoryUID);
                        $markerArraySubcategory['###SUBCATEGORY_LABEL###'] = $this->local_cObj->stdWrap($download,
                            $localConf['subcategoryLabel_stdWrap.']);
                        $markerArraySubcategory['###SUBCATEGORY_DOWNLOAD_COUNT###'] = $this->local_cObj->stdWrap($subcategoryDownloadCount,
                            '');
                        $markerArraySubcategory['###DOWNLOAD_COUNT_TOTAL###'] = $this->local_cObj->stdWrap($subcategoryLinkCountTotal + count($linksInCurrentCategory),
                            '');
                        $markerArraySubcategory['###LL_DOWNLOADS###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_downloads_lower'))),
                            '');
                        $markerArraySubcategory['###LL_TOTAL###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_total'))),
                            '');

                        if ($subcategoryLabels[$j]['description']) {
                            $markerArraySubcategory['###SUBCATEGORY_DESCRIPTION###'] = $this->pi_RTEcssText($this->local_cObj->stdWrap(trim($subcategoryLabels[$j]['description']),
                                $localConf['subcategoryDescription_stdWrap.']));
                        } else {
                            $markerArraySubcategory['###SUBCATEGORY_DESCRIPTION###'] = '';
                        }

                        $separator = $localConf['separator'];
                        if ($j == count($subcategoryLabels) - 1) {
                            $separator .= $localConf['appendix'];
                        }

                        // Substitute the markers in the given sub sub part
                        $subcategoryList .= $this->markerBasedTemplateService->substituteMarkerArray($this->markerBasedTemplateService->getSubpart($templateCode,
                                '###' . $subSubSub_subcategories . '###'), $markerArraySubcategory) . $separator;
                    }
                }

                $markerArrayCategory['###DOWNLOAD_COUNT_TOTAL###'] = $this->local_cObj->stdWrap($subcategoryDownloadCountTotal + count($downloadsInCurrentCategory),
                    '');

                $wrappedSubpartArray = [];
                $subpartArray['###SUBCATEGORIES###'] = $subcategoryList;
                $subpartContent .= $this->markerBasedTemplateService->substituteMarkerArrayCached($this->markerBasedTemplateService->getSubpart($templateCode,
                    '###' . $subSub_category . '###'), $markerArrayCategory, $subpartArray, $wrappedSubpartArray);
            }

            $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_category . '###',
                $subpartContent);
        } else {
            $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_category . '###', '');
        }

        /**
         * DOWNLOADS
         */

        // Get flexform config for displaying of downloads
        $downloadSortBy = $this->pi_getFFvalue($this->flexform, 'downloadSortBy', 's_display');
        $downloadSortOrder = $this->pi_getFFvalue($this->flexform, 'downloadSortOrder', 's_display');
        $listLimit = intval($this->pi_getFFvalue($this->flexform, 'listLimit',
            's_display') ? $this->pi_getFFvalue($this->flexform, 'listLimit', 's_display') : $this->conf['listLimit']);
        $noSponsoredPreference = $this->pi_getFFvalue($this->flexform, 'noSponsoredPreference', 's_display');

        $limitStart = 0;
        if (intval($this->piVars['pointer']) > 0) {
            $limitStart = intval($this->piVars['pointer']) * $listLimit;
        }

        if ($downloadSortBy == 'random') {
            $downloadSortBy = 'RAND()';
            $downloadSortOrder = '';
        }

        if (!$noSponsoredPreference) {
            $downloadSortBy = 'sponsored DESC,' . $downloadSortBy;
        }

        // Get downloads (only those, which are approved)
        if ($categoryUID == 0) {
            // Handle topmost category
            // Count number of downloads in current category
            $databaseTable = $this->tablePrefix . 'download';
            $whereClause = 'category=0' . $this->enableFields;
            $groupBy = '';
            $orderBy = $downloadSortBy . ' ' . $downloadSortOrder;
            $limit = $limitStart . ', ' . $listLimit;
            $downloadsResults = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $databaseTable, $whereClause, $groupBy,
                $orderBy, $limit);
        } else {
            $databaseTable = $this->tablePrefix . 'download';
            $relationTable = $this->tablePrefix . 'category_mm';
            $foreignTable = $this->tablePrefix . 'category';
            $theField = $foreignTable . '.uid';
            $theValue = $categoryUID;
            $whereClause = $this->enableFields;
            $groupBy = '';
            $orderBy = $downloadSortBy . ' ' . $downloadSortOrder;
            $limit = $limitStart . ', ' . $listLimit;
            $downloadsResults = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query($databaseTable . '.*', $databaseTable,
                $relationTable, $foreignTable, ' AND ' . $theField . '=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($theValue,
                    $databaseTable) . ' ' . $whereClause, $groupBy, $orderBy, $limit);
        }

        if ($this->debugDB) {
            $GLOBALS['TSFE']->set_no_cache();
            t3lib_utility_Debug::debug($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
        }

        // Get record overlay
        $downloads = $this->getRecordOverlay($downloadsResults, $this->tablePrefix . 'download');

        // Display downloads
        if (count($downloads) > 0) {
            $downloadList = null;
            $subpartContent = null;

            for ($i = 0; $i < count($downloads); $i++) {
                // Create marker array
                $markerArrayDownload = [];

                $this->fillMarkerArray($markerArrayDownload, $downloads[$i], $localConf, $categoryUID);

                // Substitute the markers in the given sub sub part
                $downloadList .= $this->markerBasedTemplateService->substituteMarkerArray($this->markerBasedTemplateService->getSubpart($templateCode,
                    '###' . $subSubSub_download . (($i % $this->alternatingLayouts + 1) ? '_' . ($i % $this->alternatingLayouts + 1) : '') . '###'),
                    $markerArrayDownload);
            }

            // Prepare title array
            $markerArrayTitle = [];
            $markerArrayTitle['###LL_DOWNLOADS###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_downloads'))),
                $localConf['downloads_stdWrap.']);

            $wrappedSubpartArray = [];
            $subpartArray['###DOWNLOAD###'] = $downloadList;
            $subpartContent = $this->markerBasedTemplateService->substituteMarkerArrayCached($this->markerBasedTemplateService->getSubpart($templateCode,
                '###' . $subSub_downloads . '###'), $markerArrayTitle, $subpartArray, $wrappedSubpartArray);

            $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_downloads . '###',
                $subpartContent);
        } else {
            $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_downloads . '###', '');
        }

        /**
         * PAGEBROWSER
         */

        // Render the pagebrowser if needed
        $pointerName = $this->pointerName = 'pointer';

        if (count($downloadsInCurrentCategory) > $listLimit && !$this->conf['noPageBrowser'] && !$this->pi_getFFvalue($this->flexform,
                'noPageBrowser', 's_display')) {
            // Configure pagebrowser vars
            $this->internal['res_count'] = count($downloadsInCurrentCategory);
            $this->internal['results_at_a_time'] = $listLimit;
            $this->internal['maxPages'] = $this->conf['pageBrowser.']['maxPages'] > 0 ? $this->conf['pageBrowser.']['maxPages'] : 10;
            $this->internal['action'] = $action;
            $this->internal['category_uid'] = $categoryUID;

            if (!$this->conf['pageBrowser.']['showPBrowserText']) {
                $this->LOCAL_LANG[$this->LLkey]['pi_list_browseresults_page'] = '';
            }

            if ($this->conf['userPageBrowserFunc']) {
                $markerArray = $this->userProcess('userPageBrowserFunc', $markerArray);
            } else {
                if ($this->conf['usePiBasePagebrowser']) {
                    $this->internal['pagefloat'] = $this->conf['pageBrowser.']['pagefloat'];
                    $this->internal['showFirstLast'] = $this->conf['pageBrowser.']['showFirstLast'];
                    $this->internal['showRange'] = $this->conf['pageBrowser.']['showRange'];
                    $this->internal['dontDownloadActivePage'] = $this->conf['pageBrowser.']['dontDownloadActivePage'];

                    $wrapArrFields = GeneralUtility::trimExplode(',',
                        'disabledDownloadWrap,inactiveDownloadWrap,activeDownloadWrap,browseDownloadsWrap,showResultsWrap,showResultsNumbersWrap,browseBoxWrap');
                    $wrapArr = [];
                    foreach ($wrapArrFields as $key) {
                        if ($this->conf['pageBrowser.'][$key]) {
                            $wrapArr[$key] = $this->conf['pageBrowser.'][$key];
                        }
                    }

                    // If there is a GETvar in the URL that is not in this list, caching will be disabled for the pagebrowser downloads
                    $this->pi_isOnlyFields = $pointerName . ',action,category_uid';
                    $this->pi_alwaysPrev = $this->conf['pageBrowser.']['alwaysPrev'];
                    $markerArray['###BROWSE_DOWNLOADS###'] = $this->pi_list_browseresults($this->conf['pageBrowser.']['showResultCount'],
                        $this->conf['pageBrowser.']['tableParams'], $wrapArr, $pointerName,
                        $this->conf['pageBrowser.']['hscText']);
                } else {
                    $markerArray['###BROWSE_DOWNLOADS###'] = $this->makePageBrowser($pointerName);
                }
            }
        } else {
            $markerArray['###BROWSE_DOWNLOADS###'] = '';
        }

        // Finally substitute the marker array
        $templateCode = $this->markerBasedTemplateService->substituteMarkerArray($templateCode, $markerArray);

        // Return the generated content
        $content = $templateCode;

        return $content;
    }

    /*************************************
     *
     * Additional views after user interaction
     *
     *************************************/

    /**
     * getViewClickedDownload( $uid = null )
     *
     * Increases the clicks value of a given download uid.
     * Then sends the requested file to the browser.
     *
     * @param    integer $uid UID of the download that was clicked.
     * @return    string        The generated HTML source for this view.
     */
    function getViewClickedDownload($uid = null)
    {

        if ($uid != null) {

            // Get download record
            $downloadResult = $this->getDownloadRecord($uid);

            // Get record overlay
            $download = $this->getRecordOverlay($downloadResult, $this->tablePrefix . 'download');

            // Get user's IP address
            $ip = GeneralUtility::getIndpEnv('REMOTE_ADDR');

            // Check for multiple-click
            if (!GeneralUtility::cmpIP($ip, $download[0]['click_ip'])) {
                // Update clicks and click_ip of the download
                $whereClause = "uid=$uid";
                $updateFields = [
                    'clicks'   => $download[0]['clicks'] + 1,
                    'click_ip' => $ip,
                ];
                $GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->tablePrefix . 'download', $whereClause, $updateFields);
            }
        } else {
            die('ERROR: No download UID given!');
        }

        // Send file to browser
        $file = GeneralUtility::getFileAbsFileName($this->filePath . $download[0]['file']);
        $fileInformation = $this->getTotalFileInfo($file);

        header('Content-Description: Modern Downloads File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename="' . $download[0]['file'] . '"');
        header('Content-Length: ' . $fileInformation['size']);
        @readfile($file) || die;
        exit;
    }

    /**
     * getViewDetailsForDownload( $uid = null, $categoryUID )
     *
     * Shows the detail page for a download.
     *
     * @param    integer $uid UID of the download.
     * @param    integer $categoryUID UID of the category the download belongs to.
     * @return    string        The generated HTML source for this view.
     */
    function getViewDetailsForDownload($uid = null, $categoryUID)
    {

        // Init some vars
        $content = null;
        $templateCode = null;
        $conf['subpartMarker'] = 'VIEW_DETAILS_FOR_DOWNLOAD';

        // Get the html source between subpart markers from the template file
        $templateCode = $this->markerBasedTemplateService->getSubpart($this->originalTemplateCode, '###' . $conf['subpartMarker'] . '###');

        if ($uid != null) {
            // Disable caching to always get recent data
            $GLOBALS['TSFE']->set_no_cache();

            // Get download record
            $downloadResult = $this->getDownloadRecord($uid);

            // Get record overlay
            $download = $this->getRecordOverlay($downloadResult, $this->tablePrefix . 'download');

            if ($this->debug) {
                $GLOBALS['TSFE']->set_no_cache();
                t3lib_utility_Debug::debug($download);
            }

            if (is_array($download) && $download[0]['pid'] > 0) {
                // Get file information
                $file = GeneralUtility::getFileAbsFileName($this->filePath . $download[0]['file']);
                $fileInformation = $this->getTotalFileInfo($file);

                // Create marker array
                $markerArray = [];

                $this->fillMarkerArray($markerArray, $download[0], $this->conf, $categoryUID);

                $markerArray['###CATEGORY_PATH###'] = $this->local_cObj->stdWrap($this->getCategoryPath($categoryUID),
                    $this->conf['categoryPath_stdWrap.']);
                // FIXME: Add specific detailed download view downloadImage.ATagParams
                $markerArray['###DOWNLOAD_IMAGE###'] = $this->getImageLink($download[0], 'image', 'link', 'list',
                    $categoryUID, true);
                $markerArray['###LINK_BACK_TO_CATEGORY###'] = '<a href="javascript:history.back()">' . htmlspecialchars(trim($this->pi_getLL('ll_back'))) . '</a>';

                // Substitute the title of the page with the download label
                if ($this->conf['substitutePageTitle']) {
                    $GLOBALS['TSFE']->page['title'] .= " : " . htmlspecialchars(trim($download[0]['label']));
                    $GLOBALS['TSFE']->indexedDocTitle = htmlspecialchars(trim($download[0]['label']));
                }

                // Finally substitute the marker array
                $templateCode = $this->markerBasedTemplateService->substituteMarkerArray($templateCode, $markerArray);
            } else {
                $templateCode = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('non_public_download_message'))),
                    $this->conf['nonPublicDownloadMessage_stdWrap.']);
            }
        } else {
            die('ERROR: No download UID given!');
        }

        // Return the generated content
        $content = $templateCode;
        return $content;
    }

    /**
     * getViewAddNewDownload( $categoryUID, $errormsg=null )
     *
     * Generates a html form where the user can propose a new download. The status of
     * the newly added download records will be set to 'Pending' . That is why they are not
     * displayed at once, but after approval by a typo3 backend user.
     *
     * @param    integer $categoryUID UID of the category the download shall be added to.
     * @param    string $form_errormsg Error message that shall be printed if set.
     * @return    string        The generated HTML source for this view.
     */
    function getViewAddNewDownload($categoryUID, $form_errormsg = null)
    {

        // Init some vars
        $content = '';
        $templateCode = null;
        $conf['subpartMarker'] = 'VIEW_ADD_NEW_DOWNLOAD';
        $subSub_captcha = 'CAPTCHA';

        // Get the html source between subpart markers from the template file
        $templateCode = $this->markerBasedTemplateService->getSubpart($this->originalTemplateCode, '###' . $conf['subpartMarker'] . '###');

        $selectedCategoryUID = intval($this->piVars['selectedCategoryUID']) ? intval($this->piVars['selectedCategoryUID']) : 0;

        $possibleFields = ['label', 'category', 'contact', 'image', 'file', 'description'];
        $mandatoryFields = $this->pi_getFFvalue($this->flexform, 'mandatoryFields', 'sDEF');

        // Get the maximum filesize for downloads
        $maxFileSize = $this->pi_getFFvalue($this->flexform, 'maxFileSize',
            'sDEF') ? $this->pi_getFFvalue($this->flexform, 'maxFileSize', 'sDEF') : $this->conf['maxFileSize'];

        /**
         * FORM
         */

        // Generate values for marker array
        $form_action = $this->pi_getPageLink($GLOBALS['TSFE']->id, '_self', [
            'tx_abdownloads_pi1[action]'       => 'getviewaddnewdownload',
            'tx_abdownloads_pi1[category_uid]' => $categoryUID,
            'no_cache'                         => '1',
        ]);
        $form_label_name = $this->prefixId . '[label]';
        $form_label_value = htmlspecialchars(strip_tags($this->piVars['label']));
        $form_description_name = $this->prefixId . '[description]';
        $form_description_value = strip_tags($this->piVars['description']);
        $form_contact_name = $this->prefixId . '[contact]';
        $form_contact_value = htmlspecialchars(strip_tags($this->piVars['contact']));
        $form_image_name = $this->prefixId . '[image]';
        $form_submit_button_name = $this->prefixId . '[submit_button]';
        $form_submit_button_value = htmlspecialchars(trim($this->pi_getLL('FORM_SUBMIT_BUTTON')));
        $form_cancel_button_name = $this->prefixId . '[cancel_button]';
        $form_cancel_button_value = htmlspecialchars(trim($this->pi_getLL('FORM_CANCEL_BUTTON')));

        // Create marker array
        $markerArray = [];
        $markerArray['###FORM_ACTION###'] = $this->local_cObj->stdWrap($form_action, '');
        $markerArray['###MAX_FILE_SIZE_RAW###'] = $maxFileSize;
        $markerArray['###MAX_FILE_SIZE###'] = $this->fileFunc->formatSize($maxFileSize) . 'Byte';
        $markerArray['###FORM_LABEL_NAME###'] = $this->local_cObj->stdWrap($form_label_name, '');
        $markerArray['###FORM_LABEL_VALUE###'] = $this->local_cObj->stdWrap($form_label_value, '');
        $markerArray['###FORM_CATEGORY###'] = $this->getCategorySelect($categoryUID, $selectedCategoryUID);
        $markerArray['###FORM_CONTACT_NAME###'] = $this->local_cObj->stdWrap($form_contact_name, '');
        $markerArray['###FORM_CONTACT_VALUE###'] = $this->local_cObj->stdWrap($form_contact_value, '');
        $markerArray['###FORM_IMAGE_NAME###'] = $this->local_cObj->stdWrap($form_image_name, '');
        $markerArray['###FORM_DESCRIPTION_NAME###'] = $this->local_cObj->stdWrap($form_description_name, '');
        $markerArray['###FORM_DESCRIPTION_VALUE###'] = $this->local_cObj->stdWrap($form_description_value, '');

        if (is_object($this->freeCap)) {
            $markerArray['###CAPTCHA_NOTICE###'] = '';
            $markerArray['###CAPTCHA_IMAGE###'] = '';

            $markerArray['###SR_FREECAP_NOTICE###'] = $this->local_cObj->stdWrap($markerArray['###SR_FREECAP_NOTICE###'],
                $this->conf['mandatoryField_stdWrap.']);
            $markerArray = array_merge($markerArray, $this->freeCap->makeCaptcha());
        } elseif ($this->captchaExtension == 'captcha' && \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('captcha')) {
            $markerArray['###SR_FREECAP_NOTICE###'] = '';
            $markerArray['###SR_FREECAP_CANT_READ###'] = '';
            $markerArray['###SR_FREECAP_IMAGE###'] = '';

            $markerArray['###CAPTCHA_NOTICE###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('captcha_notice'))),
                $this->conf['mandatoryField_stdWrap.']);
            $markerArray['###CAPTCHA_IMAGE###'] = '<img src="' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('captcha') . 'captcha/captcha.php" alt="" />';
        } else {
            $templateCode = $this->markerBasedTemplateService->substituteSubpart($templateCode, '###' . $subSub_captcha . '###', '');
        }

        $markerArray['###FORM_SUBMIT_BUTTON_NAME###'] = $this->local_cObj->stdWrap($form_submit_button_name, '');
        $markerArray['###FORM_SUBMIT_BUTTON_VALUE###'] = $this->local_cObj->stdWrap($form_submit_button_value, '');
        $markerArray['###FORM_CANCEL_BUTTON_NAME###'] = $this->local_cObj->stdWrap($form_cancel_button_name, '');
        $markerArray['###FORM_CANCEL_BUTTON_VALUE###'] = $this->local_cObj->stdWrap($form_cancel_button_value, '');
        $markerArray['###FORM_ERRORMSG###'] = $this->local_cObj->stdWrap($form_errormsg,
            $this->conf['formErrorMsg_stdWrap.']);
        $markerArray['###LINK_BACK_TO_CATEGORY###'] = '<a href="javascript:history.back()">' . htmlspecialchars(trim($this->pi_getLL('ll_back'))) . '</a>';

        /**
         * LL VALUES
         */

        $markerArray['###LL_ADD_DOWNLOAD###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_add_download'))),
            $this->conf['addDownload_stdWrap.']);
        $markerArray['###LL_ADD_DOWNLOAD_TEXT###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_add_download_text'))),
            $this->conf['addDownloadText_stdWrap.']);
        $markerArray['###LL_LABEL_EXAMPLE###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_label_example'))),
            '');
        $markerArray['###LL_CONTACT_EXAMPLE###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_contact_example'))),
            '');
        $markerArray['###LL_IMAGE_EXAMPLE###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_image_example'))),
            '');
        $markerArray['###LL_MAXIMUM###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_maximum'))),
            '');

        foreach ($possibleFields as $id => $possibleField) {
            if (GeneralUtility::inList($mandatoryFields, $possibleField)) {
                $wrap = $this->conf['mandatoryField_stdWrap.'];
            }

            $markerArray['###LL_' . strtoupper($possibleField) . '###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_' . $possibleField))),
                $wrap);
            $wrap = '';
        }

        // Finally substitute the marker array
        $templateCode = $this->markerBasedTemplateService->substituteMarkerArray($templateCode, $markerArray);

        // Return the generated content
        $content = $templateCode;
        return $content;
    }

    /**
     * getViewAddNewDownloadResult( $categoryUID = 0 )
     *
     * Creates a new download record in the database and shows a result page to the submitting user.
     *
     * @param    integer $categoryUID UID of the category the download shall be added to.
     * @return    string        The generated HTML source for this view.
     */
    function getViewAddNewDownloadResult($categoryUID = 0)
    {

        // Init some vars
        $content = null;
        $templateCode = null;
        $conf['subpartMarker'] = 'VIEW_ADD_NEW_DOWNLOAD_RESULT';

        // Get/Set other values
        $pid = $this->sysfolderList;
        $tstamp = $crdate = time();
        $cruserID = $GLOBALS['TSFE']->fe_user->user['uid'];
        // FIXME: Perhaps better use removeBadHTML()
        $label = htmlspecialchars(strip_tags($this->piVars['label']));
        $description = htmlspecialchars(strip_tags($this->piVars['description']));
        $category = $this->getCategoryPath($categoryUID, false);
        $contact = htmlspecialchars(strip_tags($this->piVars['contact']));
        $ip = GeneralUtility::getIndpEnv('REMOTE_ADDR');

        // Handle image (if present)
        $imageName = $this->fileFunc->cleanFileName($_FILES['image']['name']);
        // TODO: Make the image path configurable
        $uniqueImagePath = $this->fileFunc->getUniqueName($imageName,
            PATH_site . 'uploads/tx_abdownloads/downloadImages/');
        if ($imageName) {
            $uploadedTempFile = GeneralUtility::upload_to_tempfile($_FILES['image']['tmp_name']);
            GeneralUtility::upload_copy_move($uploadedTempFile, $uniqueImagePath);
            GeneralUtility::unlink_tempfile($uploadedTempFile);
        }

        // Get file name and path
        $fileName = $this->fileFunc->cleanFileName($_FILES['file']['name']);
        $uniqueFilePath = '';

        if ($fileName) {
            $uniqueFilePath = $this->fileFunc->getUniqueName($fileName, PATH_site . $this->filePath);
            $uploadedTempFile = GeneralUtility::upload_to_tempfile($_FILES['file']['tmp_name']);
            GeneralUtility::upload_copy_move($uploadedTempFile, $uniqueFilePath);
            GeneralUtility::unlink_tempfile($uploadedTempFile);
        }

        $allowAddDownloads = $this->pi_getFFvalue($this->flexform, 'allowAddDownloads',
            'sDEF') ? $this->pi_getFFvalue($this->flexform, 'allowAddDownloads',
            'sDEF') : $this->conf['allowAddDownloads'];
        $statusAddedDownloads = $this->pi_getFFvalue($this->flexform, 'statusAddedDownloads',
            'sDEF') ? $this->pi_getFFvalue($this->flexform, 'statusAddedDownloads',
            'sDEF') : $this->conf['statusAddedDownloads'];

        if ($cruserID != null) {
            // Do the query for a logged-in user
            $insertFields = [
                'pid'         => $pid,
                'tstamp'      => $tstamp,
                'crdate'      => $crdate,
                'cruser_id'   => $cruserID,
                'label'       => addslashes($label),
                'description' => addslashes($description),
                'click_ip'    => $ip,
                'vote_ip'     => $ip,
                'status'      => $statusAddedDownloads,
                'category'    => $categoryUID,
                'image'       => basename($uniqueImagePath),
                'file'        => basename($uniqueFilePath),
                'contact'     => $contact,
            ];
            $GLOBALS['TYPO3_DB']->exec_INSERTquery($this->tablePrefix . 'download', $insertFields);

            $insertFields = [
                'uid_local'   => $GLOBALS['TYPO3_DB']->sql_insert_id(),
                'uid_foreign' => $categoryUID,
                'sorting'     => 1,
            ];
            $GLOBALS['TYPO3_DB']->exec_INSERTquery($this->tablePrefix . 'category_mm', $insertFields);

        } elseif ($cruserID == null && $allowAddDownloads == 1) {
            // Do the query for an anonymous user
            $insertFields = [
                'pid'         => $pid,
                'tstamp'      => $tstamp,
                'crdate'      => $crdate,
                'label'       => addslashes($label),
                'description' => addslashes($description),
                'click_ip'    => $ip,
                'vote_ip'     => $ip,
                'status'      => $statusAddedDownloads,
                'category'    => $categoryUID,
                'image'       => basename($uniqueImagePath),
                'file'        => basename($uniqueFilePath),
                'contact'     => $contact,
            ];
            $GLOBALS['TYPO3_DB']->exec_INSERTquery($this->tablePrefix . 'download', $insertFields);

            $insertFields = [
                'uid_local'   => $GLOBALS['TYPO3_DB']->sql_insert_id(),
                'uid_foreign' => $categoryUID,
                'sorting'     => 1,
            ];
            $GLOBALS['TYPO3_DB']->exec_INSERTquery($this->tablePrefix . 'category_mm', $insertFields);
        }

        // Generate and send notification email to admin
        $name = $this->pi_getFFvalue($this->flexform, 'adminName',
            's_notification') ? $this->pi_getFFvalue($this->flexform, 'adminName',
            's_notification') : $this->conf['adminName'];
        $email = $this->pi_getFFvalue($this->flexform, 'adminEmail',
            's_notification') ? $this->pi_getFFvalue($this->flexform, 'adminEmail',
            's_notification') : $this->conf['adminEmail'];
        $subject = $this->pi_getFFvalue($this->flexform, 'emailSubjectAdd',
            's_notification') ? $this->pi_getFFvalue($this->flexform, 'emailSubjectAdd',
            's_notification') : $this->conf['emailSubjectAdd'];
        $ll_label = htmlspecialchars(trim($this->pi_getLL('ll_label')));
        $ll_description = htmlspecialchars(trim($this->pi_getLL('ll_description')));
        $ll_category = htmlspecialchars(trim($this->pi_getLL('ll_category')));
        $ll_contact = htmlspecialchars(trim($this->pi_getLL('ll_contact')));
        $ll_image = htmlspecialchars(trim($this->pi_getLL('ll_image')));
        $ll_file = htmlspecialchars(trim($this->pi_getLL('ll_file')));

        $headers = "From: \"$name\" <$email>\r\n";
        $headers .= "X-Mailer: Modern Downloads Info Mailer\r\n";

        $message = $ll_label . ': ' . $label . "\r\n" .
            $ll_description . ': ' . $description . "\r\n" .
            $ll_category . ': ' . $category . "\r\n" .
            $ll_contact . ': ' . $contact . "\r\n" .
            $ll_image . ': ' . basename($uniqueImagePath) . "\r\n" .
            $ll_file . ': ' . basename($uniqueFilePath);

        $encoding = $GLOBALS['TSFE']->config['config']['notification_email_encoding'] ? $GLOBALS['TSFE']->config['config']['notification_email_encoding'] : 'quoted-printable';

        GeneralUtility::plainMailEncoded($email, $subject, $message, $headers, $encoding);

        // Get the html source between subpart markers from the template file
        $templateCode = $this->markerBasedTemplateService->getSubpart($this->originalTemplateCode, '###' . $conf['subpartMarker'] . '###');

        $markerArray = [];

        $markerArray['###LINK_BACK_TO_CATEGORY###'] = '<a href="javascript:history.go(-2)">' . htmlspecialchars(trim($this->pi_getLL('ll_back'))) . '</a>';

        if ($statusAddedDownloads == 1) {
            $markerArray['###LL_LI_ADD_THX###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_appr_li_add_thx'))),
                '');
        } else {
            $markerArray['###LL_LI_ADD_THX###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_li_add_thx'))),
                '');
        }

        // Finally substitute the marker array
        $templateCode = $this->markerBasedTemplateService->substituteMarkerArray($templateCode, $markerArray);

        // Return the generated content
        $content = $templateCode;
        return $content;
    }

    /**
     * getViewReportBrokenDownload( $uid = null, $categoryUID )
     *
     * When a user wants to report a broken download, this view is displayed. The
     * user has to accept the report, then an e-mail is sent to the given admin address.
     *
     * @param    integer $uid UID of the broken download record.
     * @param    integer $categoryUID UID of the category the download belongs to.
     * @return    string        The generated HTML source for this view.
     */
    function getViewReportBrokenDownload($uid = null, $categoryUID)
    {

        // Init some vars
        $content = null;
        $templateCode = null;                         // Hold the template source code
        $conf['subpartMarker'] = 'VIEW_REPORT_BROKEN_DOWNLOAD';

        // Get the html source between subpart markers from the template file
        $templateCode = $this->markerBasedTemplateService->getSubpart($this->originalTemplateCode, '###' . $conf['subpartMarker'] . '###');

        if ($uid != null) {

            // Get download record
            $downloadResult = $this->getDownloadRecord($uid);

            // Get record overlay
            $download = $this->getRecordOverlay($downloadResult, $this->tablePrefix . 'download');

            if ($this->debug) {
                $GLOBALS['TSFE']->set_no_cache();
                t3lib_utility_Debug::debug($download);
            }

            // Generate values for marker array
            $form_action = $this->pi_getPageLink($GLOBALS['TSFE']->id, '_self', [
                'tx_abdownloads_pi1[action]'       => 'getviewreportbrokendownload',
                'tx_abdownloads_pi1[uid]'          => $uid,
                'tx_abdownloads_pi1[category_uid]' => $categoryUID,
                'no_cache'                         => '1',
            ]);
            $form_yes_button_name = $this->prefixId . '[submit_button]';
            $form_yes_button_value = htmlspecialchars(trim($this->pi_getLL('FORM_YES_BUTTON')));
            $form_no_button_name = $this->prefixId . '[cancel_button]';
            $form_no_button_value = htmlspecialchars(trim($this->pi_getLL('FORM_NO_BUTTON')));

            $markerArray = [];
            $markerArray['###FORM_ACTION###'] = $this->local_cObj->stdWrap($form_action, '');
            $markerArray['###FORM_YES_BUTTON_NAME###'] = $this->local_cObj->stdWrap($form_yes_button_name, '');
            $markerArray['###FORM_YES_BUTTON_VALUE###'] = $this->local_cObj->stdWrap($form_yes_button_value, '');
            $markerArray['###FORM_NO_BUTTON_NAME###'] = $this->local_cObj->stdWrap($form_no_button_name, '');
            $markerArray['###FORM_NO_BUTTON_VALUE###'] = $this->local_cObj->stdWrap($form_no_button_value, '');
            $markerArray['###DOWNLOAD_LABEL###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($download[0]['label'])),
                '');
            $markerArray['###LINK_BACK_TO_CATEGORY###'] = '<a href="javascript:history.back()">' . htmlspecialchars(trim($this->pi_getLL('ll_back'))) . '</a>';

            /**
             * LL VALUES
             */

            $markerArray['###LL_REPORT_DOWNLOAD_BROKEN_TEXT###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_report_download_broken_text'))),
                $this->conf['reportDownloadBrokenText_stdWrap.']);

            // Finally substitute the marker array
            $templateCode = $this->markerBasedTemplateService->substituteMarkerArray($templateCode, $markerArray);
        } else {
            die('ERROR: No download UID given!');
        }

        // Return the generated content
        $content = $templateCode;
        return $content;
    }

    /**
     * getViewReportBrokenDownloadResult( $uid = null, $categoryUID )
     *
     * Updates the status of the download to 'Reported broken' => '2' and shows a result page to the submitting user.
     *
     * @param    integer $uid UID of the broken download record.
     * @param    integer $categoryUID UID of the category the download belongs to.
     * @return    string        The generated HTML source for this view.
     */
    function getViewReportBrokenDownloadResult($uid = null, $categoryUID)
    {

        // Init some vars
        $content = null;
        $templateCode = null;                         // Hold the template source code
        $conf['subpartMarker'] = 'VIEW_REPORT_BROKEN_DOWNLOAD_RESULT';

        // Get the html source between subpart markers from the template file
        $templateCode = $this->markerBasedTemplateService->getSubpart($this->originalTemplateCode, '###' . $conf['subpartMarker'] . '###');

        if ($uid != null) {

            // Get download record
            $downloadResult = $this->getDownloadRecord($uid);

            // Get record overlay
            $download = $this->getRecordOverlay($downloadResult, $this->tablePrefix . 'download');

            if ($download[0]['status'] == 1) {

                // Update the status of the download to 'Reported broken' => '2'
                $whereClause = "uid=$uid";
                $updateFields = [
                    'status' => '2',
                ];
                $GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->tablePrefix . 'download', $whereClause, $updateFields);

                // Generate and send notification email to admin
                $name = $this->pi_getFFvalue($this->flexform, 'adminName',
                    's_notification') ? $this->pi_getFFvalue($this->flexform, 'adminName',
                    's_notification') : $this->conf['adminName'];
                $email = $this->pi_getFFvalue($this->flexform, 'adminEmail',
                    's_notification') ? $this->pi_getFFvalue($this->flexform, 'adminEmail',
                    's_notification') : $this->conf['adminEmail'];
                $subject = $this->pi_getFFvalue($this->flexform, 'emailSubjectBroken',
                    's_notification') ? $this->pi_getFFvalue($this->flexform, 'emailSubjectBroken',
                    's_notification') : $this->conf['emailSubjectBroken'];
                $ll_label = htmlspecialchars(trim($this->pi_getLL('ll_label')));
                $ll_description = htmlspecialchars(trim($this->pi_getLL('ll_description')));
                $ll_category = htmlspecialchars(trim($this->pi_getLL('ll_category')));

                $headers = "From: \"$name\" <$email>\r\n";
                $headers .= "X-Mailer: Modern Downloads Info Mailer\r\n";

                $message = $ll_label . ': ' . $download[0]['label'] . "\r\n" .
                    $ll_description . ': ' . $download[0]['description'] . "\r\n" .
                    $ll_category . ': ' . $this->getCategoryPath($download[0]['category'], false);

                $encoding = $GLOBALS['TSFE']->config['config']['notification_email_encoding'] ? $GLOBALS['TSFE']->config['config']['notification_email_encoding'] : 'quoted-printable';

                GeneralUtility::plainMailEncoded($email, $subject, $message, $headers,
                    $encoding);

                $markerArray = [];

                $markerArray['###LINK_BACK_TO_CATEGORY###'] = '<a href="javascript:history.go(-2)">' . htmlspecialchars(trim($this->pi_getLL('ll_back'))) . '</a>';
                $markerArray['###LL_LI_BROKEN_THX###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_li_broken_thx'))),
                    '');

                // Finally substitute the marker array
                $templateCode = $this->markerBasedTemplateService->substituteMarkerArray($templateCode, $markerArray);
            } else {
                die('ERROR: Wrong download UID given!');
            }
        } else {
            die('ERROR: No download UID given!');
        }

        // Return the generated content
        $content = $templateCode;
        return $content;
    }

    /**
     * getViewRateDownload( $uid = null, $categoryUID )
     *
     * When a user wants to rate a download, this view is displayed. The
     * user has to accept the report.
     *
     * @param    integer $uid UID of the download record to rate.
     * @param    integer $categoryUID UID of the category the download belongs to.
     * @return    string        The generated HTML source for this view.
     */
    function getViewRateDownload($uid = null, $categoryUID)
    {

        // Init some vars
        $content = null;
        $templateCode = null;       // Hold the template source code
        $conf['subpartMarker'] = 'VIEW_RATE_DOWNLOAD'; // Holds a subpart marker.

        // Get the html source between subpart markers from the template file
        $templateCode = $this->markerBasedTemplateService->getSubpart($this->originalTemplateCode, '###' . $conf['subpartMarker'] . '###');

        if ($uid != null) {

            // Get download record
            $downloadResult = $this->getDownloadRecord($uid);

            // Get record overlay
            $download = $this->getRecordOverlay($downloadResult, $this->tablePrefix . 'download');

            if ($this->debug) {
                $GLOBALS['TSFE']->set_no_cache();
                t3lib_utility_Debug::debug($download);
            }

            // Generate values for marker array
            $form_action = $this->pi_getPageLink($GLOBALS['TSFE']->id, '_self', [
                'tx_abdownloads_pi1[action]'       => 'getviewratedownload',
                'tx_abdownloads_pi1[uid]'          => $uid,
                'tx_abdownloads_pi1[category_uid]' => $categoryUID,
                'no_cache'                         => '1',
            ]);
            $form_submit_button_name = $this->prefixId . '[submit_button]';
            $form_submit_button_value = htmlspecialchars(trim($this->pi_getLL('FORM_SUBMIT_BUTTON')));
            $form_cancel_button_name = $this->prefixId . '[cancel_button]';
            $form_cancel_button_value = htmlspecialchars(trim($this->pi_getLL('FORM_CANCEL_BUTTON')));

            $markerArray = [];
            $markerArray['###FORM_ACTION###'] = $this->local_cObj->stdWrap($form_action, '');
            $markerArray['###FORM_SUBMIT_BUTTON_NAME###'] = $this->local_cObj->stdWrap($form_submit_button_name, '');
            $markerArray['###FORM_SUBMIT_BUTTON_VALUE###'] = $this->local_cObj->stdWrap($form_submit_button_value, '');
            $markerArray['###FORM_CANCEL_BUTTON_NAME###'] = $this->local_cObj->stdWrap($form_cancel_button_name, '');
            $markerArray['###FORM_CANCEL_BUTTON_VALUE###'] = $this->local_cObj->stdWrap($form_cancel_button_value, '');
            $markerArray['###DOWNLOAD_LABEL###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($download[0]['label'])),
                '');
            $markerArray['###DOWNLOAD_RATING###'] = round($this->local_cObj->stdWrap($download[0]['rating'], ''), 2);
            $markerArray['###DOWNLOAD_VOTES###'] = $this->local_cObj->stdWrap($download[0]['votes'], '');
            $markerArray['###LINK_BACK_TO_CATEGORY###'] = '<a href="javascript:history.back()">' . htmlspecialchars(trim($this->pi_getLL('ll_back'))) . '</a>';

            /**
             * LL VALUES
             */

            $markerArray['###LL_RATE_DOWNLOAD_TEXT###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_rate_download_text'))),
                $this->conf['rateDownloadText_stdWrap.']);
            $markerArray['###LL_VERY_BAD###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_very_bad'))),
                '');
            $markerArray['###LL_VERY_GOOD###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_very_good'))),
                '');
            $markerArray['###LL_RATING_CURRENT###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_rating_current'))),
                '');
            $markerArray['###LL_VOTES###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_votes'))),
                '');

            // Finally substitute the marker array
            $templateCode = $this->markerBasedTemplateService->substituteMarkerArray($templateCode, $markerArray);
        } else {
            die('ERROR: No download UID given!');
        }

        // Return the generated content
        $content = $templateCode;
        return $content;
    }

    /**
     * getViewRateDownloadResult( $uid = null, $categoryUID )
     *
     * Rates the download and shows a result page to the submitting user.
     *
     * @param    integer $uid UID of the download record to rate.
     * @param    integer $categoryUID UID of the category the download belongs to.
     * @return    string        The generated HTML source for this view.
     */
    function getViewRateDownloadResult($uid = null, $categoryUID)
    {
        // Init some vars
        $content = null;
        $templateCode = null;       // Hold the template source code
        $conf['subpartMarker'] = 'VIEW_RATE_DOWNLOAD_RESULT'; // Holds a subpart marker.
        $subSub_download = 'DOWNLOAD';

        // Get the html source between subpart markers from the template file
        $templateCode = $this->markerBasedTemplateService->getSubpart($this->originalTemplateCode, '###' . $conf['subpartMarker'] . '###');

        if ($uid != null) {

            // Get download record
            $downloadResult = $this->getDownloadRecord($uid);

            // Get record overlay
            $download = $this->getRecordOverlay($downloadResult, $this->tablePrefix . 'download');

            // Get user's IP address
            $ip = GeneralUtility::getIndpEnv('REMOTE_ADDR');

            // Check for multiple-rating
            if (!GeneralUtility::cmpIP($ip, $download[0]['vote_ip'])) {

                // Get old rating and votes of the download
                $oldRating = $download[0]['rating'];
                $oldVotes = $download[0]['votes'];

                // Get submitted rating
                $submittedRating = intval($this->piVars['rating']);

                if ($submittedRating > 0) {
                    $newVotes = $oldVotes + 1;
                    $newRating = ($oldRating * $oldVotes + $submittedRating) / $newVotes;

                    // Update rating, votes and vote_ip of the download
                    $whereClause = "uid=$uid";
                    $updateFields = [
                        'rating'  => $newRating,
                        'votes'   => $newVotes,
                        'vote_ip' => $ip,
                    ];
                    $GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->tablePrefix . 'download', $whereClause,
                        $updateFields);
                }
            }

            $markerArray = [];

            $markerArray['###LINK_BACK_TO_CATEGORY###'] = '<a href="javascript:history.go(-2)">' . htmlspecialchars(trim($this->pi_getLL('ll_back'))) . '</a>';
            $markerArray['###LL_LI_RATE_THX###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_li_rate_thx'))),
                '');

            // Finally substitute the marker array
            $templateCode = $this->markerBasedTemplateService->substituteMarkerArray($templateCode, $markerArray);
        } else {
            die('ERROR: No download UID given!');
        }

        // Return the generated content
        $content = $templateCode;
        return $content;
    }

    /*************************************
     *
     * Helper functions
     *
     *************************************/

    /**
     * makePageBrowser( $pointerName = 'pointer' )
     *
     * This is a copy of the function pi_list_browseresults from class.tslib_piBase.php
     * Returns a results browser. This means a bar of page numbers plus a "previous" and "next" link. For each entry in the bar the piVars "$pointerName" will be pointing to the "result page" to show.
     * Using $this->piVars['$pointerName'] as pointer to the page to display
     * Using $this->internal['res_count'], $this->internal['results_at_a_time'] and $this->internal['maxPages'] for count number, how many results to show and the max number of pages to include in the browse bar.
     *
     * @param    string    varname for the pointer
     * @return    string    Output HTML, wrapped in <div>-tags with a class attribute
     */
    function makePageBrowser($pointerName = 'pointer')
    {

        // Initializing variables
        $showResultCount = $this->conf['pageBrowser.']['showResultCount'];
        $tableParams = $this->conf['pageBrowser.']['tableParams'];
        $pointer = intval($this->piVars[$pointerName]);
        $count = $this->internal['res_count'];
        $results_at_a_time = t3lib_utility_Math::forceIntegerInRange($this->internal['results_at_a_time'], 1, 1000);
        $maxPages = t3lib_utility_Math::forceIntegerInRange($this->internal['maxPages'], 1, 100);
        $max = t3lib_utility_Math::forceIntegerInRange(ceil($count / $results_at_a_time), 1, $maxPages);
        $action = $this->internal['action'];
        $categoryUID = $this->internal['category_uid'];
        $links = [];

        // Make browse-table/links:
        if ($this->pi_alwaysPrev >= 0) {
            if ($pointer > 0) {
                $links[] = '<td nowrap="nowrap"><p>' . $this->pi_linkTP_keepPIvars(htmlspecialchars(trim($this->pi_getLL('pi_list_browseresults_prev',
                        '< Previous'))), [
                        'tx_abdownloads_pi1[action]'       => $action,
                        'tx_abdownloads_pi1[category_uid]' => $categoryUID,
                        $pointerName                       => ($pointer - 1 ? $pointer - 1 : ''),
                    ], $this->allowCaching) . '</p></td>';
            } elseif ($this->pi_alwaysPrev) {
                $links[] = '<td nowrap="nowrap"><p>' . htmlspecialchars(trim($this->pi_getLL('pi_list_browseresults_prev',
                        '< Previous'))) . '</p></td>';
            }
        }

        for ($a = 0; $a < $max; $a++) {
            $links[] = '<td' . ($pointer == $a ? $this->pi_classParam('browsebox-SCell') : '') . ' nowrap="nowrap"><p>' . $this->pi_linkTP_keepPIvars(trim(htmlspecialchars(trim($this->pi_getLL('pi_list_browseresults_page',
                        'Page'))) . ' ' . ($a + 1)), [
                    'tx_abdownloads_pi1[action]'       => $action,
                    'tx_abdownloads_pi1[category_uid]' => $categoryUID,
                    $pointerName                       => ($a ? $a : ''),
                ], $this->allowCaching) . '</p></td>';
        }

        if ($pointer < ceil($count / $results_at_a_time) - 1) {
            $links[] = '<td nowrap="nowrap"><p>' . $this->pi_linkTP_keepPIvars(htmlspecialchars(trim($this->pi_getLL('pi_list_browseresults_next',
                    'Next >'))), [
                    'tx_abdownloads_pi1[action]'       => $action,
                    'tx_abdownloads_pi1[category_uid]' => $categoryUID,
                    $pointerName                       => $pointer + 1,
                ], $this->allowCaching) . '</p></td>';
        }

        $pR1 = $pointer * $results_at_a_time + 1;
        $pR2 = $pointer * $results_at_a_time + $results_at_a_time;
        $sTables = '
			<!--
			 List browsing box:
			-->
			<div' . $this->pi_classParam('browsebox') . '>' . ($showResultCount ? '<p>' . ($this->internal['res_count'] ?
                    sprintf(str_replace('###SPAN_BEGIN###', '<span' . $this->pi_classParam('browsebox-strong') . '>',
                        htmlspecialchars(trim($this->pi_getLL('pi_list_browseresults_displays',
                            'Displaying results ###SPAN_BEGIN###%s to %s</span> out of ###SPAN_BEGIN###%s</span>')))),
                        $this->internal['res_count'] > 0 ? $pR1 : 0, min([$this->internal['res_count'], $pR2]),
                        $this->internal['res_count']) :
                    htmlspecialchars(trim($this->pi_getLL('pi_list_browseresults_noResults',
                        'Sorry, no items were found. ')))) . '</p>' : '') . '
				<' . trim('table ' . $tableParams) . '><tr>' . implode('', $links) . '</tr></table></div>';

        return $sTables;
    }

    /**
     * userProcess( $configKey, $variable )
     *
     * Calls a user function defined with TypoScript.
     *
     * @param    string $configKey If empty $variable is not processed.
     * @param    mixed $variable This variable is processed in the user function.
     * @return    mixed        The processed $variable
     */
    function userProcess($configKey, $variable)
    {

        if ($this->conf[$configKey]) {
            $functionConfig = $this->conf[$configKey . '.'];
            $functionConfig['parentObj'] = &$this;
            $variable = $GLOBALS['TSFE']->cObj->callUserFunction($this->conf[$configKey], $functionConfig, $variable);
        }

        return $variable;
    }

    /**
     * initPidList()
     *
     * Extends the sysfolderList given from $conf or FF recursively by the PIDs of the subpages.
     *
     * @return    void
     */
    function initPidList()
    {

        // Create sysfolderList with page IDs from where to fetch the category/download records
        $pidList = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'pages', 'sDEF');
        $pidList = $pidList ? $pidList : trim($this->cObj->stdWrap($this->conf['sysfolderList'],
            $this->conf['sysfolderList.']));
        $pidList = $pidList ? implode(',',
            GeneralUtility::intExplode(',', $pidList)) : $GLOBALS['TSFE']->id;

        // Set recursive setting
        $recursive = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'recursive', 'sDEF');
        $recursive = is_numeric($recursive) ? $recursive : $this->cObj->stdWrap($this->conf['recursive'],
            $this->conf['recursive.']);

        // Extend the sysfolderList by recursive levels
        $this->sysfolderList = $this->pi_getPidList($pidList, $recursive);
        $this->sysfolderList = $this->sysfolderList ? $this->sysfolderList : 0;
    }

    /**
     * recursiveDownloadCount( $categoryUID )
     *
     * Counts the number of downloads in the current category and recursively in all subcategories.
     *
     * @param    integer $categoryUID The UID of the category.
     * @return    integer        The number of downloads in the current category and in all subcategories.
     */
    function recursiveDownloadCount($categoryUID)
    {

        // Prepare category UID list
        $categoryUIDs = $this->recursiveCategoryGet($categoryUID);
        $categoryUIDsImploded = implode(',', $categoryUIDs);

        // Finally, append current category UID
        $categoryUIDsImploded .= ',' . $categoryUID;

        $databaseTable = $this->tablePrefix . 'download';
        $relationTable = $this->tablePrefix . 'category_mm';
        $foreignTable = $this->tablePrefix . 'category';
        $theField = $foreignTable . '.uid';
        $theValue = $categoryUID;
        $whereClause = ' AND ' . $theField . ' IN (' . $categoryUIDsImploded . ')' . $this->enableFields;
        $groupBy = '';
        $orderBy = '';
        $limit = '';
        $downloadsInCurrentCategoryResults = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query($databaseTable . '.*',
            $databaseTable, $relationTable, $foreignTable, $whereClause, $groupBy, $orderBy, $limit);

        if ($this->debugDB) {
            $GLOBALS['TSFE']->set_no_cache();
            t3lib_utility_Debug::debug($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
        }

        // Get record overlay
        $downloadsInCurrentCategory = $this->getRecordOverlay($downloadsInCurrentCategoryResults, $databaseTable);
        $count = 0;

        if (is_array($downloadsInCurrentCategory)) {
            $count = count($downloadsInCurrentCategory);
        }

        return $count;
    }

    /**
     * recursiveCategoryGet( $categoryUID )
     *
     * Adds the UID of the current category to an array and calls itself recursively to add
     * the UIDs of all subcategories.
     *
     * @param    integer $categoryUID The UID of the category.
     * @return    array        An array with the UID of the current category and of all categories below the current level.
     */
    function recursiveCategoryGet($categoryUID)
    {

        // Get subcategories
        $categoriesResults = $this->getCategoryRecords('parent_category', $categoryUID);

        // Get record overlay
        $categories = $this->getRecordOverlay($categoriesResults, $this->tablePrefix . 'category');
        $array = [];

        if (is_array($categories) && count($categories) > 0) {
            // Do the recursion for all subcategories
            for ($i = 0; $i < count($categories); $i++) {
                $array = array_merge($array, $this->recursiveCategoryGet($categories[$i]['uid']));
            }

            // Return array with the current category UID and the UIDs of all subcategories
            $array = array_merge($array, [$categoryUID]);
            return $array;
        } else {
            // No subcategories below the current level => return array with the UID of the current category
            return [$categoryUID];
        }
    }

    /**
     * getCategoryPath( $categoryUID, $showLinks = true )
     *
     * Generates a path menu with the correspondig category labels. This enables
     * the user to reach all levels in the category tree with one click.
     *
     * @param    integer $categoryUID The UID of the category the user clicked on.
     * @param    boolean $showLinks If set (default) links are created, only labels otherwise.
     * @return    string        The HTML source for the path menu.
     */
    function getCategoryPath($categoryUID, $showLinks = true)
    {

        // Init some vars
        $content = null;
        $parent_category = null;

        if ($categoryUID != '0') {
            do {
                $res = null;
                $categoryLabel = null;

                // Get category record(s)
                $categoryResult = $this->getCategoryRecords('uid', $categoryUID);

                // Get record overlay
                $category = $this->getRecordOverlay($categoryResult, $this->tablePrefix . 'category');

                // Append category link to content
                $categoryLabel = htmlspecialchars(trim($category[0]['label']));
                $pageID = intval($this->pi_getFFvalue($this->flexform, 'pageListPlugin',
                    'sDEF') ? $this->pi_getFFvalue($this->flexform, 'pageListPlugin', 'sDEF') : $GLOBALS['TSFE']->id);

                if ($showLinks) {
                    if ($this->downloadMode == 'TREE') {
                        $conf = [
                            'useCacheHash'     => $this->pi_USER_INT_obj ? 0 : $this->allowCaching,
                            'no_cache'         => $this->pi_USER_INT_obj ? 0 : !$this->allowCaching,
                            'parameter'        => $pageID,
                            'section'          => 'cat_' . $categoryUID,
                            // FIXME: Repair empty GET parameter or remove additionalParams at all if possible
                            'additionalParams' => GeneralUtility::implodeArrayForUrl('',
                                ['tx_abdownloads_pi1[category_uid]' => ''], '', 0),
                        ];
                        $link = $this->local_cObj->typoLink($categoryLabel, $conf);

                        $content = ' ' . $link . $content;
                    } else {
                        $content = ' ' . $this->pi_LinkTP($categoryLabel, [
                                'tx_abdownloads_pi1[category_uid]' => $categoryUID,
                                'tx_abdownloads_pi1[cid]'          => $this->cObj->data['uid'],
                            ], $this->allowCaching, $pageID) . $content;
                    }
                } else {
                    $content = ' ' . $categoryLabel . $content;
                }

                // Set categoryUID to parent category to continue one level up
                $categoryUID = $category[0]['parent_category'];

                // Add a '>>' between category links
                if ($showLinks) {
                    $content = ' &raquo;' . $content;
                } else {
                    $content = ' >' . $content;
                }

            } while ($categoryUID != null && $categoryUID != '0');

            // Finally add the link to the root
            if ($showLinks) {
                $content = $this->pi_LinkTP(htmlspecialchars(trim($this->pi_getLL('ll_home'))),
                        ['tx_abdownloads_pi1[cid]' => $this->cObj->data['uid']], $this->allowCaching,
                        $pageID) . $content;
            } else {
                $content = htmlspecialchars(trim($this->pi_getLL('ll_home'))) . $content;
            }
        } else {
            return "";
        }

        // Return the generated content
        return $content;
    }

    /**
     * getRecordOverlay( &$resultSet, $databaseTable )
     *
     * Returns the record overlay for categories/downloads.
     *
     * @param    object $resultSet The original SQL result set.
     * @param    string $databaseTable The database table to fetch overlay records from.
     * @return    array        The overlaid SQL result set.
     */
    function getRecordOverlay(&$resultSet, $databaseTable)
    {

        if ($resultSet != null && $databaseTable != null) {

            while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resultSet)) {
                // get the translated record if the content language is not the default language
                if ($GLOBALS['TSFE']->sys_language_content) {
                    $OLmode = ($this->sys_language_mode == 'strict' ? 'hideNonTranslated' : '');
                    $row = $GLOBALS['TSFE']->sys_page->getRecordOverlay($databaseTable, $row,
                        $GLOBALS['TSFE']->sys_language_content, $OLmode);
                }

                if ($this->versioningEnabled) {
                    // Get workspaces overlay
                    $GLOBALS['TSFE']->sys_page->versionOL($databaseTable, $row);
                    // Fix pid for record from workspace
                    $GLOBALS['TSFE']->sys_page->fixVersioningPid($databaseTable, $row);
                }

                if (is_array($row)) {
                    $overlaidSet[] = $row;
                }
            }

            return $overlaidSet;
        } else {
            return [];
        }
    }

    /**
     * getCategorySelect( $categoryUID, $selectedID = 0 )
     *
     * Generates a nested category select form field with category labels.
     *
     * @param    integer $categoryUID Start category ID from which the select is generated.
     * @param    integer $selectedID ID from preselected entry.
     * @return    string        The HTML source for the nested category select.
     */
    function getCategorySelect($categoryUID, $selectedID = 0)
    {

        // Get category label
        if ($categoryUID == 0) {
            $category[0]['label'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_cats'))), '');
        } else {
            // Get category record(s)
            $categoryResult = $this->getCategoryRecords('uid', $categoryUID);

            // Get record overlay
            $category = $this->getRecordOverlay($categoryResult, $this->tablePrefix . 'category');
        }

        $content .= '<select name="tx_abdownloads_pi1[selectedCategoryUID]">';
        if ($categoryUID != '0') {
            $content .= '<option value="' . $category[0]['uid'] . '">' . htmlspecialchars(trim($category[0]['label'])) . '</option>';
        } else {
            $content .= '<option value="' . $categoryUID . '">' . $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_cats'))),
                    '') . '</option>';
        }
        $content .= '<optgroup label="' . htmlspecialchars(trim($category[0]['label'])) . '">';

        // Get subcategories
        $categoriesResults = $this->getCategoryRecords('parent_category', $categoryUID, 'label ASC');

        // Get record overlay
        $categories = $this->getRecordOverlay($categoriesResults, $this->tablePrefix . 'category');

        if (count($categories) > 0) {
            for ($i = 0; $i < count($categories); $i++) {
                if ($selectedID == $categories[$i]['uid']) {
                    $content .= '<option value="' . $categories[$i]['uid'] . '" selected="selected">' . htmlspecialchars(trim($categories[$i]['label'])) . '</option>';
                } else {
                    $content .= '<option value="' . $categories[$i]['uid'] . '">' . htmlspecialchars(trim($categories[$i]['label'])) . '</option>';
                }
            }
        }

        $content .= '</optgroup></select>';

        return $content;
    }

    /**
     * getDownloadRecord( $downloadUID )
     *
     * Returns the download record with the given downloadUID.
     *
     * @param    integer $downloadUID The UID of the record to fetch.
     * @return    string        The download record with the given downloadUID.
     */
    function getDownloadRecord($downloadUID)
    {

        // Query database for download
        $databaseTable = $this->tablePrefix . 'download';
        $theField = 'uid';
        $theValue = $downloadUID;
        $whereClause = $this->enableFields;
        $groupBy = '';
        $orderBy = '';
        $limit = '';
        $downloadResult = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $databaseTable,
            $theField . '=' . $GLOBALS['TYPO3_DB']->quoteStr($theValue, $databaseTable) . ' ' . $whereClause, $groupBy,
            $orderBy, $limit);

        if ($this->debugDB) {
            $GLOBALS['TSFE']->set_no_cache();
            t3lib_utility_Debug::debug($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
        }

        return $downloadResult;
    }

    /**
     * getCategoryRecords( $field, $categoryUID, $orderBy, $limit )
     *
     * Returns the category record(s) where the given $field equals categoryUID.
     *
     * @param    string $field The field to consider.
     * @param    integer $categoryUID The category UID to consider.
     * @param    string $orderBy Optional ORDER BY field(s).
     * @param    string $limit Optional LIMIT value ([begin,]max).
     * @return    string        The category record(s) where the given $field equals categoryUID ordered by $orderBy and limited to $limit results.
     */
    function getCategoryRecords($field, $categoryUID, $orderBy = '', $limit = '')
    {

        // Query database for category
        $databaseTable = $this->tablePrefix . 'category';
        $theValue = $categoryUID;
        $whereClause = $this->enableFieldsCategory;
        $groupBy = '';
        $categoryResult = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $databaseTable,
            $field . '=' . $GLOBALS['TYPO3_DB']->quoteStr($theValue, $databaseTable) . ' ' . $whereClause, $groupBy,
            $orderBy, $limit);

        if ($this->debugDB) {
            $GLOBALS['TSFE']->set_no_cache();
            t3lib_utility_Debug::debug($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
        }

        return $categoryResult;
    }

    /**
     * getCategoryUID( $recordUID )
     *
     * Returns the categoryUID for the record with the given recordUID.
     *
     * @param    integer $recordUID The recordUID to consider.
     * @return    integer        The categoryUID for the record with the given recordUID.
     */
    function getCategoryUID($recordUID)
    {

        // Query database for categories
        $databaseTable = $this->tablePrefix . 'download';
        $relationTable = $this->tablePrefix . 'category_mm';
        $foreignTable = $this->tablePrefix . 'category';
        $theField = $databaseTable . '.uid';
        $theValue = $recordUID;
        $whereClause = $this->enableFields;
        $groupBy = '';
        $orderBy = 'sorting ASC';
        $limit = '';
        $categoriesResult = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query($foreignTable . '.*', $databaseTable,
            $relationTable, $foreignTable, ' AND ' . $theField . '=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($theValue,
                $databaseTable) . ' ' . $whereClause, $groupBy, $orderBy, $limit);

        if ($this->debugDB) {
            $GLOBALS['TSFE']->set_no_cache();
            t3lib_utility_Debug::debug($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
        }

        $categories = $this->getRecordOverlay($categoriesResult, $this->tablePrefix . 'category');

        return $categories[0]['uid'] ? $categories[0]['uid'] : 0;
    }

    /**
     * displayFEHelp( $type )
     *
     * Returns a help message wich will be displayed on the website.
     *
     * @param    string $type The type of the help message.
     * @return    string        HTML code for the help message.
     */
    function displayFEHelp($type)
    {

        switch ((string)$type) {
            case 'noTopListSet':
                return "Please set at least one TOP list to display in the backend.";
                break;
        }
    }

    /**
     * containsBlacklistedWords( $text )
     *
     * Checks if the given text contains any of the blacklisted words.
     *
     * @param    string $text The text to check.
     * @return    boolean        TRUE if text contains blacklisted words, FALSE otherwise
     */
    function containsBlacklistedWords($text)
    {

        $blacklist = $this->pi_getFFvalue($this->flexform, 'blacklist', 'sDEF');

        if ($blacklist) {
            $blacklistArray = GeneralUtility::trimExplode(',', $blacklist);

            foreach ($blacklistArray as $index => $word) {
                if (stristr($text, $word)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * checkInputFields( $categoryUID )
     *
     * Checks the input fields.
     *
     * @param    integer $categoryUID The UID to consider.
     * @return    boolean        TRUE if the input is valid, FALSE otherwise
     */
    function checkInputFields($categoryUID)
    {

        $mandatoryFields = $this->pi_getFFvalue($this->flexform, 'mandatoryFields', 'sDEF');
        $errormsg = '';

        if ($mandatoryFields) {
            $mandatoryFieldsExploded = GeneralUtility::trimExplode(',', $mandatoryFields);
        }

        if ($this->debug) {
            $GLOBALS['TSFE']->set_no_cache();
            t3lib_utility_Debug::printArray($mandatoryFieldsExploded);
        }

        if (is_array($mandatoryFieldsExploded)) {
            foreach ($mandatoryFieldsExploded as $id => $mandatoryField) {
                if ($mandatoryField == 'image' || $mandatoryField == 'file') {
                    $value = $_FILES[$mandatoryField]['name'];
                } elseif ($mandatoryField == 'category') {
                    $value = htmlspecialchars(strip_tags($this->piVars['selectedCategoryUID']));
                } else {
                    $value = htmlspecialchars(strip_tags($this->piVars[$mandatoryField]));
                }

                if (!$value) {
                    $errormsg = htmlspecialchars(trim($this->pi_getLL('error_required_not_filled')));
                } else {
                    if ($mandatoryField == 'label' || $mandatoryField == 'description') {
                        if ($this->containsBlacklistedWords($value)) {
                            $errormsg = htmlspecialchars(trim($this->pi_getLL('error_blacklisted_words')));
                        }
                    }

                    if ($mandatoryField == 'contact') {
                        if (!GeneralUtility::validEmail($value)) {
                            $errormsg = htmlspecialchars(trim($this->pi_getLL('error_invalid_contact')));
                        }
                    }
                }
            }
        }

        if (is_object($this->freeCap)) {
            $value = htmlspecialchars(strip_tags($this->piVars['captchaResponse']));

            if (!$this->freeCap->checkWord($value)) {
                $errormsg = htmlspecialchars(trim($this->pi_getLL('error_invalid_captcha')));
            }
        } elseif ($this->captchaExtension == 'captcha' && \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('captcha')) {
            $value = htmlspecialchars(strip_tags($this->piVars['captchaResponse']));

            session_start();
            if (isset($_SESSION['tx_captcha_string'])) {
                $captchaStr = $_SESSION['tx_captcha_string'];
                $_SESSION['tx_captcha_string'] = '';

                if (!$captchaStr || $captchaStr != $value) {
                    $errormsg = htmlspecialchars(trim($this->pi_getLL('error_invalid_captcha')));
                }
            }
        }

        $value = htmlspecialchars(strip_tags($this->piVars['selectedCategoryUID']));
        $selectedCategoryUID = $value ? $value : $categoryUID;

        if ($errormsg) {
            $content = $this->getViewAddNewDownload($categoryUID, $errormsg);
        } else {
            $content = $this->getViewAddNewDownloadResult($selectedCategoryUID);
        }

        return $content;
    }

    /**
     * getImageLink( $record, $field, $type, $view, $detailedView = false )
     *
     * Returns the image link of a database record.
     *
     * @param    array $record The database record to consider.
     * @param    string $field The database field to consider.
     * @param    string $type The type of link to create.
     * @param    string $view The view to consider.
     * @param    integer $categoryUID The category UID.
     * @param    boolean $detailedView If the link shall be created for the detailed download view.
     * @return    string        The image link of the record.
     */
    function getImageLink($record, $field, $type, $view, $categoryUID, $detailedView = false)
    {

        // Get local config
        $localConf = $this->conf[$view . 'View.'];

        // Save original ATagParams
        $originalATagParams = $GLOBALS['TSFE']->ATagParams;

        switch ((string)$type) {
            case 'link':
                if ($record[$field]) {
                    // Get flexform config for displaying of image and create config
                    if ($detailedView) {
                        $imageMaxHeight = intval($this->pi_getFFvalue($this->flexform, 'imageMaxHeightDetailed',
                            's_image') ? $this->pi_getFFvalue($this->flexform, 'imageMaxHeightDetailed',
                            's_image') : $this->conf['imageMaxHeightDetailed']);
                        $imageMaxWidth = intval($this->pi_getFFvalue($this->flexform, 'imageMaxWidthDetailed',
                            's_image') ? $this->pi_getFFvalue($this->flexform, 'imageMaxWidthDetailed',
                            's_image') : $this->conf['imageMaxWidthDetailed']);
                    } else {
                        $imageMaxHeight = intval($this->pi_getFFvalue($this->flexform, 'imageMaxHeightCategory',
                            's_image') ? $this->pi_getFFvalue($this->flexform, 'imageMaxHeightCategory',
                            's_image') : $this->conf['imageMaxHeightCategory']);
                        $imageMaxWidth = intval($this->pi_getFFvalue($this->flexform, 'imageMaxWidthCategory',
                            's_image') ? $this->pi_getFFvalue($this->flexform, 'imageMaxWidthCategory',
                            's_image') : $this->conf['imageMaxWidthCategory']);
                    }

                    $pictureConfig = [];
                    $pictureConfig['image.']['file'] = 'uploads/tx_abdownloads/downloadImages/' . $record[$field];
                    $pictureConfig['image.']['file.']['maxW'] = intval($imageMaxWidth);
                    $pictureConfig['image.']['file.']['maxH'] = intval($imageMaxHeight);
                    $pictureConfig['image.']['altText'] = htmlspecialchars(trim($record['label']));
                    $pictureConfig['image.']['titleText'] = htmlspecialchars(trim($record['label']));

                    $GLOBALS['TSFE']->ATagParams = $GLOBALS['TSFE']->ATagParams . ($localConf['downloadImage.']['ATagParams'] ? ' ' . $localConf['downloadImage.']['ATagParams'] : '');
                    $linkImages = $this->pi_getFFvalue($this->flexform, 'linkImages', 's_display');
                    if ($linkImages != 'none') {
                        if ($linkImages == 'direct') {
                            $linkImage = $this->pi_LinkTP($this->local_cObj->cObjGetSingle('IMAGE',
                                $pictureConfig['image.']), [
                                'tx_abdownloads_pi1[action]' => 'getviewclickeddownload',
                                'tx_abdownloads_pi1[uid]'    => $record['uid'],
                                'tx_abdownloads_pi1[cid]'    => $this->cObj->data['uid'],
                            ], 0);
                        } elseif ($linkImages == 'details' && !$detailedView) {
                            $linkImage = $this->pi_LinkTP($this->local_cObj->cObjGetSingle('IMAGE',
                                $pictureConfig['image.']), [
                                'tx_abdownloads_pi1[action]'       => 'getviewdetailsfordownload',
                                'tx_abdownloads_pi1[uid]'          => $record['uid'],
                                'tx_abdownloads_pi1[category_uid]' => $categoryUID,
                                'tx_abdownloads_pi1[cid]'          => $this->cObj->data['uid'],
                            ], $this->allowCaching);
                        } elseif ($linkImages == 'target') {
                            $linkImage = '<a href="' . $this->filePath . $record['file'] . '">' . $this->local_cObj->cObjGetSingle('IMAGE', $pictureConfig['image.']) . '</a>';
                        } else {
                            $pictureConfig['image.'] = array_merge($pictureConfig['image.'],
                                $this->conf['downloadImage.']);
                            $linkImage = $this->local_cObj->cObjGetSingle('IMAGE', $pictureConfig['image.']);
                        }

                        $GLOBALS['TSFE']->ATagParams = $originalATagParams;
                        return $linkImage;
                    } else {
                        $GLOBALS['TSFE']->ATagParams = $originalATagParams;
                        $pictureConfig['image.'] = array_merge($pictureConfig['image.'], $localConf['downloadImage.']);
                        return $this->local_cObj->cObjGetSingle('IMAGE', $pictureConfig['image.']);
                    }
                } else {
                    return $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('no_image_message'))), '');
                }
                break;

            case 'tree':
            case 'category':
                if ($record[$field]) {
                    // Get flexform config for displaying of image and create config
                    $imageMaxHeight = intval($this->pi_getFFvalue($this->flexform, 'categoryImageMaxHeight',
                        's_image') ? $this->pi_getFFvalue($this->flexform, 'categoryImageMaxHeight',
                        's_image') : $this->conf['categoryImageMaxHeight']);
                    $imageMaxWidth = intval($this->pi_getFFvalue($this->flexform, 'categoryImageMaxWidth',
                        's_image') ? $this->pi_getFFvalue($this->flexform, 'categoryImageMaxWidth',
                        's_image') : $this->conf['categoryImageMaxWidth']);

                    $pictureConfig = [];
                    $pictureConfig['image.']['file'] = 'uploads/tx_abdownloads/categoryImages/' . $record[$field];
                    $pictureConfig['image.']['file.']['maxW'] = intval($imageMaxWidth);
                    $pictureConfig['image.']['file.']['maxH'] = intval($imageMaxHeight);
                    $pictureConfig['image.']['altText'] = htmlspecialchars(trim($record['label']));
                    $pictureConfig['image.']['titleText'] = htmlspecialchars(trim($record['label']));

                    return $this->local_cObj->cObjGetSingle('IMAGE', $pictureConfig['image.']);
                } else {
                    $tsfe = $GLOBALS['TSFE'];
                    $incFile = $tsfe->tmpl->getFileName($this->conf['iconCategory']);
                    if ($incFile && file_exists($incFile)) {
                        $fileInfo = GeneralUtility::split_fileref($incFile);
                        $extension = $fileInfo['fileext'];
                        if ($extension === 'jpg' || $extension === 'jpeg' || $extension === 'gif' || $extension === 'png') {
                            $imgFile = $incFile;
                            $addParams = 'alt="' . htmlspecialchars(trim($record['label'])) . '" title="' . htmlspecialchars(trim($record['label'])) . '"';
                            $imgInfo = @getimagesize($imgFile);
                            return '<img src="' . htmlspecialchars($tsfe->absRefPrefix . $imgFile) . '" width="' . (int)$imgInfo[0] . '" height="' . (int)$imgInfo[1] . '"' .  ' ' . $addParams . ' />';
                        }
                    }
                }
                break;
        }
    }

    /**
     * getFileIcon( $record )
     *
     * Returns the MIME icon for a database record.
     *
     * @param    array $record The database record to consider.
     * @return    string        The MIME icon for the record.
     */
    function getFileIcon($record)
    {

        $file = GeneralUtility::getFileAbsFileName($this->filePath . $record['file']);
        $fileInformation = $this->getTotalFileInfo($file);

        if (file_exists(GeneralUtility::getFileAbsFileName('typo3/sysext/core/Resources/Public/Icons/T3Icons/mimetypes/mimetypes-' . $fileInformation['mimetype'] . '.svg'))) {
            $fileIcon = '<img src="' . GeneralUtility::getIndpEnv(TYPO3_URL_GENERAL) . 'typo3/sysext/core/Resources/Public/Icons/T3Icons/mimetypes/mimetypes-' . $fileInformation['mimetype'] . '.svg" width="18" height="16" border="0" title="' . htmlspecialchars($record['file']) . '" alt="" />';
        } else {
            $fileIcon = '<img src="' . GeneralUtility::getIndpEnv(TYPO3_URL_GENERAL) . 'typo3/sysext/core/Resources/Public/Icons/T3Icons/mimetypes/mimetypes-other-other.svg" width="18" height="16" border="0" title="' . htmlspecialchars($record['file']) . '" alt="" />';
        }

        return $fileIcon;
    }

    /**
     * getLanguage( $language_uid )
     *
     * Returns the language label for the given language_uid.
     *
     * @param    array $language_uid The UID of the language record to consider.
     * @return    string        The language label for the given language_uid.
     */
    function getLanguage($language_uid)
    {

        if ($language_uid != '0') {
            $databaseTable = 'static_languages';
            $whereClause = 'uid=' . $language_uid;
            $groupBy = '';
            $orderBy = '';
            $limit = '';
            $languageResult = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $databaseTable, $whereClause, $groupBy,
                $orderBy, $limit);

            if ($this->debugDB) {
                $GLOBALS['TSFE']->set_no_cache();
                t3lib_utility_Debug::debug($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
            }

            $language = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($languageResult);

            return $language['lg_name_en'];
        }

        return '';
    }

    /**
     * returnStarsForRating( $rating )
     *
     * Returns an image representation of a download rating.
     *
     * @param    integer $rating The rating to represent as an image.
     * @return    string        The image representation.
     */
    function returnStarsForRating($rating)
    {

        $string = null;
        $count = 0;

        $iconText = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_rating'))),
                '') . ': ' . round($rating, 2);
        if ($rating) {
            while ($rating >= 2) {
                $rating -= 2;
                $count += 1;
                $string .= $this->getImageTag($this->conf['iconStar'], $iconText, $iconText);
            }

            if ($rating >= 1) {
                $count += 1;
                $string .= $this->getImageTag($this->conf['iconHalfStar'], $iconText, $iconText);
            }

            while ($count < 5) {
                $count += 1;
                $string .= $this->getImageTag($this->conf['iconDisabledStar'], $iconText, $iconText);
            }

            return $string;
        }

        for ($i = 1; $i <= 5; $i++) {
            $string .= $this->getImageTag($this->conf['iconDisabledStar'], $iconText, $iconText);
        }

        return $string;
    }

    /**
     * fillMarkerArray( &$array, $record, $localConf, $categoryUID, $pageID = '' )
     *
     * Fills a marker array.
     *
     * @param    array $array The array to fill.
     * @param    array $record The database record to consider.
     * @param    array $localConf The local config array.
     * @param    integer $categoryUID The category UID.
     * @param    integer $pageID Optional page ID.
     */
    function fillMarkerArray(&$array, $record, $localConf, $categoryUID, $pageID = '')
    {

        $file = GeneralUtility::getFileAbsFileName($this->filePath . $record['file']);
        $fileInformation = $this->getTotalFileInfo($file);

        // TEASER
        $teaseDownloads = $this->conf['teaseDownloads'];
        $downloadteaser = '';
        if ($teaseDownloads && $record['description'] != null) {
            $downloadteaser = $this->pi_RTEcssText(nl2br($this->local_cObj->stdWrap(trim($record['description']),
                $localConf['downloadTeaser_stdWrap.'])));
        }
        $array['###DOWNLOAD_TEASER###'] = $downloadteaser;

        // LABEL
        $GLOBALS['TSFE']->ATagParams = $GLOBALS['TSFE']->ATagParams . ($localConf['downloadLabel.']['ATagParams'] ? ' ' . $localConf['downloadLabel.']['ATagParams'] : '');
        $linkLabels = $this->pi_getFFvalue($this->flexform, 'linkLabels', 's_display');
        if ($linkLabels != 'none') {
            if ($linkLabels == 'direct') {
                $downloadLabel = $this->pi_LinkTP(htmlspecialchars(trim($record['label'])), [
                    'tx_abdownloads_pi1[action]' => 'getviewclickeddownload',
                    'tx_abdownloads_pi1[uid]'    => $record['uid'],
                    'tx_abdownloads_pi1[cid]'    => $this->cObj->data['uid'],
                ], false, $pageID);
            } elseif ($linkLabels == 'details') {
                $downloadLabel = $this->pi_LinkTP(htmlspecialchars(trim($record['label'])), [
                    'tx_abdownloads_pi1[action]'       => 'getviewdetailsfordownload',
                    'tx_abdownloads_pi1[uid]'          => $record['uid'],
                    'tx_abdownloads_pi1[category_uid]' => $categoryUID,
                    'tx_abdownloads_pi1[cid]'          => $this->cObj->data['uid'],
                ], $this->allowCaching, $pageID);
            } elseif ($linkLabels == 'target') {
                $downloadLabel = $this->pi_LinkTP(htmlspecialchars(trim($record['label'])), [], false,
                    $this->filePath . $record['file']);
            }
            $array['###DOWNLOAD_LABEL###'] = $this->local_cObj->stdWrap($downloadLabel,
                $localConf['downloadLabel_stdWrap.']);
        } else {
            $array['###DOWNLOAD_LABEL###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($record['label'])),
                $localConf['downloadLabel_stdWrap.']);
        }
        $GLOBALS['TSFE']->ATagParams = $originalATagParams;

        // DETAILS
        $GLOBALS['TSFE']->ATagParams = $GLOBALS['TSFE']->ATagParams . ($localConf['downloadDetails.']['ATagParams'] ? ' ' . $localConf['downloadDetails.']['ATagParams'] : '');
        if ($this->pi_getFFvalue($this->flexform, 'iconsForLinks', 's_display')) {
            $downloadDetails = $this->pi_LinkTP($this->getImageTag(
                $this->conf['iconDetails'],
                htmlspecialchars(trim($this->pi_getLL('ll_details'))),
                htmlspecialchars(trim($this->pi_getLL('ll_details')))),
                [
                    'tx_abdownloads_pi1[action]'       => 'getviewdetailsfordownload',
                    'tx_abdownloads_pi1[uid]'          => $record['uid'],
                    'tx_abdownloads_pi1[category_uid]' => $categoryUID,
                    'tx_abdownloads_pi1[cid]'          => $this->cObj->data['uid'],
                ], $this->allowCaching, $pageID);
        } else {
            $downloadDetails = $this->pi_LinkTP($this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_details'))),
                ''), [
                'tx_abdownloads_pi1[action]'       => 'getviewdetailsfordownload',
                'tx_abdownloads_pi1[uid]'          => $record['uid'],
                'tx_abdownloads_pi1[category_uid]' => $categoryUID,
                'tx_abdownloads_pi1[cid]'          => $this->cObj->data['uid'],
            ], $this->allowCaching, $pageID);
        }

        if ($this->pi_getFFvalue($this->flexform, 'noDetailsInCategoryView',
                's_display') || $this->conf['noDetailsInCategoryView']) {
            $array['###DOWNLOAD_DETAILS###'] = '';
        } else {
            $array['###DOWNLOAD_DETAILS###'] = $this->local_cObj->stdWrap($downloadDetails,
                $localConf['downloadDetails_stdWrap.']);
        }
        $GLOBALS['TSFE']->ATagParams = $originalATagParams;

        // RATING_STARS
        if ($this->pi_getFFvalue($this->flexform, 'noStarsInCategoryView',
                's_display') || $this->conf['noStarsInCategoryView']) {
            $array['###DOWNLOAD_RATING_STARS###'] = '';
        } else {
            $array['###DOWNLOAD_RATING_STARS###'] = $this->returnStarsForRating($record['rating']);
        }

        // REPORT_BROKEN
        if ($record['status'] == 2) {
            if ($this->pi_getFFvalue($this->flexform, 'noReportingInCategoryView',
                    's_display') || $this->conf['noReportingInCategoryView']) {
                $array['###DOWNLOAD_REPORT_BROKEN###'] = '';
            } else {
                if ($this->pi_getFFvalue($this->flexform, 'iconsForLinks', 's_display')) {
                    $array['###DOWNLOAD_REPORT_BROKEN###'] = $this->local_cObj->stdWrap($this->getImageTag(
                        $this->conf['iconReportBrokenDisabled'],
                        htmlspecialchars(trim($this->pi_getLL('ll_reported_download_broken'))),
                        htmlspecialchars(trim($this->pi_getLL('ll_reported_download_broken')))),
                        $localConf['downloadReportBroken_stdWrap.']);
                } else {
                    $array['###DOWNLOAD_REPORT_BROKEN###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_reported_download_broken'))),
                        $localConf['downloadReportBroken_stdWrap.']);
                }
            }
        } else {
            $GLOBALS['TSFE']->ATagParams = $GLOBALS['TSFE']->ATagParams . ($localConf['downloadReportBroken.']['ATagParams'] ? ' ' . $localConf['downloadReportBroken.']['ATagParams'] : '');
            if ($this->pi_getFFvalue($this->flexform, 'iconsForLinks', 's_display')) {
                $download = $this->pi_LinkTP($this->getImageTag(
                    $this->conf['iconReportBroken'],
                    htmlspecialchars(trim($this->pi_getLL('ll_report_download_broken'))),
                    htmlspecialchars(trim($this->pi_getLL('ll_report_download_broken')))),
                    [
                        'tx_abdownloads_pi1[action]'       => 'getviewreportbrokendownload',
                        'tx_abdownloads_pi1[uid]'          => $record['uid'],
                        'tx_abdownloads_pi1[category_uid]' => $categoryUID,
                        'tx_abdownloads_pi1[cid]'          => $this->cObj->data['uid'],
                    ], $this->allowCaching, $pageID);
            } else {
                $download = $this->pi_LinkTP($this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_report_download_broken'))),
                    ''), [
                    'tx_abdownloads_pi1[action]'       => 'getviewreportbrokendownload',
                    'tx_abdownloads_pi1[uid]'          => $record['uid'],
                    'tx_abdownloads_pi1[category_uid]' => $categoryUID,
                    'tx_abdownloads_pi1[cid]'          => $this->cObj->data['uid'],
                ], $this->allowCaching, $pageID);
            }

            if ($this->pi_getFFvalue($this->flexform, 'noReportingInCategoryView',
                    's_display') || $this->conf['noReportingInCategoryView']) {
                $array['###DOWNLOAD_REPORT_BROKEN###'] = '';
            } else {
                $array['###DOWNLOAD_REPORT_BROKEN###'] = $this->local_cObj->stdWrap($download,
                    $localConf['downloadReportBroken_stdWrap.']);
            }
            $GLOBALS['TSFE']->ATagParams = $originalATagParams;
        }

        // RATE
        if ($this->pi_getFFvalue($this->flexform, 'noRatingInCategoryView',
                's_display') || $this->conf['noRatingInCategoryView']) {
            $array['###DOWNLOAD_RATE###'] = '';
        } else {
            $GLOBALS['TSFE']->ATagParams = $GLOBALS['TSFE']->ATagParams . ($localConf['downloadRate.']['ATagParams'] ? ' ' . $localConf['downloadRate.']['ATagParams'] : '');
            if ($this->pi_getFFvalue($this->flexform, 'iconsForLinks', 's_display')) {
                $download = $this->pi_LinkTP($this->getImageTag(
                    $this->conf['iconRate'],
                    htmlspecialchars(trim($this->pi_getLL('ll_rate_download'))),
                    htmlspecialchars(trim($this->pi_getLL('ll_rate_download')))),
                    [
                        'tx_abdownloads_pi1[action]'       => 'getviewratedownload',
                        'tx_abdownloads_pi1[uid]'          => $record['uid'],
                        'tx_abdownloads_pi1[category_uid]' => $categoryUID,
                        'tx_abdownloads_pi1[cid]'          => $this->cObj->data['uid'],
                    ], $this->allowCaching, $pageID);
            } else {
                $download = $this->pi_LinkTP($this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_rate_download'))),
                    ''), [
                    'tx_abdownloads_pi1[action]'       => 'getviewratedownload',
                    'tx_abdownloads_pi1[uid]'          => $record['uid'],
                    'tx_abdownloads_pi1[category_uid]' => $categoryUID,
                    'tx_abdownloads_pi1[cid]'          => $this->cObj->data['uid'],
                ], $this->allowCaching, $pageID);
            }
            $array['###DOWNLOAD_RATE###'] = $this->local_cObj->stdWrap($download, $localConf['downloadRate_stdWrap.']);
            $GLOBALS['TSFE']->ATagParams = $originalATagParams;
        }

        // ICON and IMAGE
        $array['###DOWNLOAD_ICON###'] = $this->getImageTag(
            $this->conf['iconDownload'],
            'Download Icon',
            'Download Icon');
        $array['###DOWNLOAD_IMAGE###'] = $this->getImageLink($record, 'image', 'link', 'list', $categoryUID);

        // MISC
        $array['###DOWNLOAD_DESCRIPTION###'] = $this->pi_RTEcssText(nl2br($this->local_cObj->stdWrap(trim($record['description']),
            $localConf['downloadDescription_stdWrap.'])));
        $array['###DOWNLOAD_SPONSORED_DESCRIPTION###'] = $this->pi_RTEcssText(nl2br($this->local_cObj->stdWrap(trim($record['sponsored_description']),
            $localConf['downloadSponsoredDescription_stdWrap.'])));
        $array['###DOWNLOAD_LANGUAGE###'] = $this->local_cObj->stdWrap($this->getLanguage($record['language_uid']),
            $localConf['downloadLanguage_stdWrap.']);
        $array['###DOWNLOAD_LICENSE###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($record['license'])),
            $localConf['downloadLicense_stdWrap.']);
        $array['###DOWNLOAD_HOMEPAGE###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($record['homepage'])),
            $localConf['downloadHomepage_stdWrap.']);
        $array['###DOWNLOAD_FILENAME###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($record['file'])), '');
        $array['###DOWNLOAD_FILEICON###'] = $this->getFileIcon($record);
        $array['###DOWNLOAD_TYPE###'] = $this->local_cObj->stdWrap(strtoupper($fileInformation['fileext']), '');
        $array['###DOWNLOAD_SIZE###'] = $this->local_cObj->stdWrap(GeneralUtility::formatSize($fileInformation['size']) . 'Byte',
            '');
        $array['###DOWNLOAD_DATE###'] = $this->local_cObj->stdWrap($record['crdate'], $this->conf['date_stdWrap.']);
        $array['###DOWNLOAD_TIME###'] = $this->local_cObj->stdWrap($record['crdate'], $this->conf['time_stdWrap.']);
        $array['###DOWNLOAD_CLICKS###'] = $this->local_cObj->stdWrap($record['clicks'], '');
        $array['###DOWNLOAD_RATING###'] = round($this->local_cObj->stdWrap($record['rating'], ''), 2);
        $array['###DOWNLOAD_VOTES###'] = $this->local_cObj->stdWrap($record['votes'], '');
        $array['###DOWNLOAD_HREF###'] = $this->pi_getPageLink($GLOBALS['TSFE']->id, '', [
            'tx_abdownloads_pi1[action]' => 'getviewclickeddownload',
            'tx_abdownloads_pi1[uid]'    => $record['uid'],
            'no_cache'                   => '1',
        ]);

        // LL VALUES
        $array['###LL_FILENAME###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_filename'))),
            '');
        $array['###LL_TYPE###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_type'))), '');
        $array['###LL_SIZE###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_size'))), '');
        $array['###LL_DESCRIPTION###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_description'))),
            '');
        $array['###LL_SPONSORED_DESCRIPTION###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_sponsored_description'))),
            '');
        $array['###LL_LANGUAGE###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_language'))),
            '');
        $array['###LL_LICENSE###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_license'))),
            '');
        $array['###LL_HOMEPAGE###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_homepage'))),
            '');
        $array['###LL_DATE_ADDED###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_added'))),
            '');
        $array['###LL_CLICKS###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_clicks_lower'))),
            '');
        $array['###LL_RATING###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_rating'))),
            '');
        $array['###LL_VOTES###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_votes'))), '');
        $array['###LL_IMAGE###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_image'))), '');
        $array['###LL_DOWNLOAD###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_download'))),
            '');
        $array['###LL_DOWNLOADS###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_downloads'))),
            $localConf['downloads_stdWrap.']);
        $array['###LL_TOTAL###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_total'))), '');
        $array['###LL_CLICKS###'] = $this->local_cObj->stdWrap(htmlspecialchars(trim($this->pi_getLL('ll_clicks_lower'))),
            '');

        // EDIT PANEL
        $array['###EDIT_PANEL###'] = $this->pi_getEditPanel($record, $this->tablePrefix . 'download', $record['uid'],
            $localConf);
        if ($array['###EDIT_PANEL###'] == $record['uid']) {
            $array['###EDIT_PANEL###'] = '';
        }

        // RECORD MARKER HOOK
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ab_downloads']['recordMarkerHook'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ab_downloads']['recordMarkerHook'] as $_classRef) {
                $_procObj = &GeneralUtility::makeInstance($_classRef);
                $array = $_procObj->recordMarkerProcessor($array, $record, $localConf, $this);
            }
        }
    }

    protected function getTotalFileInfo($file)
    {
        $pathInfo = pathinfo($file);
        $mimeType = 'other-other';
        if (file_exists($file)) {
            switch (mime_content_type($file)) {
                case 'application/pdf':
                    $mimeType = 'pdf';
                    break;
                case 'application/zip':
                    $mimeType = 'compressed';
                    break;
                case 'application/x-dosexec':
                    $mimeType = 'application';
                    break;
            }
        }
        return [
            'fileext'  => $pathInfo['extension'],
            'size'     => file_exists($file) ? filesize($file) : 0,
            'mimetype' => $mimeType,
        ];
    }

    protected function getImageTag(string $fileName, string $alt = '', string $title= ""): string
    {
        if (!$fileName) {
            return '';
        }
        $tag = '<img src="' . $GLOBALS['TSFE']->tmpl->getFileName($fileName) . '" ';
        if ($alt) {
            $tag .= 'alt="' . $alt . '" ';
        }
        if ($title) {
            $tag .= 'title="' . $title . '" ';
        }
        $tag .= '/>';
        return $tag;
    }
}

