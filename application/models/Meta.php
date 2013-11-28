<?php
/**
 * Central Metainformation model
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
class Application_Model_Meta extends Zend_Db_Table_Abstract {
	protected $_name = 'metas';
	protected $_primary = 'idmeta';
	protected $_foreign = 'namespaces_idnamespace';
	private $iIdNameSpace;
	public function __construct($iIdNameSpace) {
		if (( int ) $iIdNameSpace > 0) {
			$this->iIdNameSpace = $iIdNameSpace;
		}
		parent::__construct ();
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
	 * return all Table Columns
	 *
	 * @param $oMetavalue Application_Model_Metavalue
	 *
	 * @return mixed array
	 */
	public function getAll(Application_Model_Metavalue &$oMetavalue) {
		if (isset ( $this->iIdNameSpace )) {
			$select = $this->select ()->setIntegrityCheck ( false );
			$select->from ( $this->getTableName () )->join ( $oMetavalue->getTableName (), 'idmeta = ' . $oMetavalue->getForeign (), '*' )->where ( $this->_foreign . '=' . $this->iIdNameSpace )->order ( 'metas.name' );
			return $this->tryReturn ( $this->fetchAll ( $select ) );
		}
		return false;
	}

	/**
	 * Fetch a table row by Id
	 *
	 * @param $iId int
	 */
	public function getById($mMeta) {
		if (( int ) $mMeta > 0 || is_string ( $mMeta )) {
			$row = $this->fetchRow ( '(' . $this->getPrimary () . '=' . ( int ) $mMeta . ' OR name="' . $mMeta . '") AND namespaces_idnamespace=' . $this->iIdNameSpace );
			return $this->tryReturn ( $row );
		}

		return false;
	}

	/**
	 * return a rowset by a one element array (array('colname' => 'value'));
	 *
	 * @param $aColumnValue array
	 *
	 * @return $rowset array
	 */
	public function getIdByValue($aColumnValue) {
		if (isset ( $this->iIdNameSpace )) {
			unset ( $aColumnValue [$this->_foreign] );
			$aArrayKeys = array_keys ( $aColumnValue );

			if (count ( $aArrayKeys ) == 1) {

				$sColumn = $aArrayKeys [0];
				$sValue = $aColumnValue [$sColumn];

				// if not an integer addstringmask ('')
				if (! is_int ( $sValue )) {
					$sValue = '\'' . $sValue . '\'';
				}

				$row = $this->fetchRow ( $sColumn . "=" . $sValue . ' AND ' . $this->_foreign . '=' . $this->iIdNameSpace );

				return $this->tryReturn ( $row );
			}
		}
		return false;
	}

	/**
	 * Insert a new meta if not exist
	 *
	 * @param $aData array
	 *        	(name => value)
	 *
	 * @return Primary or false
	 */
	public function insertMeta($aData) {
		$sName = Preprocessor_String::legalizeString ( $aData ['name'] );

		$iMetasFound = ( int ) $this->fetchAll ( $this->select ()->where ( $this->_foreign . '=' . $aData [$this->_foreign] . ' AND name="' . $sName . '"' ) )->count ();

		if ($iMetasFound === 0) {
			$aData ['name'] = $sName;
			return $this->insert ( $aData );
		} else {
			return false;
		}

		return false;
	}

	/**
	 * Delete a metaitem and all of his values currently not checked if it is in
	 * use
	 *
	 * @param
	 *        	$oMetavalue
	 * @param
	 *        	$iIdMeta
	 */
	public function deleteMeta($iIdMeta) {
		if ($this->delete ( $this->getPrimary () . '=' . $iIdMeta . ' AND ' . $this->_foreign . '=' . $this->iIdNameSpace ) != 0) {
			return true;
		}
		return false;
	}
}
?>