<?php
/**
 * Indexcontroller delivers base application frame
 * This is an html file which includes Javascripts and contain base structure
 *
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 *
 *
 * @category Categories_Controller
 * @version 1.0.0
 *
 */
class NamespacesController extends Zend_Controller_Action {
	private $oCache;
	private $oSettings;
	private $oAccounts;
	private $oAccount;

	public function init() {

		/* Initialize action controller here */
		$this -> oSettings = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');
		$this -> oAccount = Application_Model_Session::getInstance('ACCOUNT');

		if (!isset($this -> oAccount -> userdata['UID'])) {
			header('Location:' . $this -> oSettings -> website -> site -> basehref);
			exit ;
		}
		$this -> oAccounts = new Application_Model_Account($this -> oSettings);
		$this -> oNameSpace = new Application_Model_Namespaces($this -> oSettings);
	}

	public function indexAction() {

		$oPage = new Application_Model_Page();
		$oApstack = new Application_Model_Appstackapi();
		$aBoxes = array('LEFT' => array(), 'CONTENT' => array(), 'RIGHT' => array());
		$oCache = new Application_Model_Cache('namespaces_navigations');

		if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {

			$aNavigations['HEADNAV'] = $oPage -> getNavi('navi_top', 'top_nav', 'apst_small');
			$aNavigations['MAINNAV'] = $oPage -> getNavi('navi_main', 'main_nav', '');
			$aNavigations['FOOTERNAV'] = $oPage -> getNavi('navi_bottom', 'footer', 'clearfix apst_small', ' | ');
			$oCache -> writeCache(serialize($aNavigations));
		}

		$aNavigations = unserialize($oCache -> readCache());

		$oCache -> setCache('namespaces_accountadmin');

		if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {
			$sAccountAdmin = $oPage -> getNavi('namespaces', 'accountadmin', 'boxnavi', false, false, array('overview' => 'namespaces/index', 'configure' => 'namespaces/configure', 'semantics' => 'namespaces/semantics', 'transfer' => 'namespaces/transfer'));
			$oCache -> writeCache($sAccountAdmin);
		}

		$sAccountAdmin = $oCache -> readCache();

		array_push($aBoxes['LEFT'], $oPage -> getBox($sAccountAdmin, 'l1', 'teaser_box'));

		$aUsage = $this -> oAccounts -> getUsage($this -> oAccount -> userdata['UID']);
		$sContent = '';
		$aRate = $this -> oAccounts -> getRate($this -> oAccount -> userdata['UID']);

		foreach ($aUsage as $sColumnName => $iValue) {

			$iSum = $aRate[$sColumnName];

			if ($iSum != -1 && $iSum != 0 && $sColumnName == 'maxnamespaces') {
				$iUsage = floor(($iValue * 100) / $iSum);
				$iAvail = 100 - $iUsage;
				$sLabel = ucfirst(str_replace('max', '', $sColumnName));
				$sHtmlStat = '<span class="apst_pink apst_mid">' . $iValue . ' of ' . $iSum . '</span>' . '<br/>' . $sLabel;

				$sContent .= $oPage -> pieChart($sHtmlStat, array('Available' => $iAvail, 'Created' => $iUsage), 300, 0, array('0e8cf6', '1742cc', 'de057e'), '282828');
			}

		}

		array_push($aBoxes['CONTENT'], $oPage -> getBox($sContent, 'c1', 'content_box'));
		array_push($aBoxes['RIGHT'], $oPage -> getBox('z', 'r1', 'teaser_box'));

		$this -> view -> sPage = $oPage -> getPage('content', $aNavigations, $aBoxes, 'landing.js');

	}

	public function configureAction() {

		if (!isset($_GET['namespace'])) {

			$oPage = new Application_Model_Page();
			$oApstack = new Application_Model_Appstackapi();
			$aBoxes = array('LEFT' => array(), 'CONTENT' => array(), 'RIGHT' => array());
			$oCache = new Application_Model_Cache('namespaces_navigations');

			if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {

				$aNavigations['HEADNAV'] = $oPage -> getNavi('navi_top', 'top_nav', 'apst_small');
				$aNavigations['MAINNAV'] = $oPage -> getNavi('navi_main', 'main_nav', '');
				$aNavigations['FOOTERNAV'] = $oPage -> getNavi('navi_bottom', 'footer', 'clearfix apst_small', ' | ');
				$oCache -> writeCache(serialize($aNavigations));
			}

			$aNavigations = unserialize($oCache -> readCache());

			$oCache -> setCache('namespaces_accountadmin');

			if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {
				$sAccountAdmin = $oPage -> getNavi('namespaces', 'accountadmin', 'boxnavi', false, false, array('overview' => 'namespaces/index', 'configure' => 'namespaces/configure', 'semantics' => 'namespaces/semantics', 'transfer' => 'namespaces/transfer'));
				$oCache -> writeCache($sAccountAdmin);
			}

			$sAccountAdmin = $oCache -> readCache();
			$sNameSpaceList = $this -> oNameSpace -> listSpaces();
			array_push($aBoxes['LEFT'], $oPage -> getBox($sAccountAdmin, 'l1', 'teaser_box'));
			array_push($aBoxes['CONTENT'], $oPage -> getBox($this -> oNameSpace -> create() . $sNameSpaceList, 'c1', 'content_box'));
			array_push($aBoxes['RIGHT'], $oPage -> getBox($oPage -> getFiltes('namespaces/configure/', $this -> oAccount -> userdata['LIMITRESULT'], ((isset($this -> oAccount -> userdata['FILTERLETTER']) ? $this -> oAccount -> userdata['FILTERLETTER'] : '*'))), 'r1', 'teaser_box'));
			$this -> view -> sPage = $oPage -> getPage('content', $aNavigations, $aBoxes, 'landing');

		} else {
			echo json_encode($this -> oNameSpace -> create($_GET['namespace']));
		}
	}

