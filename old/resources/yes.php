<?php

require_once("config.php");
require_once("user.php");

$id = $_COOKIE["user"];
$class_id = $_POST["courseId"];
//print($_POST);



$sql2 = "SELECT * FROM `Session` WHERE `id` = ".$id;
$result2 = mysqli_query($conn,$sql2);

$row = mysqli_fetch_row($result2);
$major = $row[1];
$year = $row[2];

$choice = 0;


$sql = "INSERT into Action SET `major`='".$major."',`year` = '".$year."',`course_id` ='".$class_id."',`choice` ='".$choice."', `session_id` = '".$id."'";


$result = $conn->query($sql);

$current_user = new user($conn,$_COOKIE["user"]);




///RECORD MODEL HERE.
$courses_taken = implode(",",$current_user->courses_taken);
$courses_no = implode(",",$current_user->courses_no);
$courses_yes = implode(",",$current_user->courses_yes);
$course_chosen = $class_id;
//$course_chosen = $current_user->request_course_name($conn,$class_id);

$sql = "INSERT into Model SET `session_id`='".$id."',`major`='".$major."',`year` = '".$year."',`courses_taken` ='".$courses_taken."',`courses_no` ='".$courses_no."', `courses_yes` = '".$courses_yes."', `course_chosen` = '".$course_chosen."'";
$result = $conn->query($sql);

/////

$new_course_id = $current_user->request_course_id($conn);
///////CALL MODEL HERE TO GET NEW COURSE NUMBER.


//$current_user->addCourse($new_course_id);
$new_course_name = $current_user->request_course_name($conn,$new_course_id);
$new_course_number = $current_user->request_course_number($conn,$new_course_id);
$new_course_dept = $current_user->request_course_department_name($conn,$new_course_id);
$new_course_rating = $current_user->request_course_rating($conn,$new_course_id);


//$query = "SELECT amount FROM Session WHERE `id` = '$id'";
//$result = mysqli_query($conn,$query);
//$amount = ((int) mysqli_fetch_row($result)[0])+1;

//$query2 = "UPDATE Session SET amount = '$amount' WHERE id = $id";
//echo "<h1> SESSION UPDATE:".$query."</h1>"
//$result2 = mysqli_query($conn,$query2);

//echo $new_course_id.$new_course_name;
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