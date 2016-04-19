<?php
/***************************************************************
* Copyright notice
*
* (c) 2005 - 2009 Andreas Bulling (typo3@andreas-bulling.de)
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

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   86: class tx_abdownloads_module1 extends t3lib_SCbase
 *
 *              SECTION: Main functions
 *  106:     function init()
 *  126:     function menuConfig()
 *  151:     function main()
 *  170:     function jumpToUrl(URL)
 *  219:     function printContent()
 *  234:     function moduleContent()
 *
 *              SECTION: View functions
 *  390:     function getViewDownloadsToApprove()
 *  541:     function getViewDownloadsReportedBroken()
 *  609:     function getViewCheckForBrokenDownloads()
 *  776:     function getViewImportCategoriesDownloadsDB( $type = null, $table = null, $generateQuery = null )
 *  930:     function getViewImportCategoriesDownloadsCSV( $type = null, $importCSV = null, $overwriteExisting = null )
 * 1031:     function getViewExportCategoriesDownloads( $type = null, $properties = null, $outputFormat = null, $generateOutput = null )
 * 1156:     function getViewStatistics()
 *
 *              SECTION: Action functions
 * 1339:     function doAcceptDownload( $uid = null )
 * 1390:     function doDisableDownload( $uid = null )
 * 1410:     function doDeleteDownload( $uid = null )
 * 1430:     function doCheckDownload( $file )
 *
 *              SECTION: Helper functions
 * 1455:     function makePageBrowser( $showResultCount = 1, $tableParams = '', $pointerName = 'pointer' )
 * 1522:     function generateOutput( $type = null, $properties = null, $outputFormat = null, $identifier = null )
 * 1592:     function calculatePercent( $count, $totalCount )
 * 1609:     function getTemplateField( $field, $string )
 * 1639:     function existingEntry( $uid = null, $table = null )
 *
 * TOTAL FUNCTIONS: 22
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

unset( $MCONF );
require ( 'conf.php' );
#require ( $BACK_PATH. 'init.php' );
#todo $GLOBALS['LANG']->includeLLFile( 'EXT:ab_downloads/mod1/locallang.xml' );

#todo $GLOBALS['BE_USER']->modAccess( $MCONF, 1 );	//  This checks permissions and exits if the users has no permission for entry.

/**
 * Module 'Modern Downloads' for the 'ab_downloads' extension.
 *
 * $Id: index.php 177 2009-07-30 13:20:08Z andreas $
 *
 * @author	Andreas Bulling <typo3@andreas-bulling.de>
 * @package TYPO3
 * @subpackage	tx_abdownloads
 */
class tx_abdownloads_module1 extends \TYPO3\CMS\Backend\Module\BaseScriptClass {
	var $pageinfo;
	var $debug = false;
	var $tablePrefix ='tx_abdownloads_';
	var $filePath = null;						// Holds the file path for downloads.
	var $versioningEnabled = false;					// Is the extension 'version' loaded

	/*************************************
	 *
	 * Main functions
	 *
	 *************************************/

	/**
	 * init()
	 *
	 * Calls the parent init() function.
	 *
	 * @return	void
	 */
	function init() {
		global $AB, $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $HTTP_GET_VARS, $HTTP_POST_VARS, $CLIENT, $TYPO3_CONF_VARS;
		parent::init();

		// Check for extension "version"
		if( \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded( 'version' ) ) {
			$this->versioningEnabled = true;
		}

		$filePath = $this->getTemplateField( 'constants', 'plugin.tx_abdownloads_pi1.filePath' );
		$this->filePath = $filePath ? $filePath : 'uploads/tx_abdownloads/files/';
	}

	/**
	 * menuConfig()
	 *
	 * Adds items to the MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig() {
		global $LANG;

		$this->MOD_MENU = Array (
			'function' => Array (
				'1' => $LANG->getLL( 'function1' ),
				'2' => $LANG->getLL( 'function2' ),
				'3' => $LANG->getLL( 'function3' ),
				'4' => $LANG->getLL( 'function4' ),
				'5' => $LANG->getLL( 'function5' ),
				'6' => $LANG->getLL( 'function6' ),
				'7' => $LANG->getLL( 'function7' ),
			)
		);

		parent::menuConfig();
	}

	/**
	 * main()
	 *
	 * Main function of the module. Write the content to $this->content
	 *
	 * @return	void
	 */
	function main() {
		global $AB, $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $HTTP_GET_VARS, $HTTP_POST_VARS, $CLIENT, $TYPO3_CONF_VARS;

		//  Access check!
		//  The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess( $this->id, $this->perms_clause );
		$access = is_array( $this->pageinfo ) ? 1 : 0;

		if( ( $this->id && $access ) || ( $BE_USER->user['admin'] && !$this->id ) ) {

			// Draw the header
			$this->doc = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( 'bigDoc' );
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form= '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST">';

			// JavaScript
			$this->doc->JScode = '
				<script language="javascript">
					script_ended = 0;
					function jumpToUrl(URL) {
						document.location = URL;
					}
				</script>
			';

			$this->doc->postCode= '
				<script language="javascript">
					script_ended = 1;
					if(top.theMenu) top.theMenu.recentuid = ' . intval( $this->id ) . ';
				</script>
			';

			$headerSection = $this->doc->getHeader( 'pages', $this->pageinfo, $this->pageinfo['_thePath'] ) . '<br />' . $LANG->php3Lang['labels']['path'] . ': ' . \TYPO3\CMS\Core\Utility\GeneralUtility::fixed_lgd_cs( $this->pageinfo['_thePath'],-50);

			$this->content .= $this->doc->startPage( $LANG->getLL( 'title' ) );
			$this->content .= $this->doc->header( $LANG->getLL( 'title' ) );
			$this->content .= $this->doc->spacer( 5 );
			$this->content .= $this->doc->section( '', $this->doc->funcMenu( $headerSection, t3lib_BEfunc::getFuncMenu( $this->id, 'SET[function]', $this->MOD_SETTINGS['function'], $this->MOD_MENU['function'], 'index.php' ) ) );
			$this->content .= $this->doc->divider( 5 );

			// Render content
			$this->moduleContent();

			// ShortCut
			if( $BE_USER->mayMakeShortcut() ) {
				$this->content .= $this->doc->spacer(20) . $this->doc->section( '', $this->doc->makeShortcutIcon( 'id', implode( ', ', array_keys( $this->MOD_MENU) ), $this->MCONF['name'] ) );
			}

			$this->content .= $this->doc->spacer(10);
		} else {
			// If no access or if ID == zero
			$this->doc = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( 'bigDoc' );
			$this->doc->backPath = $BACK_PATH;

			$this->content .= $this->doc->startPage( $LANG->getLL( 'title' ) );
			$this->content .= $this->doc->header( $LANG->getLL( 'title' ) );
			$this->content .= $this->doc->spacer( 5 );
			$this->content .= $this->doc->spacer( 10 );
		}
	}

	/**
	 * printContent()
	 *
	 * Prints the generated HTML source for the module.
	 *
	 * @return	string		The generated HTML source for the module.
	 */
	function printContent() {
		global $SOBE;

		$this->content .= $this->doc->endPage();
		echo $this->content;
	}

