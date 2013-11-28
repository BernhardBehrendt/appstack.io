<?php
abstract class Proxy extends Application_Model_Account {
	protected $req;
	protected $priv;
	protected $res;
	protected $usr;
	protected $method;
	protected $reqtype;
	protected $oSettings;
	protected $oError;
	private $bNameSpaceMatch = false;
	public function __construct($sClassName) {
		$this->oSettings = new Zend_Config_Ini ( APPLICATION_PATH . '/configs/application.ini', 'production' );

		if (isset ( $GLOBALS ['req'] ) && isset ( $GLOBALS ['usr'] )) {

			if ($GLOBALS ['req'] ['request'] ['res'] == $sClassName) {
				$this->req = $GLOBALS ['req'];
				$this->usr = $GLOBALS ['usr'];
				$this->method = $GLOBALS ['method'];
				header ( 'Content-type: application/json' );
			} else {
				throw new Exception ( 'Wrong Call' );
			}
		}

		$this->oError = new Application_Model_Apierror ();

		parent::__construct ( $this->oSettings );
		// Login the Rest User
		$this->restLogin ( $this->usr );

		$this->getNamespace ();
	}
	public function isReadable($bForce = false) {
		if (in_array ( 'read', $this->priv ) || $bForce) {
			if ($this->setUsage ( $this->usr, array ('maxapiget' => 1 ) )) {
				return true;
			} else {

				echo json_encode ( $this->oError->noBudget ( 'apiget' ) );
				if (isset ( $_REQUEST ['callback'] )) {
					echo ')';
				}
				exit ();
			}
		} else {
			echo json_encode ( $this->oError->noPriv ( 'READ' ) );
			if (isset ( $_REQUEST ['callback'] )) {
				echo ')';
			}
		}
	}
	public function isChangeable() {
		if (in_array ( 'change', $this->priv )) {
			if ($this->setUsage ( $this->usr, array ('maxapiput' => 1 ) )) {
				return true;
			} else {
				echo json_encode ( $this->oError->noBudget ( 'apiput' ) );
				if (isset ( $_REQUEST ['callback'] )) {
					echo ')';
				}
				exit ();
			}
		} else {
			echo json_encode ( $this->oError->noPriv ( 'CHANGE' ) );
			if (isset ( $_REQUEST ['callback'] )) {
				echo ')';
			}
		}
	}
	public function isCreateable() {
		if (in_array ( 'create', $this->priv )) {
			if ($this->setUsage ( $this->usr, array ('maxapiput' => 1 ) )) {
				return true;
			} else {
				echo json_encode ( $this->oError->noBudget ( 'apiput' ) );
				if (isset ( $_REQUEST ['callback'] )) {
					echo ')';
				}
				exit ();
			}
		} else {
			echo json_encode ( $this->oError->noPriv ( 'CREATE' ) );
			if (isset ( $_REQUEST ['callback'] )) {
				echo ')';
			}
		}
	}
	public function isDeleteable() {
		if (in_array ( 'delete', $this->priv )) {
			if ($this->setUsage ( $this->usr, array ('maxapiget' => 1 ) )) {
				return true;
			} else {
				echo json_encode ( $this->oError->noBudget ( 'apiput' ) );
				if (isset ( $_REQUEST ['callback'] )) {
					echo ')';
				}
				exit ();
			}
		} else {
			echo json_encode ( $this->oError->noPriv ( 'DELETE' ) );
			if (isset ( $_REQUEST ['callback'] )) {
				echo ')';
			}
		}
	}
	public function isExtendable() {
		if (in_array ( 'extend', $this->priv )) {
			if ($this->setUsage ( $this->usr, array ('maxapiput' => 1 ) )) {
				return true;
			} else {
				echo json_encode ( $this->oError->noBudget ( 'apiput' ) );
				if (isset ( $_REQUEST ['callback'] )) {
					echo ')';
				}
				exit ();
			}
		} else {
			echo json_encode ( $this->oError->noPriv ( 'EXTEND' ) );
			if (isset ( $_REQUEST ['callback'] )) {
				echo ')';
			}
		}
	}
	public function addEntry($sKey, $mValue, $force = false) {
		if (! isset ( $this->res ['request'] )) {
			$this->res ['request'] = $this->req;
		}
		if (! isset ( $this->res [$sKey] ) || $force) {
			$this->res [$sKey] = $mValue;
			return true;
		}
		return false;
	}
	public function getResponse() {
		return $this->res;
	}
	public function removeKeys($array, $keys = array()) {

		// If array is empty or not an array at all, don't bother
		// doing anything else.
		if (empty ( $array ) || (! is_array ( $array ))) {
			return $array;
		}

		// If $keys is a comma-separated list, convert to an array.
		if (is_string ( $keys )) {
			$keys = explode ( ',', $keys );
		}

		// At this point if $keys is not an array, we can't do anything with it.
		if (! is_array ( $keys )) {
			return $array;
		}

		// array_diff_key() expected an associative array.
		$assocKeys = array ();
		foreach ( $keys as $key ) {
			$assocKeys [$key] = true;
		}

		return array_diff_key ( $array, $assocKeys );
	}
	public function passKeys($array, $keys = array()) {

		// If array is empty or not an array at all, don't bother
		// doing anything else.
		if (empty ( $array ) || (! is_array ( $array ))) {
			return $array;
		}

		// If $keys is a comma-separated list, convert to an array.
		if (is_string ( $keys )) {
			$keys = explode ( ',', $keys );
		}

		// At this point if $keys is not an array, we can't do anything with it.
		if (! is_array ( $keys )) {
			return $array;
		}

		// array_diff_key() expected an associative array.
		$assocKeys = array ();
		foreach ( $keys as $key ) {
			$assocKeys [$key] = true;
		}

		return array_intersect_key ( $array, $assocKeys );
	}

