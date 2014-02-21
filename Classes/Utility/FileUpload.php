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
 * Utility class for uploading files
 *
 * @package TYPO3
 * @subpackage tx_newsfe
 */
class Tx_Newsfe_Utility_FileUpload {

	public static function upload($identifier, $formName = 'tx_newsfe_edit', $path = 'uploads/tx_news/') {
		if ($_FILES[$formName]) {
			$path = rtrim($path, '/') . '/';
			$identifier = explode('|', $identifier);

			$basicFileFunctions = t3lib_div::makeInstance('t3lib_basicFileFunctions');

			$newFileName = $basicFileFunctions->getUniqueName(
					$_FILES[$formName]['name'][$identifier[0]][$identifier[1]], t3lib_div::getFileAbsFileName($path));
			$result = t3lib_div::upload_copy_move($_FILES[$formName]['tmp_name'][$identifier[0]][$identifier[1]], $newFileName);

			if ($result) {
				return basename($newFileName);
			}

			return FALSE;
		}
	}

}

?>