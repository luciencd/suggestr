<?php

/* Main Application - Request Router */

// Load our config and initialization
require_once('config.php');

//header('Content-Type: text/html; charset=utf-8');

// Make sure we have a page to load.
// IIS should handle this for us through URL Rewriting.
// This script should not be loaded directly by anyone.

if(!isset($_GET['SUGGESTR_PAGE']))die();

// If this page load wasn't specified as an AJAX page load, mark it as such.
if(!isset($_GET['AJAX']))
	$_GET['AJAX'] = false;


// Controller Router
// The keys in the router array are page URLs
// The values in the router array are controller names
$router = array();
$router["/home"] = "Home";

// API Router
// The keys in the router array are page URLs
// The values in the router array are controller names
$Ajaxrouter = array();
$Ajaxrouter["Search/"] = "Search";
$Ajaxrouter["Suggest/"] = "Suggest";
$Ajaxrouter["IgnoreCourse/"] = "IgnoreCourse";
$Ajaxrouter["AddCourse/"] = "AddCourse";
$Ajaxrouter["TookCourse/"] = "TookCourse";
$Ajaxrouter["RemoveCourse/"] = "RemoveCourse";
$Ajaxrouter["AddTag/"] = "AddTag";
$Ajaxrouter["Reset/"] = "Reset";
$Ajaxrouter["AddRating/"] = "AddRating";
$Ajaxrouter["AddSessionAspect/"] = "AddSessionAspect";
// Is this an API method?
$isAjax = (isset($_GET['SUGGESTR_PAGE']) && (strpos($_GET['SUGGESTR_PAGE'],'ajax/') === 0));

// Are we authenticated?
// Everything except the login page requires authentication
/*
if(!isset($_GET['SUGGESTR_PAGE']) || $_GET['SUGGESTR_PAGE'] != "login/") {
	if(!$_GET['AJAX']&&!$isAjax) // Only redirect to the login page if this is not an ajax call (inside an existing page, NOT through the API).
		user_login();
	else{
		if(!isset($_COOKIE['userid'])&&!$isAjax) // If they are not logged in but using AJAX to load page, redirect them to an ajax version of the login screen
			$_GET['SUGGESTR_PAGE'] = 'login/';
	}
}
*/
// Redirect to the user's page if no page is specified (we already know that the user is logged in).
if($_GET['SUGGESTR_PAGE']=='')
	$_GET['SUGGESTR_PAGE'] = 'home/';

// Ensure the user is an administrator when accessing pages in /admin
/*
if(isset($_GET['SUGGESTR_PAGE']) && 
   (($_GET['SUGGESTR_PAGE'] == "admin") || 
	(strpos($_GET['SUGGESTR_PAGE'],'admin/') !== false)) && !user_isadmin()) {
	// Uh oh... This user should not be here!
	RenderError("You are not authorized to access the page requested.");
}
*/

// Ok. Now we render the appropriate controller.
//echo "<h4>suggestr.php b4 controller</h4>";
$controller;
ob_start();
if(!$isAjax){
	if(!array_key_exists($_GET['SUGGESTR_PAGE'],$router)) {
		header($_GET["SUGGESTR_PAGE"]." 404 Not Found");
		$_SYSTEM["SUGGESTR_PAGE"] = "404 - Page Not Found";
		$controller = GetController("error");
	}else{
		$controller = GetController($router[$_GET['SUGGESTR_PAGE']]);
	}
	$controller->process($_GET,$_POST,$_FILES,$_SYSTEM);
	die($controller->render(!$_GET['AJAX']));
}else{

	// We need to have authentication variables ready
	// Running user_isadmin sets them.
	// Note we don't care if the user is or is not an admin
	//user_isadmin();
	
	$method = str_replace("ajax/","",$_GET['SUGGESTR_PAGE']);
	if(!array_key_exists($method,$Ajaxrouter)) {
		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
		die(json_encode(array(
				"success" => false,
				"reason" => "Invalid Method"
			))
		);
	}else{
		$controller = GetAjaxController($Ajaxrouter[$method]);
	}
	$success = $controller->process($_GET,$_POST,$_FILES,$_SYSTEM);
	die($controller->render($success));
}