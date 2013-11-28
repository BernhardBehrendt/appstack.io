<?php
/**
 * Application_Model_Cache controlls files containing php generated data for store data
 * in a file for time there is no change in
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 *
 * @category Cache Model
 * @version 1.0.0
 *
 */
class Application_Model_Cache {

	public $sCacheDir;
	public $sCacheFile;

	/**
	 * Constructor specifies the target cache file and set the configured cache dir
	 * @param $sCacheFile
	 */
	public function __construct($sCacheFile) {
		if(!isset($sCacheFile)) {
			throw new Zend_Exception('No cachefie specified');
			return false;
		}
		$oConf = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');

		$this -> setCache($sCacheFile);
		$this -> sCacheDir = $oConf -> dir -> cache -> general;
	}

	/**
	 * Set the target cache file
	 * @param string $sNewFile
	 */
	public function setCache($sNewFile = false) {
		$this -> sCacheFile = md5($sNewFile);
	}

	/**
	 * Lookup if target cache file exists
	 *
	 * @return boolean
	 */
	public function cacheExists($bForce = false) {
		if(!file_exists($this -> sCacheDir . $this -> sCacheFile) || $bForce == true) {
			return false;
		}

		return true;
	}

	/**
	 * Delete cashfile which was confiured as target cachefile
	 *
	 * @return bolean
	 */
	public function deleteCache() {
		if($this -> cacheExists()) {
			if(unlink($this -> sCacheDir . $this -> sCacheFile)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Write string into cashfile
	 * @param string $sCacheData
	 */
	public function writeCache($sCacheData = false) {
		if(isset($sCacheData) && strlen($sCacheData) > 0) {
			if(file_put_contents($this -> sCacheDir . $this -> sCacheFile, $sCacheData)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Readout cached file and return containing string
	 *
	 * @return string
	 */
	public function readCache() {
		if(file_exists($this -> sCacheDir . $this -> sCacheFile)) {
			return file_get_contents($this -> sCacheDir . $this -> sCacheFile);
		}
		return false;
	}

	/**
	 * Lookup if cache is older as given time in seconds
	 * @param integer $iSeconds
	 */
	public function isOlderAs($iSeconds = 3600) {
		if($this -> cacheExists()) {
			return (time('void') - filemtime($this -> sCacheDir . $this -> sCacheFile) > $iSeconds) ? true : false;
		} else {
			return true;
		}
	}

}
?>