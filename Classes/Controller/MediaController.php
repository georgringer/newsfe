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
 * Controller for media elements
 *
 * @package TYPO3
 * @subpackage tx_newsfe
 */
class Tx_Newsfe_Controller_MediaController extends Tx_Newsfe_Controller_BaseController {

	/**
	 * mediaRepository
	 *
	 * @var Tx_News_Domain_Repository_MediaRepository
	 */
	protected $mediaRepository;

	/**
	 * injectNewsRepository
	 *
	 * @param Tx_News_Domain_Repository_MediaRepository $media
	 * @return void
	 */
	public function injectMediaRepository(Tx_News_Domain_Repository_MediaRepository $media) {
		$this->mediaRepository = $media;
	}


	/**
	 * action show
	 *
	 * @param $news
	 * @return void
	 */
	public function showAction(Tx_News_Domain_Model_News $news) {
		$this->view->assign('news', $news);
	}

	/**
	 * action new
	 *
	 * @param $newNews
	 * @dontvalidate $newNews
	 * @return void
	 */
	public function newAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_Media $newMedia = NULL) {
		$this->signalSlotDispatcher->dispatch(__CLASS__,  __FUNCTION__, array('news' => $news, 'media' => $newMedia), TRUE);

		if (is_null($newMedia)) {
			$newMedia = $this->objectManager->get('Tx_News_Domain_Model_Media');
		}

		$this->view->assignMultiple(array(
			'newMedia' => $newMedia,
			'news' => $news
		));
	}

	/**
	 *
	 * @param Tx_News_Domain_Model_News $news
	 * @param Tx_News_Domain_Model_Media $newMedia
	 * @validate $newMedia Tx_Newsfe_Domain_Validator_MediaValidator
	 */
	public function createAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_Media $newMedia) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		if ($access) {
			$this->signalSlotDispatcher->dispatch(__CLASS__,  __FUNCTION__, array('news' => $news, 'media' => $newMedia), TRUE);
			$news->addMedia($newMedia);
			$this->flashMessageContainer->add('Your new Media was created.');
		} else {
			$this->flashMessageContainer->add('This news does not belong to you');
		}
		$this->redirect('show', 'News', NULL, array('news' => $news));
	}

	/**
	 *
	 * @param Tx_News_Domain_Model_News $news
	 * @param Tx_News_Domain_Model_Media $media
	 */
	public function editAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_Media $media) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		$this->view->assignMultiple(array(
			'news' => $news,
			'media' => $media,
			'access' => $access
		));
	}

		/**
	 *
	 * @param Tx_News_Domain_Model_News $news
	 * @param Tx_News_Domain_Model_Media $media
	 * @validate $media Tx_Newsfe_Domain_Validator_MediaValidator
	 */
	public function updateAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_Media $media) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		if ($access) {
			$file = Tx_Newsfe_Utility_FileUpload::upload('media|image');
			if ($file !== FALSE) {
				$media->setImage($file);
			}
			$this->signalSlotDispatcher->dispatch(__CLASS__,  __FUNCTION__, array('news' => $news, 'media' => $media), TRUE);
			$this->mediaRepository->update($media);
			$this->flashMessageContainer->add('Your media was updated.');
		} else {
			$this->flashMessageContainer->add('This news does not belong to you');
		}

		$this->redirectToOverview($news);
	}

	/**
	 * Move a media object down
	 *
	 * @param Tx_News_Domain_Model_News $news
	 * @param Tx_News_Domain_Model_Media $media
	 * @api
	 */
	public function moveDownAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_Media $media) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		if ($access) {
			$tcemain = t3lib_div::makeInstance('Tx_Newsfe_Utility_Tcemain');
			$tcemain->move('tx_news_domain_model_media', $media->getUid(), 'down');

			$this->signalSlotDispatcher->dispatch(__CLASS__,  __FUNCTION__, array('news' => $news, 'media' => $media), TRUE);
			$this->flashMessageContainer->add('Your media was moved down.');
		} else {
			$this->flashMessageContainer->add('This news does not belong to you');
		}

		$this->redirectToOverview($news);
	}

	/**
	 * Move a media object up
	 *
	 * @param Tx_News_Domain_Model_News $news
	 * @param Tx_News_Domain_Model_Media $media
	 * @api
	 */
	public function moveUpAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_Media $media) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		if ($access) {
			$tcemain = t3lib_div::makeInstance('Tx_Newsfe_Utility_Tcemain');
			$tcemain->move('tx_news_domain_model_media', $media->getUid(), 'up');

			$this->signalSlotDispatcher->dispatch(__CLASS__,  __FUNCTION__, array('news' => $news, 'media' => $media), TRUE);
			$this->flashMessageContainer->add('Your media was moved up.');
		} else {
			$this->flashMessageContainer->add('This news does not belong to you');
		}

		$this->redirectToOverview($news);
	}

	/**
	 * Delete a media object
	 *
	 * @param Tx_News_Domain_Model_News $news
	 * @param Tx_News_Domain_Model_Media $media
	 * @api
	 */
	public function deleteAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_Media $media) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		if ($access) {
			$this->signalSlotDispatcher->dispatch(__CLASS__,  __FUNCTION__, array('news' => $news, 'media' => $media), TRUE);
			$this->mediaRepository->remove($media);
			$this->flashMessageContainer->add('Your media element was removed.');
		} else {
			$this->flashMessageContainer->add('This news does not belong to you');
		}

		$this->redirectToOverview($news);
	}

}

?>