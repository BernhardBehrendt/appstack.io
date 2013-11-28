<?php
/**
 * Central Table model
 * This model provides an abstraction of the user table and provide services as register,
 * rightsmanagement, etc.
 *
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 *
 *
 * @category Table Model
 * @version 0.4.1
 *
 *
 *
 */
class Application_Model_Table extends Zend_Db_Table_Abstract {

	protected $_schema;
	protected $_name;
	protected $_primary;

	// GETTERS
	/**
	 * Return name of choosen schema
	 */
	public function getSchema() {
		return $this -> _schema;
	}

	/**
	 * Return primary column of choosen table
	 *
	 * @return string
	 */
	public function getPrimary() {
		if (is_array($this -> _primary)) {
			return $this -> _primary[1];
		} else {
			return $this -> _primary;
		}

	}

	/**
	 * Return name of choosen table
	 *
	 * @return string
	 */
	public function getTable() {
		return $this -> _name;
	}

	/**
	 * Get a Row from Table by given where condition
	 * @param string $sWhere
	 */
	public function getRow($sWhere) {
		$row = $this -> fetchAll($sWhere);
		return $this -> tryReturn($row);
	}

	/**
	 * Get information for choosen table
	 * these ar as follow
	 *
	 *  - FIELDNAME			(COLUMNNAME)
	 *  - DEFAULT VALUE		(DEFAULT VALUE IF EMPTY)
	 *  - REQUIRED			(NOTNULL)
	 *  - LENGTH			(MAX FIELDLENGTH)
	 *  - TYPE				(DATATYPE)
	 *
	 *  @return array representation of choosen table
	 */
	public function getCols() {
		$this -> _setupMetadata();

		$aTblInfo = $this -> _metadata;

		$iNum = 0;
		$aPreForm = array();

		foreach ($aTblInfo as $sKey => $aValues) {
			if ($aTblInfo[$sKey]['PRIMARY']) {
				unset($aTblInfo[$sKey]);
				continue;
			}

			$aPreForm[$iNum] = array('FIELDNAME' => $aTblInfo[$sKey]['COLUMN_NAME'], 'DEFAULT' => $aTblInfo[$sKey]['DEFAULT'], 'REQUIRED' => ($aTblInfo[$sKey]['NULLABLE']) ? false : true, 'LENGTH' => $aTblInfo[$sKey]['LENGTH'], 'TYPE' => $aTblInfo[$sKey]['DATA_TYPE']);

			// Check Datataypes and Fill Array
			if (stripos($aTblInfo[$sKey]['DATA_TYPE'], 'set(') !== false) {
				$aSet = split(',', str_replace(array('set(', ')', '\''), '', $aTblInfo[$sKey]['DATA_TYPE']));

				$aPreForm[$iNum]['TYPE'] = 'set';
				$aPreForm[$iNum]['OPTIONS'] = $aSet;
				unset($aSet);
			} elseif (stripos($aTblInfo[$sKey]['DATA_TYPE'], 'enum(') !== false) {
				$aSet = split(',', str_replace(array('enum(', ')', '\''), '', $aTblInfo[$sKey]['DATA_TYPE']));

				$aPreForm[$iNum]['TYPE'] = 'enum';
				$aPreForm[$iNum]['OPTIONS'] = $aSet;
				unset($aSet);
			}

			$iNum++;
		}
		unset($aTblInfo, $iNum);

		return $aPreForm;
	}

	// SETTER
	/**
	 * Return name of choosen schema
	 */
	public function setSchema($sSchema) {
		$this -> _schema = $sSchema;
	}

	/**
	 * Set table name
	 * @param $sTableName
	 */
	public function setTable($sTableName) {
		$this -> _name = $sTableName;
	}

	/**
	 * Set Table Primary Column
	 * @param $sPrimary
	 */
	public function setPrimary($sPrimary) {
		$this -> _primary = $sPrimary;
	}

	// FUNCTIONAL METHODS
	/**
	 * Lookup for an object and check if method toArray() exists
	 * @param $oResult
	 *
	 * @return mixed array();
	 */
	private function tryReturn($oResult) {
		return (is_object($oResult) && method_exists($oResult, 'toArray')) ? $oResult -> toArray() : false;
	}

	/**
	 * Method Iterates defined table and check for POST request data
	 * If method finished completly which mean that all required fields got a value and
	 * each select / check field is valid
	 * get an build an array which can inserted in database
	 *
	 * @param Array $aRowData (regulary $_POST)
	 *
	 * @return bool
	 */
	public function insertRow($aRowData, $aBlockPass, $bBlockPass = false) {

		// Clean String before write in Table
		$aRowData = Preprocessor_String::filterBadChars($aRowData);

		$aCols = $this -> getCols();
		$aInsert = array();
		foreach ($aCols as $iKey => $aParams) {
			if (isset($aParams['FIELDNAME'])) {
				if (!$bBlockPass) {
					if (in_array($aParams['FIELDNAME'], $aBlockPass)) {
						continue;
					}
				} else {
					if (!in_array($aParams['FIELDNAME'], $aBlockPass)) {
						continue;
					}
				}
				$bIsRequired = ($aParams['REQUIRED'] == 1) ? true : false;
				//check if POST VALUE OR DIE
				// handle set
				if (isset($aRowData[$aParams['FIELDNAME']]) || isset($aRowData[$aParams['FIELDNAME'] . '_1'])) {
					if ($aParams['TYPE'] == 'set' && is_array($aRowData[$aParams['FIELDNAME']])) {
						for ($i = 0; $i < count($aParams['OPTIONS']); $i++) {
							if (array_key_exists($i, $aRowData[$aParams['FIELDNAME']])) {
								if (!isset($aInsert[$aParams['FIELDNAME']])) {
									$aInsert[$aParams['FIELDNAME']] = $aParams['OPTIONS'][$i] . ',';
								} else {
									$aInsert[$aParams['FIELDNAME']] .= $aParams['OPTIONS'][$i] . ',';
								}
							}
						}
					} elseif ($aParams['TYPE'] == 'enum') {
						if (in_array($aRowData[$aParams['FIELDNAME']], $aParams['OPTIONS'])) {
							$aInsert[$aParams['FIELDNAME']] = $aRowData[$aParams['FIELDNAME']];
						} else {
							return 'Invalid Item selected!<br/><br/><a href="#" class="form_back">Back to form</a>';
						}
					} elseif (isset($aRowData[$aParams['FIELDNAME'] . '_1']) && isset($aRowData[$aParams['FIELDNAME'] . '_2'])) {
						if ($aRowData[$aParams['FIELDNAME'] . '_1'] == $aRowData[$aParams['FIELDNAME'] . '_2']) {
							$aInsert[$aParams['FIELDNAME']] = md5($aRowData[$aParams['FIELDNAME'] . '_2']);
						} else {
							return 'password fields dont match<br/><br/><a href="#" class="form_back">Back to form</a>';
						}
					} else {
						$aInsert[$aParams['FIELDNAME']] = $aRowData[$aParams['FIELDNAME']];
					}
				} elseif ($bIsRequired) {
					return $aParams['FIELDNAME'] . ' is missing his POST data!<br/><br/><a href="#" class="form_back">Back to form</a>';
				}
			}
		}
		try {
			$tblReturn = (int)$this -> insert($aInsert);
		} catch (Exception $e) {
			return 'Entry allready exists <br/><br/><a href="#" class="form_back">Back to form</a>';
		}
		return $tblReturn;
	}

}
?>