<?php

$options =  array('extension' => '.htm');
$ajaxTemplate = new Mustache_Engine(array(
	'loader' => new Mustache_Loader_FilesystemLoader('AjaxTemplates', $options),
));

// Function to load in API Controllers.
// DO NOT TRUST THIS WITH EXTERNAL DATA!!!
// Note your class must be named in the following format:
// $name = "index" means a class name of IndexController
function GetAjaxController($name) {
	//echo "<h1>File exits?</h1>";
	if (file_exists('AjaxControllers/' . $name . ".php")) {

		require_once('AjaxControllers/' . $name . ".php");
		$className = ucwords($name)."Controller";
		return new $className;
	}else{
		die();	
	}
}

function RenderAjax($templ, $objects) {
	global $ajaxTemplate;
	$objects["BaseURL"] = $GLOBALS['CONFIG']['app-path'];
	$inner = $ajaxTemplate->render($templ, $objects);
	//echo "<h1>RENDERING AJAX?</h1>";
	return $inner;
}

class AjaxController {
	public $template = "";
	public $pageData = array();
	public $failureReason = "";
	
	public function process($get, $post) {
		// Must return if the opporations were successful
	}
	
	public function render($success) {
		// Set universal "$this->pageData['defaultPP']" items here
		if($success){
			if(file_exists('AjaxTemplates/'.$this->template.'.htm')){ // If this ajax call has a template
				echo json_encode(array(
					"success" => true,
					"data" => RenderAjax($this->template,$this->pageData)
				));
			}else{ // All that matters is that there was success (there's no template for this ajax call)
				echo json_encode(array(
					"success" => true,
					"data" => $this->pageData  // I dont get what ajax templates have to do with this.//$this->template
				));
			}
		}else{
			echo json_encode(array(
				"success" => false,
				"reason" => $this->failureReason
			));
		}
	}
}