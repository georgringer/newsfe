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
 * Controller for related file elements
 *
 * @package TYPO3
 * @subpackage tx_newsfe
 */
class Tx_Newsfe_Controller_RelatedFileController extends Tx_Newsfe_Controller_BaseController {

	/**
	 * mediaRepository
	 *
	 * @var Tx_News_Domain_Repository_FileRepository
	 */
	protected $relatedFileRepository;

	/**
	 * injectNewsRepository
	 *
	 * @param Tx_News_Domain_Repository_FileRepository $fileRepository
	 * @return void
	 */
	public function injectMediaRepository(Tx_News_Domain_Repository_FileRepository $fileRepository) {
		$this->relatedFileRepository = $fileRepository;
	}

	/**
	 * action new
	 *
	 * @param $newRelatedFile
	 * @dontvalidate $newRelatedFile
	 * @return void
	 */
	public function newAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_File $newRelatedFile = NULL) {
		$access = Tx_Newsfe_Utility_Access::check($news);

		if (is_null($newRelatedFile)) {
			$newRelatedFile = $this->objectManager->get('Tx_News_Domain_Model_File');
		}

		$this->view->assignMultiple(array(
			'news' => $news,
			'newRelatedFile' => $newRelatedFile,
			'access' => $access
		));
	}

	/**
	 *
	 * @param Tx_News_Domain_Model_News $news
	 * @param Tx_News_Domain_Model_File $newRelatedFile
	 * @validate $newRelatedFile Tx_Newsfe_Domain_Validator_RelatedFileValidator
	 */
	public function createAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_File $newRelatedFile) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		if ($access) {
			$file = Tx_Newsfe_Utility_FileUpload::upload('relatedFile|file');
			if ($file !== FALSE) {
				$newRelatedFile->setFile($file);
			}
			$this->signalSlotDispatcher->dispatch(__CLASS__,  __FUNCTION__, array('news' => $news, 'file' => $newRelatedFile), TRUE);
			$news->addRelatedFile($newRelatedFile);
			$this->flashMessageContainer->add('Your new File was created.');
		} else {
			$this->flashMessageContainer->add('This news does not belong to you');
		}

		$this->redirect('show', 'News', NULL, array('news' => $news));
	}

	/**
	 *
	 * @param Tx_News_Domain_Model_News $news
	 * @param Tx_News_Domain_Model_File $relatedFile
	 */
	public function editAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_File $relatedFile) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		$this->signalSlotDispatcher->dispatch(__CLASS__,  __FUNCTION__, array('news' => $news, 'file' => $relatedFile), TRUE);
		$this->view->assignMultiple(array(
			'news' => $news,
			'relatedFile' => $relatedFile,
			'access' => $access
		));
	}

	/**
	 *
	 * @param Tx_News_Domain_Model_News $news
	 * @param Tx_News_Domain_Model_File $relatedFile
	 * @validate $relatedFile Tx_Newsfe_Domain_Validator_RelatedFileValidator
	 */
	public function updateAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_File $relatedFile) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		if ($access) {
			$file = Tx_Newsfe_Utility_FileUpload::upload('relatedFile|file');
			if ($file !== FALSE) {
				$relatedFile->setFile($file);
			}
			$this->signalSlotDispatcher->dispatch(__CLASS__,  __FUNCTION__, array('news' => $news, 'file' => $relatedFile), TRUE);
			$this->relatedFileRepository->update($relatedFile);
			$this->flashMessageContainer->add('Your related file was updated.');
		} else {
			$this->flashMessageContainer->add('This news does not belong to you');
		}

		$this->redirectToOverview($news);
	}

	/**
	 * Move a media object down
	 *
	 * @param Tx_News_Domain_Model_News $news
	 * @param Tx_News_Domain_Model_File $relatedFile
	 * @api
	 */
	public function moveDownAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_File $relatedFile) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		if ($access) {
			$tcemain = t3lib_div::makeInstance('Tx_Newsfe_Utility_Tcemain');
			$tcemain->move('tx_news_domain_model_file', $relatedFile->getUid(), 'down');

			$this->signalSlotDispatcher->dispatch(__CLASS__,  __FUNCTION__, array('news' => $news, 'file' => $relatedFile), TRUE);
			$this->flashMessageContainer->add('Your file was moved down.');
		} else {
			$this->flashMessageContainer->add('This news does not belong to you');
		}

		$this->redirectToOverview($news);
	}

	/**
	 * Move a media object up
	 *
	 * @param Tx_News_Domain_Model_News $news
	 * @param Tx_News_Domain_Model_File $relatedFile
	 * @api
	 */
	public function moveUpAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_File $relatedFile) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		if ($access) {
			$tcemain = t3lib_div::makeInstance('Tx_Newsfe_Utility_Tcemain');
			$tcemain->move('tx_news_domain_model_file', $relatedFile->getUid(), 'up');

			$this->signalSlotDispatcher->dispatch(__CLASS__,  __FUNCTION__, array('news' => $news, 'file' => $relatedFile), TRUE);
			$this->flashMessageContainer->add('Your file was moved up.');
		} else {
			$this->flashMessageContainer->add('This news does not belong to you');
		}

		$this->redirectToOverview($news);
	}

	/**
	 * Delete a media object
	 *
	 * @param Tx_News_Domain_Model_News $news
	 * @param Tx_News_Domain_Model_File $relatedFile
	 * @api
	 */
	public function deleteAction(Tx_News_Domain_Model_News $news, Tx_News_Domain_Model_File $relatedFile) {
		$access = Tx_Newsfe_Utility_Access::check($news);
		if ($access) {
			$this->signalSlotDispatcher->dispatch(__CLASS__,  __FUNCTION__, array('news' => $news, 'file' => $relatedFile), TRUE);

			$this->mediaRepository->remove($relatedFile);
			$this->flashMessageContainer->add('Your file element was removed.');
		} else {
			$this->flashMessageContainer->add('This news does not belong to you');
		}

		$this->redirectToOverview($news);
	}



}

?>