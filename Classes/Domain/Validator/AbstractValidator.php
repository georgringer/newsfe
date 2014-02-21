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
 * Abstract validator
 *
 * @package TYPO3
 * @subpackage tx_newsfe
 */
abstract class Tx_Newsfe_Domain_Validator_AbstractValidator extends Tx_Extbase_Validation_Validator_AbstractValidator {

	/**
	 * @var Tx_News_Service_SettingsService
	 */
	protected $pluginSettingsService;

	/**
	 * @var Tx_Extbase_MVC_Web_RequestBuilder
	 */
	protected $requestBuilder;

	/**
	 * @var array
	 */
	protected $options = array();

	protected $handledProperties = array();

	/**
	 * @var Tx_News_Service_SettingsService $pluginSettingsService
	 */
	public function injectSettingsService(Tx_News_Service_SettingsService $pluginSettingsService) {
		$this->pluginSettingsService = $pluginSettingsService;
	}

	/**
	 * @param Tx_Extbase_MVC_Web_RequestBuilder $requestBuilder
	 * @return void
	 */
	public function injectRequestBuilder(Tx_Extbase_MVC_Web_RequestBuilder $requestBuilder) {
		$this->requestBuilder = $requestBuilder;
	}

	/**
	 * Validate a single model
	 *
	 * @param mixed $object
	 * @return boolean
	 */
	public function validate($object) {
		$status = TRUE;
		$actionName = $this->requestBuilder->build()->getControllerActionName();
		$className = get_class($object);

		$typoScriptConfiguration = $this->pluginSettingsService->getSettings();
		$availableProperties = Tx_Extbase_Reflection_ObjectAccess::getSettablePropertyNames($object);

		foreach ($availableProperties as $property) {
			$validatorSettings = $typoScriptConfiguration['validation'][$className][$property];
			if (isset($validatorSettings)) {
				$value = Tx_Extbase_Reflection_ObjectAccess::getProperty($object, $property);

				if (is_string($validatorSettings)) {
					$validatorSettings = array($validatorSettings);
				}
				foreach ($validatorSettings as $singleValidator) {
					$singleStatus = $this->checkSingleValidator($property, $value, $singleValidator);
					if (!$singleStatus) {
						$status = FALSE;
					}
				}
			}
		}

		return $status;
	}

	/**
	 * Validate a single validator
	 *
	 * @param string $property name of the propery
	 * @param string $value value of the property
	 * @param mixed $singleValidator validator
	 * @return boolean
	 */
	protected function checkSingleValidator($property, $value, $singleValidator) {
		$validatorClass = NULL;
		$options = array();

		if (is_string($singleValidator)) {
			$validatorClass = $singleValidator;
		} elseif (is_array($singleValidator)) {
			$validatorClass = $singleValidator['_typoScriptNodeValue'];
			$options = $singleValidator;
			unset($options['_typoScriptNodeValue']);
		} else {
			throw new Exception('trouble');
		}

		if (!class_exists($validatorClass)) {
			throw new Exception(sprintf('Validator class "%s" does not exist', $validatorClass));
		}
		$validator = new $validatorClass($options);
		if (!$validator instanceof Tx_Extbase_Validation_Validator_ValidatorInterface) {
			throw new Exception(sprintf('Validator class "%s" does not implement Tx_Extbase_Validation_Validator_ValidatorInterface', $validatorClass));
		}

		$status = TRUE;
		if ($validator->isValid($value) !== TRUE) {
			$propertyError = NULL;
			if (isset($this->handledProperties[$property])) {
				$propertyError = $this->handledProperties[$property];
			} else {
				$this->handledProperties[$property] = t3lib_div::makeInstance('Tx_Extbase_Validation_PropertyError', $property);
				$propertyError = $this->handledProperties[$property];
			}
			$propertyError->addErrors($validator->getErrors());
			$this->errors[$property] = $propertyError;
			$status = FALSE;
		}

		return $status;
	}

}