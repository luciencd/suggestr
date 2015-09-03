<?php 
require_once("config.php");
//<input type="text" id="inputMajor" name = "major" class = "form-control" placeholder = "major(Computer Science)" required autofocus>
echo "<select id='inputMajor' name = 'major' class='form-control'>";

$majors = "SELECT id,name FROM `Departments`";

$majorsResult = $conn->query($majors);

$majorArray = array();

while ($row = $majorsResult->fetch_assoc()) {
	$majorArray[$row['id']] = $row["name"];
	echo "<option value='".$row['name']."'>".$row["name"]."</option>";
}


echo "</select>";

?>