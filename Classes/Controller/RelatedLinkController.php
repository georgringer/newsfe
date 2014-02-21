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
 * Controller for related link elements
 *
 * @package TYPO3
 * @subpackage tx_newsfe
 */
class Tx_Newsfe_Controller_RelatedLinkController extends Tx_Newsfe_Controller_BaseController {

	/**
	 * mediaRepository
	 *
	 * @var Tx_News_Domain_Repository_LinkRepository
	 */
	protected $linkRepository;

	/**
	 * injectNewsRepository
	 *
	 * @param Tx_News_Domain_Repository_LinkRepository $link
	 * @return void
	 */
	public function injectMediaRepository(Tx_News_Domain_Repository_LinkRepository $link) {
		$this->linkRepository = $link;
	}

	/**
	 * action new
	 *
	 * @param $newRelatedLink
	 * @dontvalidate $newRelatedLink
	 * @return void
	 */
	public function newAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_Link $newRelatedLink = NULL) {
		$access = Tx_Newsfe_Utility_Access::check($news);

		if (is_null($newRelatedLink)) {
			$newRelatedLink = $this->objectManager->get('Tx_News_Domain_Model_Link');
		}

		$this->view->assignMultiple(array(
			'news' => $news,
			'newRelatedLink' => $newRelatedLink,
			'access' => $access
		));
	}

	/**
	 *
	 * @param Tx_News_Domain_Model_News $news
	 * @param Tx_News_Domain_Model_Link $newRelatedLink
	 * @validate $newRelatedLink Tx_Newsfe_Domain_Validator_RelatedLinkValidator
	 */
	public function createAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_Link $newRelatedLink) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		if ($access) {
			$this->signalSlotDispatcher->dispatch(__CLASS__,  __FUNCTION__, array('news' => $news, 'link' => $newRelatedLink), TRUE);
			$news->addRelatedLink($newRelatedLink);
			$this->flashMessageContainer->add('Your new Link was created.');
		} else {
			$this->flashMessageContainer->add('This news does not belong to you');
		}

		$this->redirect('show', 'News', NULL, array('news' => $news));
	}

	/**
	 *
	 * @param Tx_News_Domain_Model_News $news
	 * @param Tx_News_Domain_Model_Link $relatedLink
	 */
	public function editAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_Link $relatedLink) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		$this->view->assignMultiple(array(
			'news' => $news,
			'relatedLink' => $relatedLink,
			'access' => $access
		));
	}

	/**
	 *
	 * @param Tx_News_Domain_Model_News $news
	 * @param Tx_News_Domain_Model_Link $relatedLink
	 * @validate $relatedLink Tx_Newsfe_Domain_Validator_RelatedLinkValidator
	 */
	public function updateAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_Link $relatedLink) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		if ($access) {
			$this->signalSlotDispatcher->dispatch(__CLASS__,  __FUNCTION__, array('news' => $news, 'link' => $relatedLink), TRUE);
			$this->linkRepository->update($relatedLink);
			$this->flashMessageContainer->add('Your related link was updated.');
		} else {
			$this->flashMessageContainer->add('This news does not belong to you');
		}

		$this->redirectToOverview($news);
	}

	/**
	 * Move a media object down
	 *
	 * @param Tx_News_Domain_Model_News $news
	 * @param Tx_News_Domain_Model_Link $relatedLink
	 * @api
	 */
	public function moveDownAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_Link $relatedLink) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		if ($access) {
			$tcemain = t3lib_div::makeInstance('Tx_Newsfe_Utility_Tcemain');
			$tcemain->move('tx_news_domain_model_link', $relatedLink->getUid(), 'down');

			$this->signalSlotDispatcher->dispatch(__CLASS__,  __FUNCTION__, array('news' => $news, 'link' => $relatedLink), TRUE);
			$this->flashMessageContainer->add('Your link was moved down.');
		} else {
			$this->flashMessageContainer->add('This news does not belong to you');
		}

		$this->redirectToOverview($news);
	}

	/**
	 * Move a media object up
	 *
	 * @param Tx_News_Domain_Model_News $news
	 * @param Tx_News_Domain_Model_Link $relatedLink
	 * @api
	 */
	public function moveUpAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_Link $relatedLink) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		if ($access) {
			$tcemain = t3lib_div::makeInstance('Tx_Newsfe_Utility_Tcemain');
			$tcemain->move('tx_news_domain_model_link', $relatedLink->getUid(), 'up');

			$this->signalSlotDispatcher->dispatch(__CLASS__,  __FUNCTION__, array('news' => $news, 'link' => $relatedLink), TRUE);
			$this->flashMessageContainer->add('Your link was moved up.');
		} else {
			$this->flashMessageContainer->add('This news does not belong to you');
		}

		$this->redirectToOverview($news);
	}

	/**
	 * Delete a media object
	 *
	 * @param Tx_News_Domain_Model_News $news
	 * @param Tx_News_Domain_Model_Link $relatedLink
	 * @api
	 */
	public function deleteAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_Link $relatedLink) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		if ($access) {
			$this->signalSlotDispatcher->dispatch(__CLASS__,  __FUNCTION__, array('news' => $news, 'link' => $relatedLink), TRUE);
			$this->mediaRepository->remove($relatedLink);
			$this->flashMessageContainer->add('Your link element was removed.');
		} else {
			$this->flashMessageContainer->add('This news does not belong to you');
		}


		$this->redirectToOverview($news);
	}


}

?>