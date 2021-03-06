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
 * Validator for media elements
 *
 * @package TYPO3
 * @subpackage tx_newsfe
 */
class Tx_Newsfe_Domain_Validator_MediaValidator extends Tx_Newsfe_Domain_Validator_AbstractValidator {

	public function isValid($object = NULL) {
		$status = TRUE;

		if (!$object instanceof Tx_News_Domain_Model_Media || is_null($object)) {
			return $status;
		}

		$status = $this->validate($object);
		return $status;
	}
}

?>