<?php

function processURL($url){
	return preg_replace("/[^A-Za-z0-9 ]/", '', parse_url($url, PHP_URL_PATH));
}

// Check that a desired template is specified
if(!isset($_POST['url']))
	exit();

$_GET['SUGGESTR_PAGE'] = processURL($_POST['url']).'/'; // Set desired page /user/
$_GET['AJAX'] = true; // Tell controller that this is an ajax request for a page
// Generate GET variables from desired URL

$getString = parse_url($_POST['url'], PHP_URL_QUERY);
$getVars = explode('&',$getString);
foreach($getVars as $varPair){
	$vars = explode('=', $varPair);
	if(count($vars)==2){
		$_GET[$vars[0]] = $vars[1];
	}
}
// Call main site script
require_once('../../suggestr.php');

/*
// Get supporting
require_once('../../config.php');
require_once(BASE.'templating.php');

// Check template is real
if(file_exists(BASE.'Templates/'.processURL($_POST['url']).'.htm'))
	echo $template->render(processURL($_POST['url']), $objects, false);
*/

/*
function processURL($url){
	if($url[0]=='/')
		$url = substr($url, 1);
	if($url[strlen($url)-1]=='/')
		$url = substr($url, 0, -1);
	return $url;
}

$acceptable = array('host','hosts','user','users','event','collections','discover','group');
if(isset($_POST[url])){
	if(substr($_POST[url], -1)!='/')
		$url = dirname($_POST[url]);
	else
		$url = $_POST[url];
	if(file_exists('../..'.$url.'/index.php')){
		if(in_array(str_replace('/', '', $url), $acceptable)){
			if(parse_url($_POST[url], PHP_URL_QUERY)!=NULL){ // Some get variables exist
				$getVars = explode('&', parse_url($_POST[url], PHP_URL_QUERY));
				foreach($getVars as $var){
					$tempSplit = explode('=', $var);
					$_GET[$tempSplit[0]] = $tempSplit[1]; // Add get variables to $_GET
				}
			}
			require('../..'.$url.'/index.php');
		}else
			echo 'Sorry, there was an error accessing the page.';
		
	}else
		echo 'Sorry, there was an error accessing the page.';
	
}else
	echo 'Sorry, there was an error accessing the page.';
*/

?>