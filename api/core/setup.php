<?php
// Set include path
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . realpath('../proxyclasses') . '/');
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . realpath('../../application/models/') . '/');
set_include_path(implode(PATH_SEPARATOR, array(realpath('../../library'), get_include_path())));

// Defines
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath('../../application'));
defined('BASE_PATH') || define('BASE_PATH', realpath('../../public'));

define('RESSOURCE_IDENTIFIER', 'res');
define('NAMESPACE_IDENTIFIER', 'ns');

//Load external classes
require_once 'Zend/Loader/Autoloader.php';
require_once '../../api/core/init.php';
require_once 'classes/static/class.preprocessor.array.php';
require_once 'classes/static/class.preprocessor.string.php';
require_once 'classes/static/class.preprocessor.header.php';
require_once 'rest/RestServer.php';

function fetchClass($class) {
	if (stripos($class, 'Zend_') === false && stripos($class, 'Smarty')===false) {
		$class = str_replace('Application_Model_', '', $class);
		$sFilePath = stream_resolve_include_path($class . '.php');
		if ($sFilePath !== false) {
			require_once ($sFilePath);
		} else {
			die(json_encode(array('error' => 'true', 'message' => '404 Ressource Not Found')));
		}
	}
}

spl_autoload_register('fetchClass');

$oLoader = Zend_Loader_Autoloader::getInstance();
$oAuthProc = Application_Model_Session::getInstance('OAUTHPROC');
?>