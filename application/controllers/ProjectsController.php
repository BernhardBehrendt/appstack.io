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
class ProjectsController extends Zend_Controller_Action {
	private $oCache;
	private $oSettings;
	private $oAccount;
	public function init() {
		/* Initialize action controller here */
		$this -> oSettings = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');
	}

	public function indexAction() {

		$this -> oCache = new Application_Model_Cache(md5('projects/index'));
		if ($this -> oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {
			$aBoxes = array();
			$aNavigations = array();
			$oPage = new Application_Model_Page();
			$oApstack = new Application_Model_Appstackapi;

			$oApstack -> setCompReq('index_news', 'appstack', 'index_news');
			$oApstack -> setCompReq('files', 'appstack', false, 281);
			$oApstack -> setCompReq('social', 'appstack', false, 277);
			$oApstack -> setCompReq('wide', 'appstack', false, 269);
			$aWideImages = $oApstack -> ask($oApstack -> getReq('wide', 'comp'));
			$aContent = $oApstack -> ask($oApstack -> getReq('index_news', 'comp'), true);
			$aIndexFiles = $oApstack -> ask($oApstack -> getReq('files', 'comp'));
			$aSocialFiles = $oApstack -> ask($oApstack -> getReq('social', 'comp'));
			$sWideImages = '';
			$sIndexFilesHTML = '';
			$sSocialNetworks = '';

			foreach ($aIndexFiles as $iNumComp => $aComposite) {
				$sIndexFilesHTML .= $oPage -> teaserFile($aComposite);
			}

			foreach ($aSocialFiles as $iNumComp => $aComposite) {
				$sSocialNetworks .= $oPage -> teaserSocial($aComposite);
			}

			foreach ($aWideImages as $iWideImg => $aComposite) {
				$sWideImages .= $oPage -> linkImage($aComposite);
			}

			// Dont edit the following three lines
			$aNavigations['HEADNAV'] = $oPage -> getNavi('navi_top', 'top_nav', 'apst_small');
			$aNavigations['MAINNAV'] = $oPage -> getNavi('navi_main', 'main_nav', '');
			$aNavigations['FOOTERNAV'] = $oPage -> getNavi('navi_bottom', 'footer', 'clearfix apst_small', ' | ');

			// Fill boxes with content
			array_push($aBoxes, $oPage -> getBox('<div id="wide_slide"><div id="wide_slide_slider" class="clearfix">' . $sWideImages . '</div></div>', '', 'big_box'));
			array_push($aBoxes, $oPage -> getBox($aContent[0]['source_content'], false, 'small_box'));
			array_push($aBoxes, $oPage -> getTabbox('tabboxes'));
			array_push($aBoxes, $oPage -> getBox($sIndexFilesHTML, 'documents', 'small_left'));
			array_push($aBoxes, $oPage -> getBox('DATEN', 'signup', 'small_left'));
			array_push($aBoxes, $oPage -> getBox($sSocialNetworks, 'social', 'small_box'));

			$this -> oCache -> writeCache($oPage -> getPage('door', $aNavigations, $aBoxes, 'landing.js'));
			$this -> view -> sPage = $this -> oCache -> readCache();
		} else {
			$this -> view -> sPage = $this -> oCache -> readCache();
		}
	}

	public function contentpageAction() {

		$this -> oCache = new Application_Model_Cache(md5('projects/contentpage'));

		if ($this -> oCache -> isOlderAs($this -> oSettings -> website -> cachetime)) {
			$aBoxes = array('LEFT' => array(), 'CONTENT' => array(), 'RIGHT' => array());
			$aNavigations = array();
			$oPage = new Application_Model_Page();
			$oApstack = new Application_Model_Appstackapi;

			$aNavigations['HEADNAV'] = $oPage -> getNavi('navi_top', 'top_nav', 'apst_small');
			$aNavigations['MAINNAV'] = $oPage -> getNavi('navi_main', 'main_nav', '');
			$aNavigations['FOOTERNAV'] = $oPage -> getNavi('navi_bottom', 'footer', 'clearfix apst_small', ' | ');

			array_push($aBoxes['LEFT'], $oPage -> getBox('X', 'l1', 'teaser_box'));
			array_push($aBoxes['CONTENT'], $oPage -> getBox('y', 'c1', 'content_box'));
			array_push($aBoxes['RIGHT'], $oPage -> getBox('z', 'r1', 'teaser_box'));

			$this -> oCache -> writeCache($oPage -> getPage('content', $aNavigations, $aBoxes, 'landing.js'));
			$this -> view -> sPage = $this -> oCache -> readCache();
		} else {
			$this -> view -> sPage = $this -> oCache -> readCache();
		}
	}

}
?>