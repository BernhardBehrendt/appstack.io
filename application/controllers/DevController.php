<?php
/**
 * Central Metainformation controller
 * This controller provides create, delete, modify of metas and their values
 * on base of the following models
 *
 * 1) Application_Model_Meta
 * 2) Application_Model_Metavalue
 *
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 *
 * @category Metainformation_Controller
 * @version 1.0.0
 *
 * @uses Application_Model_Meta, Application_Model_Metavalue
 */
class DevController extends Zend_Controller_Action {
	private $config;
	private $oCacheReq;
	private $oCacheAcc;
	private $oAccount;
	private $oSettings;
	public function init() {
		$this->config = $config = array ('callbackUrl' => 'http://localhost:10088/appstack/public/dev/drin/', 'siteUrl' => 'http://api.appstack.io/oauth.php', 'consumerKey' => 'da916b60070b4181095dc4b89a9d11e004f077f99', 'consumerSecret' => 'c2d96d81560053c63ed514b206308a47' );
		$this->oAccount = Application_Model_Session::getInstance ( 'ACCOUNT' );
		$this->oCacheAcc = new Application_Model_Cache ( 'access_token_' . $this->oAccount->userdata ['UID'] );
		$this->oCacheReq = new Application_Model_Cache ( 'request_token_' . $this->oAccount->userdata ['UID'] );
	}
	
	/**
	 * Represent all metas and route them to json output
	 *
	 * @return none
	 */
	public function indexAction() {
		
		$consumer = new Zend_Oauth_Consumer ( $this->config );
		
		// Holt den Anfragetoken
		$token = $consumer->getRequestToken ();
		// Den token im Speicher fixieren
		$this->oCacheReq->writeCache ( serialize ( $token ) );
		
		// Den Benutzer umleiten
		$consumer->redirect ();
	
	}
	
	public function drinAction() {
		
		if ($this->oCacheReq->cacheExists ()) {
			$consumer = new Zend_Oauth_Consumer ( $this->config );
			
			if (! empty ( $_GET )) {
				$token = $consumer->getAccessToken ( $_GET, unserialize ( $this->oCacheReq->readCache () ) );
				$this->oCacheAcc->writeCache ( serialize ( $token ) );
				header ( 'Location: ../callback/' );
			} else {
				exit ( 'Ungültige Callback Anfrage. Oops. Entschuldigung.' );
			}
		} else {
			$this->_redirect ( 'dev' );
		}
	}
	
	public function callbackAction() {
		Preprocessor_Header::setContentType ( 'json' );
		if ($this->oCacheAcc->cacheExists ()) {
			$statusMessage = 'Ich sende über Twitter und verwende Zend_Oauth!';
			
			$token = unserialize ( $this->oCacheAcc->readCache () );
			// echo '<pre>' . print_r($token, true) . '</pre>';
			$client = $token->getHttpClient ( $this->config );
			$aMeta = array ('name' => 'eine neue meta', 'properties' => array ('erste eigenschaft' => 'Hallo schöne welt', 'zweiter' => 'Welt', 'boolsch' => true, 'zahl' => 123 ) );
			$client->setUri ( 'http://api.appstack.io/ns/kjgozig/res/metas/create/' . urlencode ( (json_encode ( $aMeta )) ) );
			$client->setMethod ( Zend_Http_Client::POST );
			
			$client->setParameterGet ( 'testbb', json_encode ( array ('a' => 'b', 'c' => 'd' ) ) );
			$response = $client->request ();
			
			// $data = Zend_Json::decode($response->getBody());
			$result = $response->getBody ();
			/*
			 * if (isset($data->text)) { $result = 'true'; }
			 */
			// echo '<pre>' . print_r($token, true) . '</pre>';
			$this->oCacheAcc->deleteCache ();
			$this->oCacheReq->deleteCache ();
			echo $result;
		} else {
			$this->_redirect ( 'dev' );
		}
	}
}
?>