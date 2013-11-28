<?php
/**
 * Static class provides preprocessing strings
 * eg. html taggs containing attributes and data
 *
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 * @category static preprocessor string
 *
 *
 */
class Preprocessor_String {

	/**
	 * returns a finished javascript snippet containing a redirect (required for
	 * ajax based requests)
	 *
	 * @param unknown_type $sToRessource
	 */
	public static function jsRedirect($sToRessource) {
		$sScript = 'var sUrl = \'' . $sToRessource . '\';' . "\n" . 'loginForm();' . '//redirect(sUrl);';

		return self::createTag ( 'script', array ('type' => 'text/javascript' ), $sScript );
	}

	/**
	 * Create an HTML element by valid XHTML Rules
	 *
	 * @param string $sTagName
	 * @param mixed $mAttributes
	 * @param string $sAfterInner
	 * @param bools $bClosed
	 * @return Ambigous <string, js redirect>|boolean
	 */
	public static function createTag($sTagName = false, $mAttributes = false, $sAfterInner = false, $bClosed = true) {
		if (isset ( $sTagName ) && is_string ( $sTagName )) {
			if (isset ( $mAttributes )) {

				$sAttributes = '';

				// Process Attributes
				if (is_array ( $mAttributes )) {
					foreach ( $mAttributes as $sAttribute => $mValue ) {
						$sAttributes .= $sAttribute . '="';
						if (is_array ( $mValue )) {
							$sAttributes .= implode ( ' ', $mValue );
						} else {
							$sAttributes .= $mValue;
						}
						$sAttributes .= '" ';
					}
				} else {
					$sAttributes = $mAttributes;
				}
			}

			// Build tag
			$sBuildTag = '<' . $sTagName . ' ' . $sAttributes;

			// Process tag close and fill content
			if ($bClosed) {
				$sBuildTag .= '>' . (($sAfterInner) ? $sAfterInner : '') . '</' . $sTagName . '>';
			} else {
				$sBuildTag .= '/>' . ($sAfterInner) ? $sAfterInner : '';
			}
			return $sBuildTag;
		}

		return false;
	}

	/**
	 * Converts machine formated data into a readable one
	 *
	 * @param
	 *        	$mDebug
	 * @param
	 *        	$bReturn
	 */
	public static function debugOutput($mDebug, $bReturn = true) {
		$mDebug = str_replace ( array ("\n", " ", "	" ), array ('<br/>', '&nbsp;', '&nbsp;&nbsp;&nbsp;&nbsp;' ), print_r ( $mDebug, true ) );
		// cryptic but very efficient
		$mDebug = self::createTag ( 'script', array ('type' => 'text/javascript' ), 'opendDialog(\'done\', \'Debug output\', \'' . $mDebug . '\', \'Close\', false, true); sRequestSender = \'close\';' );
		if (! $bReturn) {
			echo $mDebug;
		} else {
			return $mDebug;
		}
	}

	/**
	 * Filter out bad chars out of a overgiven string
	 *
	 * @param string $sIn
	 * @return string
	 */
	public static function filterBadChars($mIn) {
		if (isset ( $mIn )) {
			if (is_array ( $mIn )) {
				foreach ( $mIn as $sKey => $mValue ) {
					$mIn [$sKey] = self::filterBadChars ( $mValue );
				}
				return $mIn;
			} else {
				return str_replace ( array ('\n', '\r', "\n", "\r", "'" ), '', preg_replace ( '{[!"§$%&\/()´`;<>]*}', '', $mIn ) );
			}
		}
	}

	/**
	 * Prepares string for storage in tagitall db
	 * Its a central way to prepare string before store
	 *
	 * @param string $sIn
	 */
	public static function legalizeString($mIn) {
		if (isset ( $mIn )) {
			if (is_array ( $mIn )) {
				foreach ( $mIn as $sKey => $mValue ) {
					$mIn [$sKey] = self::filterBadChars ( $mValue );
				}
				return $mIn;
			} else {
				$aMatch = array ('Ä', 'Ö', 'Ü' );
				$aReplace = array ('ä', 'ö', 'ü' );
				$mIn = strtolower ( self::filterBadChars ( $mIn ) );

				$mIn = ltrim ( trim ( str_replace ( '-', ' ', $mIn ) ) );

				if (strlen ( $mIn ) > 0) {
					$mIn = ereg_replace ( "( +)", "-", $mIn );
					$mIn = strtolower ( str_replace ( '	', '-', $mIn ) );
					$mIn = str_replace ( $aMatch, $aReplace, $mIn );
					return (strlen ( $mIn ) > 0) ? $mIn : false;
				}
				return false;
			}
		}
	}
	public static function minifyString($mIn) {
		if (isset ( $mIn )) {
			if (is_array ( $mIn )) {
				foreach ( $mIn as $sKey => $mValue ) {
					$mIn [$sKey] = self::filterBadChars ( $mValue );
				}
				return $mIn;
			} else {
				$aMatch = array ("\n", "\r", '	', '  ' );
				$aReplace = array ('' );

				if (strlen ( $mIn ) > 0) {
					$mIn = str_replace ( $aMatch, $aReplace, $mIn );
					return (strlen ( $mIn ) > 0) ? $mIn : false;
				}
				return false;
			}
		}
	}
	public static function apiStringSafe($sIn, $bReconvert = false) {
		if (is_string ( $sIn )) {
			$sIn = rtrim ( trim ( $sIn ) );
		}
		if ($sIn === false || strtolower ( $sIn ) == 'false') {
			if ($bReconvert) {
				$sIn = false;
			} else {
				$sIn = 'false';
			}
		}
		if ($sIn === true || strtolower ( $sIn ) == 'true') {
			if ($bReconvert) {
				$sIn = true;
			} else {
				$sIn = 'true';
			}
		}

		if ($sIn === null || strtolower ( $sIn ) == 'null' || $sIn === '') {
			$sIn = null;
		}

		return $sIn;
	}
}
?>