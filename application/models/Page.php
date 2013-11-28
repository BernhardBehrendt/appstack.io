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
define('TPL_FILTERS', 'website.filters.html');
define('TPL_USERTABLE', 'website.user.table.html');
define('TPL_LINECHART', 'website.line.chart.html');
define('TPL_PIECHART', 'website.pie.chart.html');
define('TPL_IMG_LINK', 'website.imagelink.html');
define('TPL_MAIN_PAGE', 'website.main.page.html');
define('TPL_NAVI', 'website.navi.html');
define('TPL_TABBOX', 'website.door.tabbox.html');
define('TPL_BIGBOX', 'website.box.html');
define('TPL_TEASER_FILE', 'website.teaser.file.html');
define('TPL_TEASER_SOCIAL', 'website.teaser.social.html');

class Application_Model_Page extends Application_Model_Appstackapi {

	private $oSmarty = false;
	private $oSettings;
	private $sJsStorage;
	public function __construct() {
		$this -> oSettings = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');
		$this -> oSmarty = Application_Model_Smarty::getInstance();
	}

	public function getPage($sMode, $aNavigations, $aContents, $sLinkedJs) {
		$bSuccessPage = false;
		if ($sMode == 'door') {
			if (is_array($aContents)) {
				$this -> oSmarty -> assign('SM_ARR_DOOR_BOXES', $aContents);
				$this -> oSmarty -> assign('SM_ARR_CONT_BOXES', false);
				$bSuccessPage = true;
			} else {
				return 'Doorway Content can only be a numeric array';
			}
		}
		if ($sMode == 'content') {

			if (!isset($aContents['LEFT']) || !isset($aContents['CONTENT']) || !isset($aContents['RIGHT'])) {
				return 'A content page requires an array array(LEFT => array(), CONTENT => array(), RIGHT => array())';
			}

			$this -> oSmarty -> assign('SM_ARR_CONT_BOXES', $aContents);
			$this -> oSmarty -> assign('SM_ARR_DOOR_BOXES', false);
			$bSuccessPage = true;
		}

		if ($bSuccessPage) {
			if (is_array($aNavigations)) {
				if (isset($aNavigations['HEADNAV'])) {
					$this -> oSmarty -> assign('SM_STR_HEAD_NAV', $aNavigations['HEADNAV']);
				}
				if (isset($aNavigations['MAINNAV'])) {
					$this -> oSmarty -> assign('SM_STR_MAIN_NAV', $aNavigations['MAINNAV']);
				}
				if (isset($aNavigations['FOOTERNAV'])) {
					$this -> oSmarty -> assign('SM_STR_FOOTER_NAV', $aNavigations['FOOTERNAV']);
				}
			}
			$this -> oSmarty -> assign('SM_STR_BASEHREF', $this -> oSettings -> website -> site -> basehref);
			$this -> oSmarty -> assign('SM_STR_STATICDOMAIN', $this -> oSettings -> website -> data -> static);
			$this -> oSmarty -> assign('SM_STR_JQUERY', $this -> oSettings -> website -> frameworks -> jquery);
			$this -> oSmarty -> assign('SM_STR_JQUERYUI', $this -> oSettings -> website -> frameworks -> jqueryui);
			$this -> oSmarty -> assign('SM_BOOL_SECURITY', $this -> oSettings -> website -> security -> ssl);

			$this -> oSmarty -> assign('SM_STR_LINKED_JS', $sLinkedJs);
			$this -> oSmarty -> assign('SM_STR_JS_ATTACH', $this -> sJsStorage);

			$sPage = $this -> oSmarty -> fetch(TPL_MAIN_PAGE);
			if ($this -> oSettings -> output -> minify -> html == 1) {
				$sPage = Preprocessor_String::minifyString($sPage);
			}
			return $sPage;
		} else {
			print_r($sMode);
			return 'Unknown pagemode (its only door and content mode allowed)';
		}
	}

