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
 *   46: class tx_abdownloads_pi1_wizicon
 *   54:     function proc( $wizardItems)
 *   74:     function includeLocalLang()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

/**
 * Class that adds the wizard icon.
 *
 * $Id: class.tx_abdownloads_pi1_wizicon.php 120 2007-07-15 13:56:17Z andreas $
 *
 * @author    Andreas Bulling <typo3@andreas-bulling.de>
 * @package    TYPO3
 * @subpackage    tx_abdownloads
 */
class tx_abdownloads_pi1_wizicon
{
    /**
     * Adds the ab_downloads wizard icon
     *
     * @param    array        Input array with wizard items for plugins.
     * @return    array        Modified input array, having the item for ab_downloads added.
     */
    public function proc($wizardItems)
    {
        global $LANG;

        $LL = $this->includeLocalLang();

        //		$wizardItems['plugins_tx_abdownloads_pi1'] = [
        //			'icon'        => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('ab_downloads') . "pi1/ce_wiz.gif",
        //			'title'       => $LANG->getLLL('pi1_title', $LL),
        //			'description' => $LANG->getLLL('pi1_plus_wiz_description', $LL),
        //			'params'      => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=ab_downloads_pi1',
        //		];

        return $wizardItems;
    }

    /**
     * Includes the locallang file for the 'ab_downloads' extension
     *
     * @return    array        The LOCAL_LANG array
     */
    public function includeLocalLang()
    {
        $llFile = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('ab_downloads') . 'locallang.xml';
        $languageFactory = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Localization\LocalizationFactory::class);
        $LOCAL_LANG = $languageFactory->getParsedData($llFile, $GLOBALS['LANG']->lang);

        return $LOCAL_LANG;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ab_downloads/pi1/class.tx_abdownloads_pi1_wizicon.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ab_downloads/pi1/class.tx_abdownloads_pi1_wizicon.php']);
}
