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
 * Viewhelper to get label of a field
 *
 * @package TYPO3
 * @subpackage tx_newsfe
 */
class Tx_Newsfe_ViewHelpers_LabelViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 *
	 * @param string $table
	 * @param string $field
	 * @param string $default
	 * @return string
	 */
	public function render($table, $field, $default = '') {
		$value = '';
		$table = 'tx_news_domain_model_' . $table;
		t3lib_div::loadTCA($table);
		$value = $GLOBALS['TCA'][$table]['columns'][$field]['label'];

		if (empty($value)) {
			return $default;
		}

		return Tx_Extbase_Utility_Localization::translate($value, 'newsfe');
	}
}

?>
