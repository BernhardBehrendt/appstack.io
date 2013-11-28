<?php
class Application_Model_Apirequest {

	private $oError;

	public function __construct(Application_Model_Apierror $oError) {
		$this -> oError = $oError;
	}

	/**
	 * Validates the REequest types and return is used to extend response
	 */
	public function processRequest() {

		if (stripos($_SERVER['REQUEST_URI'], '/' . NAMESPACE_IDENTIFIER . '/') === false) {
			$aPreRequest = preg_replace("/\?.*/", "", substr($_SERVER['REQUEST_URI'], stripos($_SERVER['REQUEST_URI'], '/' . RESSOURCE_IDENTIFIER . '/')));
			$aPreRequest = (substr($aPreRequest, 0, 1) == '/') ? substr($aPreRequest, 1, strlen($aPreRequest)) : $aPreRequest;
		} else {
			$aPreRequest = preg_replace("/\?.*/", "", substr($_SERVER['REQUEST_URI'], stripos($_SERVER['REQUEST_URI'], '/' . NAMESPACE_IDENTIFIER . '/')));
			$aPreRequest = (substr($aPreRequest, 0, 1) == '/') ? substr($aPreRequest, 1, strlen($aPreRequest)) : $aPreRequest;
		}

		$oResponse['requeststring'] = $aPreRequest;
		$aPreRequest = array_unique(explode('/', $aPreRequest));
		$aPostRequest = false;

		if (is_array($aPreRequest)) {
			for ($i = 0; $i < count($aPreRequest); $i++) {
				if ((($i + 1) % 2) == 1) {
					if (isset($aPreRequest[$i + 1])) {
						if (!isset($aPostRequest[$aPreRequest[$i]])) {
							if ($aPreRequest[$i] != RESSOURCE_IDENTIFIER) {
								$aPreRequest[$i + 1] = (substr($aPreRequest[$i + 1], 0, 1) == '.') ? substr($aPreRequest[$i + 1], 1, strlen($aPreRequest[$i + 1])) : $aPreRequest[$i + 1];
								$aPreRequest[$i + 1] = (substr($aPreRequest[$i + 1], strlen($aPreRequest[$i + 1]) - 1, strlen($aPreRequest[$i + 1])) == '.') ? substr($aPreRequest[$i + 1], 0, strlen($aPreRequest[$i + 1]) - 1) : $aPreRequest[$i + 1];

								if (stripos($aPreRequest[$i + 1], ',') !== false) {
									$aPreRequest[$i + 1] = array_unique(explode(',', $aPreRequest[$i + 1]));
								}
							}
							$aPostRequest[$aPreRequest[$i]] = $aPreRequest[$i + 1];
						}
					}
				}
				continue;
			}
		}
		if ((isset($aPostRequest[NAMESPACE_IDENTIFIER]) && isset($aPostRequest[RESSOURCE_IDENTIFIER])) || (isset($aPostRequest[RESSOURCE_IDENTIFIER]) && $aPostRequest[RESSOURCE_IDENTIFIER] == 'me')) {
			return $aPostRequest;
		} else {
			$this -> oError -> notFound();
		}
	}

	/**
	 * Validates the request
	 */
	public function validateRequest() {
		if (stripos($_SERVER['REQUEST_URI'], '/' . RESSOURCE_IDENTIFIER . '/') !== false) {
			return true;
		}
		return false;
	}

	// Prepare the request for rest server
	public function prepareRequest($iIdUser, $oReqest) {
		if (isset($oReqest['request'][RESSOURCE_IDENTIFIER]) && $oReqest['request'][RESSOURCE_IDENTIFIER] == 'me') {
			$sBasePath = str_replace('index.php', '', $_SERVER['SCRIPT_NAME'] . 'res/' . $oReqest['request']['res'] . '/');
			$GLOBALS['req'] = $oReqest;
			$GLOBALS['usr'] = $iIdUser;

			return $sBasePath;
		} elseif (isset($oReqest['request'][RESSOURCE_IDENTIFIER]) && isset($oReqest['request'][NAMESPACE_IDENTIFIER])) {
			$GLOBALS['req'] = $oReqest;
			$GLOBALS['usr'] = $iIdUser;
			$sBasePath = str_replace('index.php', '', $_SERVER['SCRIPT_NAME'] . 'ns/' . $oReqest['request'][NAMESPACE_IDENTIFIER] . '/res/' . $oReqest['request']['res'] . '/');

			return $sBasePath;
		} else {
			return false;
		}
	}

}
?>