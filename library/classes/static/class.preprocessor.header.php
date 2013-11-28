<?php
/**
 * Static class provides header settings and o on
 * eg. set a json header
 *
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 * @category static preprocessor header
 *
 *
 */
class Preprocessor_Header {

	/**
	 * set the content type for requested document
	 *
	 */
	public static function setContentType($sContenttype) {

		$sContenttype = strtolower($sContenttype);
		switch ($sContenttype) {
			case 'json' :
				header('Content-type: application/json', true);
				break;
			case 'javascript' :

			case 'text/javascript' :
				header('Content-type: text/javascript', true);
				break;
			case 'html' :

			case 'xhtml' :

			default :
				header('Content-type: text/html', true);
		}
	}

}
?>
