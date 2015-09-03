<?php

require_once("config.php");

$major = $_POST["major"];
$year = $_POST["year"];


$insert = "INSERT INTO Session (id,major,year,taken,keep,amount,department_id) VALUES (0,'NULL','NULL',0,0,0,0)";
$result = $conn->query($insert);
$id = $conn->insert_id;
/*
$insert = "UPDATE Session SET department_id = 617 WHERE id = 617";
$result = $conn->query($insert);
$id = $conn->insert_id;*/

setcookie("user", $id, time() + (86400 * 30), "/");


//SQL prepared statement.
//$find_department_id = $conn->prepare("SELECT * FROM Departments WHERE name ='".$major."'");

$department_result = $conn->query("SELECT * FROM Departments WHERE name ='".$major."'");
$department_id = mysqli_fetch_row($department_result)[0];

//$sql = "UPDATE Session SET `major`='".$major."',`year` = '".$year."',`amount`=0, `department_id`='".$department_id."' WHERE `id` = ".$id;
$sql = "UPDATE Session SET `major`='".$major."',`year` = '".$year."',`amount`=0, `department_id`='".$department_id."' WHERE `id` = ".$id;
$result = $conn->query($sql);

echo json_encode(array( "user" => $id,
						"department_id" => $department_id,
						"sql" => $insert,
						"priority" => $priority),JSON_PRETTY_PRINT);

?>