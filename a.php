<?php

/*
 * This file is designed to redirect AJAX calls to suggestr.php. The purpose is to make the POST calls in the JS look cleaner. Basically,
 * 		/a.php?p=Login_Join
 * points to:
 *      /suggestr.php?SUGGESTR_PAGE=/ajax/Login_Join
 */

if(isset($_GET['p'])){
	
	$_GET['SUGGESTR_PAGE'] = 'ajax/'.str_replace('/', '', $_GET['p']).'/';

	require('suggestr.php');
}

?>