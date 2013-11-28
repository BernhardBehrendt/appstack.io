<?php
class Application_Model_Crons extends Application_Model_Table {
	private $oUsage;
	public function __construct($oSettings) {
		parent::__construct();

		$this -> oSettings = $oSettings;
		$this -> setTable('stats');
		$this -> oUsage = new Application_Model_Table();
		$this -> oUsage -> setTable('usages');
		$this -> oUsage -> setPrimary('idusage');

	}

	public function resetUsage() {
		$hMysql = mysql_connect($this -> oSettings -> resources -> db -> params -> host, $this -> oSettings -> resources -> db -> params -> username, $this -> oSettings -> resources -> db -> params -> password);
		//print_r($hMysql);
		mysql_query('USE ' . $this -> oSettings -> resources -> db -> params -> dbname, $hMysql);

		$sQueryString = 'insert into stats (apiget, apiput, accounts_idaccount)
						(SELECT usages.maxapiget, usages.maxapiput, accountrates_accounts_idaccount FROM usages where usages.maxapiget>0 OR usages.maxapiput>0)';

		$result = mysql_query($sQueryString, $hMysql);
		mysql_query('update usages set maxapiget=0, maxapiput=0', $hMysql);
		return 'MADE UPDATE @ Time' . time();
	}

}
?>
