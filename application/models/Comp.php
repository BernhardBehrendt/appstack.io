<?php
/**
 * Central Composite model
 * This model provides an abstraction of the metatable an controlls the metanamevalues
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 *
 * @category Metainformations_Model
 * @version 1.0.0
 *
 */
class Application_Model_Comp extends Zend_Db_Table_Abstract {
	protected $_name = 'composites';
	protected $_primary = 'idcomposite';
	protected $_foreign = 'categories_idcategory';
	protected $_sModified = 'modified';
	protected $_toString = 'name';
	private $iIdNamespace;
	private $oAccounts;
	private $oSettings;
	public function __construct($iIdNamespace) {
		if (! $iIdNamespace) {
			throw new Exception ( "No owner was set!" );
		} else {
			$this->iIdNamespace = $iIdNamespace;
			$this->oSettings = new Zend_Config_Ini ( APPLICATION_PATH . '/configs/application.ini', 'production' );
			$this->oAccounts = new Application_Model_Account ( $this->oSettings );
		}
		parent::__construct ();
		if (! $this->_toString) {
			$this->_toString = $this->_primary [1];
		}
	}

	/**
	 * Lookup for an object and check if method toArray() exists
	 *
	 * @param
	 *        	$oResult
	 *
	 * @return mixed array();
	 */
	private function tryReturn($oResult) {
		return (is_object ( $oResult ) && method_exists ( $oResult, 'toArray' )) ? $oResult->toArray () : false;
	}

	/**
	 * Returns table primary columnname
	 *
	 * @return string
	 */
	public function getPrimary() {
		if (is_array ( $this->_primary )) {
			return $this->_primary [1];
		}

		return $this->_primary;
	}

	/**
	 * Returns table name
	 *
	 * @return string
	 */
	public function getTableName() {
		return $this->_name;
	}