	public function getNavi($sAPSTRootcat, $sIdName, $sClasses, $sSeparator = false, $sBaseAttatch = '', $aRedirect = false) {
		$this -> setCatReq($sAPSTRootcat, 'appstack', $sAPSTRootcat);
		$this -> oSmarty -> assign('SM_A_MAINNAV', $this -> ask($this -> getReq($sAPSTRootcat, 'cat')));
		$this -> oSmarty -> assign('SM_STR_IDNAME', $sIdName);
		$this -> oSmarty -> assign('SM_STR_CLASSES', $sIdName);
		$this -> oSmarty -> assign('SM_STR_SEPARATOR', $sSeparator);

		if (is_array($aRedirect)) {
			$this -> oSmarty -> assign('SM_A_REDIRECT', $aRedirect);
		}

		$this -> oSmarty -> assign('SM_STR_DIR', $this -> oSettings -> website -> site -> basehref . $sBaseAttatch);
		return $this -> oSmarty -> fetch(TPL_NAVI);
	}

	public function getTabbox($APSTCategory) {

		$this -> setCompReq($APSTCategory, 'appstack', false, $APSTCategory);
		$this -> oSmarty -> assign('SM_A_TABS', $this -> ask($this -> getReq($APSTCategory, 'comp')));

		return $this -> oSmarty -> fetch(TPL_TABBOX);
	}

	public function getBox($sContent, $sIdName, $sClasses) {
		$this -> oSmarty -> assign('SM_BOX_CONTENT', $sContent);
		$this -> oSmarty -> assign('SM_STR_IDNAME', $sIdName);
		$this -> oSmarty -> assign('SM_STR_CLASSES', $sClasses);
		return $this -> oSmarty -> fetch(TPL_BIGBOX);
	}

	public function teaserFile($aComposite) {
		if (isset($aComposite['tags']['mime'])) {

			$sImgMimeDir = BASE_PATH . '/img/filetypes/';
			$sFiletype = strtolower($aComposite['tags']['mime']['properties']['mimetype']);
			$sFileName = strtolower($aComposite['tags']['file']['properties']['name']);
			if (file_exists($sImgMimeDir . $sFiletype . '.jpg')) {
				$sImgMimePath = $this -> oSettings -> website -> data -> static . 'img/filetypes/' . $sFiletype . '.jpg';
			}
			if (isset($sImgMimePath)) {
				$this -> oSmarty -> assign('SM_STR_MIME_SRC', $sImgMimePath);
			}
			$this -> oSmarty -> assign('SM_STR_ALT', $aComposite['name']);
			$this -> oSmarty -> assign('SM_STR_FILEDESC', $aComposite['name']);
			$this -> oSmarty -> assign('SM_STR_HREF', $aComposite['source'] . $sFileName . '.' . $sFiletype);

			return $this -> oSmarty -> fetch(TPL_TEASER_FILE);
		}
		return false;
	}

	public function teaserSocial($aComposite) {
		if (isset($aComposite['tags']['network'])) {

			$sImgNetworkDir = BASE_PATH . '/img/networks/';
			$sNetwork = strtolower($aComposite['tags']['network']['properties']['name']);
			if (file_exists($sImgNetworkDir . $sNetwork . '.png')) {
				$sImgNetwork = $this -> oSettings -> website -> data -> static . 'img/networks/' . $sNetwork . '.png';
			}
			if (isset($sImgNetwork)) {
				$this -> oSmarty -> assign('SM_STR_SOCIAL_SRC', $sImgNetwork);
			}
			$this -> oSmarty -> assign('SM_STR_ALT', $aComposite['name']);
			$this -> oSmarty -> assign('SM_STR_SOCIALDESC', $aComposite['name']);
			$this -> oSmarty -> assign('SM_STR_HREF', $aComposite['source']);

			return $this -> oSmarty -> fetch(TPL_TEASER_SOCIAL);
		}
		return false;
	}

	public function linkImage($aComposite) {
		$this -> oSmarty = Application_Model_Smarty::getInstance(true);
		$aTags = $aComposite['tags'];
		if (isset($aTags['style']['properties']['classes'])) {
			$this -> oSmarty -> assign('SM_STR_CLASS', $aTags['style']['properties']['classes']);
		}
		if (isset($aTags['link']['properties']['href'])) {
			$this -> oSmarty -> assign('SM_STR_HREF', $aTags['link']['properties']['href']);
		}
		if (isset($aTags['link']['properties']['rel'])) {
			$this -> oSmarty -> assign('SM_STR_REL', $aTags['link']['properties']['rel']);
		}
		if (isset($aTags['file']['properties']['name']) && isset($aTags['mime']['properties']['mimetype'])) {
			$this -> oSmarty -> assign('SM_STR_IMGSRC', $aComposite['source'] . $aTags['file']['properties']['name'] . '.' . $aTags['mime']['properties']['mimetype']);
			$this -> oSmarty -> assign('SM_STR_IMGALT', $aTags['file']['properties']['name']);

		}

		return $this -> oSmarty -> fetch(TPL_IMG_LINK);
	}

