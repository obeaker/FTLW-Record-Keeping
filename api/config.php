<?php
/**
 * Database configuration
 */
define('DB_USERNAME', 'username');
define('DB_PASSWORD', 'password');
define('DB_HOST', 'localhost');
define('DB_NAME', 'ftlw_recordkeeping');
define("RESTRICTED_IP", "192.168.0.2");
define("TBL_ATTEMPTS", "login_attempts");
define("TBL_USERS", "ftlw_users");
define("ATTEMPTS_NUMBER", "3");
define("TIME_PERIOD", "60");
define("COOKIE_EXPIRE", 60*60*24*100);  
define("COOKIE_PATH", "/");
define("EMAIL_USERNAME", "");
define("EMAIL_PASSWORD", "");
define("FROM_EMAIL", "");
define("FROM_NAME", "");
define("REPLY_TO_EMAIL", "");
define("REPLY_TO_NAME", "");
define("TO_EMAIL", "");
define("TO_NAME", "");
?>
