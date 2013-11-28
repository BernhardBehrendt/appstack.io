<?php
/**
 * PHP Plugin for send requests to {our software}
 *
 *
 * @author	  Bernhard Bezdek
 * @copyright Bernhard Bezdek / Dominik Kloke (bernhard.bezdek@googlemail.com)
 * @license   GPL
 * @link	  http://www.dsbrg.net/api
 *
 * @version   0.9.0
 *
 * Created:   2011-08-05  Bernhard Bezdek
 *
 * HOW TO USE:
 * You are allowed to call public data defined in your or foreign systems
 * Defined Namespaces:
 * - users
 * - metas
 * - categories
 * - composites
 *
 * // USER REQUEST EXAMPLE
 * {
 * 	error:errorFunction 			// Function called on error (occurred error will be assigned)
 *  success:successFunction 		// Function called after successful apicall (Response object will be assigned)
 * }
 * // META REQUEST EXAMPLES
 *
 * // CATEGORY REQUEST EXAMPLES
 *
 * // COMPOSITE REQUEST EXAMPLE
 *
 *
 * !!!!!!!!!!!! NOTE WE ARE IN BETA STATE !!!!!!!!!!!!
 * please send us an email for further informations about changes on api
 *
 */
class askAPIRequest {

	// Store requests
	private $aReqStore;

	private function registerReq($mReqIdent, $sMethod, $sRequest, $bForce=false) {
		if(isset($mReqIdent) && isset($sMethod) && isset($sRequest)) {
			$aReplace = array(__CLASS__, ':', 'set', 'Req');
			$sReqSpace = strtolower(str_replace($aReplace, '', $sMethod));
			if(strlen($sReqSpace) > 0 && strlen($sRequest) > 0) {
				if(!isset($this -> aReqStore[$sReqSpace][$mReqIdent])) {
					$this -> aReqStore[$sReqSpace][$mReqIdent] = $sRequest;
				} else {
					if($bForce) {
						$this -> aReqStore[$sReqSpace][$mReqIdent] = $sRequest;
					} else {
						return false;
					}
				}
			} else {
				return false;
			}
			return true;
		}
		return false;
	}

	public function getReq($mReqIdent, $sMethod) {
		if(isset($this -> aReqStore[$sMethod][$mReqIdent])) {
			return $this -> aReqStore[$sMethod][$mReqIdent];
		} else {
			return false;
		}
	}

	public function setUserReq($mReqIdent, $bForce=false) {
		return $this -> registerReq($mReqIdent, __METHOD__, 'users', $bForce);
	}

	public function setMetaReq($mReqIdent, $sWho, $sPropertyName=false, $sPropertyValue=false, $bForce=false) {
		// Find real request
		$sRequest = 'metas/';
		if(isset($sWho)) {
			$sRequest .= $sWho . '/';
			if($sPropertyName) {
				$sRequest .= $sPropertyName . '/';
				if($sPropertyValue) {
					$sRequest .= $sPropertyValue . '/';
				}
			}

			return $this -> registerReq($mReqIdent, __METHOD__, $sRequest, $bForce);
		}
		return false;
	}

	public function setCatReq($mReqIdent, $sWho, $mChildsOf=false, $bForce=false) {
		$sRequest = 'categories/';
		if(isset($sWho)) {
			$sRequest .= $sWho . '/';
			if($mChildsOf) {
				$sRequest .= 'subcategories/' . $mChildsOf . '/';
			}
		}

		return $this -> registerReq($mReqIdent, __METHOD__, $sRequest, $bForce);
	}

	public function setCompReq($mReqIdent, $sWho, $mComposite=false, $mCategory=false, $sMetaName=false, $sPropertyName=false, $sPropertyValue=false, $bForce=false) {
		$sRequest = 'composites/';

		if(isset($sWho)) {
			$sRequest .= $sWho . '/';
			if(!$mComposite && !$mCategory) {
				if($sMetaName) {
					$sRequest .= 'meta/' . $sMetaName . '/';
					if($sPropertyName) {
						$sRequest .= $sPropertyName . '/';

						if($sPropertyValue) {
							$sRequest .= $sPropertyValue . '/';
						}
					}
				} else {
					if($sPropertyName) {
						$sRequest .= 'property/' . $sPropertyName . '/';
						if($sPropertyValue) {
							$sRequest .= $sPropertyValue . '/';
						}
					}
				}
			} else {
				if($mComposite && !$mCategory) {
					$sRequest .= $mComposite . '/';
				} elseif(!$mComposite && $mCategory) {
					$sRequest .= 'category/' . $mCategory . '/';
				} else {

				}
			}
			return $this -> registerReq($mReqIdent, __METHOD__, $sRequest, $bForce);
		}
		return false;
	}

}

class askAPI {
	private $sFormat = 'php';
	private $sApiAddr = 'http://appstack.io/api/';

	public function ask($sRequest) {
		// SETUP CURL
		$sURL = $this -> sApiAddr . $sRequest . '?format=' . $this -> sFormat;

		$hCurl = curl_init($sURL);
		curl_setopt($hCurl, CURLOPT_RETURNTRANSFER, true);

		$serCurlResponse = curl_exec($hCurl);

		if($serCurlResponse) {
			return  unserialize($serCurlResponse);
		} else {
			return array('error' => 'no response');
		}

	}

}
?>