	public function pieChart($sName, $aDatSet, $iWidth, $iRotate, $aColors, $sHexBg) {

		$sSize = 'chs=' . $iWidth . 'x' . floor(($iWidth / 2.5)) . '&amp;';
		$sRotate = 'chp=' . $iRotate . '&amp;';
		foreach ($aDatSet as $sLabel => $iValue) {
			if (!isset($sLabels)) {
				$sLabels = 'chl=' . $sLabel;
			} else {
				$sLabels .= '|' . $sLabel;
			}
			if (!isset($sValues)) {
				$sValues = 'chd=t:' . $iValue;
			} else {
				$sValues .= ',' . $iValue;
			}
		}

		if (isset($sValues) && isset($sLabels)) {
			$sValues .= '&amp;';
			$sLabels .= '&amp;';
		} else {
			echo 'Wrong Data';
			exit ;
		}

		if (count($aColors) == 2 && isset($aColors[0]) && isset($aColors[1])) {
			$sColors = 'chco=' . $aColors[0] . ',' . $aColors[1] . '&amp;';
		} else {
			$sColors = 'chco=' . implode(',', $aColors) . '&amp;';
		}

		$sBackground = 'chf=bg,s,' . preg_replace('/#/', '', $sHexBg) . '&amp;';
		$sChartURL = 'http://chart.apis.google.com/chart?&amp;cht=p3&amp;' . $sSize . $sRotate . $sValues . $sLabels . $sColors . $sBackground . 'chxt=x';

		$oSmarty = Application_Model_Smarty::getInstance(true);
		$oSmarty -> assign('SM_STR_IMGPATH', $sChartURL);
		$oSmarty -> assign('SM_STR_CHARTNAME', $sName);

		return $oSmarty -> fetch(TPL_PIECHART);
	}

	public function getFiltes($sAction, $iLimitation, $sFilterLetter) {
		$oSmarty = Application_Model_Smarty::getInstance(true);
		$oSmarty -> assign('SM_STR_ACTION', $this -> oSettings -> website -> site -> basehref . $sAction);
		$oSmarty -> assign('SM_A_LIMITS', array(5, 10, 25, 50, 100));
		$oSmarty -> assign('SM_I_LIMITATION', $iLimitation);
		$oSmarty -> assign('SM_STR_LETTER', $sFilterLetter);
		$oSmarty -> assign('SM_A_LETTERS', array('*', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'));
		return $oSmarty -> fetch(TPL_FILTERS);
	}

	public function getUserTable($sAction, $aUsers, $iSumAccounts, $iPerPage, $iCurIndex) {
		$oSmarty = Application_Model_Smarty::getInstance(true);
		$oSmarty -> assign('SM_I_USERS', $iSumAccounts);
		$oSmarty -> assign('SM_I_PERPAGE', $iPerPage);
		$oSmarty -> assign('SM_I_INDEX', $iCurIndex);
		$oSmarty -> assign('SM_A_USERS', $aUsers);
		$oSmarty -> assign('SM_I_LIMITATION', $this -> oSettings -> website -> site -> basehref . $sAction);
		return $oSmarty -> fetch(TPL_USERTABLE);
	}

	public function lineChart($sTarget, $sIdName, $aChartConf) {

		$oSmarty = Application_Model_Smarty::getInstance(true);
		$oSmarty -> assign('SM_ARR_CHARTCONF', $aChartConf);
		$oSmarty -> assign('SM_STR_CHARTID', $sIdName);
		$oSmarty -> assign('SM_STR_CHART_TARGET', $sTarget);
		//$oSmarty -> assign('SM_STR_CHARTNAME', $sName);

		$this -> sJsStorage = $oSmarty -> fetch(TPL_LINECHART) . $this -> sJsStorage;
	}

}
?>