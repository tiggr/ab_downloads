<?php
/***************************************************************
* Copyright notice
*
* (c) 2005 - 2007 Andreas Bulling <typo3@andreas-bulling.de>
* All rights reserved
*
* This script is part of the TYPO3 project. The TYPO3 project is
* free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http:// www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * This example shows how you can substitute the built-in pagebrowser with your own pagebrowser script.
 * It uses the function userPageBrowserFunc() from the tx_abdownloads_pi1 class.
 *
 * $Id: example_userPageBrowserFunc.php 115 2007-07-14 08:53:38Z andreas $
 *
 * @author	Andreas Bulling	<typo3@andreas-bulling.de>
 */


/*
* This is a changed version of the pagebrowser function from class.pi_base (in TYPO3 version below 3.8.0).
*
* The differences are:
* 1. The values from get_LL are not parsed through htmlspecialchars(). So you can use
*    HTML-code for the "next" and "previous" downloads.
* 2. The caching behaviour of the pagebrowser downloads is now configurable with the TS-parameter "allowCaching"
*
* Example Configuration (add this to your TS setup):

# include the php script for the pageBrowser userfunction
includeLibs.userPageBrowserFunc = EXT:ab_downloads/res/example_userPageBrowserFunc.php

# call user function
plugin.tx_abdownloads_pi1.userPageBrowserFunc = user_substPageBrowser

plugin.tx_abdownloads_pi1 {
    # Example for overriding values from locallang.php with HTML-code displaying images instead of text
    _LOCAL_LANG.default {
        pi_list_browseresults_prev = <img src="typo3/gfx/pil2left.gif" border="0" height="12" width="7" alt="previous" title="previous">
        pi_list_browseresults_next = <img src="typo3/gfx/pil2right.gif" border="0" height="12" width="7" alt="next" title="next">
    }
}

*/

/**
 * Alternative pagebrowser function
 *
 * @param	array	$markerArray	Array
 * @param	array	$conf	Config
 * @return	array	Array with filled in pagebrowser marker
 */
function user_substPageBrowser($markerArray, $conf)
{
    $pObj = &$conf['parentObj'];

    // Initializing variables
    $showResultCount = $pObj->conf['pageBrowser.']['showResultCount'];
    $tableParams = $pObj->conf['pageBrowser.']['tableParams'];
    $pointer = $pObj->piVars['pointer'];
    $count = $pObj->internal['res_count'];
    $results_at_a_time = t3lib_utility_Math::forceIntegerInRange($pObj->internal['results_at_a_time'], 1, 1000);
    $maxPages = t3lib_utility_Math::forceIntegerInRange($pObj->internal['maxPages'], 1, 100);
    $max = t3lib_utility_Math::forceIntegerInRange(ceil($count / $results_at_a_time), 1, $maxPages);
    $pointer = intval($pointer);
    $action = $pObj->internal['action'];
    $categoryUID = $pObj->internal['category_uid'];
    $downloads = array();

    // Make browse-table/downloads:
    if ($pObj->pi_alwaysPrev >= 0) {
        if ($pointer > 0) {
            $downloads[] = '<td nowrap="nowrap"><p>' . $pObj->pi_downloadTP_keepPIvars($pObj->pi_getLL('pi_list_browseresults_prev', '< Previous'), array( 'pointer' => ($pointer - 1 ? $pointer - 1 : '') ), $pObj->allowCaching) . '</p></td>';
        } elseif ($pObj->pi_alwaysPrev) {
            $downloads[] = '<td nowrap="nowrap"><p>' . $pObj->pi_getLL('pi_list_browseresults_prev', '< Previous') . '</p></td>';
        }
    }

    for ($a = 0; $a < $max; $a++) {
        $downloads[] = '<td' . ($pointer == $a ? $pObj->pi_classParam('browsebox-SCell') : '') . ' nowrap="nowrap"><p>' . $pObj->pi_downloadTP_keepPIvars(trim($pObj->pi_getLL('pi_list_browseresults_page', 'Page') . ' ' . ($a + 1)), array( 'pointer' => ($a ? $a : '') ), $pObj->allowCaching) . '</p></td>';
    }

    if ($pointer < ceil($count / $results_at_a_time) - 1) {
        $downloads[] = '<td nowrap="nowrap"><p>' . $pObj->pi_downloadTP_keepPIvars($pObj->pi_getLL('pi_list_browseresults_next', 'Next >'), array( 'pointer' => $pointer + 1), $pObj->allowCaching) . '</p></td>';
    }

    $pR1 = $pointer * $results_at_a_time + 1;
    $pR2 = $pointer * $results_at_a_time + $results_at_a_time;
    $sTables = '

		<!--
			List browsing box:
		-->
		<div' . $pObj->pi_classParam('browsebox') . '>' . ($showResultCount ? '<p>' . ($pObj->internal['res_count'] ?
            sprintf(
                str_replace('###SPAN_BEGIN###', '<span' . $pObj->pi_classParam('browsebox-strong') . '>', $pObj->pi_getLL('pi_list_browseresults_displays', 'Displaying results ###SPAN_BEGIN###%s to %s</span> out of ###SPAN_BEGIN###%s</span>')),
                $pObj->internal['res_count'] > 0 ? $pR1 : 0,
                min(array($pObj->internal['res_count'], $pR2 )),
                $pObj->internal['res_count']
            ) :
            $pObj->pi_getLL('pi_list_browseresults_noResults', 'Sorry, no items were found.')) . '</p>' : '') . '
			<' . trim('table ' . $tableParams) . '><tr>' . implode('', $downloads) . '</tr></table></div>';

    $markerArray['###BROWSE_LINKS###'] = $sTables;

    return $markerArray;
}