	/**
	 * Converts the Namespace into an ID and checks if key is allowed to access
	 * namespace
	 */
	public function getNamespace() {
		if (! $this->bNameSpaceMatch) {
			$oGroups = new Application_Model_Groups ( $this->oSettings );

			$aAccessible = $oGroups->accountGroups ();
			if (isset ( $aAccessible ['MEMBER'] ) && isset ( $aAccessible ['OWNER'] )) {
				$aAccessible = array_merge ( $aAccessible ['MEMBER'], $aAccessible ['OWNER'] );
			} elseif (isset ( $aAccessible ['MEMBER'] )) {
				$aAccessible = $aAccessible ['MEMBER'];
			} elseif (isset ( $aAccessible ['OWNER'] )) {
				$aAccessible = $aAccessible ['OWNER'];
			}

			if (isset ( $this->req ['request'] ['ns'] )) {
				if (( int ) $this->req ['request'] ['ns'] == 0) {
					foreach ( $aAccessible as $iKey => $aPrivileges ) {
						if (in_array ( $this->req ['request'] ['ns'], $aPrivileges ['SPACES'] )) {
							foreach ( $aPrivileges ['SPACES'] as $iNameSpaceId => $sNamespaceName ) {
								if ($sNamespaceName == $this->req ['request'] ['ns']) {
									$this->req ['request'] ['ns'] = $iNameSpaceId;
									$this->priv = $aPrivileges ['RIGHTS'];
									break;
								}
							}
							$this->bNameSpaceMatch = true;
							break;
						}
					}
				} else {
					foreach ( $aAccessible as $iKey => $aPrivileges ) {
						if (array_key_exists ( $this->req ['request'] ['ns'], $aPrivileges ['SPACES'] )) {
							$this->bNameSpaceMatch = true;
							$this->priv = $aPrivileges ['RIGHTS'];
							break;
						}
					}
				}
			}
		}
		return $this->bNameSpaceMatch;
	}
}
?>