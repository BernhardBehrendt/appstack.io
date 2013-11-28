<?php
/**
 * Tagitall controller delivers base application frame
 *
 * @author Bernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 *
 *
 * @category Sematics frontend controller
 * @version 1.0.0
 *
 */
class BuilderController extends Zend_Controller_Action {

	private $oAccount;
	private $oCategory;
	private $oGroups;
	private $oBuilder;
	//NEW
	private $oCache;
	private $oSettings;
	private $oAccounts;
	private $oRights;
	public $sDebug;

	public function init() {
		/* Initialize action controller here */

		// Check for Session  and redirect if there is no one
		$this -> oAccount = Application_Model_Session::getInstance('ACCOUNT');
		$this -> oSettings = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');

		if (!$this -> oAccount -> userdata['UID']) {
			header('Location:' . $this -> oSettings -> website -> site -> basehref);
		}

		$this -> oAccounts = new Application_Model_Account($this -> oSettings);
		$this -> oGroups = new Application_Model_Groups($this -> oSettings);
		$this -> oRights = new Application_Model_Rights();
		$this -> oBuilder = new Application_Model_Builder($this -> oSettings);

	}

	public function indexAction() {

		$oPage = new Application_Model_Page();
		$oApstack = new Application_Model_Appstackapi();
		$aBoxes = array('LEFT' => array(), 'CONTENT' => array(), 'RIGHT' => array());
		$oCache = new Application_Model_Cache('groups_navigations');

		if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {

			$aNavigations['HEADNAV'] = $oPage -> getNavi('navi_top', 'top_nav', 'apst_small');
			$aNavigations['MAINNAV'] = $oPage -> getNavi('navi_main', 'main_nav', '');
			$aNavigations['FOOTERNAV'] = $oPage -> getNavi('navi_bottom', 'footer', 'clearfix apst_small', ' | ');
			$oCache -> writeCache(serialize($aNavigations));
		}

		$aNavigations = unserialize($oCache -> readCache());

		$oCache -> setCache('builder_administration');

		if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {
			$sAccountAdmin = $oPage -> getNavi('348', 'accountadmin', 'boxnavi', false, false, array('overview' => 'builder/index', 'builder' => 'builder/builder'));
			$oCache -> writeCache($sAccountAdmin);
		}

		$sAccountAdmin = $oCache -> readCache();

		array_push($aBoxes['LEFT'], $oPage -> getBox($sAccountAdmin, 'l1', 'teaser_box'));

		$aUsage = $this -> oAccounts -> getUsage($this -> oAccount -> userdata['UID']);
		$sContent = '';
		$aRate = $this -> oAccounts -> getRate($this -> oAccount -> userdata['UID']);
		$aSpaces = $this -> oBuilder -> getMembersSpaces();

		$iSum = count($aSpaces['OWNER']) + count($aSpaces['MEMBER']);
		if ($iSum > 0) {
			$iOwner = floor((count($aSpaces['OWNER']) * 100) / $iSum);
			$iMember = 100 - $iOwner;

			$sHtmlStat = '<span class="apst_pink apst_very_small">Effective Builderaccess groups<p class="apst_blue">' . (count($aSpaces['OWNER'])) . ' own group(s)</br>' . (count($aSpaces['MEMBER'])) . ' mebership(s)</p></span>';

			$sContent .= $oPage -> pieChart($sHtmlStat, array('Owner' => $iOwner, 'Member	' => $iMember), 300, 0, array('0e8cf6', '1742cc', 'de057e'), '282828');
		}

		if ($iSum > 0) {
			$aEffectiveMemberSpaces = array();
			if (is_array($aSpaces['MEMBER'])) {
				foreach ($aSpaces['MEMBER'] as $iGroupId => $aGroupConf) {
					$aEffectiveMemberSpaces = array_merge($aEffectiveMemberSpaces, $aGroupConf['SPACES']);
				}
			}

			$aEffectiveOwnerSpaces = array();
			if (is_array($aSpaces['OWNER'])) {
				foreach ($aSpaces['OWNER'] as $iGroupId => $aGroupConf) {
					$aEffectiveOwnerSpaces = array_merge($aEffectiveOwnerSpaces, $aGroupConf['SPACES']);
				}
			}
			$iSum = count($aEffectiveMemberSpaces) + count($aEffectiveOwnerSpaces);
			if ($iSum > 0) {
				$iOwner = floor((count($aEffectiveOwnerSpaces) * 100) / ($iSum));
				$iMember = 100 - $iOwner;
				$sHtmlStat = '<span class="apst_pink apst_very_small">Effective accessible Namespaces<p class="apst_blue"></br>' . (count($aEffectiveOwnerSpaces)) . ' own spaces(s)</br>' . (count($aEffectiveMemberSpaces)) . ' group spaces(s)</p></span>';

				$sContent .= $oPage -> pieChart($sHtmlStat, array('Owner' => $iOwner, 'Member	' => $iMember), 300, 0, array('0e8cf6', '1742cc', 'de057e'), '282828');
			}
		}
		array_push($aBoxes['CONTENT'], $oPage -> getBox($sContent, 'c1', 'content_box'));
		array_push($aBoxes['RIGHT'], $oPage -> getBox('z', 'r1', 'teaser_box'));

		$this -> view -> sPage = $oPage -> getPage('content', $aNavigations, $aBoxes, 'landing.js');
	}

