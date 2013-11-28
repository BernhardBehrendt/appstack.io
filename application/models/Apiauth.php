<?php
class Application_Model_Apiauth {
	private $db;
	private $iIdUser = null;
	private $sAuthlevel = null;
	public function __construct($db) {
		if (is_resource ( $db )) {
			$this->db = $db;
		}
	}
	public function auth() {
		if (isset ( $_GET ['apikey'] )) {
			if ($this->verifyApikey ( $_GET ['apikey'] )) {
				$this->sAuthlevel = 'apikey';
			}
		} else {
			$server = new OAuthServer ();
			if ($server->verifyIfSigned ()) {
				$this->sAuthlevel = 'oauth';
			}
		}
		if ($this->sAuthlevel != null) {
			return $this->getUser ();
		}
	}
	public function getAuthType() {
		return $this->sAuthlevel;
	}
	public function getUser() {
		if (isset ( $_SESSION ['verificated'] )) {
			$this->iIdUser = ( int ) $_SESSION ['verificated'];
			unset ( $_SESSION ['verificated'] );
		}
		
		return $this->iIdUser;
	}
	public function verifyApikey($sKey) {
		$bVerifyed = false;
		$keyExists = mysql_query ( 'SELECT * FROM browserkeys WHERE browserkeys.key="' . mysql_real_escape_string ( $sKey ) . '"', $this->db );
		
		if (is_resource ( $keyExists )) {
			$sReferer = $_SERVER ['HTTP_REFERER'];
			if (substr ( $sReferer, 0, 7 ) == 'http://') {
				$sReferer = substr ( $sReferer, 7, strlen ( $sReferer ) );
			}
			$oRow = mysql_fetch_object ( $keyExists );
			if (is_object ( $oRow )) {
				if (isset ( $oRow->key )) {
					
					if ($oRow->anywhere == 1) {
						$_SESSION ['verificated'] = $oRow->accounts_idaccount;
						$bVerifyed = true;
					}
					
					if ($oRow->anywhere == 0 && isset ( $sReferer )) {
						$sUrlRelevantPart = substr ( $sReferer, 0, strlen ( $oRow->domain ) );
						
						if ($sUrlRelevantPart == $oRow->domain) {
							$_SESSION ['verificated'] = $oRow->accounts_idaccount;
							$bVerifyed = true;
						} else {
							Preprocessor_Header::setContentType ( 'json' );
							header ( 'HTTP/1.1 401 Unauthorized' );
							echo json_encode ( array ('error' => true, 'message' => 'Invalid  domain' ) );
							exit ();
						}
					} elseif ($oRow->anywhere == 0 && ! isset ( $_SERVER ['HTTP_REFERER'] )) {
						Preprocessor_Header::setContentType ( 'json' );
						header ( 'HTTP/1.1 401 Unauthorized' );
						echo json_encode ( array ('error' => true, 'message' => 'Invalid  domain' ) );
						exit ();
					}
				}
			}
		} else {
			header ( 'HTTP/1.1 401 Unauthorized' );
			sleep ( 5 );
			exit ();
		}
		return $bVerifyed;
	}
}
?>