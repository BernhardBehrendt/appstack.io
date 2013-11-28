<?php
/**
 * Central Javascript controller
 * This controller provides simple javascript protection provided by a
 * php adaption of Dean Edwards JavaScript's Packer portet to php by Nicolas Martin
 *
 * It calls javascript files placed in application/javascript and cache them to public/cache
 * as minified base encoded .js file
 *
 *
 * @author Bbernhard Bezdek
 * @copyright Bernhard Bezdek
 *
 *
 * @category Javascript functionality
 * @version 1.0.0
 *
 * @uses JavaScriptPacker
 */
class JsController extends Zend_Controller_Action {
	private $sRequestFile;

	private $sPackerClass;
	private $sRessourcePath;

	private $sLinkSource = false;

	private $sCachePath;
	private $iCacheTime;

	private $oActionTraversing;
	private $oConf;
	private $bLoadJquery;
	private $bLoadJqueryUI;

	private $aJsStack = array('FRAMEWORKS' => array('jquery' => 'jquery'), // Loads jquery.js
		'PLUGINS' => array('ui' => 'jqueryui'), // Loads jqueryui.js
		'DEFINITIONS' => array('areas' => 'area_definitions'), // Loads area_definitions.js
		'STACKFUNCTIONS' => array('ajax' => 'ajax'), // Loads functions.ajax.js
		'NAMESPACES' => array('categories' => 'categories'), // Loads functions.categories.js and
		'INIT' => array('vars' => 'vars'), // Loads vars.js
		'MAIN' => array('main' => 'main'));
	// Loads main.js

	/**
	 * Catch the requested action and root to index if not existing
	 *
	 */
	public function init() {
		$offset = (86400 * 10);
		$expire = "expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
		header($expire);
		Preprocessor_Header::setContentType('text/javascript');
		$this -> oConf = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');
		$this -> oActionTraversing = $this -> getRequest();

		$sActionName = $this -> oActionTraversing -> getActionName();

		// Set path to packer class
		$this -> sPackerClass = $this -> oConf -> includePaths -> library . '/classes/class.javascriptpacker.php';
		// Set js caching and directory properties
		$this -> sRessourcePath = $this -> oConf -> dir -> js -> pre;
		$this -> sCachePath = $this -> oConf -> dir -> js -> post;
		$this -> iCacheTime = $this -> oConf -> dir -> js -> cachetime;

		// Store requested action
		$this -> sRequestFile = $sActionName;

		if (!method_exists($this, $sActionName . 'Action')) {
			// Run index action and perform file conectivity and process
			$this -> oActionTraversing -> setActionName('index');
		}
	}

