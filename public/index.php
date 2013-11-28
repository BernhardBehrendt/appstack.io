<?php
// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(realpath(APPLICATION_PATH . '/../library'), get_include_path(), )));

// Modifications
// define base path (public)
define('BASE_PATH', realpath(dirname(__FILE__)));

define('SMARTY_PATH', realpath(BASE_PATH . '/../library/Smarty/'));

/** Zend_Application */
require_once 'Zend/Application.php';
require_once 'classes/static/class.preprocessor.array.php';
require_once 'classes/static/class.preprocessor.string.php';
require_once 'classes/static/class.preprocessor.header.php';
// Modificatoions END

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
Zend_Session::start();

$application -> bootstrap() -> run();
?>
