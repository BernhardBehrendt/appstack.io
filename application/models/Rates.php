<?php
define('RATE_UNLIMITED', 0);
define('RATE_NOTHING', -1);
class Application_Model_Rates extends Application_Model_Table {
	private $oSettings;
	public function __construct($oSettings) {
		parent::__construct();

		$this -> oSettings = $oSettings;
		$this -> setTable('rates');
		$this -> setPrimary('idrate');

		$this -> setupRates();

	}

	private function setupRates() {
		$oCache = new Application_Model_Cache('rates');
		if (!$oCache -> cacheExists()) {
			if ($this -> fetchAll() -> count() === 0) {
				$aInsertBaseRate = array('maxapiget' => 900, 'maxapiput' => 150, 'maxmetas' => 50, 'maxcats' => 25, 'maxcomps' => 250, 'maxgroups' => 3, 'maxusers' => 6, 'maxnamespaces' => 1, 'maxbrowserkeys' => 3, 'maxserverkeys' => 1, 'cloneuser' => RATE_NOTHING, 'clonegroup' => RATE_NOTHING, 'clonenamespace' => RATE_NOTHING, );
				$this -> insert($aInsertBaseRate);
				$oCache -> writeCache('Installed rates.');
				echo 'Installed rates. Reload Page';
				exit ;
			}
		}
	}

	public function getRate($iRateId) {
		if ((int)$iRateId > 0) {
			$oCache = new Application_Model_Cache('rates' . $iRateId);

			if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {
				$oRateMatch = $this -> fetchRow($this -> select() -> where('idrate=' . $iRateId));
				$oCache -> writeCache(serialize($oRateMatch));
			}

			$oRateMatch = unserialize($oCache -> readCache());

			if (is_object($oRateMatch)) {
				return $oRateMatch;
			}
		}
		return false;
	}

}
?>