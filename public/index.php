<?php
function getmicrotime() 
{
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec); 
}
$start = getmicrotime();

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();

$finish = getmicrotime();

printf("Microtime: %.3f seconds<br/>", $finish - $start);
printf("Memory Peak: %s<br/>", memory_get_peak_usage(false));
printf("Memory Peak Real: %s<br/>", memory_get_peak_usage(true));
printf("Memory Limit: %s<br/>", ini_get('memory_limit'));