	/**
	 * Fetch a table row by Id
	 *
	 * @param int $iId
	 */
	public function getById($iId) {
		if (isset ( $iId )) {
			$row = $this->fetchRow ( $this->_primary . '=' . $iId );
			return $this->tryReturn ( $row );
		}

		return false;
	}
	public function addComp($uid, $sName, $iIdCategory, $sSource = 'false', $bPublic = false) {
		$aResult = array ('error' => true, 'message' => 'Server error' );
		$sAlias = Preprocessor_String::legalizeString ( $sName );
		if (( int ) $this->iIdNamespace > 0 && ( int ) $iIdCategory > 0 && strlen ( $sAlias ) > 0) {
			$oTableCategories = new Application_Model_Table ();
			$oTableCategories->setTable ( 'categories' );
			$oTableCategories->setPrimary ( 'idcategory' );

			$aMatchCat = $oTableCategories->getRow ( 'namespaces_idnamespace=' . $this->iIdNamespace . ' AND idcategory=' . $iIdCategory );

			if (count ( $aMatchCat ) !== 0) {

				if ($this->oAccounts->setUsage ( $uid, array ('maxcomps' => 1 ) )) {
					$iInserted = $this->insert ( array ('categories_idcategory' => $iIdCategory, 'accounts_idaccount' => $uid, 'alias' => $sAlias, 'name' => $sName, 'source' => $sSource, 'namespaces_idnamespace' => $this->iIdNamespace, 'pub' => (($bPublic == true) ? 1 : 0) ) );
					if (( int ) $iInserted > 0) {
						$aResult = array ('error' => false, 'composite' => ( int ) $iInserted, 'message' => 'Composite was created', 'insertin' => $iIdCategory );
					}
				} else {
					$aResult = array ('error' => true, 'message' => 'The limit of your composites budget has been reached' );
				}
			} else {
				$aResult = array ('error' => true, 'message' => 'Target category doesn\\\'t exist' );
			}
		}

		return $aResult;
	}
	public function duplicate($uid, $iIdComposite, $sNewName = false, $sSource = false, $iIdCategory = false, $bPublic = false) {
		$aResult = array ('error' => true, 'message' => 'Server error' );
		$sSelect = $this->select ()->where ( 'namespaces_idnamespace=' . $this->iIdNamespace . ' AND ' . $this->getPrimary () . '=' . $iIdComposite );
		$oComposite = $this->fetchRow ( $sSelect );

		if (is_object ( $oComposite )) {
			$aInsert = array ();
			$aInsertColumns = array ('alias', 'name', 'source', 'categories_idcategory', 'namespaces_idnamespace' );
			foreach ( $oComposite as $sColumn => $mValue ) {
				if (in_array ( $sColumn, $aInsertColumns )) {
					// CATEGORY//source

					$aInsert [$sColumn] = $mValue;
				}
			}

			if ($sNewName && strlen ( $sNewName ) > 0) {
				$aInsert ['name'] = Preprocessor_String::filterBadChars ( $sNewName );
			} else {
				$sAddNo = 2;
				if (stripos ( $aInsert ['name'], '_' ) !== false) {
					$aSplitName = explode ( '_', $aInsert ['name'] );
					if (is_array ( $aSplitName ) && ( int ) $aSplitName [count ( $aSplitName ) - 1] > 0) {
						$sAddNo = ( int ) $aSplitName [count ( $aSplitName ) - 1] + 1;
						$aInsert ['name'] = str_replace ( '_' . $aSplitName [count ( $aSplitName ) - 1], '', $aInsert ['name'] );
					}
				}
				$aInsert ['name'] = $aInsert ['name'] . '_' . $sAddNo;
			}

			if ($sSource && strlen ( $sSource ) > 0) {
				$aInsert ['source'] = $sSource;
			}

			if ($iIdCategory && ( int ) $iIdCategory > 0) {
				$oCategory = new Application_Model_Cat ( $this->iIdNamespace );
				if ($oCategory->isInSpace ( ( int ) $iIdCategory )) {
					$aInsert ['categories_idcategory'] = ( int ) $iIdCategory;
				}
			}

			if ($bPublic === true) {
				$aInsert ['pub'] = 1;
			}

			$aInsert ['alias'] = Preprocessor_String::legalizeString ( $aInsert ['name'] );

			$aInsert ['accounts_idaccount'] = $uid;

			if ($this->oAccounts->setUsage ( $uid, array ('maxcomps' => 1 ) )) {
				$iPrimaryNew = $this->createRow ( $aInsert )->save ();
				if ($iPrimaryNew) {

					$oTblCompMetas = new Application_Model_Table ();
					$oTblCMNV = new Application_Model_Table ();

					$oTblCompMetas->setTable ( 'comp_metas' );
					$oTblCMNV->setTable ( 'comp_meta_values' );

					$oTblCMNV->setPrimary ( 'idcomp_meta_value' );
					$oTblCompMetas->setPrimary ( 'idcomp_meta' );

					$sSelect = $oTblCompMetas->select ()->where ( 'composites_idcomposite=' . $oComposite->idcomposite )->order ( $oTblCompMetas->getPrimary () );

					$oCompMetas = $oTblCompMetas->fetchAll ( $sSelect );
					// @TODO CREATE A MULTIINSERT BY TIME
					if (is_object ( $oCompMetas )) {
						foreach ( $oCompMetas as $oRow ) {

							$iIdCompMetaNew = $oTblCompMetas->createRow ( array ('metas_idmeta' => $oRow->metas_idmeta, 'composites_idcomposite' => $iPrimaryNew ) )->save ();
							$sSelectCMNV = $oTblCMNV->select ()->where ( 'comp_metas_idcomp_meta=' . $oRow->idcomp_meta );
							$oCMNV = $oTblCMNV->fetchAll ( $sSelectCMNV );

							foreach ( $oCMNV as $oRowb ) {
								$oTblCMNV->createRow ( array ('comp_metas_idcomp_meta' => $iIdCompMetaNew, 'valname' => $oRowb->valname, 'valdef' => $oRowb->valdef ) )->save ();
							}
						}
					}
					$aResult = array ('error' => false, 'message' => 'Composite was duplicated', 'composite' => $iPrimaryNew, 'category' => $oComposite->categories_idcategory );
				} else {
					$aResult = array ('error' => true, 'message' => 'Error by creating composite' );
				}
			} else {
				$aResult = array ('error' => true, 'message' => 'The limit of your composites budget has been reached' );
			}
		} else {
			$aResult = array ('error' => true, 'message' => 'Error can\'t duplicate composite' );
		}

		return $aResult;
	}
	public function insertComp($iIdComp, $iIdCat) {
		if (is_array ( $this->_primary )) {
			$sPrimary = $this->_primary [1];
		} else {
			$sPrimary = $this->_primary;
		}
		if ($this->_db->query ( "UPDATE {$this->_name} SET modified = CURRENT_TIMESTAMP, {$this->_foreign} = $iIdCat WHERE {$sPrimary} = {$iIdComp}" )) {
			return true;
		}
		return false;
	}
	public function renameComp($iIdComposite, $sNewName) {
		if (is_array ( $this->_primary )) {
			$sPrimary = $this->_primary [1];
		} else {
			$sPrimary = $this->_primary;
		}

		if (strlen ( $sNewName ) > 0) {
			$sNewAlias = Preprocessor_String::legalizeString ( $sNewName );

			$this->_db->query ( "UPDATE {$this->_name} SET {$this->_toString} = '{$sNewName}', alias='{$sNewAlias}' WHERE {$sPrimary} = {$iIdComposite} AND namespaces_idnamespace = {$this->iIdNamespace}" );

			$this->updateComp ( $iIdComposite );

			return true;
		}
		return false;
	}
	public function relocateComp($uid, $iIdComposite, $sSource) {
		if (strlen ( $sSource ) > 0) {
			// str_replace(array("\n", "\r", " "), '', $sSource);
			// echo strlen($sSource);exit;
			// ini_set ( 'display_errors', E_ALL );
			$oRow = $this->update ( array ('source' => urlencode ( $sSource ), 'modified' => new Zend_Db_Expr ( 'CURRENT_TIMESTAMP' ) ), 'idcomposite=' . $iIdComposite . ' AND namespaces_idnamespace=' . $this->iIdNamespace . ' AND (pub=1 OR accounts_idaccount=' . $uid . ')' );
			if (( int ) $oRow == 1) {
				$this->updateComp ( $iIdComposite );
				return true;
			}
		}
		return false;
	}
	public function statusComp($iIdUser, $iIdComposite, $iStatus) {
		if (is_array ( $this->_primary )) {
			$sPrimary = $this->_primary [1];
		} else {
			$sPrimary = $this->_primary;
		}

		if (strlen ( $iStatus ) == 1 && ( int ) $iIdUser > 0) {
			if ($iStatus == 1 && $this->fetchAll ( $this->select ()->where ( $sPrimary . '=' . $iIdComposite . ' AND namespaces_idnamespace=' . $this->iIdNamespace . ' AND accounts_idaccount=' . $iIdUser ) )->count () > 0) {
				$this->_db->query ( "UPDATE {$this->_name} SET pub = '{$iStatus}' WHERE {$sPrimary} = {$iIdComposite} AND namespaces_idnamespace = {$this->iIdNamespace} AND accounts_idaccount={$iIdUser}" );
				$this->updateComp ( $iIdComposite );
				return true;
			}

			if ($iStatus == 0) {
				$this->_db->query ( "UPDATE {$this->_name} SET pub = '{$iStatus}' WHERE {$sPrimary} = {$iIdComposite} AND namespaces_idnamespace = {$this->iIdNamespace}" );
				$this->updateComp ( $iIdComposite );
				return true;
			}
		}
		return false;
	}
	public function touch($iIdUser, $iIdComposite, $iIdMeta) {
		$oMeta = new Application_Model_Meta ( $this->iIdNamespace );

		if (( int ) $iIdMeta == 0) {
			$oRow = $oMeta->fetchRow ( $oMeta->select ()->where ( 'name="' . $iIdMeta . '" AND namespaces_idnamespace=' . $this->iIdNamespace ) );
			if (isset ( $oRow->idmeta ) && ( int ) $oRow->idmeta > 0) {
				$iIdMeta = ( int ) $oRow->idmeta;
			}
		}
		$aResponse = array ('error' => true, 'message' => 'Unknown error' );
		if (( int ) $iIdComposite > 0 && ( int ) $iIdMeta > 0 && ( int ) $iIdUser > 0) {
			// $aCompTarget = $this->getIdByValue ( array ($this->getPrimary ()
			// => $iIdComposite ) );
			$aCompTarget = $this->fetchRow ( $this->select ()->where ( $this->getPrimary () . '=' . $iIdComposite . ' AND (accounts_idaccount=' . $iIdUser . ' OR pub=1)' ) );
			if (is_object ( $aCompTarget )) {
				$aCompTarget = $aCompTarget->toArray ();
			}
			if (is_array ( $aCompTarget ) && isset ( $aCompTarget [$this->getPrimary ()] )) {

				$aMetaPut = $oMeta->getIdByValue ( array ($oMeta->getPrimary () => Preprocessor_String::filterBadChars ( $iIdMeta ) ) );

				if (is_array ( $aMetaPut ) && isset ( $aMetaPut [$oMeta->getPrimary ()] )) {
					// Diese Werte in die comp metas schreiben
					$oTableCompMetas = new Application_Model_Table ();
					$oTableCompMetas->setTable ( 'comp_metas' );
					$oTableCompMetas->setPrimary ( 'idcomp_meta' );

					$oSelectCompMetas = $oTableCompMetas->select ()->where ( 'composites_idcomposite=' . $aCompTarget [$this->getPrimary ()] . ' AND metas_idmeta=' . $aMetaPut [$oMeta->getPrimary ()] );
					if ($oTableCompMetas->fetchAll ( $oSelectCompMetas )->count () == 0) {

						$iIdCompMeta = $oTableCompMetas->insert ( array ('metas_idmeta' => $aMetaPut [$oMeta->getPrimary ()], 'composites_idcomposite' => $aCompTarget [$this->getPrimary ()] ) );

						// Here We can start the transfer process;
						$oMetaValue = new Application_Model_Metavalue ();
						$oMetaValues = $oMetaValue->fetchAll ( $oMetaValue->getForeign () . '=' . $aMetaPut [$oMeta->getPrimary ()] );

						$oTableCMNV = new Application_Model_Table ();
						$oTableCMNV->setTable ( 'comp_meta_values' );
						$oTableCMNV->setPrimary ( 'idcomp_meta_value' );

						foreach ( $oMetaValues as $sColumn => $oColumnValue ) {
							$oTableCMNV->insert ( array ('comp_metas_idcomp_meta' => $iIdCompMeta, 'valname' => $oColumnValue->valname, 'valdef' => (isset ( $oColumnValue->valdef )) ? $oColumnValue->valdef : NULL ) );
						}
						$aResponse = array ('error' => false, 'message' => 'Meta was touched to composite', 'composite' => ( int ) $iIdComposite );
					} else {
						$aResponse = array ('error' => true, 'message' => 'Meta is already in touch' );
					}
				} else {
					$aResponse = array ('error' => true, 'message' => 'Meta was not found' );
				}
			} else {
				$aResponse = array ('error' => true, 'message' => 'Cannot touch metas to composites. Is it your own?' );
			}
		} else {
			$aResponse = array ('error' => true, 'message' => 'Invalid identifiers given' );
		}
		return $aResponse;
	}
	/**
	 *
	 * @param unknown_type $iIdUser
	 * @param unknown_type $sMetaName
	 * @param unknown_type $iIdComposite
	 * @param unknown_type $oProperties
	 */
	public function changeProps($iIdUser, $sMetaName, $iIdComposite, $oProperties) {
		$aResponse = array ('error' => true, 'message' => 'Unknown error' );
		if (is_object ( $oProperties )) {
			if (( int ) $iIdComposite > 0) {
				$iCompInSpace = $this->fetchAll ( $this->select ()->where ( $this->getPrimary () . '=' . ( int ) $iIdComposite . ' AND (pub=1 OR accounts_idaccount=' . $iIdUser . ')' ) )->count ();
				if ($iCompInSpace == 1) {
					$oMetas = new Application_Model_Meta ( $this->iIdNamespace );
					$oMetaChange = $oMetas->fetchRow ( $oMetas->select ()->where ( 'name="' . Preprocessor_String::legalizeString ( $sMetaName ) . '" AND namespaces_idnamespace=' . $this->iIdNamespace ) );
					if (is_object ( $oMetaChange )) {
						$oCompMetas = new Application_Model_Table ();
						$oCompMetas->setTable ( 'comp_metas' );
						$oCompMetas->setPrimary ( 'idcomp_meta' );

						$oCompMeta = $oCompMetas->fetchRow ( $oCompMetas->select ()->where ( 'composites_idcomposite=' . $iIdComposite . ' AND metas_idmeta=' . $oMetaChange->idmeta ) );
						if (is_object ( $oCompMeta )) {
							$oCompMetaValues = new Application_Model_Table ();
							$oCompMetaValues->setTable ( 'comp_meta_values' );
							$oCompMetaValues->setPrimary ( 'idcomp_meta_value' );
							$aChanges = array ();
							$bUpdated = false;
							foreach ( $oProperties as $sName => $sNewValue ) {
								$sNewValue = Preprocessor_String::apiStringSafe ( $sNewValue );
								$aChanges [$sName] = 'property is not defined';
								$iUpdated = $oCompMetaValues->update ( array ('valdef' => $sNewValue ), 'comp_metas_idcomp_meta=' . $oCompMeta->idcomp_meta . ' AND valname="' . $sName . '"' );
								if ($iUpdated > 0) {
									$bUpdated = true;
									$aChanges [$sName] = $sNewValue;
								}
							}
							if ($bUpdated) {
								$aResponse = array ('error' => false, 'message' => 'Properties changed', 'changes' => $aChanges );
							} else {
								$aResponse = array ('error' => true, 'message' => 'Propertylist contains no defined properties or has already the property values from list' );
							}
						} else {
							$aResponse = array ('error' => true, 'message' => 'Metaitem is not in touch with composite' );
						}
					} else {
						$aResponse = array ('error' => true, 'message' => 'Metaitem not found' );
					}
				} else {
					$aResponse = array ('error' => true, 'message' => 'Composite not found' );
				}
			} else {
				$aResponse = array ('error' => true, 'message' => 'Invalid identifiers given' );
			}
		} else {
			$aResponse = array ('error' => true, 'message' => 'Got no properties to change' );
		}
		return $aResponse;
	}
	public function remove($iIdUser, $iIdComposite) {
		$aResponse = array ('error' => true, 'message' => 'Unknown error' );
		$aCat = $this->getById ( ( int ) $iIdComposite );
		if ($aCat ['namespaces_idnamespace'] == $this->iIdNamespace && ( int ) $iIdComposite > 0) {
			if ($this->delete ( 'idcomposite=' . $iIdComposite . ' AND (accounts_idaccount=' . ( int ) $iIdUser . ' OR pub=1)' )) {
				$aResponse = array ('error' => false, 'message' => 'Composite was deleted', 'category' => $aCat ['categories_idcategory'], 'composite' => $_POST ['comp'] );

				// DELETA ALL COMP METAS
				$oTableCompMetas = new Application_Model_Table ();
				$oTableCompMetas->setTable ( 'comp_metas' );
				$oTableCompMetas->setPrimary ( 'idcomp_meta' );

				$oTableCompMetas->delete ( 'composites_idcomposite=' . $iIdComposite );
				$iGiveBudgetBack = ( int ) $iIdUser;
				if (isset ( $aCat ['accounts_idaccount'] ) && ( int ) $aCat ['accounts_idaccount'] > 0) {
					$iGiveBudgetBack = $aCat ['accounts_idaccount'];
				}
				$this->oAccounts->setUsage ( $iGiveBudgetBack, array ('maxcomps' => - 1 ) );
			} else {
				$aResponse = array ('error' => true, 'message' => 'Composite is not yours' );
			}
		} else {
			$aResponse = array ('error' => true, 'message' => 'Composite not found!' );
		}
		return $aResponse;
	}
	private function updateComp($iIdComposite) {
		if (is_array ( $this->_primary )) {
			$sPrimary = $this->_primary [1];
		} else {
			$sPrimary = $this->_primary;
		}
		$this->_db->query ( "UPDATE {$this->_name} SET {$this->_sModified} = CURRENT_TIMESTAMP WHERE {$sPrimary} = {$iIdComposite} AND namespaces_idnamespace = {$this->iIdNamespace}" );
	}
	public function getByCategory($iFkCat) {
		if (isset ( $iFkCat )) {
			$row = $this->fetchAll ( $this->_foreign . '=' . $iFkCat );
			return $this->tryReturn ( $row );
		}

		return false;
	}
	public function getByUserId($iFkCat) {
		if (isset ( $iFkCat )) {
			$row = $this->fetchAll ( 'namespaces_idnamespace=' . $iFkCat );
			return $this->tryReturn ( $row );
		}

		return false;
	}

