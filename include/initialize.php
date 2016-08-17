<?php
// DIRECTORY_SEPARATOR is a PHP pre-defined constant
// (\ for Windows, / for Unix)
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

/*********** Need to change site root!! **************/
// on a mac it would be something like:
defined('SITE_ROOT') ? null : define('SITE_ROOT', DS.'Users'.DS.'xxxx'.DS.'Sites'.DS.'RegistrationForm');
// on a server it might be something like:
// defined('SITE_ROOT') ? null : define('SITE_ROOT', DS.'home'.DS.'my_acccont_name'.DS);

defined('LIB_PATH') ? null : define('LIB_PATH', SITE_ROOT.DS.'include');

// load config file first
// Database Constants
defined('DB_SERVER') ? null : define("DB_SERVER", "localhost");
defined('DB_USER')   ? null : define("DB_USER", "RegForm_user");
defined('DB_PASS')   ? null : define("DB_PASS", "@RegistrationForm12passWrd");
defined('DB_NAME')   ? null : define("DB_NAME", "RegistrationForm");

// make sure everything is set to UTF-8 (we don't want mixed encoding!)
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
date_default_timezone_set('Europe/London');

defined('WEBSITE_EMAIL') ? null : define("WEBSITE_EMAIL", "admin@mySite.com");
defined('WEBSITE_NAME') ? null : define("WEBSITE_NAME", "mySite.com");
defined('ADMIN_NAME') ? null : define("ADMIN_NAME", "Web Master");
defined('ADMIN_EMAIL') ? null : define("ADMIN_EMAIL", "webmaster@mySite.com");

// set this to "file" / "email" / "db" to save the error logs to a file / email / database respectively
defined('ERROR_LOG_TYPE')   ? null : define("ERROR_LOG_TYPE", "file");

// directory for error logs when using file type
defined('ERROR_LOG_DIR')   ? null : define("ERROR_LOG_DIR", LIB_PATH.DS."logs/php_error.log");

// as a security measure to prevent 'brute force attacks' (if a user continues 
// to try and login but uses incorrect detials)
// Once they exceed the number defined as THROTTLE_VALUE then they will not 
// be able to attempt a login for n number of minutes where 'n' is defined as THROTTLE_MINUTES. 
defined('THROTTLE_VALUE') ? null : define("THROTTLE_VALUE", 15);

// the number of minutes a user will not be able to log back in 
// after repeated failed login attempts
defined('THROTTLE_MINUTES') ? null : define("THROTTLE_MINUTES", 30);

// set this to a random key 
defined('RANDOM_KEY') ? null : define("RANDOM_KEY", "IRNOuXdAVRMHGRJTO88HmCznJIJMcTlPkHv5aSCy");


?>