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
 * Utility class for tcemain commands like sorting
 *
 * @package TYPO3
 * @subpackage tx_newsfe
 */
class Tx_Newsfe_Utility_Tcemain {

	/**
	 * @var t3lib_TCEmain
	 */
	protected $tcemain;

	public function __construct() {
		if (!isset($this->tcemain)) {
			$this->tcemain = t3lib_div::makeInstance('t3lib_TCEmain');
			$this->tcemain->stripslashes_values = 0;
		}
	}


	/**
	 * Move a record
	 *
	 * @param	string		The table name for the record to move.
	 * @param	integer		The UID for the record to move.
	 * @param	string		The direction to move, either 'up' or 'down'.
	 * @param	integer		The UID of record to move after. This is specified for dragging only.
	 * @return	void
	 */
	public function move($table, $uid, $direction = '', $afterUID = 0) {
		$cmdData = array();
		$sortField = $GLOBALS['TCA'][$table]['ctrl']['sortby'];
		if ($sortField) {
				// Get self:
			$fields = array_unique(t3lib_div::trimExplode(',', $GLOBALS['TCA'][$table]['ctrl']['copyAfterDuplFields'] . ',uid,pid,' . $sortField, TRUE));
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(implode(',', $fields), $table, 'uid=' . $uid);
			if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$copyAfterFieldsQuery = '';
				if ($GLOBALS['TCA'][$table]['ctrl']['copyAfterDuplFields']) {
					$cAFields = t3lib_div::trimExplode(',', $GLOBALS['TCA'][$table]['ctrl']['copyAfterDuplFields'], TRUE);
					foreach ($cAFields as $fieldName) {
						$copyAfterFieldsQuery .= ' AND ' . $fieldName . '="' . $row[$fieldName] . '"';
					}
				}
				if (!empty($direction)) {
					if ($direction == 'up') {
						$operator = '<';
						$order = 'DESC';
					} else {
						$operator = '>';
						$order = 'ASC';
					}
					$sortCheck = ' AND ' . $sortField . $operator . intval($row[$sortField]);
				}
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'uid,pid',
					$table,
						'pid=' . intval($row['pid']) .
								$sortCheck .
								$copyAfterFieldsQuery .
								$GLOBALS['TSFE']->sys_page->enableFields($table, '', $ignore),
					'',
						$sortField . ' ' . $order,
					'2'
				);
				if ($row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					if ($afterUID) {
						$cmdData[$table][$uid]['move'] = -$afterUID;
					}
					elseif ($direction == 'down') {
						$cmdData[$table][$uid]['move'] = -$row2['uid'];
					}
					elseif ($row3 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) { // Must take the second record above...
						$cmdData[$table][$uid]['move'] = -$row3['uid'];
					}
					else { // ... and if that does not exist, use pid
						$cmdData[$table][$uid]['move'] = $row['pid'];
					}
				} elseif ($direction == 'up') {
					$cmdData[$table][$uid]['move'] = $row['pid'];
				}
			}
			if (!empty($cmdData)) {
				$this->tcemain->start(array(), $cmdData);
				$this->tcemain->process_cmdmap();
			}
		}
	}

}

?>