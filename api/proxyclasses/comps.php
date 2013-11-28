<?php

require_once 'abstract.proxy.php';
class comps extends Proxy {
	private $oComps;
	public function __construct() {
		parent::__construct ( __CLASS__ );
		$this->oComps = new Application_Model_Comp ( $this->req ['request'] ['ns'] );
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
				$oSelect = $this->oComps->select ()->setIntegrityCheck ( false )->from ( $this->oComps->getTableName (), array ('idcomposite', 'name as compname', 'alias', 'categories_idcategory as category', 'created', 'modified', 'source', 'accounts_idaccount as creator' ) )->joinLeft ( 'comp_metas', 'comp_metas.composites_idcomposite=composites.idcomposite' )->joinLeft ( 'metas', 'metas.idmeta=comp_metas.metas_idmeta' )->joinLeft ( 'comp_meta_values', 'comp_meta_values.comp_metas_idcomp_meta=comp_metas.idcomp_meta' );

				if (isset ( $_REQUEST ['apikey'] )) {
					$oSelect->where ( 'composites.namespaces_idnamespace=' . $this->req ['request'] ['ns'] . ' AND composites.pub=1' );
				} else {
					$oSelect->where ( 'composites.namespaces_idnamespace=' . $this->req ['request'] ['ns'] . ' AND composites.pub=1 OR composites.pub=0 AND composites.accounts_idaccount=' . $this->usr );
				}
				// echo $oSelect;exit;
				$oSelect->order ( array ('compname', 'name', 'valname' ) );
				$oComps = $this->oComps->fetchAll ( $oSelect )->toArray ();
				if (count ( $oComps ) > 0) {
					$aComps = $this->renderComps ( $oComps );

					$this->addEntry ( 'response', array ('composites' => $aComps ) );
				} else {
					$this->addEntry ( 'response', $this->oError->throwError ( 'NO_RESULT' ) );
				}
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
	 * @url GET /in/$idcategory
	 * @url GET /$name/in/$idcategory
	 */
	public function in($name = '', $idcategory) {
		if ($this->getNamespace ()) {
			if ($this->isReadable ()) {
				$oSelect = $this->oComps->select ()->setIntegrityCheck ( false )->from ( $this->oComps->getTableName (), array ('idcomposite', 'name as compname', 'alias', 'categories_idcategory as category', 'created', 'modified', 'source', 'accounts_idaccount as creator' ) )->joinLeft ( 'comp_metas', 'comp_metas.composites_idcomposite=composites.idcomposite' )->joinLeft ( 'metas', 'metas.idmeta=comp_metas.metas_idmeta' )->joinLeft ( 'comp_meta_values', 'comp_meta_values.comp_metas_idcomp_meta=comp_metas.idcomp_meta' );

				if (isset ( $_REQUEST ['apikey'] )) {

					$oSelect->where ( 'composites.namespaces_idnamespace=' . $this->req ['request'] ['ns'] . ' AND composites.pub=1 AND categories_idcategory=' . (( int ) $idcategory) . (($name !== false && strlen ( $name ) > 0) ? ' AND (composites.idcomposite=' . ( int ) $name . ' OR composites.name="' . $name . '" OR composites.alias="' . $name . '")' : '') );
				} else {
					$oSelect->where ( 'composites.namespaces_idnamespace=' . $this->req ['request'] ['ns'] . ' AND (composites.pub=1 OR composites.pub=0 AND composites.accounts_idaccount=' . $this->usr . ') AND categories_idcategory=' . (( int ) $idcategory) . (($name !== false && strlen ( $name ) > 0) ? ' AND (composites.idcomposite=' . ( int ) $name . ' OR composites.name="' . $name . '" OR composites.alias="' . $name . '")' : '') );
				}
				// echo $oSelect;exit;
				$oSelect->order ( array ('compname', 'name', 'valname' ) );
				$oComps = $this->oComps->fetchAll ( $oSelect )->toArray ();
				if (count ( $oComps ) > 0) {
					$aComps = $this->renderComps ( $oComps );

					$this->addEntry ( 'response', array ('composites' => $aComps ) );
				} else {
					$this->addEntry ( 'response', $this->oError->throwError ( 'NO_RESULT' ) );
				}
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
	 * @url GET /$compname/
	 */
	public function name($compname) {
		if ($this->getNamespace ()) {
			if ($this->isReadable ()) {
				$oSelect = $this->oComps->select ()->setIntegrityCheck ( false )->from ( $this->oComps->getTableName (), array ('idcomposite', 'name as compname', 'alias', 'categories_idcategory as category', 'created', 'modified', 'source', 'accounts_idaccount as creator' ) )->joinLeft ( 'comp_metas', 'comp_metas.composites_idcomposite=composites.idcomposite' )->joinLeft ( 'metas', 'metas.idmeta=comp_metas.metas_idmeta' )->joinLeft ( 'comp_meta_values', 'comp_meta_values.comp_metas_idcomp_meta=comp_metas.idcomp_meta' );

				if (isset ( $_REQUEST ['apikey'] )) {
					$oSelect->where ( 'composites.namespaces_idnamespace=' . $this->req ['request'] ['ns'] . ' AND (composites.name="' . $compname . '" OR composites.idcomposite=' . (( int ) $compname).')' );
				} else {
					$oSelect->where ( 'composites.namespaces_idnamespace=' . $this->req ['request'] ['ns'] . ' AND (composites.pub=1 OR composites.pub=0 AND composites.accounts_idaccount=' . $this->usr . ') AND (composites.name="' . $compname . '" OR composites.idcomposite=' . (( int ) $compname).')' );
				}

				$oSelect->order ( array ('compname', 'name', 'valname' ) );
				$oComps = $this->oComps->fetchAll ( $oSelect )->toArray ();
				if (count ( $oComps ) > 0) {
					$aComps = $this->renderComps ( $oComps );
					if (count ( $aComps ) == 1 && isset ( $aComps [0] )) {
						$aComps = $aComps [0];
					}
					$this->addEntry ( 'response', array ('composites' => $aComps ) );
				} else {
					$this->addEntry ( 'response', $this->oError->throwError ( 'NO_RESULT' ) );
				}
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'NAMESPACE_NOT_FOUND' ) );
		}
		return $this->getResponse ();
	}

	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 * @url GET /meta/$name
	 * @url GET /meta/$name/$property
	 * @url GET /meta/$name/$property/$value
	 *
	 * @url GET /property/$property
	 * @url GET /property/$property/$value
	 */
	public function meta($name = false, $property = false, $value = false) {
		if ($name) {
			$name = Preprocessor_String::filterBadChars ( $name );
			if (stripos ( $name, ',' ) !== false && ! $property && ! $value) {
				$aNames = array_unique ( explode ( ',', $name ) );

				if (is_array ( $aNames )) {
					foreach ( $aNames as $iKey => $sName ) {
						if (! isset ( $sExpr )) {
							$sExpr = '';
						}

						if (( int ) $sName > 0) {
							$sExpr .= (($iKey > 0) ? 'OR ' : '') . '(metas.idmeta=' . ( int ) $sName . ' OR metas.name="' . $sName . '") ';
						} else {
							$sExpr .= (($iKey > 0) ? 'OR ' : '') . '(metas.name="' . $sName . '") ';
						}
					}
				}
			}
		}

		if (! $name && $property && ! $value) {
			$property = Preprocessor_String::filterBadChars ( $property );
			if (stripos ( $property, ',' ) !== false) {
				$aProps = array_unique ( explode ( ',', $property ) );

				if (is_array ( $aProps )) {
					foreach ( $aProps as $iKey => $sProp ) {
						if (! isset ( $sExpr )) {
							$sExpr = '';
						}

						$sExpr .= (($iKey > 0) ? 'OR ' : '') . '(comp_meta_values.valname="' . $sProp . '") ';
					}
				}
			}
		}

		if ($this->getNamespace () && strlen ( $name ) < 100) {
			if ($this->isReadable ()) {
				$oSelect = $this->oComps->select ()->setIntegrityCheck ( false )->from ( $this->oComps->getTableName (), array ('idcomposite', 'name as compname', 'alias', 'categories_idcategory as category', 'created', 'modified', 'source', 'accounts_idaccount as creator' ) )->joinLeft ( 'comp_metas', 'comp_metas.composites_idcomposite=composites.idcomposite' )->joinLeft ( 'metas', 'metas.idmeta=comp_metas.metas_idmeta' )->joinLeft ( 'comp_meta_values', 'comp_meta_values.comp_metas_idcomp_meta=comp_metas.idcomp_meta' );
				if (! isset ( $sExpr )) {

					$sExpr = '';

					if ($name) {
						$sExpr .= '(metas.name="' . $name . '" OR metas.idmeta=' . (( int ) $name) . ')';
					}
					if ($property) {

						$sExpr .= ((strlen ( $sExpr ) > 0) ? ' AND ' : ' ') . 'comp_meta_values.valname="' . Preprocessor_String::filterBadChars ( $property ) . '" ';
					}

					if ($value) {
						$sExpr .= ((strlen ( $sExpr ) > 0) ? ' AND ' : ' ') . 'comp_meta_values.valdef="' . Preprocessor_String::filterBadChars ( $value ) . '" ';
					}
				}
				if (isset ( $_REQUEST ['apikey'] )) {
					$oSelect->where ( 'composites.namespaces_idnamespace=' . $this->req ['request'] ['ns'] . ' AND ' . $sExpr );
				} else {
					$oSelect->where ( 'composites.namespaces_idnamespace=' . $this->req ['request'] ['ns'] . ' AND (composites.pub=1 OR composites.pub=0 AND composites.accounts_idaccount=' . $this->usr . ') AND ' . $sExpr );
				}
				$oSelect->order ( array ('compname', 'name', 'valname' ) );
				// echo $oSelect;
				// exit ();
				$oComps = $this->oComps->fetchAll ( $oSelect )->toArray ();

				$aComps = array ();
				foreach ( $oComps as $oRow ) {
					$aComps [$oRow ['idcomposite']] = $oRow ['idcomposite'];
				}
				if (count ( $aComps ) > 0) {
					$oSelect = $this->oComps->select ()->setIntegrityCheck ( false )->from ( $this->oComps->getTableName (), array ('idcomposite', 'name as compname', 'alias', 'categories_idcategory as category', 'created', 'modified', 'source', 'accounts_idaccount as creator' ) )->joinLeft ( 'comp_metas', 'comp_metas.composites_idcomposite=composites.idcomposite' )->joinLeft ( 'metas', 'metas.idmeta=comp_metas.metas_idmeta' )->joinLeft ( 'comp_meta_values', 'comp_meta_values.comp_metas_idcomp_meta=comp_metas.idcomp_meta' );

					if (isset ( $_REQUEST ['apikey'] )) {
						$oSelect->where ( 'composites.namespaces_idnamespace=' . $this->req ['request'] ['ns'] . ' AND composites.idcomposite IN(' . implode ( ',', $aComps ) . ')' );
					} else {
						$oSelect->where ( 'composites.namespaces_idnamespace=' . $this->req ['request'] ['ns'] . ' AND (composites.pub=1 OR composites.pub=0 AND composites.accounts_idaccount=' . $this->usr . ') AND composites.idcomposite IN(' . implode ( ',', $aComps ) . ')' );
					}
					// echo $oSelect;exit;
					$oSelect->order ( array ('compname', 'name', 'valname' ) );
					// echo $oSelect;exit;
					$oComps = $this->oComps->fetchAll ( $oSelect )->toArray ();

					$aComps = $this->renderComps ( $oComps );

					$this->addEntry ( 'response', array ('composites' => $aComps ) );
				} else {
					$this->addEntry ( 'response', $this->oError->throwError ( 'NO_RESULT' ) );
				}
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'NAMESPACE_NOT_FOUND' ) );
		}
		return $this->getResponse ();
	}

	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 * @url POST /add/$name/in/$category/
	 */
	public function add($name, $category) {
		if ($this->req ['auth_type'] == 'oauth') {
			$sSource = 'false';
			$bPuplic = false;
			if (isset ( $_POST ['source'] )) {
				$sSource = $_POST ['source'];
			}
			if (isset ( $_POST ['public'] ) && $_POST ['public'] == 'true') {
				$bPuplic = true;
			}
			if (( int ) $category > 0) {
				if ($this->isCreateable ()) {
					$aResponse = $this->oComps->addComp ( $this->usr, $name, $category, $sSource, $bPuplic );

					if (! $aResponse ['response']) {
						$this->addEntry ( 'response', array ('composite' => $aResponse ['composite'], 'message' => $aResponse ['message'] ) );
					} else {
						$this->addEntry ( 'response', $this->oError->throwError ( $aResponse ['message'] ) );
					}
				}
			} else {
				$this->addEntry ( 'response', $this->oError->throwError ( 'CATEGORY_IDENT_NOT_INTEGER' ) );
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'OAUTH_ONLY' ) );
		}
		return $this->getResponse ();
	}

	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 * @url POST duplicate/$composite/
	 * @url POST duplicate/$composite/in/$category/
	 */
	public function duplicate($composite, $category = false) {
		if ($this->req ['auth_type'] == 'oauth') {
			if (( int ) $composite > 0) {
				if ($this->isCreateable ()) {
					$bPublic = false;
					$sSource = false;
					$sNewName = false;

					if (isset ( $_POST ['public'] ) && $_POST ['public'] == 'true') {
						$bPublic = true;
					}
					if (isset ( $_POST ['source'] ) && strlen ( $_POST ['source'] ) > 0) {
						$sSource = $_POST ['source'];
					}
					if (isset ( $_POST ['name'] ) && strlen ( $_POST ['name'] ) > 3) {
						$sNewName = $_POST ['name'];
					}
					$aResponse = $this->oComps->duplicate ( $this->usr, $composite, $sNewName, $sSource, $category, $bPublic );
					if (! $aResponse ['response']) {
						$this->addEntry ( 'response', array ('composite' => $aResponse ['composite'], 'message' => $aResponse ['message'] ) );
					} else {
						$this->addEntry ( 'response', $this->oError->throwError ( $aResponse ['message'] ) );
					}
				}
			} else {
				$this->addEntry ( 'response', $this->oError->throwError ( 'COMPOSITE_IDENT_NOT_INTEGER' ) );
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'OAUTH_ONLY' ) );
		}

		return $this->getResponse ();
	}

	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 * @url POST change/source/from/$composite
	 */
	public function changesource($composite) {
		if ($this->req ['auth_type'] == 'oauth') {
			if (( int ) $composite > 0) {
				if ($this->isChangeable ()) {
					if (isset ( $_POST ['source'] ) && strlen ( $_POST ['source'] ) > 0) {
						$mUpdate = $this->oComps->update ( array ('source' => $_POST ['source'], 'modified' => new Zend_Db_Expr ( 'CURRENT_TIMESTAMP' ) ), 'idcomposite=' . $composite . ' AND namespaces_idnamespace=' . $this->req ['request'] ['ns'] . ' AND (pub=1 OR accounts_idaccount=' . $this->usr . ')' );
						if ($mUpdate == 1) {
							$this->addEntry ( 'response', array ('message' => 'Composite\'s source was updated' ) );
						} else {
							$this->addEntry ( 'response', $this->oError->throwError ( 'COMPOSITE_NOT_FOUND_OR_SOURCE_ALREADY_SET' ) );
						}
					} else {
						$this->addEntry ( 'response', $this->oError->throwError ( 'SOURCE_IS_EMPTY' ) );
					}
				}
			} else {
				$this->addEntry ( 'response', $this->oError->throwError ( 'COMPOSITE_IDENT_NOT_INTEGER' ) );
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'OAUTH_ONLY' ) );
		}

