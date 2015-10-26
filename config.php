<?php

$GLOBALS['CONFIG'] = array();

$CONFIG['development'] = true; // Flag for development mode
$CONFIG['app-path'] = "http://suggestr.me/";
$CONFIG['db-address'] = "";
$CONFIG['db-database'] = "";
$CONFIG['db-username'] = "";
$CONFIG['db-password'] = "";
$CONFIG['smtp-host'] = "mail.suggestr.me";

// Development mode overrides
if($CONFIG['development']) {
	$CONFIG['app-path'] = "http://localhost:8888/";
	$CONFIG['db-address'] = "localhost";
	$CONFIG['db-database'] = "suggestr";
	$CONFIG['db-username'] = "root";
	$CONFIG['db-password'] = "root";
}

// Define the root directory
define("ROOT", __DIR__ ."/");

/* Initialization Code  */
// No configurable options past this point !!
$_SYSTEM = array();

if($CONFIG['development']) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
} else {
	
}
ini_set("SMTP",$CONFIG['smtp-host']);


require_once(ROOT.'templating.php');
require_once(ROOT.'ajax.php');
require_once(ROOT.'database.php');