<?php
class SourceController extends Zend_Controller_Action {

	private $oClient;
	private $oSourceDom;
	private $oAccount;

	public $sMode;
	public $sLocation;
	public $sElement;
	public $sAttr;
	public $iLimit;

	public function init() {
		$this -> oAccount = new Zend_Session_Namespace('ACCOUNT');

		if (!isset($this -> oAccount -> userdata['UID']) || !isset($this -> oAccount -> userdata['NAMESPACE'])) {
			$aLocParams = $this -> getRequest() -> getParams();
			header('Location:' . $this -> oSettings -> website -> site -> basehref);
			exit ;
		} else {
			$this -> oClient = new Zend_Http_Client($this -> sLocation);
		}

		/* Initialize action controller here */

	}

	public function xpathAction() {
		$this -> sMode = (isset($_REQUEST['mode'])) ? $_REQUEST['mode'] : false;
		$this -> sLocation = (isset($_REQUEST['location'])) ? $_REQUEST['location'] : false;
		$this -> sElement = (isset($_REQUEST['element'])) ? $_REQUEST['element'] : false;
		$this -> sAttr = (isset($_REQUEST['attr'])) ? $_REQUEST['attr'] : false;
		$this -> iLimit = (isset($_REQUEST['limit'])) ? $_REQUEST['limit'] : false;

		if ($this -> sMode) {
			if ($this -> sMode == 'attribute') {
				$this -> view -> modeAttribute = 'selected="selected"';
			}
			if ($this -> sMode == 'content') {
				$this -> view -> modeContent = 'selected="selected"';
			}

		}

		$this -> sLocation = 'http://' . str_replace('http://', '', $this -> sLocation);

		$bValid = false;
		$aMatchUrl = parse_url($this -> sLocation);

		if (isset($aMatchUrl['host'])) {
			$this -> sLocation = $aMatchUrl['host'] . ((isset($aMatchUrl['path'])) ? $aMatchUrl['path'] : '');

			if (stripos($this -> sLocation, '.') !== false) {
				$bValid = true;
			}
		}

		$this -> sLocation = str_replace('http://', '', $this -> sLocation);

		$this -> view -> location_status = '';
		$this -> view -> location = (!$this -> sLocation) ? 'mydomain.com' : $this -> sLocation;
		$this -> view -> element_status = '';
		$this -> view -> element = (!$this -> sElement) ? 'select (x)html tag' : $this -> sElement;
		$this -> view -> attr_status = '';
		$this -> view -> attr = (!$this -> sAttr) ? 'access attribute' : $this -> sAttr;
		$this -> view -> limit_status = '';
		$this -> view -> limit = (!$this -> iLimit) ? '1' : $this -> iLimit;

		if (!$this -> sLocation || !$bValid) {
			$this -> view -> sError = 'Error <br/>missing parameter <b>location</b></i><br/>';

		} else {
			$this -> sLocation = 'http://' . str_replace('http://', '', $this -> sLocation);

			$this -> sLocation = parse_url($this -> sLocation);
			$this -> sLocation = $this -> sLocation['scheme'] . '://' . $this -> sLocation['host'];
			$aResponse = array();

			$this -> view -> Tag = $this -> sElement;
			$this -> view -> Attr = $this -> sAttr;

			if ($this -> sMode == 'attr') {

				$oResponse = $this -> oClient -> request();

				$this -> oSourceDom = new Zend_Dom_Query($oResponse -> getBody());
				if (!$this -> sElement) {
					$this -> view -> sError = 'Error <br/>missing parameter <b>filter</b></i><br/>';
				} else {
					$oResult = $this -> oSourceDom -> query($this -> sElement);

					if ($this -> iLimit) {
						$bSetStopper = true;
					} else {
						$bSetStopper = false;
					}

					foreach ($oResult as $oDomElement) {
						if (!$this -> sAttr) {
							$this -> view -> sError = 'Error <br/>missing parameter <b>attr</b></i><br/>';
						}

						$sAttrValue = trim($oDomElement -> getAttribute($this -> sAttr));

						if ($this -> sAttr == 'src' || $this -> sAttr == 'href') {

							if (substr($sAttrValue, 0, 2) == '//') {

							} else {
								if (substr($sAttrValue, 0, 1) != '/' && substr($this -> sLocation, strlen($this -> sLocation) - 2, strlen($this -> sLocation) - 2) != '/') {
									$this -> sLocation = $this -> sLocation;
								}

								$aPathToSrc = parse_url($sAttrValue);

								if (!isset($aPathToSrc['host'])) {
									$sPart = substr($sAttrValue, 0, 1);
									if ($sPart == '/') {
										$sSeparator = '';
									} else {
										$sSeparator = '/';
									}
									$sAttrValue = $this -> sLocation . $sSeparator . str_replace('http://', '', $sAttrValue);
								}
							}
						}

						if (strlen(trim($oResult -> current() -> textContent)) > 0) {
							$aResponse[count($aResponse)] = array('location' => $sAttrValue, 'content' => trim($oResult -> current() -> textContent));
						} else {
							$aResponse[count($aResponse)] = array('location' => $sAttrValue);
						}
						if ($bSetStopper) {
							$this -> iLimit--;
							if ($this -> iLimit == 0) {
								break;
							}
						}
					}
					$this -> view -> response = $aResponse;
				}

			}
			if ($this -> sMode == 'content') {
				$oResponse = $this -> oClient -> request();
				$aHeaders = $oResponse -> getHeaders();

				$bDoEncode = false;

				if (isset($aHeaders['Content-type']) && stripos(strtolower($aHeaders['Content-type']), 'utf-8') !== false) {
					$bDoEncode = true;
				}

				$this -> oSourceDom = new Zend_Dom_Query($oResponse -> getBody());
				if (!$this -> sElement) {
					$this -> view -> sError = 'Error <br/>missing parameter <b>filter</b></i><br/>';
				} else {
					$oResult = $this -> oSourceDom -> query($this -> sElement);

					if ($this -> iLimit) {
						$bSetStopper = true;
					} else {
						$bSetStopper = false;
					}
					$sResponse = '';
					foreach ($oResult as $oDomElement) {

						$sResponse .= $oResult -> current() -> textContent;

						if (strlen(trim($oResult -> current() -> textContent)) > 0) {
							$aResponse[count($aResponse)] = array('content' => trim($oResult -> current() -> textContent));

							if ($bSetStopper) {
								$this -> iLimit--;
								if ($this -> iLimit == 0) {
									break;
								}
							}
						}
					}

					if ($bDoEncode) {
						#$sResponse = utf8_decode($sResponse);
					}

					$this -> view -> response = $aResponse;
				}
			}

			if ($this -> sMode != 'content' && $this -> sMode != 'attr') {
				$this -> view -> sError = 'Error <b>mode</b><br/>Expectet <i>content</i> or <i>attribute</i><br/>';
			}
		}
	}

	/**
	 * (Interface implementet methods override)
	 * @see var/www/dev/tagitall/library/Tagitall/TagitallDefaults::addAction()
	 *
	 */

	public function addAction() {
		echo __METHOD__;
	}

	public function deleteAction() {
		echo __METHOD__;
	}

	public function saveAction() {
		echo __METHOD__;
	}

	public function listAction() {
		$aServices = array('xpath' => 'XPATH');

		$this -> view -> sInitNo = '0';
		$this -> view -> sInitText = 'Please select';
		$this -> view -> options = $aServices;

	}

}
?>