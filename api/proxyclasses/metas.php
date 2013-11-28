<?php
// cde638c0d676be42c2f2ad39e6d68a2c

require_once 'abstract.proxy.php';
class metas extends Proxy {
	private $oMetas;
	private $oMetavalue;
	public 

	function __construct() {
		parent::__construct ( __CLASS__ );
		$this->oMetas = new Application_Model_Meta ( $this->req ['request'] ['ns'] );
		$this->oMetavalue = new Application_Model_Metavalue ();
	}
	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 *
	 * @url GET /all/
	 */
	public function all() {
		if ($this->getNamespace ()) {
			if ($this->isReadable ()) {
				$this->addEntry ( 'response', array ('metas' => $this->renderMeta ( $this->oMetas->getAll ( $this->oMetavalue ) ) ) );
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'NAMESPACE_NOT_FOUND' ) );
		}
		return $this->getResponse ();
	}
	
	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 *
	 * @url GET /name/$name
	 */
	public function byName($name) {
		if (strlen ( $name ) > 0) {
			$name = strtolower ( $name );
			if (stripos ( $name, ',' ) !== false) {
				$name = explode ( ',', $name );
			}
			if ($this->getNamespace ()) {
				if ($this->isReadable ()) {
					
					$aResponsePre = $this->renderMeta ( $this->oMetas->getAll ( $this->oMetavalue ) );
					$aResponsePost = false;
					foreach ( $aResponsePre as $sKey => $meta ) {
						if (! is_array ( $name )) {
							if ($sKey === $name) {
								$aResponsePost [$sKey] = $meta;
								break;
							}
						} else {
							if (in_array ( $sKey, $name )) {
								$aResponsePost [$sKey] = $meta;
							}
						}
					}
					
					$this->addEntry ( 'response', array ('metas' => $aResponsePost ) );
				}
			} else {
				$this->addEntry ( 'response', $this->oError->throwError ( 'NAMESPACE_NOT_FOUND' ) );
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'Nothing to search for' ) );
		}
		return $this->getResponse ();
	}
	
	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 *
	 * @url GET /property/$property
	 * @url GET /property/$property/$strict
	 */
	public function byProperty($property, $strict = false) {
		if (is_string ( $strict ) && strtolower ( $strict ) === 'strict') {
			$strict = true;
		}
		if (strlen ( $property ) > 0) {
			$property = strtolower ( $property );
			if (stripos ( $property, ',' ) !== false) {
				$property = explode ( ',', $property );
			}
			if ($this->getNamespace ()) {
				if ($this->isReadable ()) {
					
					$aResponsePre = $this->renderMeta ( $this->oMetas->getAll ( $this->oMetavalue ) );
					$aResponsePost = false;
					foreach ( $aResponsePre as $sKey => $meta ) {
						if (! is_array ( $property ) && array_key_exists ( $property, $meta ['properties'] )) {
							$aResponsePost [$sKey] = $meta;
							continue;
						} elseif (! $strict && is_array ( $property )) {
							foreach ( $property as $iKey => $sIsIn ) {
								if (array_key_exists ( $sIsIn, $meta ['properties'] )) {
									$aResponsePost [$sKey] = $meta;
									break;
								}
							}
						} elseif ($strict && is_array ( $property )) {
							$iToMatch = count ( $property );
							foreach ( $property as $iKey => $sIsIn ) {
								if (array_key_exists ( $sIsIn, $meta ['properties'] )) {
									$iToMatch --;
								}
							}
							if ($iToMatch === 0) {
								$aResponsePost [$sKey] = $meta;
							}
						}
					}
					
					$this->addEntry ( 'response', array ('metas' => $aResponsePost ) );
				}
			} else {
				$this->addEntry ( 'response', $this->oError->throwError ( 'NAMESPACE_NOT_FOUND' ) );
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'NO_SEARCH_REQUEST' ) );
		}
		
		return $this->getResponse ();
	}
	
	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 *
	 * @url POST /create/$newmeta/
	 */
	public function create($newmeta) {
		if ($this->req ['auth_type'] == 'oauth') {
			if (is_string ( $newmeta ) && isset ( $_POST ['properties'] ) && is_object ( json_decode ( $_POST ['properties'] ) )) {
				if ($this->isCreateable ()) {
					// @TODO
					// Need to convert because there is an old structure
					$aMetavalues = array ();
					foreach ( json_decode ( $_POST ['properties'] ) as $sName => $sValue ) {
						array_push ( $aMetavalues, array ('name' => $sName, 'value' => $sValue ) );
					}
					if (count ( $aMetavalues ) <= 150) {
						$iPrimaryMeta = ( int ) $this->oMetas->insertMeta ( array ('name' => $newmeta, 'accounts_idaccount' => $this->usr, 'namespaces_idnamespace' => $this->req ['request'] ['ns'] ) );
						
						if (( int ) $iPrimaryMeta > 0 && count ( $aMetavalues ) > 0) {
							
							if ($this->setUsage ( $this->usr, array ('maxmetas' => 1 ) )) {
								if ($this->oMetavalue->insertValue ( $aMetavalues, $iPrimaryMeta )) {
									$this->addEntry ( 'response', array ('message' => 'Metaitem was created', 'meta' => $iPrimaryMeta ) );
								}
							} else {
								$this->addEntry ( 'response', $this->oError->throwError ( 'META_BUDGET_LIMIT_REACHED' ) );
							}
						} else {
							$this->addEntry ( 'response', $this->oError->throwError ( 'META_EXISTS' ) );
						}
					} else {
						$this->addEntry ( 'response', $this->oError->throwError ( 'TO_MUCH_PROPERTYS' ) );
					}
				}
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'OAUTH_ONLY' ) );
		}
		return $this->getResponse ();
	}
	
	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 *
	 * @url POST /change/defaults/$meta/
	 */
	public function changedefaults($meta) {
		if ($this->req ['auth_type'] == 'oauth') {
			$oPropData = false;
			if (isset ( $_REQUEST ['properties'] ) && is_object ( json_decode ( $_REQUEST ['properties'] ) )) {
				$oPropData = json_decode ( $_REQUEST ['properties'] );
				
				if (is_string ( $meta ) || ( int ) $meta > 0) {
					$oMeta = $aResponse = $this->oMetas->getById ( $meta );
					if (is_array ( $oMeta )) {
						if ($this->isChangeable ()) {
							$aChanges = array ();
							$aCompsAffected = 0;
							foreach ( $oPropData as $sKey => $sDefault ) {
								$sDefault = Preprocessor_String::apiStringSafe ( $sDefault );
								$oRowCurrent = $this->oMetavalue->fetchRow ( 'metas_idmeta=' . $oMeta ['idmeta'] . ' AND valname="' . $sKey . '"' );
								if (is_object ( $oRowCurrent )) {
									if ($oRowCurrent->valdef != $sDefault) {
										$mWasUpdated = $this->oMetavalue->update ( array ('valdef' => $sDefault ), 'metas_idmeta=' . $oMeta ['idmeta'] . ' AND valname="' . $sKey . '"' );
										if (( int ) $mWasUpdated) {
											$aChanges [$sKey] = Preprocessor_String::apiStringSafe ( $sDefault, true );
											if (isset ( $_REQUEST ['restrictive'] ) && $_REQUEST ['restrictive'] == 'yes') {
												if (! isset ( $oCompMeta )) {
													$oCompMeta = new Application_Model_Table ();
													$oCompMeta->setTable ( 'comp_metas' );
													$oCompMeta->setPrimary ( 'idcomp_meta' );
													
													$oCompMetaValue = new Application_Model_Table ();
													$oCompMetaValue->setTable ( 'comp_meta_values' );
													$oCompMetaValue->setPrimary ( 'idcomp_meta_value' );
													
													$oMetaUsed = $oCompMeta->fetchAll ( 'metas_idmeta=' . $oMeta ['idmeta'] );
												}
												
												if (is_object ( $oMetaUsed )) {
													foreach ( $oMetaUsed as $oCompMetaMatch ) {
														
														if ($oRowCurrent->valdef === null) {
															$mValDef = new Zend_Db_Expr ( 'valdef IS NULL' );
														} else {
															$mValDef = 'valdef="' . $oRowCurrent->valdef . '"';
														}
														$iCompChangeMade = $oCompMetaValue->update ( array ('valdef' => $sDefault ), 'comp_metas_idcomp_meta=' . $oCompMetaMatch->idcomp_meta . ' AND valname="' . $sKey . '" AND ' . $mValDef );
														if (( int ) $iCompChangeMade > 0) {
															$aCompsAffected ++;
														}
													}
												}
											}
										}
									} else {
										$aChanges [$sKey] = 'DEFAULT_VALUE_ALREADY_SET';
									}
								} else {
									$aChanges [$sKey] = 'PROPERTY_NOT_FOUND';
								}
							}
							$aResponse = array ('changes' => $aChanges );
							if (isset ( $_REQUEST ['restrictive'] ) && $_REQUEST ['restrictive'] == 'yes') {
								$aResponse ['comps_affected'] = $aCompsAffected;
							}
							$this->addEntry ( 'response', $aResponse );
						}
					} else {
						$this->addEntry ( 'response', $this->oError->throwError ( 'META_NOT_FOUND' ) );
					}
				} else {
					$this->addEntry ( 'response', $this->oError->throwError ( 'INVALID_META_IDENTIFIER' ) );
				}
			} else {
				$this->addEntry ( 'response', $this->oError->throwError ( 'Got no properties to change' ) );
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'OAUTH_ONLY' ) );
		}
		return $this->getResponse ();
	}
	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 *
	 * @url GET /find/default/$sDefault
	 */
	public function byValue() {
		if ($this->getNamespace ()) {
			if ($this->isReadable ()) {
				$this->addEntry ( 'response', array ('metas' => $this->renderMeta ( $this->oMetas->getAll ( $this->oMetavalue ) ) ) );
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'NAMESPACE_NOT_FOUND' ) );
		}
		return $this->getResponse ();
	}
	
	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 *
	 * @url DELETE /$meta/
	 */
	public function deletemeta($meta) {
		if ($this->req ['auth_type'] == 'oauth') {
			if (( int ) $meta == 0) {
				$oRow = $this->oMetas->getById ( $meta );
				if (is_array ( $oRow )) {
					$meta = ( int ) $oRow ['idmeta'];
				}
			}
			
			if (( int ) $meta > 0) {
				
				$oTableCompMetas = new Application_Model_Table ();
				$oTableCompMetas->setTable ( 'comp_metas' );
				$oTableCompMetas->setPrimary ( 'idcomp_meta' );
				$oSelect = $oTableCompMetas->fetchAll ( $oTableCompMetas->select ()->from ( $oTableCompMetas->getTable () )->where ( 'metas_idmeta=' . ( int ) $meta ) );
				if (isset ( $_GET ['force'] ) && $_GET ['force'] == 'yes') {
					if ($this->isExtendable ()) {
						if ($this->oMetas->deleteMeta ( $meta )) {
							$iGiveBudgetBack = $this->usr;
							if (isset ( $oRowToDelete->accounts_idaccount ) && ( int ) $oRowToDelete->accounts_idaccount > 0) {
								$iIdCreator = ( int ) $oRowToDelete->accounts_idaccount;
								$iGiveBudgetBack = ($iIdCreator > 0) ? $iIdCreator : $iGiveBudgetBack;
							}
							
							$this->setUsage ( $iGiveBudgetBack, array ('maxmetas' => - 1 ) );
							$this->addEntry ( 'response', array ('meta' => $meta, 'message' => 'META_WAS_DELETED', 'comps_affected' => ( int ) $oSelect->count () ) );
						}
					} else {
						$this->addEntry ( 'response', $this->oError->throwError ( 'MISSING_EXTEND_RIGHT' ) );
					}
				} else {
					if ($this->isDeleteable ()) {
						if ($oSelect->count () == 0) {
							$oRowToDelete = $this->oMetas->fetchRow ( $this->oMetas->select ()->where ( $this->oMetas->getPrimary () . '=' . $meta ) );
							if ($this->oMetas->deleteMeta ( $meta )) {
								
								$iGiveBudgetBack = $this->usr;
								if (isset ( $oRowToDelete->accounts_idaccount ) && ( int ) $oRowToDelete->accounts_idaccount > 0) {
									$iIdCreator = ( int ) $oRowToDelete->accounts_idaccount;
									$iGiveBudgetBack = ($iIdCreator > 0) ? $iIdCreator : $iGiveBudgetBack;
								}
								
								$this->setUsage ( $iGiveBudgetBack, array ('maxmetas' => - 1 ) );
								$this->addEntry ( 'response', array ('meta' => $meta, 'message' => 'META_WAS_DELETED' ) );
							}
						} else {
							$this->addEntry ( 'response', $this->oError->throwError ( 'META_IN_TOUCH_WITH_COMPOSITE' ) );
						}
					} else {
						$this->addEntry ( 'response', $this->oError->throwError ( 'MISSING_DELETE_RIGHT' ) );
					}
				}
			} else {
				$this->addEntry ( 'response', $this->oError->throwError ( 'META_NOT_FOUND' ) );
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'OAUTH_ACCESS_ONLY' ) );
		}
		return $this->getResponse ();
	}
	private function renderMeta($aMetaResult) {
		$aMetaResponse = array ();
		foreach ( $aMetaResult as $iKey => $aMetaEntry ) {
			if (! isset ( $aMetaResponse [$aMetaEntry ['name']] )) {
				$aMetaResponse [$aMetaEntry ['name']] ['id'] = $aMetaEntry ['idmeta'];
				$aMetaResponse [$aMetaEntry ['name']] ['creator'] = $aMetaEntry ['accounts_idaccount'];
				$aMetaResponse [$aMetaEntry ['name']] ['properties'] = array ();
			}
			$aMetaResponse [$aMetaEntry ['name']] ['properties'] [$aMetaEntry ['valname']] = $aMetaEntry ['valdef'];
			ksort ( $aMetaResponse [$aMetaEntry ['name']] ['properties'] );
		}
		
		return $aMetaResponse;
	}
}

?>