<?php
// Code to initialize Mustache and associated helper functions
require_once(ROOT.'/Vendor/Mustache/Autoloader.php');
Mustache_Autoloader::register();
$options =  array('extension' => '.htm');
$template = new Mustache_Engine(array(
	'loader' => new Mustache_Loader_FilesystemLoader(ROOT.'/Templates', $options),
));

function RenderError($description, $addtl = null) {
	$controller = GetController("error");
	$_SYSTEM = array("SUGGESTR_ERROR"=>$description);
	if($GLOBALS['CONFIG']['development']) $_SYSTEM["SUGGESTR_EXTENDED_ERROR"] = $addtl;
	$controller->process($_GET,$_POST,$_FILES,$_SYSTEM);
	echo $controller->render();
	die();
}

function Render($templ, $objects, $useMain=true) {
	global $template;
	$objects["BaseURL"] = $GLOBALS['CONFIG']['app-path'];
	$inner = $template->render($templ, $objects);
	$objects["BaseContent"] = $inner;	
	$objects["UserExists"] = isset($_COOKIE['userid']); // Checks if user id exists at all
	if($objects["UserExists"]){
		// Setup Mustache variables that need to be accessible on every page..
	}
	//$objects["Validated"] = isset($_GET['validated']); // User was newly validated
	if($useMain){
		// This is the place to make other ajax calls that don't use main and need to be loaded in..
		// Tasks
		// $tasksController = GetController('tasks');
		// $tasksController->process($_GET,$_POST);
		// $objects["Tasks"] = Render($tasksController->pageTemplate,$tasksController->pageData,false);
		
		return $template->render('base', $objects);
	}else
		return $inner;
}

/*
function RenderEmail($templ, $objects, $useMain=true) {
	global $template;
	$inner = $template->render('Email/'.$templ, $objects);
	$objects["BaseContent"] = $inner;	
	if($useMain) return $template->render('Email/base', $objects);
	else return $inner;
}
*/

// Function to load in Page Controllers.
// DO NOT TRUST THIS WITH EXTERNAL DATA!!!
// Note your class must be named in the following format:
// $name = "index" means a class name of IndexController
function GetController($name) {
	if (file_exists(ROOT.'/Controllers/' . $name . ".php")) {
		require_once(ROOT.'/Controllers/' . $name . ".php");
		$className = ucwords($name)."Controller";
		return new $className;
	}else{
		die();	
	}
}

class PageController {
	public $pageTemplate = "";
	public $pageData = array();

	public function process($get, $post) {
		
	}
	
	public function render($displayBase=true){
		// Could add to pageData here...
		echo Render($this->pageTemplate,$this->pageData,$displayBase);
	}
}