	/**
	 * The indexaction is traversing foreign requests if an action like the requested is not represented in JsController
	 *
	 * This is the base action for lookup if the javascript protected cachefile exists
	 * and if lifetime is in range (configured in application configs)
	 *
	 * If not a new protected cache file will be generated and be written in cache
	 * To cache a javascript file the file has to be in application/javascript/ and can be called as follow:
	 * http://my_application.com/js/main => calls pat_to_application/javascript/main.js
	 * and generate /public/cache/main.min.js
	 */
	public function indexAction() {

		$sPreFile = $this -> sRessourcePath . '/' . $this -> sRequestFile;

		if (file_exists($sPreFile) && !$this -> sLinkSource) {
			$sFileSource = file_get_contents($sPreFile);
			$sPostFileName = md5(str_replace('.js', '', $this -> sRequestFile)) . '.min.js';
		} elseif (file_exists($this -> sLinkSource)) {
			$sFileSource = file_get_contents($this -> sLinkSource);
			$sPostFileName = md5(str_replace('.js', '', $this -> sLinkSource)) . '.min.js';
		}

		if ($this -> oConf -> dir -> js -> status == 0 && isset($sFileSource)) {

			$oDir = dir($this -> oConf -> dir -> js -> post);

			while (false !== ($sFileName = $oDir -> read())) {
				if (stripos($sFileName, '.js') !== false) {
					unlink($this -> oConf -> dir -> js -> post . '/' . $sFileName);
				}
			}

			echo $sFileSource;
			exit ;
		}

		$sPostFile = $this -> sCachePath . '/' . $sPostFileName;
		$bRefreshFile = @filectime($sPostFile) + $this -> iCacheTime >= time('void') ? false : true;

		if (!file_exists($sPostFile) || $bRefreshFile) {
			if (file_exists($this -> sPackerClass)) {
				ini_set('memory_limit', '512M');
				require_once ($this -> sPackerClass);

				if (isset($sFileSource)) {
					for ($i = 0; $i < $this -> oConf -> dir -> js -> crypt_turns; $i++) {
						$oJsPacker = new JavaScriptPacker($sFileSource, 'Normal', true, false);
						$sFileSource = $oJsPacker -> pack();
						unset($oJsPacker);
					}

					// For temporary cache override
					//$sPacked = file_get_contents($sPreFile);

					// Write compressed data in cachefile
					file_put_contents($sPostFile, $sFileSource);
					echo file_get_contents($sPostFile);

					// Some debug code (note only javascript code here)
					/*echo 'alert("A cashed version of file:\n  - '.
					 $this->sRequestFile.'.js\nwas created in:\n  - '.
					 $this->sRequestFile.'.min.js\n\nLookup cache directory in public path");';*/

				} else {
					$this -> view -> sMissingFile = $this -> sRequestFile;
					$this -> oActionTraversing -> setActionName('error');
				}
			}
		} else {
			echo file_get_contents($sPostFile);
		}
	}

