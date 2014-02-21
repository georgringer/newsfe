<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Georg Ringer <typo3@ringerge.org>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Utility class for checking access to news
 *
 * @package TYPO3
 * @subpackage tx_newsfe
 */
class Tx_Newsfe_Utility_Access {

	/**
	 * Check if a news item can be edited by current user
	 *
	 * @param Tx_News_Domain_Model_News $news
	 * @return boolean
	 */
	public static function check(Tx_News_Domain_Model_News $news) {
		$status = FALSE;

		$feUser = $GLOBALS['TSFE']->fe_user->user;
		if (is_array($feUser)) {
			$feUserOfNews = (int)$news->getTxNewsfeFeuser();
			if ($feUserOfNews != 0 && (int) $feUser['uid'] === $feUserOfNews) {
				$status = TRUE;
			}
		}

		$signalSlotDispatcher = t3lib_div::makeInstance('Tx_Extbase_SignalSlot_Dispatcher');
		$signalSlotDispatcher->dispatch(__CLASS__, 'check', array('news' => $news, 'status' => $status), TRUE);

		return $status;
	}

	/**
	 * Get current feuser
	 *
	 * @return int|null
	 */
	public static function getFeUserUid() {
		$feUser = $GLOBALS['TSFE']->fe_user->user;
		if (!is_array($feUser)) {
			return NULL;
		}
		return (int) $feUser['uid'];
	}

}

?>