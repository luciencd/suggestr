<?php
// Code to initialize Mustache and associated helper functions
require_once(ROOT.'Controllers/suggestions.php');
require_once('Vendor/Mustache/Autoloader.php');
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
	echo "Error:".$controller->render(); // this might be cauing ajax errors.
	die();
}

function Render($templ, $objects, $useMain=true) {
	global $template;
	$objects["BaseURL"] = $GLOBALS['CONFIG']['app-path'];
	$inner = $template->render($templ, $objects);
	$objects["BaseContent"] = $inner;


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
// took away / from file thing
// require_once('config.php');
function GetController($name) {
	if (file_exists('Controllers/'.$name.'.php')) {//must change.
		require_once('Controllers/'.$name.'.php');
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