	public function builderAction() {

		if (isset($this -> oAccount -> userdata['UID'])) {
			$oPage = new Application_Model_Page();
			$oApstack = new Application_Model_Appstackapi();
			$aBoxes = array('LEFT' => array(), 'CONTENT' => array(), 'RIGHT' => array());
			$oCache = new Application_Model_Cache('groups_navigations');

			if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {

				$aNavigations['HEADNAV'] = $oPage -> getNavi('navi_top', 'top_nav', 'apst_small');
				$aNavigations['MAINNAV'] = $oPage -> getNavi('navi_main', 'main_nav', '');
				$aNavigations['FOOTERNAV'] = $oPage -> getNavi('navi_bottom', 'footer', 'clearfix apst_small', ' | ');
				$oCache -> writeCache(serialize($aNavigations));
			}

			$aNavigations = unserialize($oCache -> readCache());

			$oCache -> setCache('builder_administration');

			if ($oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {
				$sAccountAdmin = $oPage -> getNavi('348', 'accountadmin', 'boxnavi', false, false, array('overview' => 'builder/index', 'builder' => 'builder/builder'));
				$oCache -> writeCache($sAccountAdmin);
			}

			$sAccountAdmin = $oCache -> readCache();

			$sMembersSpaces = $this -> oBuilder -> listMembersSpaces();
			array_push($aBoxes['LEFT'], $oPage -> getBox($sAccountAdmin, 'l1', 'teaser_box'));
			array_push($aBoxes['CONTENT'], $oPage -> getBox($sMembersSpaces, 'c1', 'content_box'));
			array_push($aBoxes['RIGHT'], $oPage -> getBox('', 'r1', 'teaser_box'));
			$this -> view -> sPage = $oPage -> getPage('content', $aNavigations, $aBoxes, 'landing.js');
		} else {
			header('Location:' . $this -> oSettings -> website -> site -> basehref);
		}

	}

	/**
	 * The builder action provides the main stage/system which is required for further
	 * operations controlled by javascript
	 * After loading this screen further communication is provided by AJAX (JSON/XML)
	 *
	 */
	public function runAction() {
		///
		if (isset($this -> oAccount -> userdata['UID']) && isset($_REQUEST['idns']) && (int)$_REQUEST['idns'] > 0) {
			$this -> oAccount -> userdata['NAMESPACE'] = (int)$_REQUEST['idns'];
		} else {
			header('Location:' . $this -> oSettings -> website -> site -> basehref);
		}
	}

}
?>
