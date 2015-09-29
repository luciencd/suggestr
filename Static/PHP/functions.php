<?php

/*
function roundUpToAny($n,$x=5) {
	return round(($n+$x/2)/$x)*$x;
}
*/

/* Hash a password */
function hashPassword($password, $joinDate){
	$salt = hash('sha512', time().uniqid(mt_rand(), true).$joinDate.rand());
	$hash = $salt.$password;
	for($i=0;$i<10000;$i++){
		$hash = hash('sha384', $hash);
	}
	return $salt.$hash;
}

/* Check if a password hash and a text password match */
function passwordsMatch($real, $new){
	$real = trim($real);
	$new = trim($new);
	$salt = substr($real, 0, 128);
	$new = $salt.$new;
	for($i=0;$i<10000;$i++){
		$new = hash('sha384', $new);
	}
	$new = $salt.$new;
	if($real==$new)
		return true;
	else
		return false;
}

/* Get the client's ip address */
function getRealIpAddr(){
	if(!empty($_SERVER['HTTP_CLIENT_IP'])){   //check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){   //to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}else{
    	$ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function strToHugType($str){
	/* Convert a hug string (e.g.: "host") to a hug type id */
	switch($str){
		case 'host':
			return 0;
			break;
		case 'user':
			return 1;
			break;
		case 'group':
			return 2;
			break;
	}
}

function isRealUser(){
	try {
		$user = new User();
		$user->findById($_COOKIE['userid']);
	} catch (Exception $e){
		return false;
	}
	return true;
}

function typeString($type){
	switch($type){
		case '0':
			return 'host';
		case '1':
			return 'user';
		case '2':
			return 'group';
		case '3':
			return 'event';
		case '4':
			return 'schedule';
	}
	return $type;
}
function typeNumber($type){
	switch($type){
		case 'host':
			return '0';
		case 'user':
			return '1';
		case 'group':
			return '2';
		case 'event':
			return '3';
		case 'schedule':
			return '4';
	}
	return $type;
}




//Returns the name of the poster of a comment or post.
function nameFromORM($ORMitem){
	switch(typeNumber($ORMitem->get('shareType'))){
		case '0':
			$ORMname = new Host();
			$ORMname->findById($ORMitem->get('shareId'));
			$name = $ORMname->get('name');
			break;
		case '1':
			$ORMname = new User();
			$ORMname->findById($ORMitem->get('shareId'));
			$name = $ORMname->get('firstname').' '.$ORMname->get('surname');
			break;
		case '2':
			$ORMname = new Group();
			$ORMname->findById($ORMitem->get('shareId'));
			$name = $ORMname->get('name');
			break;
	}
	return $name;
}

//Returns an orm of the poster of a comment or post.
function HUGORM($ORMitem){
	switch(typeNumber($ORMitem->get('shareType'))){
		case '0':
			$ORMname = new Host();
			$ORMname->findById($ORMitem->get('shareId'));
			break;
		case '1':
			$ORMname = new User();
			$ORMname->findById($ORMitem->get('shareId'));
			break;
		case '2':
			$ORMname = new Group();
			$ORMname->findById($ORMitem->get('shareId'));
			break;
	}
	return $ORMname;
}

function nameFromORM_P($ORMitem){
	switch(typeNumber($ORMitem->get('posterType'))){
		case '0':
			$ORMname = new Host();
			$ORMname->findById($ORMitem->get('posterId'));
			$name = $ORMname->get('name');
			break;
		case '1':
			$ORMname = new User();
			$ORMname->findById($ORMitem->get('posterId'));
			$name = $ORMname->get('firstname').' '.$ORMname->get('surname');
			break;
		case '2':
			$ORMname = new Group();
			$ORMname->findById($ORMitem->get('posterId'));
			$name = $ORMname->get('name');
			break;
	}
	return $name;
}

//Returns an orm of the poster of a comment or post based on PosterType.
function HUGORM_P($ORMitem){
	switch(typeNumber($ORMitem->get('posterType'))){
		case '0':
			$ORMname = new Host();
			$ORMname->findById($ORMitem->get('posterId'));
			break;
		case '1':
			$ORMname = new User();
			$ORMname->findById($ORMitem->get('posterId'));
			break;
		case '2':
			$ORMname = new Group();
			$ORMname->findById($ORMitem->get('posterId'));
			break;
	}
	return $ORMname;
}
	
// For checking if a string is a valid geographical coordinate
function isValidCoord($coord){
	return preg_match('/^(\-?\d+(\.\d+)?)$/', $coord);
}

// Fixing text
function fixText($str){
	return htmlentities(str_replace('\\', '', $str), ENT_QUOTES);
}

// Calculate textual duration between two days
function textualDuration($duration){
	if($duration/(60*60*24*29.6041667)>=1)
		return 'in '.round($duration/(60*60*24*7*29.6041667)).' month'.((round($duration/(60*60*24*7*29.6041667))==1)?'':'s');
	else if($duration/(60*60*24*7)>=1)
		return 'in '.round($duration/(60*60*24*7)).' week'.((round($duration/(60*60*24*7))==1)?'':'s');
	else if($duration/(60*60*24)>=1)
		return 'in '.round($duration/(60*60*24)).' day'.((round($duration/(60*60*24))==1)?'':'s');
	else if($duration/(60*60)>=1)
		return 'in '.round($duration/(60*60)).' hour'.((round($duration/(60*60))==1)?'':'s');
	else if($duration<=0)
		return 'already happened';
	else
		return 'in less than an hour';
}

?>