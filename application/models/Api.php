<?php
class Application_Model_Api {
	private $db;
	private $oSettings;
	private $oLogs;
	public $oError;
	public $oAuth;
	public $sAuthLevel;
	public $oRequest;
	public $oResponse;
	public $iIdUser;
	public function __construct($db) {
		header ( 'Content-type: application/json' );
		if (is_resource ( $db )) {

			$this->db = $db;
			$this->oSettings = new Zend_Config_Ini ( APPLICATION_PATH . '/configs/application.ini', 'production' );
			$oAdapter = Zend_Db::factory ( $this->oSettings->resources->db->adapter, $this->oSettings->resources->db->params->toArray () );

			Zend_Db_Table_Abstract::setDefaultAdapter ( $oAdapter );

			$this->oAuth = new Application_Model_Apiauth ( $this->db );
			$this->oError = new Application_Model_Apierror ( $this->db = $db );

			// / AUTH
			if ($this->oAuth->auth ()) {
				$this->oRequest = new Application_Model_Apirequest ( $this->oError );
				$this->oResponse = new Application_Model_Apiresponse ( $this->db = $db );
				$this->oLogs = new Application_Model_Apilog ( $this->db );
				$this->oResponse->extendResponse ( 'timestamp', time () );
				$this->oResponse->extendResponse ( 'auth_type', $this->oAuth->getAuthType () );
				$this->oResponse->extendResponse ( 'request', $this->oRequest->processRequest () );
				$this->handle ();
			} else {
				$this->oError->throwError('REQUESTER_IS_NOT_IDENTIFIABLE', true);
			}
		}
	}
	public function __destruct() {
		if (stripos ( $_SERVER ['HTTP_HOST'], 'localhost:10088' ) === false) {
			Zend_Session::destroy ();
			unset ( $_SESSION );
		}
	}
	public function handle() {
		$sBasePath = $this->oRequest->prepareRequest ( $this->oAuth->getUser (), $this->oResponse->getResponse () );
		if (is_string ( $sBasePath )) {

			$mode = (stripos ( $_SERVER ['HTTP_USER_AGENT'], $this->oSettings->dev->debug->key ) !== false) ? 'debug' : 'server';

			$this->oResponse->extendResponse ( 'servermode', $mode );

			$oReq = $this->oResponse->getResponse ();

			$oRestServer = new RestServer ( $mode );
			$GLOBALS ['method'] = $oRestServer->getMethod ();
			$oRestServer->addClass ( $oReq ['request'] ['res'], $sBasePath, $oReq );

			if (isset ( $_REQUEST ['callback'] )) {
				echo substr ( $_REQUEST ['callback'], 0, 64 ) . '(';
			}

			$oRestServer->handle ();

			if (isset ( $_REQUEST ['callback'] )) {
				echo ')';
			}
		}
	}
}
?>
