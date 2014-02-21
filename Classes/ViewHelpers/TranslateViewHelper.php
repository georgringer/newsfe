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
 * Viewhelper to translate by using existing locallang of EXT:news
 *
 * @package TYPO3
 * @subpackage tx_newsfe
 */
class Tx_Newsfe_ViewHelpers_TranslateViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Translate a given key or use the tag body as default.
	 *
	 * @param string $key The locallang key
	 * @param string $default if the given locallang key could not be found, this value is used. . If this argument is not set, child nodes will be used to render the default
	 * @param boolean $htmlEscape TRUE if the result should be htmlescaped. This won't have an effect for the default value
	 * @param array $arguments Arguments to be replaced in the resulting string
	 * @return string The translated key or tag body if key doesn't exist
	 */
	public function render($key, $default = NULL, $htmlEscape = TRUE, array $arguments = NULL) {
		$key = 'LLL:EXT:news/Resources/Private/Language/locallang_db.xml:tx_news_domain_model_' . $key;

		$value = Tx_Extbase_Utility_Localization::translate($key, 'newsfe', $arguments);
		if ($value === NULL) {
			$value = $default !== NULL ? $default : $this->renderChildren();
		} elseif ($htmlEscape) {
			$value = htmlspecialchars($value);
		}
		return $value;
	}
}

?>