	public function linkerAction() {
		$sReqestUri = $_SERVER['REQUEST_URI'];
		$sActionName = 'linker';
		$iActionPos = stripos($sReqestUri, $sActionName) + strlen($sActionName) + 1;
		$sLinkFile = str_replace('.js', '', substr($sReqestUri, $iActionPos, strlen($sReqestUri)));

		$sLinkCache = $this -> oConf -> dir -> js -> post . '/' . $sLinkFile . '.linked.js';

		if (file_exists($sLinkCache)) {
			$bLinkFileValid = (filectime($sLinkCache) + $this -> iCacheTime > time('void') ? true : false);
		}

		$this -> sLinkSource = $sLinkCache;

		if (!file_exists($sLinkCache) || !$bLinkFileValid || $this -> oConf -> dir -> js -> status == 0) {
			$sLinkStream = '';
			switch($sLinkFile) {
				case 'index' :
					$this -> aJsStack = array('PLUGINS' => array('jqueryuitabs' => 'jqueryuitabs'), 'MAIN' => array('ws.main' => 'ws.main'));
					$this -> bLoadJquery = true;
					$this -> bLoadJqueryUI = false;
					break;
				case 'landing' :
					$this -> aJsStack = array('MAIN' => array('ws.main' => 'ws.main'));
					$this -> bLoadJquery = true;
					$this -> bLoadJqueryUI = true;
					break;
				case 'groups' :
					$this -> aJsStack = array('PLUGINS' => array('jquery.cookie' => 'jquery.cookie', 'jquery.hotkeys' => 'jquery.hotkeys', 'jquery.jstree' => 'jquery.jstree'), 'MAIN' => array('ws.main' => 'ws.main'));
					$this -> bLoadJquery = true;
					$this -> bLoadJqueryUI = true;
					break;
				case 'account' :
					$this -> aJsStack = array('PLUGINS' => array('raphael' => 'raphael', 'popup' => 'popup', 'analytics' => 'analytics'), 'MAIN' => array('ws.main' => 'ws.main'));
					$this -> bLoadJquery = true;
					$this -> bLoadJqueryUI = true;
					break;

				case 'transfer' :
					$this -> aJsStack = array('PLUGINS' => array('jqueryuitouchpunch'=>'jqueryuitouchpunch', 'fader' => 'fader'), 'MAIN' => array('ws.main' => 'ws.main'));
					$this -> bLoadJquery = true;
					$this -> bLoadJqueryUI = true;
					break;

				case 'run' :
					$this -> aJsStack = array('PLUGINS' => array('jqueryuitouchpunch'=>'jqueryuitouchpunch','mousewheel' => 'jquerymousewheel'), 'DEFINITIONS' => array('areas' => 'area_definitions'), 'STACKFUNCTIONS' => array('ajax' => 'ajax', 'array' => 'array', 'browser' => 'browser', 'color' => 'color', 'css' => 'css', 'forms' => 'forms', 'number' => 'number', 'string' => 'string', 'time' => 'time', 'users' => 'user'), 'INIT' => array('vars' => 'init_vars'), 'MAIN' => array('main' => 'main'), 'NAMESPACES' => array('categories' => 'categories', 'composites' => 'composites', 'console' => 'console', 'dialogs' => 'dialogs', 'groups' => 'groups', 'metas' => 'metas', 'system' => 'system'));
					$this -> bLoadJquery = true;
					$this -> bLoadJqueryUI = true;
					break;
				default :
					$this -> aJsStack = false;
					break;
			}

			if ($this -> bLoadJquery) {
				$sLinkStream .= file_get_contents($this -> oConf -> website -> frameworks -> jquery);
				if ($this -> bLoadJqueryUI) {
					$sLinkStream .= file_get_contents($this -> oConf -> website -> frameworks -> jqueryui);
				}
			}

			if (is_array($this -> aJsStack)) {
				foreach ($this->aJsStack as $sScriptFor => $aFileLinks) {

					if ($sScriptFor == 'FRAMEWORKS' || $sScriptFor == 'PLUGINS' || $sScriptFor == 'DEFINITIONS' || $sScriptFor == 'INIT' || $sScriptFor == 'MAIN') {
						foreach ($aFileLinks as $iFileFor => $sFilePartName) {

							$sFileName = $this -> sRessourcePath . '/' . $sFilePartName . '.js';

							if (file_exists($sFileName)) {
								$sLinkStream .= file_get_contents($sFileName);
							} else {
								throw new Exception($sFileName . ' was not found on filesystem');
							}
						}
					}

					if ($sScriptFor == 'STACKFUNCTIONS') {
						foreach ($aFileLinks as $iFileFor => $sFilePartName) {

							$sFileName = $this -> sRessourcePath . '/functions.' . $sFilePartName . '.js';

							if (file_exists($sFileName)) {
								$sLinkStream .= file_get_contents($sFileName);
							} else {
								throw new Exception($sFileName . ' was not found on filesystem');
							}
						}
					}

					if ($sScriptFor == 'NAMESPACES') {
						foreach ($aFileLinks as $iFileFor => $sFilePartName) {

							$sFileNameFunctions = $this -> sRessourcePath . '/functions.' . $sFilePartName . '.js';
							$sFileNameInteractions = $this -> sRessourcePath . '/interactions.' . $sFilePartName . '.js';
							if (file_exists($sFileNameFunctions) && file_exists($sFileNameInteractions)) {
								$sLinkStream .= file_get_contents($sFileNameFunctions);
								$sLinkStream .= file_get_contents($sFileNameInteractions);
							} else {
								throw new Exception($sFileNameInteractions . ' / ' . $sFileNameFunctions . ' was not found on filesystem');
							}
						}
					}
				}

				file_put_contents($sLinkCache, $sLinkStream);
			}
		}
		$this -> indexAction();
	}

	/**
	 * Important method it is empty.
	 * View will be generated in index Action
	 *
	 *
	 */
	public function errorAction() {

	}

	/**
	 * testAction for special traversings
	 * NOTE its not possible to create a javascript file named test.js
	 * because of this existing action
	 *
	 * @return null
	 */
	public function testAction() {
		$this -> view -> sFilename = $this -> sRequestFile . '.js';
	}

}
?>