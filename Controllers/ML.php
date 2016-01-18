<?php
ini_set('display_errors', 1);
// Page Controller for the Index Page
require_once('Controllers/suggestions.php');
//error_reporting(0);
class MLController extends PageController {
	public $pageTemplate = "ML";

	public function process($get, $post) {
		//$_COOKIE['sessionId'] = 1;
		$this->pageData["Title"] = "ML";
		
	}

}