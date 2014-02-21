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
 * Extended News repository
 *
 * @package TYPO3
 * @subpackage tx_newsfe
 */
class Tx_Newsfe_Domain_Repository_NewsRepository extends Tx_Extbase_Persistence_Repository {

	public function getNewsOfUser($demand) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);

		$constraints = $this->createConstraintsFromDemand($query, $demand);
		$constraints[] = $query->equals('txNewsfeFeuser', $GLOBALS['TSFE']->fe_user->user['uid']);

		$query->matching(
			$query->logicalAnd($constraints)
		);

		return $query->execute();
	}

}

?>