	/**
	 * return a rowset by a one element array (array('colname' => 'value'));
	 *
	 * @param array $aColumnValue
	 *
	 * @return $rowset array
	 */
	public function getIdByValue($aColumnValue) {
		if (isset ( $this->iIdNamespace )) {
			unset ( $aColumnValue [$this->_foreign] );
			$aArrayKeys = array_keys ( $aColumnValue );

			if (count ( $aArrayKeys ) == 1) {

				$sColumn = $aArrayKeys [0];
				$sValue = $aColumnValue [$sColumn];

				// if not an integer addstringmask ('')
				if (! is_int ( $sValue )) {
					$sValue = '\'' . $sValue . '\'';
				}

				$row = $this->fetchRow ( $sColumn . "=" . $sValue . ' AND namespaces_idnamespace=' . $this->iIdNamespace );

				return $this->tryReturn ( $row );
			}
		}
		return false;
	}
	public function extendTree($iIdUser, $aTree) {
		if (is_array ( $aTree )) {
			for($i = 0; $i < count ( $aTree ); $i ++) {
				// Ermittle die anzahl der composites für eine category
				$aTree [$i] ['comps'] = count ( $this->fetchAll ( $this->_foreign . '=' . $aTree [$i] ['ident'] . ' AND (accounts_idaccount=' . ( int ) $iIdUser . ' OR pub=1)' ) );
			}
		}

		return $aTree;
	}
}
?>