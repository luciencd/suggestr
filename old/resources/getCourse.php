<?php

require_once("config.php");
require_once("user.php");

$current_user = new user($conn,$_COOKIE["user"]);



$user_id = $_COOKIE['user'];

$new_course_id = $current_user->request_course_id($conn);
$new_course_name = $current_user->request_course_name($conn,$new_course_id);
$new_course_number = $current_user->request_course_number($conn,$new_course_id);
$new_course_dept = $current_user->request_course_department_name($conn,$new_course_id);
$new_course_rating = $current_user->request_course_rating($conn,$new_course_id);

//Good^



$query = "SELECT amount FROM Session WHERE `id` = '$user_id'";
$result = mysqli_query($conn,$query);
$amount = ((int) mysqli_fetch_row($result)[0])+1;
//echo "<h1>".$amount."</h1>";

$query2 = "UPDATE Session SET amount = '$amount' WHERE id = $user_id";
//echo "<h1> SESSION UPDATE:".$query."</h1>"
$result2 = mysqli_query($conn,$query2);

//setcookie("priority",++$_COOKIE['priority']);
//echo $new_course_id.$new_course_name;
//$new_course_id = '1';
//$new_course_name = 'calculus 1';
echo json_encode(array( "getid" => $new_course_id,
						"getname" => $new_course_name,
						"getnumber" => $new_course_number,
						"getdept" => $new_course_dept,
						"getrating" => $new_course_rating),JSON_PRETTY_PRINT);
//$_COOKIE["user"] = var_dump($_POST);
//header("Location: took.php");
//exit;
//echo $class_id.var_dump($_POST);
//echo var_dump($_POST);



?>