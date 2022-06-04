<?php

/* Main Application - Request Router */
// Load our config and initialization
require_once('config.php');
require_once('./AjaxModels/SessionModel.php');

//header('Content-Type: text/html; charset=utf-8');

// Make sure we have a page to load.
// IIS should handle this for us through URL Rewriting.
// This script should not be loaded directly by anyone.

if(!isset($_GET['SUGGESTR_PAGE'])){
	die();
}

// If this page load wasn't specified as an AJAX page load, mark it as such.
if(!isset($_GET['AJAX']))
	$_GET['AJAX'] = false;


// Controller Router
// The keys in the router array are page URLs
// The values in the router array are controller names
$router = array();
$router["/landing"] = "Landing";
$router["/home"] = "Home";
$router["/ml"] = "ML";

//$router["/visualization"] = "Visual";


// API Router
// The keys in the router array are page URLs
// The values in the router array are controller names
$Ajaxrouter = array();
$Ajaxrouter["Search/"] = "Search";
$Ajaxrouter["Suggest/"] = "Suggest";
$Ajaxrouter["Slider/"] = "Slider";
$Ajaxrouter["IgnoreCourse/"] = "IgnoreCourse";
$Ajaxrouter["AddCourse/"] = "AddCourse";
$Ajaxrouter["TookCourse/"] = "TookCourse";
$Ajaxrouter["RemoveCourse/"] = "RemoveCourse";
$Ajaxrouter["AddTag/"] = "AddTag";
$Ajaxrouter["Reset/"] = "Reset";
$Ajaxrouter["AddRating/"] = "AddRating";
$Ajaxrouter["AddSessionAspect/"] = "AddSessionAspect";
$Ajaxrouter["MajorRelations/"] = "MajorRelations";
$Ajaxrouter["AddAdvisory/"] = "AddAdvisory";
$Ajaxrouter["GetMajors/"] = "GetMajors";
$Ajaxrouter["CreateSession/"] = "CreateSession";
$Ajaxrouter["SimilarSuggestions/"] = "SimilarSuggestions";
$Ajaxrouter["ListCourses/"] = "ListCourses";
$Ajaxrouter["AdvisoryQuery/"] = "AdvisoryQuery";
$Ajaxrouter["CustomAlgorithm/"] = "CustomAlgorithm";

// Is this an API method?
$isAjax = (isset($_GET['SUGGESTR_PAGE']) && (strpos($_GET['SUGGESTR_PAGE'],'ajax/') === 0));

// Redirect to the user's page if no page is specified (we already know that the user is logged in).
//echo $_GET['SUGGESTR_PAGE'];

//Cookie only set temporarily
//setcookie('sessionId', 4996842, time()+315360000, '/');
//***********HOW TO get to work this without default***/
//$_GET['SUGGESTR_PAGE'] = '/ml';
//$_GET['SUGGESTR_PAGE'] = '/landing';
#echo "cookie:",$_COOKIE['sessionId'];

$session_model = new SessionModel();
#$session_model->handleSession();
#echo "is in session_id:",var_dump($session_model->isInSession());
if($_GET['SUGGESTR_PAGE']=='/home'){
	if(!$session_model->isInSession()){
	//echo "Clear everything";
	//$_GET['SUGGESTR_PAGE'] = '/home'
		$_GET['SUGGESTR_PAGE'] = '/landing';

	}else{
		$_GET['SUGGESTR_PAGE'] = '/home';
	}
}
#echo "page:",$_GET['SUGGESTR_PAGE'];

#echo "is in session_id:",var_dump($session_model->isInSession());
 // Needs to reload since a cookie must be set at the start of the request.

$controller;
ob_start();
if(!$isAjax){
	if(!array_key_exists($_GET['SUGGESTR_PAGE'],$router)) {
		//echo "404";
		header($_GET["SUGGESTR_PAGE"]." 404 Not Found");
		$_SYSTEM["SUGGESTR_PAGE"] = "404 - Page Not Found";
		$controller = GetController("error");
	}else{
		//echo "getcontroller";
		$controller = GetController($router[$_GET['SUGGESTR_PAGE']]);
	}
	//echo "process";
	$controller->process($_GET,$_POST,$_FILES,$_SYSTEM);
	die($controller->render(!$_GET['AJAX']));
}else{

	// We need to have authentication variables ready
	// Running user_isadmin sets them.
	// Note we don't care if the user is or is not an admin
	//user_isadmin();

	//Basically $_GET['SUGGESTR_PAGE'] tells the controller to display the home.htm
	//and to render it with home.php
	//echo "replace";
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