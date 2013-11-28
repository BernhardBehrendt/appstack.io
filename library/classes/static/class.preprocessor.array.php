<?php
/**
 * Static class provides preprocessing arrays
 * eg. check if array is assocative or sequential
 *
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 * @category static preprocessor array
 *
 *
 */
class Preprocessor_Array {

	/**
	 * returns a finished array which can be handled by javascript after convert in JSON after return
	 * array needs 1 subarrays like this structure
	 *
	 * Example:
	 *
	 * array(	'type'		=>	'error/and more...',
	 * 				'headline'	=>	'Your headline',
	 * 				'text'		=>	'Your text',
	 * 				'greenlabel'=>	'Green Button Label',	// Label only change if debug isn�t allready open
	 * 				'redlabel'	=>	'Red button label'		// Label only change if debug isn�t allready open
	 *		)
	 * @param array $aDebugConfiguration
	 */
	public static function debugOutputArray($sHeadline, $sText, $sGreenlabel='Ok', $sRedlabel='Abort', $sType='debug') {
		if(isset($sHeadline) && isset($sText)) {
			return array('message' => array('type' => $sType, 'headline' => $sHeadline, 'text' => $sText, 'greenlabel' => $sGreenlabel, 'redlabel' => $sRedlabel));
		} else {
			return self::debugOutputArray('Exception::' . __METHOD__, 'Missing propertys in call.');
		}

	}

	/**
	 * checks given array by reference and return a bolean
	 *
	 * Return values
	 * (bool) true given array is associative
	 * (bool) false given array is numeric
	 *
	 * be careful using this with very large arrays because
	 * there are allready no benchmark informations
	 *
	 * @param array $aArry
	 * @return bool
	 */
	public static function isAssocArray(&$aArry) {
		return array_keys($aArry) !== range(0, count($aArry) - 1);
	}

	public static function isNumArray($array) {
		$r = false;
		if(is_array($array)) {
			foreach($array as $n => $v) {
				if(is_array($array[$n])) {
					$r = self::isNumArray($array[$n]);
					if($r == false)
						break;
				} else if(!is_numeric($v)) {
					$r = false;
					break;
				} else
					$r = true;
			}
		}
		return $r;
	}

	/**
	 * Count an array anc check if it has a 2 divide modulo 0
	 *
	 * @param array $aArray
	 * @return boolean
	 */
	public static function getCountMod2(array$aArray) {
		return ((count($aArray) % 2) == 0) ? true : false;
	}

	// Shuffles associative arrays for randomisation
	public static function shuffle_assoc($array) {
		$keys = array_keys($array);
		shuffle($keys);
		return    array_merge(array_flip($keys), $array);
	}

}
?>
