<?php
/***************************************************************
* Copyright notice
*
* (c) 2005 - 2007 Andreas Bulling (typo3@andreas-bulling.de)
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
 *   47: class ext_update
 *   54:     function main()
 *  144:     function access( $what = 'all' )
 *  180:     function query( $updateWhat )
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

/**
 * Class for updating ab_downloads content elements.
 *
 * $Id: class.ext_update.php 120 2007-07-15 13:56:17Z andreas $
 *
 * @author	Andreas Bulling <typo3@andreas-bulling.de>
 * @package	TYPO3
 * @subpackage	tx_abdownloads
 */
class ext_update
{
    /**
     * Main function, returning the HTML content of the module
     *
     * @return	string		HTML
     */
    public function main()
    {
        $res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($this->query('downloads'));

        if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
            $countDownloads = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
        }

        $res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($this->query('categories'));

        if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
            $countCategories = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
        }

        $res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($this->query('categoriesMM'));

        if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
            $countCategoriesMM = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
        }

        if (!\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('do_update')) {
            $onClick = "document.location='" . \TYPO3\CMS\Core\Utility\GeneralUtility::linkThisScript(array( 'do_update' => 1 )) . "'; return false;";

            if ($countDownloads) {
                $returnThis = '<b>' . $countDownloads . ' downloads should be updated to reflect the changes concerning versioning of downloads and workspaces.</b><br /><br />';
            }

            if ($countCategories) {
                $returnThis .= '<b>' . $countCategories . ' categories should be updated to reflect the changes concerning the new database scheme.</b><br /><br />';
            }

            if ($countCategoriesMM) {
                $returnThis .= '<b>' . $countCategoriesMM . ' category relations should be updated to reflect the changes concerning the new database scheme.</b><br /><br />';
            }

            $returnThis .= '<b>Do you want to perform this update, now?</b><br /><br /><form action=""><input type="submit" value="DO IT" onclick="' . htmlspecialchars($onClick) . '"></form>';

            return $returnThis;
        } elseif ($countDownloads || $countCategories || $countCategoriesMM) {
            if ($countDownloads) {
                $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_abdownloads_download', 't3ver_id=0 AND t3ver_state=0', array( 't3ver_id' => 1 ));
                $returndoupdate = $countDownloads . ' row(s) updated.<br /><br />';
            }

            if ($countCategories) {
                $GLOBALS['TYPO3_DB']->sql_query('UPDATE tx_abdownloads_category SET parent_category=catuid_before');
                if (!mysql_errno()) {
                    $GLOBALS['TYPO3_DB']->sql_query('ALTER TABLE tx_abdownloads_category DROP catuid_before');
                }
                $returndoupdate .= $countCategories . ' row(s) updated.<br /><br />';
            }

            if ($countCategoriesMM) {
                // Copy tx_abdownloads_category_catuid_before_mm table
                $GLOBALS['TYPO3_DB']->sql_query('INSERT INTO tx_abdownloads_category_mm SELECT * FROM tx_abdownloads_category_catuid_before_mm');
                if (!mysql_errno()) {
                    $GLOBALS['TYPO3_DB']->sql_query('DROP TABLE tx_abdownloads_category_catuid_before_mm');
                }

                // Update tx_abdownloads_category_mm table
                $records = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_abdownloads_download, tx_abdownloads_category_mm', 'tx_abdownloads_download.uid=tx_abdownloads_category_mm.uid_local', '', '', '');
                while ($record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($records)) {
                    if ($record['category'] != $record['uid_foreign']) {
                        $GLOBALS['TYPO3_DB']->sql_query('UPDATE tx_abdownloads_category_mm SET uid_foreign=' . $record['category'] . ' WHERE uid_local=' . $record['uid'] . '');
                    }
                }

                // Fix records with missing tx_abdownloads_category_catuid_before_mm entry
                $records = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_abdownloads_download', '1=1', '', '', '');
                while ($record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($records)) {
                    $res7 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_abdownloads_category_mm', 'uid_local=' . $record['uid'], '', '', '');
                    if ($res7 && !$GLOBALS['TYPO3_DB']->sql_num_rows($res7)) {
                        $insertFields = array(
                            'uid_local' => $record['uid'],
                            'uid_foreign' => $record['category'],
                            'sorting' => 1,
                            );
                        $result = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_abdownloads_category_mm', $insertFields);
                    }
                }

                $returndoupdate .= $countCategoriesMM . ' row(s) inserted/updated.<br /><br />';
            }

            return $returndoupdate;
        }
    }

    /**
     * Checks how many rows are found and returns true if there are any
     * (this function is called from the extension manager)
     *
     * @param	string		$what: what should be updated
     * @return	boolean
     */
    public function access($what = 'all')
    {
        if ($what = 'all') {
            if (is_object($GLOBALS['TYPO3_DB'])) {
                if (in_array('tx_abdownloads_download', $GLOBALS['TYPO3_DB']->admin_get_tables()) && array_key_exists('t3ver_id', $GLOBALS['TYPO3_DB']->admin_get_fields('tx_abdownloads_download'))) {
                    $res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($this->query('downloads'));

                    if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
                        return true;
                    }
                }

                if (in_array('tx_abdownloads_category', $GLOBALS['TYPO3_DB']->admin_get_tables()) && array_key_exists('catuid_before', $GLOBALS['TYPO3_DB']->admin_get_fields('tx_abdownloads_category'))) {
                    $res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($this->query('categories'));

                    if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
                        return true;
                    }
                }

                if (in_array('tx_abdownloads_category_catuid_before_mm', $GLOBALS['TYPO3_DB']->admin_get_tables())) {
                    $res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($this->query('categoriesMM'));

                    if ($res && $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
                        return true;
                    }
                }
            }
        }
    }

    /**
     * Creates query finding all ab_downloads elements which need to be updated.
     *
     * @param	string		$updateWhat: determines which query should be returned
     * @return	string		Full query
     */
    public function query($updateWhat)
    {
        if ($updateWhat == 'downloads') {
            $query = array(
                'SELECT' => '*',
                'FROM' => 'tx_abdownloads_download',
                'WHERE' => 't3ver_id=0 AND t3ver_state=0',
                'GROUPBY' => '');
            return $query;
        } elseif ($updateWhat == 'categories') {
            $query = array(
                'SELECT' => '*',
                'FROM' => 'tx_abdownloads_category',
                'WHERE' => 'parent_category!=catuid_before',
                'GROUPBY' => '');
            return $query;
        } elseif ($updateWhat == 'categoriesMM') {
            $query = array(
                'SELECT' => '*',
                'FROM' => 'tx_abdownloads_category_catuid_before_mm',
                'WHERE' => '1=1',
                'GROUPBY' => '');
            return $query;
        }
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ab_downloads/class.ext_update.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ab_downloads/class.ext_update.php']);
}
