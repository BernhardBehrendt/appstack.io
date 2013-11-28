<?php
class Application_Model_Apierror {
	public function notFound() {
		$sError = '"HTTP/1.0 404 Not Found"';
		$this->extendHeader ( $sError );
		$this->throwError ( $sError, true );
	}
	public function notImplemented() {
		$sError = '501 Not Implemented';
		$this->extendHeader ( $sError );
		$this->throwError ( $sError, true );
	}
	public function accepted() {
		$sError = '202 Accepted';
		$this->extendHeader ( $sError );
		$this->throwError ( $sError, true );
	}
	public function nocontent() {
		$sError = '204 No Content';
		$this->extendHeader ( $sError );
		$this->throwError ( $sError, true );
	}
	public function unauthorized() {
		$sError = '401 Unauthorized';
		$this->extendHeader ( $sError );
		$this->throwError ( $sError, true );
	}
	public function forbidden() {
		$sError = '403 Forbidden';
		$this->extendHeader ( $sError );
		$this->throwError ( $sError, true );
	}
	public function unavailable() {
		$sError = '503 Service Unavailable';
		$this->extendHeader ( $sError );
		$this->throwError ( $sError, true );
	}
	public function noBudget($sType) {
		return array ('error' => true, 'message' => ' Your ' . $sType . ' budget limit was reached.', 'notice' => 'Next budget reset in ' . (60 - ( int ) date ( 'i' )) . ' minutes.' );
	}
	public function noPriv($sPrivilege) {
		return array ('error' => true, 'message' => 'Missing privilege ' . $sPrivilege, 'notice' => 'Check account settings or contact application administrator' );
	}
	private function extendHeader($sHeader) {
		if (strlen ( $sHeader )) {
			header ( $sHeader );
			return true;
		}
		return false;
	}
	public function throwError($sError, $bDirectOutput = false) {
		$aError = array ('error' => true, 'code' => $this->getErrorCode ( $sError ), 'message' => $sError );

		if (! $bDirectOutput) {
			return $aError;
		} else {
			echo json_encode ( array ('response' => $aError ) );
			exit ();
		}
	}
	private function getErrorCode($sMessage) {
		$aErrorCodes = array ('NAMESPACE_NOT_FOUND' => 1000, 'OAUTH_ONLY' => 2000 );

		if (array_key_exists ( $sMessage, $aErrorCodes )) {
			return $aErrorCodes [$sMessage];
		} else {
			if (isset ( $_SERVER ['HTTP_REFERER'] )) {
				mail ( 'bernhardb@samplosition.com', 'Unknown errormessage', $sMessage . "\n " . 'is unknown please add to getErrorCode Method<code>' . print_r ( $_SERVER, true ) . '</code><hr/><code>' . print_r ( $_REQUEST, true ) . '</code>' );
			}
			return 0;
		}
	}
}
?>