	public function semanticsAction() {

		if (!isset($this -> oAccount -> userdata['UID'])) {
			header('Location:' . $this -> oSettings -> website -> site -> basehref);
			exit ;
		}

		if (!isset($_GET['namespace'])) {

			$oPage = new Application_Model_Page();
			$oApstack = new Application_Model_Appstackapi();
			$aBoxes = array('LEFT' => array(), 'CONTENT' => array(), 'RIGHT' => array());
			$oCache = new Application_Model_Cache('namespaces_navigations');

			if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {

				$aNavigations['HEADNAV'] = $oPage -> getNavi('navi_top', 'top_nav', 'apst_small');
				$aNavigations['MAINNAV'] = $oPage -> getNavi('navi_main', 'main_nav', '');
				$aNavigations['FOOTERNAV'] = $oPage -> getNavi('navi_bottom', 'footer', 'clearfix apst_small', ' | ');
				$oCache -> writeCache(serialize($aNavigations));
			}

			$aNavigations = unserialize($oCache -> readCache());

			$oCache -> setCache('namespaces_accountadmin');

			if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {
				$sAccountAdmin = $oPage -> getNavi('namespaces', 'accountadmin', 'boxnavi', false, false, array('overview' => 'namespaces/index', 'configure' => 'namespaces/configure', 'semantics' => 'namespaces/semantics', 'transfer' => 'namespaces/transfer'));
				$oCache -> writeCache($sAccountAdmin);
			}

			$sAccountAdmin = $oCache -> readCache();
			$sNameSpaceList = $this -> oNameSpace -> listSpaces();
			array_push($aBoxes['LEFT'], $oPage -> getBox($sAccountAdmin, 'l1', 'teaser_box'));
			array_push($aBoxes['CONTENT'], $oPage -> getBox($this -> oNameSpace -> create() . $sNameSpaceList, 'c1', 'content_box'));
			array_push($aBoxes['RIGHT'], $oPage -> getBox($oPage -> getFiltes('namespaces/semantics/', $this -> oAccount -> userdata['LIMITRESULT'], ((isset($this -> oAccount -> userdata['FILTERLETTER']) ? $this -> oAccount -> userdata['FILTERLETTER'] : '*'))), 'r1', 'teaser_box'));
			$this -> view -> sPage = $oPage -> getPage('content', $aNavigations, $aBoxes, 'landing.js');
		} else {
			echo json_encode($this -> oNameSpace -> create($_GET['namespace']));
		}
	}

	public function lookupAction() {
		if (!isset($this -> oAccount -> userdata['UID'])) {
			header('Location:' . $this -> oSettings -> website -> site -> basehref);
			exit ;
		}
		Preprocessor_Header::setContentType('json');

		$_GET['namespace'] = Preprocessor_String::filterBadChars(ltrim(trim($_GET['namespace'])));

		if (isset($_GET['namespace']) && strlen($_GET['namespace']) > 3) {
			$this -> view -> bUserExists = json_encode($this -> oNameSpace -> lookup($_GET['namespace']));
		}

	}

	public function listAction() {
		if (!isset($this -> oAccount -> userdata['UID'])) {
			header('Location:' . $this -> oSettings -> website -> site -> basehref);
			exit ;
		}

		$this -> view -> sPage = $this -> oNameSpace -> listSpaces();
	}

	public function deleteAction() {
		if (!isset($this -> oAccount -> userdata['UID'])) {
			header('Location:' . $this -> oSettings -> website -> site -> basehref);
			exit ;
		}

		if (isset($_GET['idns']) && (int)$_GET['idns'] > 0) {

			$aReturn = array('error' => true, 'message' => 'unknown error');
			if (isset($this -> oAccount -> userdata['UID'])) {
				if ($this -> view -> sPage = $this -> oNameSpace -> deleteNamespace($_GET['idns'])) {
					$aReturn = array('error' => false, 'message' => 'Namespace was deleted');
				} else {
					$aReturn = array('error' => true, 'message' => 'Namespace wasn\'t deleted');
				}
			} else {
				$aReturn = array('error' => true, 'message' => 'Session expired');
			}
		} else {
			$aReturn = array('error' => true, 'message' => 'Invalid namespace');
		}

		$this -> view -> sPage = json_encode($aReturn);
	}

}
?>