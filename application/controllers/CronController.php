<?php
/**
 * Croncontroller
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 *
 *
 * @version 1.0.0
 *
 */
class CronController extends Zend_Controller_Action {
	private $oSettings;
	private $oCron;
	private $oCache;
	public function init() {

		/* Initialize action controller here */
		$this -> oSettings = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');
	}

	public function resetusageAction() {
		$this -> oCache = new Application_Model_Cache('masterclock');

		if($this -> oCache -> isOlderAs($this -> oSettings -> cron -> clock -> master)) {
			if(isset($_GET['authsalt']) && $_GET['authsalt'] == $this -> oSettings -> cron -> clock -> auth) {
				$oCron = new Application_Model_Crons($this -> oSettings);
				echo $oCron -> resetUsage();
				$this -> oCache -> writeCache(time());
			}
		} else {

			$iLastUpdate = (int)$this -> oCache -> readCache();
			$iNow = (int)time();

			echo 'No Update Made because last update was before ' . ($iNow - $iLastUpdate) . ' seconds (Next update in ' . ($this -> oSettings -> cron -> clock -> master - ($iNow - $iLastUpdate)) . ')';
		}
	}

}
?>