	/**
	 * moduleContent()
	 *
	 * Decides which function to call and adds its output to the content.
	 *
	 * @return	void
	 */
	function moduleContent() {
		global $LANG;

		// Get action and uid values
		$action = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'action' );
		$uid = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'uid' );

		switch( (string)$this->MOD_SETTINGS['function'] ) {
			/**
			 * FUNCTION: Approve Downloads
			 */
			case 1:
				// Decide what to do
				if( $action == null || $action == '' ) {
					$content .= $this->getViewDownloadsToApprove();

				} elseif( $action == 'getViewAcceptDownload' ) {
					// Update the database
					$this->doAcceptDownload( $uid );

					$content .= $this->getViewDownloadsToApprove();

				} elseif( $action == 'getViewDeleteDownload' ) {
					// Update the database
					$this->doDeleteDownload( $uid );

					$content .= $this->getViewDownloadsToApprove();
				}

				$this->content .= $this->doc->section( $LANG->getLL( 'function1' ), $content, 0, 1 );
			break;

			/**
			 * FUNCTION: Downloads reported broken
			 */
			case 2:
				// Decide what to do
				if( $action == null || $action == '' ) {
					$content .= $this->getViewDownloadsReportedBroken();

				} elseif( $action == 'getViewAcceptDownload' ) {
					// Update the database
					$this->doAcceptDownload( $uid );

					$content .= $this->getViewDownloadsReportedBroken();

				} elseif( $action == 'getViewDisableDownload' ) {
					// Update the database
					$this->doDisableDownload( $uid );

					$content .= $this->getViewDownloadsReportedBroken();
				}

				$this->content .= $this->doc->section( $LANG->getLL( 'function2' ), $content, 0, 1 );
			break;

			/**
			 * FUNCTION: Check for non-working downloads
			 */
  			case 3:
  				// Decide what to do
  				if( $action == null || $action == '' ) {
  					$content .= $this->getViewCheckForBrokenDownloads();

  				} elseif( $action == 'getViewEnableDownload' ) {
  					// Update the database
  					$this->doAcceptDownload( $uid );

  					$content .= $this->getViewCheckForBrokenDownloads();

  				} elseif( $action == 'getViewDisableDownload' ) {
  					// Update the database
  					$this->doDisableDownload( $uid );

  					$content .= $this->getViewCheckForBrokenDownloads();
  				}

  				$this->content .= $this->doc->section( $LANG->getLL( 'function3' ), $content, 0, 1 );
  			break;

			/**
			 * FUNCTION: Import categories/downloads from DB
			 */
  			case 4:
				$type = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'type' );
				$table = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'table' );
				$generateQuery = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'generateQuery' );

  				// Decide what to do
  				if( $action == null || $action == '' ) {
  					$content .= $this->getViewImportCategoriesDownloadsDB( $type, $table, $generateQuery );
  				}

  				$this->content .= $this->doc->section( $LANG->getLL( 'function4' ), $content, 0, 1 );
  			break;

			/**
			 * FUNCTION: Import categories/downloads from CSV
			 */
			case 5:
				$type = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'type' );
				$importCSV = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'importCSV' );
				$overwriteExisting = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'overwriteExisting' );

  				// Decide what to do
  				if( $action == null || $action == '' ) {
  					$content .= $this->getViewImportCategoriesDownloadsCSV( $type, $importCSV, $overwriteExisting );
  				}

  				$this->content .= $this->doc->section( $LANG->getLL( 'function5' ), $content, 0, 1 );
  			break;

			/**
			 * FUNCTION: Export categories/downloads
			 */
  			case 6:
				$type = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'type' );
				$properties = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'properties' );
				if( is_array( $properties ) )
					$properties = implode( ',', $properties );

				$outputFormat = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'outputFormat' );
				$generateOutput = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'generateOutput' );

  				// Decide what to do
  				if( $action == null || $action == '' ) {
  					$content .= $this->getViewExportCategoriesDownloads( $type, $properties, $outputFormat, $generateOutput );
  				}

  				$this->content .= $this->doc->section( $LANG->getLL( 'function6' ), $content, 0, 1 );
  			break;

			/**
			 * FUNCTION: Statistics
			 */
  			case 7:
				$content .= $this->getViewStatistics();
				$this->content .= $this->doc->section( $LANG->getLL( 'function7' ), $content, 0, 1 );
			break;
  		}
  	}

	/*************************************
	 *
	 * View functions
	 *
	 *************************************/

	/**
	 * getViewDownloadsToApprove()
	 *
	 * Checks for downloads proposed by frontend users.
	 * Provides the possibility to accept and delete downloads.
	 *
	 * @return	string		The generated HTML source for this view.
	 */
	function getViewDownloadsToApprove() {
		global $LANG;

		// Init some vars
		$content = null;
		$downloadsToApprove = null;
		$doc = get_object_vars( $this->doc);
		$switch = true;

		// Output description
		$content .= $LANG->getLL( 'ViewDownloadsToApprove_text' ) . '<br /><br />';

		// Get downloads from database
		$theTable = $this->tablePrefix . 'download AS download, ' . $this->tablePrefix . 'category AS cat';
		$theField = 'download.status';
		$theValue = '0';
		$whereClause = 'AND download.sys_language_uid IN (-1,0) AND download.category=cat.uid AND download.deleted=0 AND download.hidden=0 AND download.pid!=-1';
		$groupBy = '';
		$orderBy = '';
		$limit = '';
		$downloadsToApprove = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows( 'download.uid,download.label,download.pid,download.description,download.file,download.category,cat.label AS catlabel', $theTable, $theField . '=' . $GLOBALS['TYPO3_DB']->quoteStr( $theValue, $theTable ) . ' ' . $whereClause, $groupBy, $orderBy, $limit );

		$theTable = $this->tablePrefix . 'download';
		$theField = 'status';
		$theValue = '0';
		$whereClause = 'AND sys_language_uid IN (-1,0) AND category=0 AND deleted=0 AND hidden=0 AND pid!=-1';
		$groupBy = '';
		$orderBy = '';
		$limit = '';
		$downloadsToApproveWithoutCategory = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows( 'uid,label,pid,description,file', $theTable, $theField . '=' . $GLOBALS['TYPO3_DB']->quoteStr( $theValue, $theTable ) . ' ' . $whereClause, $groupBy, $orderBy, $limit );

		// Get categories from database
		$theTable = $this->tablePrefix . 'category';
		$whereClause = 'sys_language_uid IN (-1,0) AND deleted=0 AND hidden=0';
		$groupBy = '';
		$orderBy = '';
		$limit = '';
		$categories = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows( '*', $theTable, $whereClause, $groupBy, $orderBy, $limit );

		// Debugging output
		if( $this->debug ) {
			t3lib_utility_Debug::printArray( $downloadsToApprove );
			t3lib_utility_Debug::printArray( $downloadsToApproveWithoutCategory );
			t3lib_utility_Debug::printArray( $categories );
		}

		$content .= '<table width="100%">';
		$content .= '<tr bgcolor="' . $doc['bgColor4'] . '">';
		$content .= '<td><b>Label</b></td>';
		$content .= '<td><b>Description</b></td>';
		$content .= '<td><b>Filename</b></td>';
		$content .= '<td><b>Category</b></td>';
		$content .= '<td>&nbsp;</td>';
		$content .= '<td>&nbsp;</td>';
		$content .= '</tr>';

		if( count( $downloadsToApprove ) > 0 ) {
			// Downloads with category
			for( $i = 0; $i < count( $downloadsToApprove ); $i++ ) {
				$content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST">';

				// Alternating row colors
				$content .= $switch ? '<tr bgcolor="' . $doc['bgColor5'] . '">' : '<tr>' ;
				$switch = !$switch;

				// Starting content
				$content .= '<td style="vertical-align:top;"><input type="text" name="label" value="' . $downloadsToApprove[$i]['label'] . '" size="30" /></td>';
				$content .= '<td><textarea name="description" rows="5">' . \TYPO3\CMS\Core\Utility\GeneralUtility::formatForTextarea( $downloadsToApprove[$i]['description'] ) . '</textarea></td>';
				$content .= '<td style="vertical-align:top;"><input type="text" name="file" value="' . $downloadsToApprove[$i]['file'] . '" size="30" /></td>';

				$content .= '<td style="vertical-align:top;"><select name="categoryUID"><option value="0"></option>';
				foreach( $categories as $category ) {
					$content .= '<option value="' . $category['uid'] . '"';

					if( $downloadsToApprove[$i]['category'] == $category['uid'] ) {
						$content .= 'selected="selected"';
					}

					$content .= '>';

					if( $category['parent_category'] > 0 ) {

						$content .= '-- ';
					}

					$content .= $category['label'] . '</option>';
				}
				$content .= '</select></td>';

				$content .= '<td style="vertical-align:top;"><input type="image" src="action_accept.gif" style="border:0px;" alt="' . $LANG->getLL( 'ViewDownloadsToApprove_accept' ) . '" title="' . $LANG->getLL( 'ViewDownloadsToApprove_accept' ) . '" /></td>';
				$content .= "<td style=\"vertical-align:top;\"><a href=\"?action=getViewDeleteDownload&id=" . $this->id . "&uid=" . $downloadsToApprove[$i]['uid'] . "\"><img src=\"action_delete.gif\" border=\"0\" alt=\"" . $LANG->getLL( 'ViewDownloadsToApprove_delete' ) . "\" title=\"" . $LANG->getLL( 'ViewDownloadsToApprove_delete' ) . "\"></a></td>";
				$content .= '</tr>';

				$content .= '<input type="hidden" name="id" value="' . $this->id . '" /><input type="hidden" name="uid" value="' . $downloadsToApprove[$i]['uid'] . '" /><input type="hidden" name="action" value="getViewAcceptDownload" /></form>';
			}
		}

		if( count( $downloadsToApproveWithoutCategory ) > 0 ) {
			// Downloads without category
			for( $i = 0; $i < count( $downloadsToApproveWithoutCategory ); $i++ ) {
				$content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST">';

				// Alternating row colors
				$content .= $switch ? '<tr bgcolor="' . $doc['bgColor5'] . '">' : '<tr>' ;
				$switch = !$switch;

				// Starting content
				$content .= '<td style="vertical-align:top;"><input type="text" name="label" value="' . $downloadsToApproveWithoutCategory[$i]['label'] . '" size="30" /></td>';
				$content .= '<td><textarea name="description" rows="5">' . \TYPO3\CMS\Core\Utility\GeneralUtility::formatForTextarea( $downloadsToApproveWithoutCategory[$i]['description'] ) . '</textarea></td>';
				$content .= '<td style="vertical-align:top;"><input type="text" name="file" value="' . $downloadsToApproveWithoutCategory[$i]['file'] . '" size="30" /></td>';

				$content .= '<td style="vertical-align:top;"><select name="categoryUID"><option value="0"></option>';
				foreach( $categories as $category ) {
					$content .= '<option value="' . $category['uid'] . '">';

					if( $category['parent_category'] > 0 ) {

						$content .= '-- ';
					}

					$content .= $category['label'] . '</option>';
				}
				$content .= '</select></td>';

				$content .= '<td style="vertical-align:top;"><input type="image" src="action_accept.gif" style="border:0px;" alt="' . $LANG->getLL( 'ViewDownloadsToApprove_accept' ) . '" title="' . $LANG->getLL( 'ViewDownloadsToApprove_accept' ) . '" /></td>';
				$content .= "<td style=\"vertical-align:top;\"><a href=\"?action=getViewDeleteDownload&id=" . $this->id . "&uid=" . $downloadsToApproveWithoutCategory[$i]['uid'] . "\"><img src=\"action_delete.gif\" border=\"0\" alt=\"" . $LANG->getLL( 'ViewDownloadsToApprove_delete' ) . "\" title=\"" . $LANG->getLL( 'ViewDownloadsToApprove_delete' ) . "\"></a></td>";
				$content .= '</tr>';

				$content .= '<input type="hidden" name="id" value="' . $this->id . '" /><input type="hidden" name="uid" value="' . $downloadsToApproveWithoutCategory[$i]['uid'] . '" /><input type="hidden" name="action" value="getViewAcceptDownload" /></form>';
			}
		}

		$content .= '</table>';

		// Legend
		$content .= $this->doc->divider(10);
		$content .= '<b>' . $LANG->getLL( 'legend' ) . '</b><br /><br />';
		$content .= '<img src="action_accept.gif" border="0" alt="' . $LANG->getLL( 'ViewDownloadsToApprove_accept' ) . '" title="' . $LANG->getLL( 'ViewDownloadsToApprove_accept' ) . '">&nbsp;' . $LANG->getLL( 'ViewDownloadsToApprove_accept' );
		$content .= '<br /><img src="action_delete.gif" border="0" alt="' . $LANG->getLL( 'ViewDownloadsToApprove_delete' ) . '" title="' . $LANG->getLL( 'ViewDownloadsToApprove_delete' ) . '">&nbsp;' . $LANG->getLL( 'ViewDownloadsToApprove_delete' );

		return $content;
	}

	/**
	 * getViewDownloadsReportedBroken()
	 *
	 * Checks for downloads reported broken by frontend users.
	 * Provides the possibility to accept and disable downloads.
	 *
	 * @return	string		The generated HTML source for this view.
	 */
	function getViewDownloadsReportedBroken() {
		global $LANG;

		// Init some vars
		$content = null;
		$downloadsReportedBroken = null;
		$doc = get_object_vars( $this->doc);
		$switch = true;

		// Output header and description
		$content .= $LANG->getLL( 'ViewDownloadsReportedBroken_text' ) . '<br /><br />';

		// Get downloads from database
		$theTable = $this->tablePrefix . 'download';
		$theField = 'status';
		$theValue = '2';
		$whereClause = 'AND sys_language_uid IN (-1,0) AND deleted=0 AND hidden=0 AND pid!=-1';
		$groupBy = '';
		$orderBy = '';
		$limit = '';
		$downloadsReportedBroken = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows( 'uid,label,file,pid', $theTable, $theField . '=' . $GLOBALS['TYPO3_DB']->quoteStr( $theValue, $theTable ) . ' ' . $whereClause, $groupBy, $orderBy, $limit );

		// Debugging output
		if( $this->debug ) {
			t3lib_utility_Debug::printArray( $downloadsReportedBroken );
		}

		// Ouput downloads
		$content .= '<table width="100%">';
		$content .= '<tr bgcolor="' . $doc['bgColor4'] . '">';
		$content .= '<td><b>Label</b></td>';
		$content .= '<td><b>Filename</b></td>';
		$content .= '<td>&nbsp;</td>';
		$content .= '<td>&nbsp;</td>';
		$content .= '</tr>';

		for( $i = 0; $i < count( $downloadsReportedBroken); $i++ ) {
			// Alternating row colors
			$content .= $switch ? '<tr bgcolor="' . $doc['bgColor5'] . '">' : '<tr>' ;
			$switch = !$switch;

			// Starting content
			$content .= '<td><a href="../../../../typo3/alt_doc.php?returnUrl=%2Fcms%2Ftypo3%2Fsysext%2Fcms%2Flayout%2Fdb_layout.php%3Fid%3D162&amp;edit[tx_ablinklist_link][' . $downloadsReportedBroken[$i]['uid'] . ']=edit">' . $downloadsReportedBroken[$i]['label'] . '</a></td>';
			$content .= '<td><a href="' . \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv( TYPO3_SITE_URL ) . $this->filePath . $downloadsReportedBroken[$i]['file'] . '">' . $downloadsReportedBroken[$i]['file'] . '</a></td>';
			$content .= '<td><a href="?action=getViewAcceptDownload&uid=' . $downloadsReportedBroken[$i]['uid'] . '&id=' . $this->id. '"><img src="action_accept.gif" border="0" alt="' . $LANG->getLL( 'ViewDownloadsToApprove_accept' ) . '" title="' . $LANG->getLL( 'ViewDownloadsToApprove_accept' ) . '"></a></td>';
			$content .= '<td><a href="?action=getViewDisableDownload&uid=' . $downloadsReportedBroken[$i]['uid'] . '&id=' . $this->id. '"><img src="action_disable.gif" border="0" alt="' . $LANG->getLL( 'ViewDownloadsReportedBroken_disable' ) . '" title="' . $LANG->getLL( 'ViewDownloadsReportedBroken_disable' ) . '"></a></td>';
			$content .= '</tr>';
		}

		$content .= '</table>';

		// Legend
		$content .= $this->doc->divider(10);
		$content .= '<b>' . $LANG->getLL( 'legend' ) . '</b><br /><br />';
		$content .= '<img src="action_accept.gif" border="0" alt="' . $LANG->getLL( 'ViewDownloadsToApprove_accept' ) . '" title="' . $LANG->getLL( 'ViewDownloadsToApprove_accept' ) . '">&nbsp;' . $LANG->getLL( 'ViewDownloadsToApprove_accept' );
		$content .= '<br /><img src="action_disable.gif" border="0" alt="' . $LANG->getLL( 'ViewDownloadsReportedBroken_disable' ) . '" title="' . $LANG->getLL( 'ViewDownloadsReportedBroken_disable' ) . '">&nbsp;' . $LANG->getLL( 'ViewDownloadsReportedBroken_disable' );

		return $content;
	}

	/**
	 * getViewCheckForBrokenDownloads()
	 *
	 * Checks for non-working downloads.
	 * Provides the possibility to enable and disable downloads.
	 *
	 * @return	string		The generated HTML source for this view.
	 */
	function getViewCheckForBrokenDownloads() {
		global $LANG;

		// Init some vars
		$content = null;
		$downloads = null;
		$doc = get_object_vars( $this->doc);
		$switch = true;
		$downloadsOnline = array();
		$downloadsOffline = array();
		$downloadsNotChecked = array();

		// Get flexform config
		$theTable = 'tt_content';
		$whereClause = 'pid=' . $this->id . ' AND list_type="ab_downloads_pi1" AND deleted=0';
		$groupBy = '';
		$orderBy = '';
		$limit = '';
		$flexform = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows( '*', $theTable, $whereClause, $groupBy, $orderBy, $limit );
		$flexarray = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array( $flexform[0]['pi_flexform'] );

		if( $this->debug )
			t3lib_utility_Debug::printArray( $flexarray );

		if( is_array( $flexarray ) ) {
			$listLimit = $flexarray['data']['s_display']['lDEF']['listLimit']['vDEF'] ? $flexarray['data']['s_display']['lDEF']['listLimit']['vDEF'] : 10;
 		} else {
			$listLimit = 10;
		}

		// Get and set pagebrowser values
		$pointer = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'pointer' );
		if ( $pointer > 0 ) {
			$pointer = $pointer * $listLimit;
		} else {
			$pointer = 0;
		}

		// Output header and description
		$content .= $LANG->getLL( 'ViewCheckForBrokenDownloads_text' ) . '<br /><br />';

		// Get total number of downloads from database
		$theTable = $this->tablePrefix . 'download';
		$theField = 'status';
		$theValue = 'IN (0,1,2)';
		$whereClause = 'AND sys_language_uid IN (-1,0) AND deleted=0 AND hidden=0 AND pid!=-1';
		$groupBy = '';
		$orderBy = '';
		$limit = '';
		$downloadsTotal = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows( 'uid,label,pid,file,status', $theTable, $theField . ' ' . $theValue . $whereClause, $groupBy, $orderBy, $limit );

		// Get downloads from database
		$theTable = $this->tablePrefix . 'download';
		$theField = 'status';
		$theValue = 'IN (0,1,2)';
		$whereClause = 'AND sys_language_uid IN (-1,0) AND deleted=0 AND hidden=0 AND pid!=-1';
		$groupBy = '';
		$orderBy = '';
		$limit = $pointer . ', ' . $listLimit;
		$downloads = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows( 'uid,label,pid,file,status', $theTable, $theField . ' ' . $theValue . $whereClause, $groupBy, $orderBy, $limit );

		// Debugging output
		if( $this->debug ) {
			t3lib_utility_Debug::printArray( $downloads );
		}

		// Check the downloads
		for( $i = 0; $i < count( $downloads ); $i++ ) {
			// Get download status
			$downloadstatus = $this->doCheckDownload( $downloads[$i]['file'] );

			if( $downloadstatus == true ) {
				// Download is online.
				$downloadsOnline[] = $downloads[$i];
			} elseif( $downloadstatus == false ) {
				// Download seems to be offline.
				$downloadsOffline[] = $downloads[$i];
			}
		}

		// Ouput downloads
		$content .= "<table width=\"100%\">";
		$content .= "<tr bgcolor=\"" . $doc['bgColor4'] . "\">";
		$content .= '<td>&nbsp;</td>';
		$content .= '<td><b>Label</b></td>';
		$content .= '<td><b>Filename</b></td>';
		$content .= '<td>&nbsp;</td>';
		$content .= '<td>&nbsp;</td>';
		$content .= '</tr>';

		// Output offline downloads
		for( $i = 0; $i < count( $downloadsOffline ); $i++ ) {
			$content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST">';

			// Extract url
			$downloadsOffline[$i]['file'] = explode( ' ', $downloadsOffline[$i]['file'] );
			$downloadsOffline[$i]['file'] = $downloadsOffline[$i]['file'][0];

			// Alternating row colors
			$content .= $switch ? '<tr bgcolor="' . $doc['bgColor5'] . '">' : '<tr>' ;
			$switch = !$switch;

			// Starting content
			$content .= "<td><img src=\"icon_offline.gif\" border=\"0\" alt=\"" . $LANG->getLL( 'ViewCheckForBrokenDownloads_offline' ) . "\" title=\"" . $LANG->getLL( 'ViewCheckForBrokenDownloads_offline' ) . "\"></td>";
			$content .= '<td><a href="../../../../typo3/alt_doc.php?returnUrl=%2Fcms%2Ftypo3%2Fsysext%2Fcms%2Flayout%2Fdb_layout.php%3Fid%3D162&amp;edit[tx_ablinklist_link][' . $downloadsOffline[$i]['uid'] . ']=edit">' . $downloadsOffline[$i]['label'] . '</a></td>';
			$content .= '<td><input type="text" name="file" value="' . $downloadsOffline[$i]['file'] . '" size="30" /></td>';
			$content .= '<td><input type="image" src="action_enable.gif" style="border:0px;" alt="' . $LANG->getLL( 'ViewCheckForBrokenDownloads_enable' ) . '" title="' . $LANG->getLL( 'ViewCheckForBrokenDownloads_enable' ) . '" /></td>';
			$content .= "<td><a href=\"?action=getViewDisableDownload&id=" . $this->id . "&uid=" . $downloadsOffline[$i]['uid'] . "\"><img src=\"action_disable.gif\" border=\"0\" alt=\"" . $LANG->getLL( 'ViewDownloadsReportedBroken_disable' ) . "\" title=\"" . $LANG->getLL( 'ViewDownloadsReportedBroken_disable' ) . "\"></a></td>";
			$content .= '</tr>';

			$content .= '<input type="hidden" name="id" value="' . $this->id . '" /><input type="hidden" name="uid" value="' . $downloadsOffline[$i]['uid'] . '" /><input type="hidden" name="action" value="getViewEnableDownload" /></form>';
		}

		// Output online downloads
		for( $i = 0; $i < count( $downloadsOnline ); $i++ ) {
			// Extract url
			$downloadsOnline[$i]['file'] = explode( ' ', $downloadsOnline[$i]['file'] );
			$downloadsOnline[$i]['file'] = $downloadsOnline[$i]['file'][0];

			// Alternating row colors
			$content .= $switch ? '<tr bgcolor="' . $doc['bgColor4'] . '">' : '<tr>' ;
			$switch = !$switch;

			// Starting content
			$content .= "<td><img src=\"icon_online.gif\" border=\"0\" alt=\"" . $LANG->getLL( 'ViewCheckForBrokenDownloads_online' ) . "\" title=\"" . $LANG->getLL( 'ViewCheckForBrokenDownloads_online' ) . "\"></td>";
			$content .= '<td><a href="../../../../typo3/alt_doc.php?returnUrl=%2Fcms%2Ftypo3%2Fsysext%2Fcms%2Flayout%2Fdb_layout.php%3Fid%3D162&amp;edit[tx_ablinklist_link][' . $downloadsOnline[$i]['uid'] . ']=edit">' . $downloadsOnline[$i]['label'] . '</a></td>';
			$content .= '<td><a href="' . \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv( TYPO3_SITE_URL ) . $this->filePath . $downloadsOnline[$i]['file'] . '">' . $downloadsOnline[$i]['file'] . '</a></td>';
			$content .= "<td><img src=\"action_enable_disabled.gif\" border=\"0\" alt=\"" . $LANG->getLL( 'ViewCheckForBrokenDownloads_enable' ) . "\" title=\"" . $LANG->getLL( 'ViewCheckForBrokenDownloads_enable' ) . "\"></td>";
			$content .= "<td><a href=\"?action=getViewDisableDownload&id=" . $this->id . "&uid=" . $downloadsOnline[$i]['uid'] . "\"><img src=\"action_disable.gif\" border=\"0\" alt=\"" . $LANG->getLL( 'ViewDownloadsReportedBroken_disable' ) . "\" title=\"" . $LANG->getLL( 'ViewDownloadsReportedBroken_disable' ) . "\"></a></td>";
			$content .= '</tr>';
		}

		$content .= '</table>';

		// Pagebrowser
		if( count( $downloadsTotal ) > $listLimit ) {
			//  configure pagebrowser vars
			$this->internal['res_count'] = count( $downloadsTotal );
			$this->internal['results_at_a_time'] = $listLimit;
			$this->internal['maxPages'] = 100;
			$this->internal['action'] = '';

			$content .= '<br />';
			$content .= $this->makePageBrowser();
		}

		// Legend
		$content .= $this->doc->divider(10);
		$content .= '<b>' . $LANG->getLL( 'legend' ) . '</b><br /><br />';
		$content .= '<img src="action_enable.gif" border="0" alt="' . $LANG->getLL( 'ViewCheckForBrokenDownloads_enable' ) . '" title="' . $LANG->getLL( 'ViewCheckForBrokenDownloads_enable' ) . '">&nbsp;' . $LANG->getLL( 'ViewCheckForBrokenDownloads_enable' ) . '<br />';
		$content .= '<img src="action_disable.gif" border="0" alt="' . $LANG->getLL( 'ViewDownloadsToApprove_delete' ) . '" title="' . $LANG->getLL( 'ViewDownloadsToApprove_delete' ) . '">&nbsp;' . $LANG->getLL( 'ViewDownloadsReportedBroken_disable' ) . '<br />';
		$content .= '<img src="icon_offline.gif" border="0" alt="' . $LANG->getLL( 'ViewCheckForBrokenDownloads_offline' ) . '" title="' . $LANG->getLL( 'ViewCheckForBrokenDownloads_offline' ) . '"> &nbsp;' . $LANG->getLL( 'ViewCheckForBrokenDownloads_offline' ) . '<br />';
		$content .= '<img src="icon_online.gif" border="0" alt="' . $LANG->getLL( 'ViewCheckForBrokenDownloads_online' ) . '" title="' . $LANG->getLL( 'ViewCheckForBrokenDownloads_online' ) . '"> &nbsp;' . $LANG->getLL( 'ViewCheckForBrokenDownloads_online' );

		return $content;
	}

	/**
	 * getViewImportCategoriesDownloadsDB( $type = null, $table = null, $generateQuery = null )
	 *
	 * Imports categories and downloads from DB.
	 *
	 * @param	string		$type	What type of record to import.
	 * @param	string		$table	The table name if selected.
	 * @param	string		$generateQuery	If the generateQuery button was pressed.
	 * @return	string		The generated HTML source for this view.
	 */
	function getViewImportCategoriesDownloadsDB( $type = null, $table = null, $generateQuery = null ) {
		global $LANG;

		// Init some vars
		$content = null;
		$doc = get_object_vars( $this->doc );

		// Output header and description
		$content .= $LANG->getLL( 'ViewImportCategoriesDownloadsDB_text' ) . '<br /><br />';

		// Step 1: Show type form
		$types = array( '', 'categories', 'downloads' );

		$content .= '<img src="import_step_1.gif" border="0" /> <b>Select what you want to import:</b><br />';
		$content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST"><select name="type" onchange="jumpToUrl(\'?id=' . $this->id . '&amp;type=\'+this.options[this.selectedIndex].value,this);">';
		foreach( $types as $typesType ) {
			if( $type == $typesType ) {
				$content .= '<option value="' . $typesType  . '" selected="selected">' . $typesType . '</option>';
			} else {
				$content .= '<option value="' . $typesType  . '">' . $typesType . '</option>';
			}
		}

		$content .= '</select></form><br /><br />';

		// Step 2: Show table form
		if( $type != null ) {
			$tableBlacklist = array( 'sys_', 'cache_', 'fe_', 'be_', 'index_', 'static_', 'pages', 'tx_abdownloads' );

			$content .= '<img src="import_step_2.gif" border="0" /> <b>Select the foreign database table from which to import the ' . $type . ':</b><br />';
			$content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST"><select name="table" onchange="jumpToUrl(\'?id=' . $this->id . '&amp;type=' . $type . '&amp;table=\'+this.options[this.selectedIndex].value,this);">';
			$content .= '<option value=""></option>';

			$tables = $GLOBALS['TYPO3_DB']->admin_get_tables(TYPO3_db);
			foreach( $tables as $tableName => $tableStatus ) {
				$hide = false;

				foreach( $tableBlacklist as $index => $value ) {
					if( ereg( '^' . $value, $tableName ) )
						$hide = true;
				}

				if( !$hide ) {
					$content .= '<option value="' . $tableName;

					if( $tableName == $table ) {
						$content .= '" selected="selected">' . $tableName . '</option>';
					} else {
						$content .= '">' . $tableName . '</option>';
					}

					$content .= "\n";
				}
			}

			$content .= '</select></form><br /><br />';
		}

		// Step 3: Show fields form
		if( $type != null && $table != null ) {
			$fields = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'field' );
			$defines = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'define' );
			$fieldBlacklist = array( 't3ver_', 't3_' );

			$content .= '<img src="import_step_3.gif" border="0" /> <b>Assign the foreign database fields to the available database fields of this extension:</b><br />';
			$content .= '<div style="color:red;">Please notice: If you have already added "Modern Downloads" ' . $type . ' <b>don\'t</b> assign or define the "uid" field because then your ' . $type . ' could get overwritten!<br />Just leave the field blank in this case.</div>';
			$content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST"><table border="1"><tr><td><b>Database field</b></td><td><b>Foreign field</b></td><td><b>Manually defined value</b></td></tr>';

			$fieldTable = ( $type == 'categories' ) ? 'category' : 'download';
			$extensionFields = $GLOBALS['TYPO3_DB']->admin_get_fields( $this->tablePrefix . $fieldTable );

			foreach( $extensionFields as $row ) {
				$hide = false;

				foreach( $fieldBlacklist as $index => $value ) {
					if( ereg( '^' . $value, $row['Field'] ) )
						$hide = true;
				}

				if( !$hide ) {
					$content .= '<tr><td>' . $row['Field'] . '</td><td><select name="field[' . $row['Field'] . ']">';

					$foreignFields = $GLOBALS['TYPO3_DB']->admin_get_fields( $table );
					$content .= '<option value=""></option>';

					foreach( $foreignFields as $row2 ) {
						$content .= '<option value="' . $row2['Field'];

						if( $row2['Field'] == $fields[$row['Field']] ) {
							$content .= '" selected="selected">' . $row2['Field'] . '</option>';
						} else {
							$content .= '">' . $row2['Field'] . '</option>';
						}
					}

					$content .= '</select></td>';
					$content .= '<td><input type="text" name="define[' . $row['Field'] . ']"';
					if( $defines[$row['Field']] != null && $fields[$row['Field']] == null ) {
						 $content .= 'value="' . $defines[$row['Field']] . '" /></td></tr>';
					} else {
						 $content .= '/></td></tr>';
					}
					$content .= "\n";
				}
			}

			$content .= '</table><br />';
			$content .= '<input type="submit" name="generateQuery" value="Generate SQL query" />';
			$content .= '<input type="hidden" name="type" value="' . $type . '" />';
			$content .= '<input type="hidden" name="table" value="' . $table . '" />';
			$content .= '<input type="hidden" name="id" value="' . $this->id . '" />';
			$content .= '</form><br /><br />';
		}

		// Step 4: Show query
		if( $type != null && $table != null && $generateQuery != null ) {
			$content .= '<img src="import_step_4.gif" border="0" /> <b>Use the following SQL query to import the ' . $type . ' manually:</b><br />';

			$content .= '<div style="border:solid 1px;padding:5px;background-color:' . $doc['bgColor4'] . ';">INSERT INTO ' . $this->tablePrefix . $fieldTable . ' SELECT ';
			$i = 0;
			while( list( $extensionField, $foreignField ) = each( $fields ) ) {
				if( $foreignField != null ) {
					$content .= $foreignField . ' AS ' . $extensionField . ', ';
				} else {
					if( $defines[$extensionField] != null ) {
						$content .= $defines[$extensionField] . ' AS ' . $extensionField;
					} else {
						$content .= '\'\' AS ' . $extensionField;
					}

					if( $i != count( $fields ) - 1 ) {
						$content .= ', ';
					}
				}

				$i++;
			}

			$content .= ' FROM ' . $table . ';</div>';
		}

		return $content;
	}

	/**
	 * getViewImportCategoriesDownloadsCSV( $type = null, $importCSV = null, $overwriteExisting = null )
	 *
	 * Imports categories and downloads from CSV.
	 *
	 * @param	string		$type	What type of record to import.
	 * @param	string		$importCSV	If the importCSV button was pressed.
	 * @param	string		$overwriteExisting	If existing entries shall be overwritten.
	 * @return	string		The generated HTML source for this view.
	 */
	function getViewImportCategoriesDownloadsCSV( $type = null, $importCSV = null, $overwriteExisting = null ) {
		global $LANG;

		// Init some vars
		$content = null;
		$doc = get_object_vars( $this->doc );

		// Output header and description
		$content .= $LANG->getLL( 'ViewImportCategoriesLinksCSV_text' ) . '<br /><br />';

		// Step 1: Show type form
		$types = array( '', 'categories', 'downloads' );

		$content .= '<img src="import_step_1.gif" border="0" /> <b>Select what you want to import:</b><br />';
		$content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST"><select name="type" onchange="jumpToUrl(\'?id=' . $this->id . '&amp;type=\'+this.options[this.selectedIndex].value,this);">';
		foreach( $types as $typesType ) {
			if( $type == $typesType ) {
				$content .= '<option value="' . $typesType  . '" selected="selected">' . $typesType . '</option>';
			} else {
				$content .= '<option value="' . $typesType  . '">' . $typesType . '</option>';
			}
		}

		$content .= '</select></form><br /><br />';

		// Step 2: Show file form
		if( $type != null ) {
			$content .= '<img src="import_step_2.gif" border="0" /> <b>Select the CSV file from which to import the ' . $type . ':</b><br />';
			$content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST" enctype="multipart/form-data">';
			$content .= '<input type="checkbox" name="overwriteExisting" checked="checked"> Overwrite existing entries</input>';
			$content .= '<input type="file" name="file" size="50" /><br /><input type="submit" name="importCSV" value="Import" />';
			$content .= '<input type="hidden" name="id" value="' . $this->id . '" />';
			$content .= '<input type="hidden" name="type" value="' . $type . '" />';
			$content .= '</form><br /><br />';
		}

		// Step 3: Import
		if( $type != null && $importCSV != null ) {
			$fileFunc = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( \TYPO3\CMS\Core\Utility\File\BasicFileUtility::class );

			// Get file name and path
			$fileName = $fileFunc->cleanFileName( $_FILES['file']['name'] );

			if( $fileName ) {
				$uniqueFilePath = $fileFunc->getUniqueName( $fileName, PATH_site . $this->filePath );
				$uploadedTempFile = \TYPO3\CMS\Core\Utility\GeneralUtility::upload_to_tempfile( $_FILES['file']['tmp_name'] );

				// Read in CSV file
				$firstLine = true;
				$lines = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode( chr( 10 ), \TYPO3\CMS\Core\Utility\GeneralUtility::getUrl( $uploadedTempFile ), 1 );

				foreach( $lines as $line ) {
					$parts = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode( ',', str_replace( '"', '', $line ) );

					if( $firstLine ) {
						$fields = $parts;
						$firstLine = false;
					} else {
						$insertFields = array_combine( $fields, $parts );
						$fieldTable = ( $type == 'categories' ) ? 'category' : 'download';

						if( $fieldTable == 'download' ) {
							if( $this->existingEntry( $insertFields['uid'], $fieldTable ) && $overwriteExisting ) {
								$whereClause = 'uid=' . $insertFields['uid'];
								$GLOBALS['TYPO3_DB']->exec_UPDATEquery( $this->tablePrefix . $fieldTable, $whereClause, $insertFields );
								$content .= 'Download ' . $insertFields['uid'] . ' (' . $insertFields['file'] . ') successfully updated in the database.<br />';
							} else {
								$GLOBALS['TYPO3_DB']->exec_INSERTquery( $this->tablePrefix . $fieldTable, $insertFields );
								$content .= 'Download ' . $insertFields['uid'] . ' (' . $insertFields['file'] . ') successfully added to the database.<br />';
							}
						} elseif( $fieldTable == 'category' ) {
							if( $this->existingEntry( $insertFields['uid'], $fieldTable ) && $overwriteExisting ) {
								$whereClause = 'uid=' . $insertFields['uid'];
								$GLOBALS['TYPO3_DB']->exec_UPDATEquery( $this->tablePrefix . $fieldTable, $whereClause, $insertFields );
								$content .= 'Category ' . $insertFields['uid'] . ' (' . $insertFields['label'] . ') successfully updated in the database.<br />';
							} else {
								$GLOBALS['TYPO3_DB']->exec_INSERTquery( $this->tablePrefix . $fieldTable, $insertFields );
								$content .= 'Category ' . $insertFields['uid'] . ' (' . $insertFields['label'] . ') successfully added to the database.<br />';
							}
						}
					}
				}

				\TYPO3\CMS\Core\Utility\GeneralUtility::unlink_tempfile( $uploadedTempFile );
			}
		}

		return $content;
	}

	/**
	 * getViewExportCategoriesDownloads( $type = null, $properties = null, $outputFormat = null, $generateQuery = null )
	 *
	 * Exports categories and downloads.
	 *
	 * @param	string		$type	What type of record to export.
	 * @param	array		$properties	The properties if selected.
	 * @param	string		$outputFormat	The output format.
	 * @param	string		$generateOutput	If the generateOutput button was pressed.
	 * @return	string		The generated HTML source for this view.
	 */
	function getViewExportCategoriesDownloads( $type = null, $properties = null, $outputFormat = null, $generateOutput = null ) {
		global $LANG, $TYPO3_CONF_VARS;

		// Init some vars
		$content = null;
		$doc = get_object_vars( $this->doc );

		// Output JavaScript
		$content .= "<script>function selectAll(sel) {
				var checkboxes = document.getElementsByName('properties[]');
				for (var i = 0; i < checkboxes.length; i++) {
					document.getElementsByName('properties[]')[i].checked = sel;
				}
				return true;
				}</script>\n";

		// Output header and description
		$content .= $LANG->getLL( 'ViewExportCategoriesDownloads_text' ) . '<br /><br />';

		// Step 1: Show type form
		$types = array( '', 'categories', 'downloads' );

		$content .= '<img src="import_step_1.gif" border="0" /> <b>Select what you want to export:</b><br />';
		$content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST"><select name="type" onchange="jumpToUrl(\'?id=' . $this->id . '&amp;type=\'+this.options[this.selectedIndex].value,this);">';
		foreach( $types as $typesType ) {
			if( $type == $typesType ) {
				$content .= '<option value="' . $typesType  . '" selected="selected">' . $typesType . '</option>';
			} else {
				$content .= '<option value="' . $typesType  . '">' . $typesType . '</option>';
			}
		}

		$content .= '</select></form><br /><br />';

		// Step 2: Show properties form
		if( $type != null ) {
//			$propertyBlacklist = array( 'pid', 'image', 'l18n_parent', 'l18n_diffsource', 'sorting', 't3ver_oid', 't3ver_id', 't3ver_wsid', 't3ver_label', 't3ver_state', 't3ver_stage', 't3ver_count', 't3ver_tstamp', 't3_origuid' );
			$propertyBlacklist = array();

			$content .= '<img src="import_step_2.gif" border="0" /> <b>Select the properties you want to export:</b><br />';
			$content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST"><table border="1"><tr><td><b></b></td><td><b>Property</b></td></tr>';

			$fieldTable = ( $type == 'categories' ) ? 'category' : 'download';
			$fields = $GLOBALS['TYPO3_DB']->admin_get_fields( $this->tablePrefix . $fieldTable );
			foreach( $fields as $row ) {
				if( !\TYPO3\CMS\Core\Utility\GeneralUtility::inArray( $propertyBlacklist, $row['Field'] ) ) {
					$content .= '<tr><td><input type="checkbox" name="properties[]" value="' . $row['Field'];

					if( \TYPO3\CMS\Core\Utility\GeneralUtility::inList( $properties, $row['Field'] ) ) {
						$content .= '" checked="checked"></td><td>' . $row['Field'] . '</td></tr>';
					} else {
						$content .= '"></td><td>' . $row['Field'] . '</td></tr>';
					}

					$content .= "\n";
				}
			}

			$content .= '</table><br />';
			$content .= '<input type="submit" name="submitProperties" value="Submit properties" />';
			$content .= '<input type="submit" value="Select all" onClick="javascript:selectAll(true );">';
			$content .= '<input type="submit" value="Unselect all" onClick="javascript:selectAll(false );">';
			$content .= '<input type="hidden" name="id" value="' . $this->id . '" />';
			$content .= '<input type="hidden" name="type" value="' . $type . '" />';
			$content .= '</form><br /><br />';
		}

		// Step 3: Show output format form
		if( $type != null && $properties != null ) {
			$outputTypes = array( '', 'HTML', 'XML', 'TXT', 'CSV' );

			$content .= '<img src="import_step_3.gif" border="0" /> <b>Select the designated output format:</b><br />';
			$content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST"><select name="outputFormat" onchange="jumpToUrl(\'?id=' . $this->id . '&amp;type=' . $type . '&amp;properties=' . $properties . '&amp;outputFormat=\'+this.options[this.selectedIndex].value,this);">';
			foreach( $outputTypes as $typesType ) {
				if( $outputFormat == $typesType ) {
					$content .= '<option value="' . $typesType  . '" selected="selected">' . $typesType . '</option>';
				} else {
					$content .= '<option value="' . $typesType  . '">' . $typesType . '</option>';
				}
			}

			$content .= '</select></form><br /><br />';

		}

		// Step 4: Output categories/downloads
		if( $type != null && $properties != null && $outputFormat != null ) {
			$content .= '<img src="import_step_4.gif" border="0" /> <b>Export all ' . $type . ' to ' . $outputFormat . ' format:</b><br />';
			$content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST">';

			$content .= '<input type="submit" name="generateOutput" value="Export" />';
			$content .= '<input type="hidden" name="id" value="' . $this->id . '" />';
			$content .= '<input type="hidden" name="type" value="' . $type . '" />';
			$content .= '<input type="hidden" name="properties" value="' . $properties . '" />';
			$content .= '<input type="hidden" name="outputFormat" value="' . $outputFormat . '" />';
			$content .= '</form><br /><br />';

		}

		// Step 5: Generate output
		if( $type != null && $properties != null && $outputFormat != null && $generateOutput != null ) {
			$identifier = 'ab_downloads_' . $type . '_' . \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv( 'HTTP_HOST' ) . '_' . date( $TYPO3_CONF_VARS['SYS']['ddmmyy'] );

			header( 'Content-Description: Modern Downloads File Transfer' );
			header( 'Content-type: application/force-download' );
			header( 'Content-Disposition: attachment; filename="' . $identifier . '.' . strtolower( $outputFormat ) . '"' );

			$output = $this->generateOutput( $type, $properties, $outputFormat, $identifier );

//			header( 'Content-Length: ' . sizeof( $output ) );
			echo $output;

			exit;
		}

		return $content;
	}

	/**
	 * getViewStatistics()
	 *
	 * Shows some statistics
	 *
	 * @return	string		The generated HTML source for this view.
	 */
	function getViewStatistics() {
		global $LANG;

		// Init some vars
		$content = null;
		$doc = get_object_vars( $this->doc );

		// Output header and description
		$content .= $LANG->getLL( 'ViewStatistics_text' ) . '<br /><br />';

		// Prepare variables
		$categoriesCount = 0;
		$categoriesDeletedCount = 0;
		$categoriesHiddenCount = 0;

		$downloadsCount = 0;
		$downloadsClicksCount = 0;
		$downloadsVotesCount = 0;
		$downloadsDeletedCount = 0;
		$downloadsHiddenCount = 0;
		$downloadsStageEditingCount = 0;
		$downloadsStageReviewCount = 0;
		$downloadsStagePublishCount = 0;
		$downloadsPublishedCount = 0;
		$downloadsPendingCount = 0;
		$downloadsApprovedCount = 0;
		$downloadsReportedBrokenCount = 0;
		$downloadsDisabledCount = 0;

		// Get category statistics from database
		$theTable = $this->tablePrefix . 'category';
		$whereClause = 'sys_language_uid IN (-1,0)';
		$groupBy = '';
		$orderBy = '';
		$limit = '';
		$categories = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows( '*', $theTable, $whereClause, $groupBy, $orderBy, $limit );
		$categoriesCount = count( $categories );

		// Get download statistics from database
		$theTable = $this->tablePrefix . 'download';
		$whereClause = 'sys_language_uid IN (-1,0)';
		$groupBy = '';
		$orderBy = '';
		$limit = '';
		$downloads = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', $theTable, $whereClause, $groupBy, $orderBy, $limit );

		/**
		 * Calculate absolute values
		 */

		for( $i = 0; $i < $categoriesCount; $i++ ) {
			if( $categories[$i]['deleted'] == 1 ) {
				$categoriesDeletedCount++;
			}

			if( $categories[$i]['hidden'] == 1 ) {
				$categoriesHiddenCount++;
			}
		}

		while ( $download = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $downloads ) ) {
			$downloadsClicksCount += $download['clicks'];
			$downloadsVotesCount += $download['votes'];

			if( $download['pid'] != -1 ) {
				$downloadsCount++;
			}

			if( $download['deleted'] == 1 ) {
				$downloadsDeletedCount++;
			}

			if( $download['hidden'] == 1 ) {
				$downloadsHiddenCount++;
			}

			if( $download['t3ver_stage'] == 0 && $download['pid'] == -1 ) {
				$downloadsStageEditingCount++;
			}

			if( $download['t3ver_stage'] == 1 ) {
				$downloadsStageReviewCount++;
			}

			if( $download['t3ver_stage'] == 10 ) {
				$downloadsStagePublishCount++;
			}

			if( $download['t3ver_stage'] == 0 && $download['pid'] == 0 ) {
				$downloadsPublishedCount++;
			}

			if( $download['status'] == 0 && $download['deleted'] == 0 && $download['hidden'] == 0 ) {
				$downloadsPendingCount++;
			}

			if( $download['status'] == 1 && $download['deleted'] == 0 && $download['hidden'] == 0 ) {
				$downloadsApprovedCount++;
			}

			if( $download['status'] == 2 && $download['deleted'] == 0 && $download['hidden'] == 0 ) {
				$downloadsReportedBrokenCount++;
			}

			if( $download['status'] == 3 && $download['deleted'] == 0 && $download['hidden'] == 0 ) {
				$downloadsDisabledCount++;
			}
		}

		/**
		 * Calculate percent values
		 */

		$categoriesDeletedPercent = $this->calculatePercent( $categoriesDeletedCount, $categoriesCount );
		$categoriesHiddenPercent = $this->calculatePercent( $categoriesHiddenCount, $categoriesCount );

		$downloadsDeletedPercent = $this->calculatePercent( $downloadsDeletedCount, $downloadsCount );
		$downloadsHiddenPercent = $this->calculatePercent( $downloadsHiddenCount, $downloadsCount );
		$downloadsStageEditingPercent = $this->calculatePercent( $downloadsStageEditingCount, $downloadsCount );
		$downloadsStageReviewPercent = $this->calculatePercent( $downloadsStageReviewCount, $downloadsCount );
		$downloadsStagePublishPercent = $this->calculatePercent( $downloadsStagePublishCount, $downloadsCount );
		$downloadsPublishedPercent = $this->calculatePercent( $downloadsPublishedCount, $downloadsCount );
		$downloadsPendingPercent = $this->calculatePercent( $downloadsPendingCount, $downloadsCount );
		$downloadsApprovedPercent = $this->calculatePercent( $downloadsApprovedCount, $downloadsCount );
		$downloadsReportedBrokenPercent = $this->calculatePercent( $downloadsReportedBrokenCount, $downloadsCount );
		$downloadsDisabledPercent = $this->calculatePercent( $downloadsDisabledCount, $downloadsCount );

		/**
		 * Output results
		 */

		$content .= '<table>';
		$content .= '<tr bgcolor="' . $doc['bgColor4'] . '"><td colspan="3"><img src="statistics.gif" border="0" /> <b>Categories</b></td></tr>';
		$content .= '<tr><td style="padding-left:20px;padding-right:20px;">' . $LANG->getLL( 'ViewStatistics_categoriesCount' ) . '</td><td colspan="2" style="padding-left:20px;padding-right:20px;"><b>' . $categoriesCount . '</b></td></tr>';
		$content .= '<tr><td colspan="2" style="height:10px;"></td></tr>';
		$content .= '<tr><td style="padding-left:20px;padding-right:20px;">' . $LANG->getLL( 'ViewStatistics_categoriesDeletedCount' ) . '</td><td style="padding-left:20px;padding-right:20px;"><b>' . $categoriesDeletedCount . '</b></td><td style="padding-left:20px;padding-right:20px;">' . $categoriesDeletedPercent . '</td></tr>';
		$content .= '<tr><td style="padding-left:20px;padding-right:20px;">' . $LANG->getLL( 'ViewStatistics_categoriesHiddenCount' ) . '</td><td style="padding-left:20px;padding-right:20px;"><b>' . $categoriesHiddenCount . '</b></td><td style="padding-left:20px;padding-right:20px;">' . $categoriesHiddenPercent . '</td></tr>';
		$content .= '</table>';

		$content .= '<br />';

		$content .= '<table>';
		$content .= '<tr bgcolor="' . $doc['bgColor4'] . '"><td colspan="3"><img src="statistics_downloads.gif" border="0" /> <b>Downloads</b></td></tr>';
		$content .= '<tr><td style="padding-left:20px;padding-right:20px;">' . $LANG->getLL( 'ViewStatistics_downloadsCount' ) . '</td><td colspan="2" style="padding-left:20px;padding-right:20px;"><b>' . $downloadsCount . '</b></td></tr>';
		$content .= '<tr><td style="padding-left:20px;padding-right:20px;">' . $LANG->getLL( 'ViewStatistics_downloadsClicksCount' ) . '</td><td colspan="2" style="padding-left:20px;padding-right:20px;"><b>' . $downloadsClicksCount . '</b></td></tr>';
		$content .= '<tr><td style="padding-left:20px;padding-right:20px;">' . $LANG->getLL( 'ViewStatistics_downloadsVotesCount' ) . '</td><td colspan="2" style="padding-left:20px;padding-right:20px;"><b>' . $downloadsVotesCount . '</b></td></tr>';
		$content .= '<tr><td colspan="2" style="height:10px;"></td></tr>';
		$content .= '<tr><td style="padding-left:20px;padding-right:20px;">' . $LANG->getLL( 'ViewStatistics_downloadsDeletedCount' ) . '</td><td style="padding-left:20px;padding-right:20px;"><b>' . $downloadsDeletedCount . '</b></td><td style="padding-left:20px;padding-right:20px;">' . $downloadsDeletedPercent . '</td></tr>';
		$content .= '<tr><td style="padding-left:20px;padding-right:20px;">' . $LANG->getLL( 'ViewStatistics_downloadsHiddenCount' ) . '</td><td style="padding-left:20px;padding-right:20px;"><b>' . $downloadsHiddenCount . '</b></td><td style="padding-left:20px;padding-right:20px;">' . $downloadsHiddenPercent . '</td></tr>';
		$content .= '<tr><td colspan="2" style="height:10px;"></td></tr>';

		if( $this->versioningEnabled ) {
			$content .= '<tr><td style="padding-left:20px;padding-right:20px;">' . $LANG->getLL( 'ViewStatistics_downloadsStageEditingCount' ) . '</td><td style="padding-left:20px;padding-right:20px;"><b>' . $downloadsStageEditingCount . '</b></td><td style="padding-left:20px;padding-right:20px;">' . $downloadsStageEditingPercent . '</td></tr>';
			$content .= '<tr><td style="padding-left:20px;padding-right:20px;">' . $LANG->getLL( 'ViewStatistics_downloadsStageReviewCount' ) . '</td><td style="padding-left:20px;padding-right:20px;"><b>' . $downloadsStageReviewCount . '</b></td><td style="padding-left:20px;padding-right:20px;">' . $downloadsStageReviewPercent . '</td></tr>';
			$content .= '<tr><td style="padding-left:20px;padding-right:20px;">' . $LANG->getLL( 'ViewStatistics_downloadsStagePublishCount' ) . '</td><td style="padding-left:20px;padding-right:20px;"><b>' . $downloadsStagePublishCount . '</b></td><td style="padding-left:20px;padding-right:20px;">' . $downloadsStagePublishPercent . '</td></tr>';
			$content .= '<tr><td style="padding-left:20px;padding-right:20px;">' . $LANG->getLL( 'ViewStatistics_downloadsPublishedCount' ) . '</td><td style="padding-left:20px;padding-right:20px;"><b>' . $downloadsPublishedCount . '</b></td><td style="padding-left:20px;padding-right:20px;">' . $downloadsPublishedPercent . '</td></tr>';
		}

		$content .= '<tr><td colspan="2" style="height:10px;"></td></tr>';
		$content .= '<tr><td style="padding-left:20px;padding-right:20px;">' . $LANG->getLL( 'ViewStatistics_downloadsPendingCount' ) . '</td><td style="padding-left:20px;padding-right:20px;"><b>' . $downloadsPendingCount . '</b></td><td style="padding-left:20px;padding-right:20px;">' . $downloadsPendingPercent . '</td></tr>';
		$content .= '<tr><td style="padding-left:20px;padding-right:20px;">' . $LANG->getLL( 'ViewStatistics_downloadsApprovedCount' ) . '</td><td style="padding-left:20px;padding-right:20px;"><b>' . $downloadsApprovedCount . '</b></td><td style="padding-left:20px;padding-right:20px;">' . $downloadsApprovedPercent . '</td></tr>';
		$content .= '<tr><td style="padding-left:20px;padding-right:20px;">' . $LANG->getLL( 'ViewStatistics_downloadsReportedBrokenCount' ) . '</td><td style="padding-left:20px;padding-right:20px;"><b>' . $downloadsReportedBrokenCount . '</b></td><td style="padding-left:20px;padding-right:20px;">' . $downloadsReportedBrokenPercent . '</td></tr>';
		$content .= '<tr><td style="padding-left:20px;padding-right:20px;">' . $LANG->getLL( 'ViewStatistics_downloadsDisabledCount' ) . '</td><td style="padding-left:20px;padding-right:20px;"><b>' . $downloadsDisabledCount . '</b></td><td style="padding-left:20px;padding-right:20px;">' . $downloadsDisabledPercent . '</td></tr>';
		$content .= '</table>';

		return $content;
	}

	/*************************************
	 *
	 * Action functions
	 *
	 *************************************/

	/**
	 * doAcceptDownload( $uid = null )
	 *
	 * Updates the status of a download in the database to '1' (--> Approved ).
	 * So the download is displayed after that.
	 *
	 * @param	integer		$uid	UID of the download record.
	 * @return	void
	 */
	function doAcceptDownload( $uid = null ) {
		if( $uid != null ) {

			// Get download from database
			$theTable = $this->tablePrefix . 'download';
			$theField = 'uid';
			$theValue = $uid;
			$whereClause = 'AND sys_language_uid IN (-1,0) AND deleted=0 AND hidden=0';
			$groupBy = '';
			$orderBy = '';
			$limit = '';
			$download = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows( 'label,description,file,category', $theTable, $theField . '=' . $GLOBALS['TYPO3_DB']->quoteStr( $theValue, $theTable ) . ' ' . $whereClause, $groupBy, $orderBy, $limit );

			// Get variables from POST values or database record
			$label = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'label' ) ? \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'label' ) : $download[0]['label'];
			$description = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'description' ) ? \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'description' ) : $download[0]['description'];
			$file = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'file' ) ? \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'file' ) : $download[0]['file'];
			$categoryUID = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'categoryUID' ) ? \TYPO3\CMS\Core\Utility\GeneralUtility::_GP( 'categoryUID' ) : $download[0]['category'];

			if( $this->debug ) {
				echo urldecode( $_SERVER['QUERY_STRING'] ) . "<br />";
				echo "uid: " . $uid . "<br />";
				echo "label: " . $label . "<br />";
				echo "description: " . $description . "<br />";
				echo "file: " . $file . "<br />";
				echo "categoryUID: " . $categoryUID . "<br />";
				t3lib_utility_Debug::printArray( $download );
			}

			// Update the download record
			$whereClause = "uid=$uid";
			$updateFields = array(
					'label' => $label,
					'description' => $description,
					'file' => $file,
					'status' => '1',
					'category' => $categoryUID,
			);
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery( $this->tablePrefix . 'download', $whereClause, $updateFields );
		}
	}

	/**
	 * doDisableDownload( $uid = null )
	 *
	 * Updates the status of a download in the database to '3' (--> Disabled ).
	 * So the download will not be displayed any further.
	 *
	 * @param	integer		$uid	UID of the download record.
	 * @return	void
	 */
	function doDisableDownload( $uid = null ) {
		if( $uid != null ) {
			// Update the download record
			$theTable = $this->tablePrefix . 'download';
			$whereClause = "uid=$uid";
			$updateFields = array(
					'status' => '3',
			);
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery( $theTable, $whereClause, $updateFields );
		}
	}

	/**
	 * doDeleteDownload( $uid = null )
	 *
	 * Deletes the download with given UID by setting deleted to "1" in the database.
	 *
	 * @param	integer		$uid	UID of the download record.
	 * @return	void
	 */
	function doDeleteDownload( $uid = null ) {
		if( $uid != null ) {
			// Delete download from database
			$theTable = $this->tablePrefix . 'download';
			$whereClause = "uid=$uid";
			$updateFields = array(
					'deleted' => '1',
			);
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery( $theTable, $whereClause, $updateFields );
		}
	}

	/**
	 * doCheckDownload( $file )
	 *
	 * Checks if a download is online or offline.
	 *
	 * @param	string		The download to check.
	 * @return	boolean		TRUE if online, FALSE otherwise.
	 */
	function doCheckDownload( $file ) {
		if( $file == null ) {
			return false;
		}

		return file_exists( \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName( $this->filePath . $file ) );
	}

	/*************************************
	 *
	 * Helper functions
	 *
	 *************************************/

	/**
	 * This is a copy of the function pi_list_browseresults from class.\TYPO3\CMS\Frontend\Plugin\AbstractPlugin.php
	 * Returns a results browser. This means a bar of page numbers plus a "previous" and "next" download. For each entry in the bar the piVars "$pointerName" will be pointing to the "result page" to show.
	 * Using $this->piVars['$pointerName'] as pointer to the page to display
	 * Using $this->internal['res_count'], $this->internal['results_at_a_time'] and $this->internal['maxPages'] for count number, how many results to show and the max number of pages to include in the browse bar.
	 *
	 * @param	boolean		If set (default) the text "Displaying results... " will be show, otherwise not.
	 * @param	string		Attributes for the table tag which is wrapped around the table cells containing the browse downloads
	 * @param	string		varname for the pointer
	 * @return	string		Output HTML, wrapped in <div>-tags with a class attribute
	 */
	function makePageBrowser( $showResultCount = 1, $tableParams = '', $pointerName = 'pointer' ) {
		global $LANG;

		// Initializing variables:
		$pointer = intval( $_GET[$pointerName] );
		$count = $this->internal['res_count'];
		$results_at_a_time = t3lib_utility_Math::forceIntegerInRange( $this->internal['results_at_a_time'], 1, 1000);
		$maxPages = t3lib_utility_Math::forceIntegerInRange( $this->internal['maxPages'], 1, 100);
		$max = t3lib_utility_Math::forceIntegerInRange( ceil( $count/$results_at_a_time), 1, $maxPages );
		$downloads = array();

		// Make browse-table/downloads:
		if( $pointer > 0 ) {
			$downloads[] = '<td nowrap="nowrap"><p><a href="?id=' . $this->id . '&amp;' . $pointerName . '=' . ( $pointer - 1 ? $pointer - 1 : '' ) . '">&laquo;</a></p></td>';
		} elseif( $this->pi_alwaysPrev ) {
			$downloads[] = '<td nowrap="nowrap"><p>Previous</p></td>';
		}

		for( $a = 0; $a < $max; $a++ ) {
			if( $pointer != $a ) {
				$downloads[] = '<td nowrap="nowrap"><p><a href="?id=' . $this->id . '&amp;' . $pointerName . '=' . ( $a ? $a : '' ) . '">' . ( $a + 1 ) . '</a></p></td>';
			} else {
				$downloads[] = '<td nowrap="nowrap"><p>' . ( $a + 1 ) . '</p></td>';
			}
		}

		if( $pointer < ceil( $count/$results_at_a_time ) - 1 ) {
			$downloads[] = '<td nowrap="nowrap"><p><a href="?id=' . $this->id . '&amp;' . $pointerName . '=' . ( $pointer + 1 ) . '">&raquo;</a></p></td>';
		}

		$pR1 = $pointer * $results_at_a_time + 1;
		$pR2 = $pointer * $results_at_a_time + $results_at_a_time;
		$sTables = '
			<!--
			 List browsing box:
			-->
			<div>' . ( $showResultCount ? '<p>' .
				( $this->internal['res_count'] ?
				   sprintf(
				 str_replace( '###SPAN_BEGIN###', '<span>', $LANG->getLL( 'pi_list_browseresults_displays', 'Displaying results ###SPAN_BEGIN###%s to %s</span> out of ###SPAN_BEGIN###%s</span>' ) ),
				 $this->internal['res_count'] > 0 ? $pR1 : 0,
				 min(array( $this->internal['res_count'], $pR2) ),
				 $this->internal['res_count']
				) :
				$LANG->getLL( 'pi_list_browseresults_noResults', 'Sorry, no items were found. ' ) ) . '</p>' : '' ) . '

			   <' .trim( 'table ' . $tableParams ) . '>
				<tr>
				 ' .implode( '', $downloads ) . '
				</tr>
			   </table>
			  </div>';

		return $sTables;
	}

	/**
	 * generateOutput( $type = null, $properties = null, $outputFormat = null, $identifier = null )
	 *
	 * Generates the output in the designated format.
	 *
	 * @param	string		The type of database records to export.
	 * @param	string		The properties of the database records to export.
	 * @param	string		The output format to use.
	 * @param	string		The output identifier.
	 * @return	string		The output in the designated format.
	 */
	function generateOutput( $type = null, $properties = null, $outputFormat = null, $identifier = null ) {
		global $TYPO3_CONF_VARS;

		if( $type != null && $properties != null && $outputFormat != null && $identifier != null ) {

			// Get total number of downloads from database
			$theTable = $this->tablePrefix . 'download';
			$theField = 'status';
			$theValue = 'IN (1,2)';
			$whereClause = 'AND sys_language_uid IN (-1,0) AND deleted=0 AND hidden=0 AND pid!=-1';
			$groupBy = '';
			$orderBy = '';
			$limit = '';
			$downloads = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows( $properties, $theTable, $theField . ' ' . $theValue . $whereClause, $groupBy, $orderBy, $limit );

			$output = null;

			switch( $outputFormat ) {
				case 'HTML':
					$output .= '<?xml version="1.0" encoding="' . $TYPO3_CONF_VARS['BE']['forceCharset'] . '"?>
<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">';
					$output .= "\n<head>\n\t<title>Modern Downloads (ab_downloads) $type on " . \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv( 'HTTP_HOST' ) . " : $outputFormat export</title>";
					$output .= "\n\t<meta name=\"author\" content=\"Modern Downloads (ab_downloads) Export Generator\" />";
					$output .= "\n\t<meta name=\"DC.Creator\" content=\"Modern Downloads (ab_downloads) Export Generator\" />\n</head>\n<body>\n<h2>\n";

					$output .= t3lib_utility_Debug::viewArray( $downloads ) . "\n";

					$output .= "</h2>\n</body>\n</html>";
				break;

				case 'XML':
					$output .= \TYPO3\CMS\Core\Utility\GeneralUtility::array2xml_cs( $downloads, $identifier );
				break;

				case 'TXT':
					$output .= "=================================================================================\n";
					$output .= "Modern Downloads (ab_downloads) $type on " . \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv( 'HTTP_HOST' ) . " : $outputFormat export\n";
					$output .= "=================================================================================\n\n";

					for( $i = 0; $i < count( $downloads ); $i++ ) {
						$output .= utf8_decode( print_r( $downloads[$i], true ) );
						$output .= "---------------------------------------------------------------------------------\n";
					}
				break;

				case 'CSV':
					$output .= \TYPO3\CMS\Core\Utility\GeneralUtility::csvValues( \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode( ',', $properties ) ) . "\n";

					for( $i = 0; $i < count( $downloads ); $i++ ) {
						$output .= \TYPO3\CMS\Core\Utility\GeneralUtility::csvValues( $downloads[$i] ) . "\n";
					}
				break;
			}

			return $output;
		}
	}

	/**
	 * calculatePercent( $count, $totalCount )
	 *
	 * Calculates a percent value for a value in relation to another value.
	 *
	 * @param	string		The value to consider.
	 * @param	string		The value with the total count.
	 * @return	string		The percent value and the percent sign.
	 */
	function calculatePercent( $count, $totalCount ) {
		if( !$totalCount ) {
			$totalCount = 1;
		}

		return round( $count * 100 / $totalCount, 2 ) . '%';
	}

	/**
	 * getTemplateField( $field, $string )
	 *
	 * Returns the value of the given string considering the given field of the sys_template table.
	 *
	 * @param	string		The field to consider.
	 * @return	string		The value of the string.
	 */
	function getTemplateField( $field, $string ) {
		// Get template config
		$theTable = 'sys_template';
		$whereClause = 'pid=' . $this->id . ' AND deleted=0';
		$groupBy = '';
		$orderBy = '';
		$limit = '';
		$config = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows( '*', $theTable, $whereClause, $groupBy, $orderBy, $limit );

		// Get constants
		$constants = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode( chr( 10 ), $config[0][$field], true );

		foreach( $constants as $index => $content ) {
			if( eregi( $string, $content ) ) {
				$contentExploded = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode( '=', $content, true );

				return ltrim( $contentExploded[1] );
			}
		}
	}

	/**
	 * existingEntry( $uid = null, $table = null )
	 *
	 * Checks if the given uid of an entry is already contained in the database.
	 *
	 * @param	string		$uid	The uid of the entry to check.
	 * @param	string		$table	The table name if selected.
	 * @return	boolean		TRUE if uid is contained, FALSE otherwise
	 */
	function existingEntry( $uid = null, $table = null ) {

		// Query database for link
		$databaseTable = $this->tablePrefix . $table;
		$whereClause = 'uid=' . $uid;
		$groupBy = '';
		$orderBy = '';
		$limit = '';
		$linkResult = $GLOBALS['TYPO3_DB']->exec_SELECTquery( '*', $databaseTable, $whereClause, $groupBy, $orderBy, $limit );

		if( mysql_num_rows( $linkResult ) > 0 ) {
			return true;
		}

		return false;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ab_downloads/mod1/index.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ab_downloads/mod1/index.php']);
}

// Make instance:
$SOBE = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance( 'tx_abdownloads_module1' );
$SOBE->init();

// Include files?
reset( $SOBE->include_once);
while( list(, $INC_FILE) = each( $SOBE->include_once) ) { include_once( $INC_FILE); }

$SOBE->main();
$SOBE->printContent();
?>
