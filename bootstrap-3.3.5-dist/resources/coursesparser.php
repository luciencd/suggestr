<?php
	require_once("config.php");
	
    //read the json file contents
    /*
    $json = file_get_contents('http://yacs.me/api/4/courses/?semester_id=85363');
    $full = json_decode($json,true);
    $result = $full['result'];
    foreach($result as $obj) {
    	$conn->query("INSERT INTO Courses(id,name,department_id,number) VALUES('".$obj['id']."','".$obj['name']."','".$obj['department_id']."','".$obj['number']."')");
    }*/

    /*
    $json = file_get_contents('http://yacs.me/api/4/departments');
    $full = json_decode($json,true);
    $result = $full['result'];
    foreach($result as $obj) {
    	$conn->query("INSERT INTO Departments(id,name) VALUES('".$obj['id']."','".$obj['name']."')");
    }*/

?>