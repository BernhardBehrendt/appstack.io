<?php
/**
 * Central Metavalue model
 * This model provides an abstraction of the metavaluename table
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 *
 * @category Metanamevalue_Model
 * @version 1.0.0
 *
 */
class Application_Model_Metavalue extends Zend_Db_Table_Abstract {
	protected $_name = 'meta_namevalues';
	protected $_primary = 'idmeta_namevalue';
	protected $_foreign = 'metas_idmeta';
	
	/**
	 * Lookup for an object and check if method toArray() exists
	 *
	 * @param
	 *       	 $oResult
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
	 * Returns table foreign key
	 */
	public function getForeign() {
		return $this->_foreign;
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
	 * return a rowset by a one element array (array('colname' => 'value'));
	 *
	 * @param $aColumnValue array       	
	 *
	 * @return $rowset array
	 */
	public function getIdByValueAndParent($aColumnValue, $iParentMeta) {
		
		$aArrayKeys = array_keys ( $aColumnValue );
		
		if (count ( $aArrayKeys ) == 1) {
			
			$sColumn = $aArrayKeys [0];
			$sValue = $aColumnValue [$sColumn];
			
			// if not an integer addstringmask ('')
			if (! is_int ( $sValue )) {
				$sValue = '\'' . $sValue . '\'';
			}
			
			$row = $this->fetchRow ( $sColumn . "=" . $sValue . " AND " . $this->_foreign . "=" . $iParentMeta );
			
			return $this->tryReturn ( $row );
		
		}
		
		return false;
	}
	
	/**
	 * Inserts a value for a metaobject if it was't matched
	 * as an Item of the given MetaId
	 *
	 * @param
	 *       	 $aData
	 *       	
	 * @return sPRIMARY
	 */
	public function insertValue($aData, $iParentMeta) {
		if (is_array ( $aData )) {
			$sMsg = '';
			$iTurns = count ( $aData );
			$iTurns = ($iTurns > 10) ? 10 : $iTurns;
			for($i = 0; $i < $iTurns; $i ++) {
				$bInsertDo = false;
				
				$aColumnValue = array ('valname' => Preprocessor_String::legalizeString ( $aData [$i] ['name'] ) );
				
				if (isset ( $aData [$i] ['name'] )) {
					$aData [$i] ['name'] = Preprocessor_String::legalizeString ( $aData [$i] ['name'] );
					// value with default
					if (isset ( $aData [$i] ['value'] )) {
						if ($aData [$i] ['value'] === true) {
							$aData [$i] ['value'] = 'true';
						}
						if ($aData [$i] ['value'] === false) {
							$aData [$i] ['value'] = 'false';
						}
						$aInsert = array ('valname' => Preprocessor_String::legalizeString ( $aData [$i] ['name'] ), 'valdef' => ltrim ( trim ( strtolower ( $aData [$i] ['value'] ) ) ), $this->_foreign => $iParentMeta );
						$bInsertDo = true;
					}
					
					// value without default
					if (empty ( $aData [$i] ['value'] )) {
						$aInsert = array ('valname' => Preprocessor_String::legalizeString ( $aData [$i] ['name'] ), 'valdef' => NULL, $this->_foreign => $iParentMeta );
						$bInsertDo = true;
					}
					
					if ($bInsertDo === true) {
						// Pruef ob wertkombo vorhanden
						$aIsCreated = $this->getIdByValueAndParent ( $aColumnValue, $iParentMeta );
						
						// IS Created noch prufen kommt nicht richtig
						if (! is_array ( $aIsCreated )) {
							$this->insert ( $aInsert );
						} else {
						
						}
					}
				}
			}
			
			// Somewhen Throw Exception
			
			return true;
		}
		return false;
	}
	
	/**
	 * Update changes mase on an metavalue
	 *
	 * @param $aData (a
	 *       	 request containing an container)
	 *       	
	 * @return boolean
	 */
	public function updateValue($aData) {
		
		$bChangeMade = false;
		
		$oTableCompMetas = new Application_Model_Table ();
		$oTableCompMetas->setTable ( 'comp_metas' );
		$oTableCompMetas->setPrimary ( 'idcomp_meta' );
		
		$oTableCMNV = new Application_Model_Table ();
		$oTableCMNV->setTable ( 'comp_meta_values' );
		$oTableCMNV->setPrimary ( 'id_cmnv' );
		
		foreach ( $_REQUEST ['container'] as $sKey => $mValue ) {
			
			if (stripos ( $sKey, 'valname' ) !== false) {
				$aTmp = split ( '_', $sKey );
				$iIdMetavalue = $aTmp [1];
				
				$iFkMeta = $_REQUEST ['container'] [$this->_foreign . '_' . $iIdMetavalue];
				$sValueDefault = trim ( strtolower ( $_REQUEST ['container'] ['valname_' . $iIdMetavalue] ) );
				
				if ($sValueDefault == 'null') {
					$sValueDefault = NULL;
				}
				
				$aMetaValueBefore = $this->fetchRow ( $this->getPrimary () . '=' . $iIdMetavalue . ' AND ' . $this->_foreign . '=' . $iFkMeta );
				
				$sSelect = $oTableCompMetas->select ()->where ( 'metas_idmeta=' . $iFkMeta );
				$oCompMetas = $oTableCompMetas->fetchAll ( $sSelect );
				
				foreach ( $oCompMetas as $oResult ) {
					$sCompMetas = (! isset ( $sCompMetas )) ? $oResult->idcomp_meta : $sCompMetas . ',' . $oResult->idcomp_meta;
				}
				
				$sSelectProperty = $this->select ()->from ( $this->getTableName (), 'valname' )->where ( $this->getPrimary () . '=' . $iIdMetavalue . ' AND ' . $this->_foreign . '=' . $iFkMeta );
				$oRow = $this->fetchRow ( $sSelectProperty );
				if (strlen ( trim ( $sValueDefault ) ) > 0 || $sValueDefault === NULL) {
					if (isset ( $sCompMetas )) {
						$sValBefore = ($aMetaValueBefore->valdef === NULL) ? 'NULL' : '"' . $aMetaValueBefore->valdef . '"';
						$oTableCMNV->update ( array ('valdef' => $sValueDefault ), 'valdef=' . $sValBefore . ' AND valname="' . $oRow->valname . '" AND comp_metas_idcomp_meta IN(' . $sCompMetas . ')' );
					}
					if ($this->update ( array ('valdef' => $sValueDefault ), $this->getPrimary () . '=' . $iIdMetavalue . ' AND ' . $this->_foreign . '=' . $iFkMeta ) > 0) {
						$bChangeMade = true;
					}
				}
			}
		
		}
		
		return $bChangeMade;
	}
	
	/**
	 * Delete all metaitem values by overgiven ID
	 *
	 * @param
	 *       	 $iParentMeta
	 * @return boolean
	 */
	public function deleteValueByParent($iParentMeta) {
		return $this->delete ( $this->_foreign . '=' . $iParentMeta );
	}

}
?>