		return $this->getResponse ();
	}

	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 * @url POST change/properties/from/$meta/in/$composite
	 */
	public function changeprops($meta, $composite) {
		if ($this->req ['auth_type'] == 'oauth') {
			$oPropData = false;
			if (isset ( $_REQUEST ['properties'] ) && is_object ( json_decode ( $_REQUEST ['properties'] ) )) {
				$oPropData = json_decode ( $_REQUEST ['properties'] );

				if (( int ) $composite > 0 && is_string ( $meta )) {
					if ($this->isChangeable ()) {
						$aResponse = $this->oComps->changeProps ( $this->usr, $meta, $composite, $oPropData );
						if (! $aResponse ['response']) {
							unset ( $aResponse ['response'] );
							$this->addEntry ( 'response', $aResponse );
						} else {
							$this->addEntry ( 'response', $this->oError->throwError ( $aResponse ['message'] ) );
						}
					}
				} else {
					$this->addEntry ( 'response', $this->oError->throwError ( 'COMPOSITE_IDENT_NOT_INTEGER' ) );
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
	 * @url POST change/$composite/to/$status
	 */
	public function status($composite, $status) {
		if ($this->req ['auth_type'] == 'oauth') {

			if (( int ) $composite > 0) {
				if ($status == 'public' || $status == 'private') {
					if ($status == 'public') {
						$status = 1;
					} else {
						$status = 0;
					}
					if ($this->isChangeable ()) {
						if ($this->oComps->statusComp ( $this->usr, $composite, $status )) {
							$this->addEntry ( 'response', array ('message' => 'Composite is ' . (($status == 1) ? 'public' : 'private') ) );
						} else {
							$sAddString = '';
							if ($status == 1) {
								$sAddString = '(only owners are able to set a composite public)';
							}
							$this->addEntry ( 'response', $this->oError->throwError ( 'Cannot change status ' . $sAddString ) );
						}
					}
				} else {
					$this->addEntry ( 'response', $this->oError->throwError ( 'Unknown composite status' ) );
				}
			} else {
				$this->addEntry ( 'response', $this->oError->throwError ( 'Invalid composite ID (only numeric values allowed)' ) );
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'OAUTH_ONLY' ) );
		}
		return $this->getResponse ();
	}

	/**
	 * Returns a JSON string object to the browser when hitting the root of the
	 * domain
	 * @url POST touch/$meta/to/$composite/
	 */
	public function touch($meta, $composite) {
		if ($this->req ['auth_type'] == 'oauth') {
			if ($this->isChangeable ()) {
				$aResponse = $this->oComps->touch ( $this->usr, $composite, $meta );
				if (! $aResponse ['response']) {
					unset ( $aResponse ['response'] );
					$this->addEntry ( 'response', $aResponse );
				} else {
					$this->addEntry ( 'response', $this->oError->throwError ( $aResponse ['message'] ) );
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
	 * @url DELETE /$composite/
	 */
	public function delete($composite) {
		if ($this->req ['auth_type'] == 'oauth') {
			if (( int ) $composite > 0) {
				if ($this->isDeleteable ()) {
					$aResponse = $this->oComps->remove ( $this->usr, $composite );
					if (! $aResponse ['response']) {
						$this->addEntry ( 'response', array ('composite' => ( int ) $composite, 'message' => $aResponse ['message'] ) );
					} else {
						$this->addEntry ( 'response', $this->oError->throwError ( $aResponse ['message'] ) );
					}
				} else {
					$this->addEntry ( 'response', $this->oError->throwError ( 'COMPOSITE_IDENT_NOT_INTEGER' ) );
				}
			}
		} else {
			$this->addEntry ( 'response', $this->oError->throwError ( 'OAUTH_ONLY' ) );
		}
		return $this->getResponse ();
	}
	private function renderComps($aComps) {
		$aCompsOut = array ();

		foreach ( $aComps as $iKey => $aComp ) {
			if (! isset ( $aCompsOut [$aComp ['idcomposite']] )) {
				$aCompsOut [$aComp ['idcomposite']] = array ();
				$aCompsOut [$aComp ['idcomposite']] ['id'] = ( int ) $aComp ['idcomposite'];
				$aCompsOut [$aComp ['idcomposite']] ['name'] = $aComp ['compname'];
				$aCompsOut [$aComp ['idcomposite']] ['alias'] = $aComp ['alias'];
				$aCompsOut [$aComp ['idcomposite']] ['source'] = $aComp ['source'];
				$aCompsOut [$aComp ['idcomposite']] ['category'] = ( int ) $aComp ['category'];
				$aCompsOut [$aComp ['idcomposite']] ['created'] = $aComp ['created'];
				$aCompsOut [$aComp ['idcomposite']] ['modified'] = $aComp ['modified'];
				$aCompsOut [$aComp ['idcomposite']] ['author'] = $aComp ['creator'];

				$aCompsOut [$aComp ['idcomposite']] ['metas'] = array ();
			}

			if (! isset ( $aCompsOut [$aComp ['idcomposite']] ['metas'] [$aComp ['name']] )) {
				$aCompsOut [$aComp ['idcomposite']] ['metas'] [$aComp ['name']] = array ();

				$aCompsOut [$aComp ['idcomposite']] ['metas'] [$aComp ['name']] ['id'] = ( int ) $aComp ['metas_idmeta'];
				$aCompsOut [$aComp ['idcomposite']] ['metas'] [$aComp ['name']] ['author'] = ( int ) $aComp ['accounts_idaccount'];

				$aCompsOut [$aComp ['idcomposite']] ['metas'] [$aComp ['name']] ['properties'] = array ();
			}

			if (! isset ( $aCompsOut [$aComp ['idcomposite']] ['metas'] [$aComp ['name']] ['properties'] [$aComp ['valname']] )) {
				$aCompsOut [$aComp ['idcomposite']] ['metas'] [$aComp ['name']] ['properties'] [$aComp ['valname']] = $aComp ['valdef'];
			}
		}

		$aCompsOut = array_merge ( $aCompsOut );
		return $aCompsOut;
	}
}

?>
