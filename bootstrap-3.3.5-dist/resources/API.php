<?php 
require_once("config.php");

function request_course_id($conn){
	$sql = "SELECT * FROM Courses";
	$result = $conn->query($sql);
	$class_id = rand(1,$result->num_rows);

	$sql2 = "SELECT * FROM `Courses` WHERE `id` = ".$class_id;
	$result2 = mysqli_query($conn,$sql2);

	$course = mysqli_fetch_row($result2);

	return $course[0];

}
function request_course_name($conn,$course_id){
	$sql = "SELECT * FROM Courses";
	$result = $conn->query($sql);

	$sql2 = "SELECT * FROM `Courses` WHERE `id` = ".$course_id;
	$result2 = mysqli_query($conn,$sql2);

	$course = mysqli_fetch_row($result2);

	return $course[1];
}

function get_array_similar($conn){

	$sql = "SELECT * FROM Session";
	$result = $conn->query($sql);
	
	$user_id = $_COOKIE['user'];

	$sql2 = "SELECT * FROM `Session` WHERE `id` = ".$user_id;
	$user_query = mysqli_query($conn,$sql2);

	$user_row = mysqli_fetch_row($user_query);

	$major = $user_row[1];
	$year = $user_row[2];

	echo "Users who are: ".$major.$year." Chose to take these classes:";
	//$sql3 = "SELECT * FROM `Action` WHERE `major` = '".$major."' AND `year` = '".$year."' AND `choice` = '0' ORDER by course_id DESC";
	$sql3 = "SELECT course_id,count(*) FROM `Action` WHERE `major` = '".$major."' AND `year` = '".$year."' AND `choice` = '0' GROUP by course_id ORDER BY count(*) DESC";
	echo $sql3;
	$selected_courses = mysqli_query($conn,$sql3);
	//Choose the classes "chosen" by previous people who were of your major.
	//$priorityQueue = new PQtest();
	//$priorityQueue->insert($potential_course[3],$times);
	/*echo "<ul>";
	while($potential_course = mysqli_fetch_row($selected_courses)){
		echo "<li>".$potential_course[1]." : ".request_course_name($conn,$potential_course[0])."</li>";
	}
	echo "</ul>";
	*/
	return $selected_courses;

}
?>