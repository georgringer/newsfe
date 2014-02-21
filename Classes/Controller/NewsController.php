<?php

/* * *************************************************************
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
 * ************************************************************* */

/**
 * Controller for news elements
 *
 * @package TYPO3
 * @subpackage tx_newsfe
 */
class Tx_Newsfe_Controller_NewsController extends Tx_Newsfe_Controller_BaseController {

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$demand = $this->createDemandObjectFromSettings();

		$newsRepository = $this->objectManager->get('Tx_News_Domain_Repository_NewsRepository');

		$this->view->assign('newsList', $newsRepository->getNewsOfUser($demand));
	}

	/**
	 * action show
	 *
	 * @param $news
	 * @return void
	 */
	public function showAction(Tx_News_Domain_Model_News $news) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		$this->view->assignMultiple(array(
			'news' => $news,
			'access' => $access,
		));
	}

	/**
	 * action new
	 *
	 * @param $newNews
	 * @dontvalidate $newNews
	 * @return void
	 */
	public function newAction(Tx_News_Domain_Model_News $newNews = NULL) {
		if (is_null($newNews)) {
			$newNews = $this->objectManager->get('Tx_News_Domain_Model_News');
		}

		$categories = $this->getCategories();
		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__, array('news' => $news, 'categories' => $categories), TRUE);

		$this->view->assignMultiple(array(
			'newNews' => $newNews,
			'availablecategories' => $categories,
		));
	}

	/**
	 * action create
	 *
	 * @param $newNews
	 * @return void
	 * @validate $newNews Tx_Newsfe_Domain_Validator_NewsValidator
	 */
	public function createAction(Tx_News_Domain_Model_News $newNews) {
		$currentUserUid = Tx_Newsfe_Utility_Access::getFeUserUid();
		if (is_null($currentUserUid)) {
			$this->flashMessageContainer->add('You are not logged in as feuser, no news can be created then');
		} else {
			$newNews->setTxNewsfeFeuser($currentUserUid);
			$predefinedPid = (int)$this->settings['news']['pidForNewRecords'];
			if ($predefinedPid > 0) {
				$newNews->setPid($predefinedPid);
			}
			$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__, array('news' => $newNews), TRUE);

			$this->newsRepository->add($newNews);
			$this->flashMessageContainer->add('Your new News was created.');
		}
		$this->redirect('list');
	}

	/**
	 * action edit
	 *
	 * @param $news
	 * @return void
	 * @xvalidate $news Tx_Newsfe_Domain_Validator_NewsValidator
	 */
	public function editAction(Tx_News_Domain_Model_News $news) {
		$access = Tx_Newsfe_Utility_Access::check($news);

		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__, array('news' => $news), TRUE);

		$this->view->assignMultiple(array(
			'news' => $news,
			'access' => $access,
			'availablecategories' => $this->getCategories(),
		));
	}

	/**
	 *
	 * @param Tx_News_Domain_Model_News $news
	 * @validate $news Tx_Newsfe_Domain_Validator_NewsValidator
	 */
	public function previewAction(Tx_News_Domain_Model_News $news) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__, array('news' => $news), TRUE);

		$this->view->assignMultiple(array(
			'news' => $news,
			'access' => $access,
			'availablecategories' => $this->getCategories(),
		));
	}

	/**
	 * action update
	 *
	 * @param $news
	 * @return void
	 * @validate $news Tx_Newsfe_Domain_Validator_NewsValidator
	 */
	public function updateAction(Tx_News_Domain_Model_News $news) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		if ($access) {
			$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__, array('news' => $news), TRUE);
			$this->newsRepository->update($news);
			$this->flashMessageContainer->add('Your News was updated.');
		} else {
			$this->flashMessageContainer->add('This news does not belong to you');
		}
		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param $news
	 * @return void
	 */
	public function deleteAction(Tx_News_Domain_Model_News $news) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		if ($access) {
			$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__, array('news' => $news), TRUE);
			$this->newsRepository->remove($news);
			$this->flashMessageContainer->add('Your News was removed.');
		} else {
			$this->flashMessageContainer->add('This news does not belong to you');
		}
		$this->redirect('list');
	}

	/**
	 * Create the demand object which define which records will get shown
	 *
	 * @return Tx_News_Domain_Model_NewsDemand
	 */
	protected function createDemandObjectFromSettings() {
		$settings = $this->settings['listDemand'];

		/* @var $demand Tx_News_Domain_Model_NewsDemand */
		$demand = $this->objectManager->get('Tx_News_Domain_Model_NewsDemand');

		$demand->setCategories(t3lib_div::trimExplode(',', $settings['categories'], TRUE));
		$demand->setCategoryConjunction($settings['categoryConjunction']);
		$demand->setIncludeSubCategories($settings['includeSubCategories']);

		$demand->setTopNewsRestriction($settings['topNewsRestriction']);
		$demand->setTimeRestriction($settings['timeRestriction']);
		$demand->setArchiveRestriction($settings['archiveRestriction']);

		if ($settings['orderBy']) {
			$demand->setOrder($settings['orderBy'] . ' ' . $settings['orderDirection']);
		}

		$demand->setTopNewsFirst($settings['topNewsFirst']);

		$demand->setLimit($settings['limit']);
		$demand->setOffset($settings['offset']);

		$demand->setSearchFields($settings['search']['fields']);
		$demand->setDateField($settings['dateField']);

		$demand->setStoragePage(Tx_News_Utility_Page::extendPidListByChildren($settings['startingpoint'], $settings['recursive']));
		return $demand;
	}


	/**
	 * Get categories
	 *
	 * @return Tx_Extbase_Persistence_QueryInterface
	 */
	protected function getCategories() {
		$idList = t3lib_div::intExplode(',', $this->settings['news']['categoryList']);

		$categoryRepository = t3lib_div::makeInstance('Tx_News_Domain_Repository_CategoryRepository');
		$categories = $categoryRepository->findByIdList($idList);

		$this->signalSlotDispatcher->dispatch(__CLASS__, __FUNCTION__, array('categories' => $categories, 'settings' => $this->settings), TRUE);

		return $categories;
	}

}

?>