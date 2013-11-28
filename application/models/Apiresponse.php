<?php
class Application_Model_Apiresponse {
	private $aResponse;

	public function __construct() {
		$this -> aResponse = array();
	}

	public function extendResponse($sIdentifier, $mValue, $bForce = false) {
		if ((!$bForce && isset($this -> aResponse[$sIdentifier])) || empty($sIdentifier) || empty($mValue)) {
			return false;
		}
		$this -> aResponse[$sIdentifier] = $mValue;
	}

	public function getResponse() {
		return $this -> aResponse;
	}
}
?>