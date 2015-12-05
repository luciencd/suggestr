<?php

class user
{
    // property declaration
    private $id;

    private $major;
    private $year;
    private $department_id;
    private $sql2;

    private $selected = array();
    private $priorityQueue = array();


    //All data for model
    public $courses_taken = array();
    public $courses_no = array();
    public $courses_yes = array();




    // method declaration

    function __construct($conn,$id2) {
    	//echo "<h1>TEST</h1>";
    	$this->id = $id2;

    	$sql2 = "SELECT * FROM `Session` WHERE `id` = ".$this->id;
 		$this->sql = $sql2;
    	$result2 = mysqli_query($conn,$sql2);

		$row = mysqli_fetch_row($result2);
		$this->major = $row[1];
		$this->year = $row[2];
		$this->department_id = $row[6];


		$this->courses_no = array();
		$this->courses_yes = array();
		$this->courses_taken = array();

		$sql = "SELECT * FROM `Action` WHERE `session_id` = ".$this->id;
		$result = mysqli_query($conn,$sql);
		while($current_row = mysqli_fetch_row($result)){
			if($current_row[4]==0){
				array_push($this->courses_yes,$current_row[3]);
			}
			if($current_row[4]==1){
				array_push($this->courses_taken,$current_row[3]);
			}
			if($current_row[4]==2){
				array_push($this->courses_no,$current_row[3]);
			}
		}

		//echo "<h1>".$row[1].",".$row[2].",".$row[6].",".$this->department_id."</h1>";
		
		//$this->priorityQueue = $this->get_array_similar($conn);
		//echo "<ul>";
		//while($potential_course = mysqli_fetch_row($this->$priorityQueue)){
		//	echo "<li>".$potential_course[1]." : ".request_course_name($conn,$potential_course[0])."</li>";
		//}
		//echo "</ul>";
    	//Sql all the variables.
    }

    //public function onDeck($courseId){//Checking if the course is already on the page.
    	//echo "<h1>Course ID:".$courseId."</h1>";

    	//Go through mysql table, and select 4 most recent 
    	//return $_COOKIE['array'][$courseId];
    	
    //}
    //public function get_data($conn){

		//while($potential_course = mysqli_fetch_row($courses_list)){
		//$priority_queue = mysqli_fetch_all($courses_list);
		
        //}

    //}
    /*public function heuristic_course_id($conn){
    	//Assign 
    	//Make array of all courses in the 
    	//Gather data on courses currently being offered. assign each a weight of -1.

    	//





    	//return number betwen 1 and number of courses


    }*/
    public function getPriority($conn){

    	$user_id = $_COOKIE['user'];

		$query = "SELECT amount FROM `Session` WHERE `id` = '".$user_id."'";
		$result = mysqli_query($conn,$query);
		
		if($row = mysqli_fetch_row($result)){
			return $row[0];
		}else{
			return 0;
		}
    }

    public function request_course_id($conn){
    	
		$courses_list = $this->get_array_similar($conn);

		
		$i = 0;
		foreach($courses_list as $key => $value){
			//echo "<h1>".$key."</h1>";
			if($i == $this->getPriority($conn)){

		        return $key;
		    }
		    $i+=1;
	    }
		//$sql = "SELECT * FROM Courses";
		//$result = $conn->query($sql);
		//$class_id = rand(1,$result->num_rows);
		//while($this->onDeck($class_id)){
		//	$class_id = $class_id+1;
		//}
		
		//$class_id = rand(1,$result->num_rows);
		//$class_id = time()%10;//count($this->selected)+1;
		
		//We've chosen our course.
		/*$sql2 = "SELECT * FROM `Courses` WHERE `id` = ".$class_id;
		$result2 = mysqli_query($conn,$sql2);

		$course = mysqli_fetch_row($result2);

		return $course[0];*/
		
	}
	public function request_course_rating($conn,$course_id){
    	
		$courses_list = $this->priorityQueue;
		foreach($courses_list as $key => $value){
			if($key == $course_id){
		        return $value;
		    }
	    }
	}
	public function request_course_name($conn,$course_id){

		$sql2 = "SELECT * FROM `Courses` WHERE `id` = ".$course_id;
		$result2 = mysqli_query($conn,$sql2);

		$course = mysqli_fetch_row($result2);

		return $course[1];
	}
	public function request_course_number($conn,$course_id){

		$sql2 = "SELECT * FROM `Courses` WHERE `id` = ".$course_id;
		$result2 = mysqli_query($conn,$sql2);

		$course = mysqli_fetch_row($result2);

		return $course[3];
	}

	public function request_course_department($conn,$course_id){

		$sql2 = "SELECT * FROM `Courses` WHERE `id` = ".$course_id;
		$result2 = mysqli_query($conn,$sql2);

		$course = mysqli_fetch_row($result2);

		return $course[2];
	}
	public function request_course_department_name($conn,$course_id){

		$sql2 = "SELECT * FROM `Courses` WHERE `id` = ".$course_id;
		$result2 = mysqli_query($conn,$sql2);

		$course = mysqli_fetch_row($result2);

		$sql3 = "SELECT * FROM `Departments` WHERE `id` = ".$course[2];
		$result3 = mysqli_query($conn,$sql3);

		$dept = mysqli_fetch_row($result3);
		return $dept[1];
	}

	public function get_array_similar($conn){

		$sql = "SELECT * FROM Session";
		$result = $conn->query($sql);
		
		$user_id = $_COOKIE['user'];
		$stop = $_COOKIE['stop'];

		$sql2 = "SELECT * FROM `Session` WHERE `id` = ".$user_id;
		$user_query = mysqli_query($conn,$sql2);

		$user_row = mysqli_fetch_row($user_query);

		$major = $user_row[1];// session_id < user_id doesnt work because of lexicographics
		$year = $user_row[2];

		//echo "Users who are: ".$major.$year." Chose to take these classes:";
		$yesql = "SELECT course_id,count(*) FROM `Action` WHERE `major` = '$major' AND `year` = '$year' AND `choice` = '0' AND `session_id` < '$user_id' GROUP by course_id ORDER BY count(*) DESC";
		$yes_courses = $conn->query($yesql);
		$yesary = array();
		while ($row = $yes_courses->fetch_assoc()) {
    		$yesary[$row['course_id']] = $row["count(*)"];
		}

		// Count people in major and year who chose not to take that course.
		$nosql = "SELECT course_id,count(*) FROM `Action` WHERE `major` = '$major' AND `year` = '$year' AND `choice` = '2'  AND `session_id` < '$user_id' GROUP by course_id ORDER BY count(*) DESC";
		$no_courses = $conn->query($nosql);
		$noary = array();
		while ($row = $no_courses->fetch_assoc()) {
    		$noary[$row['course_id']] = $row["count(*)"];
		}

		$yesql_all = "SELECT course_id,count(*) FROM `Action` WHERE `year` = '$year' AND `choice` = '0'  AND `session_id` < '$user_id' GROUP by course_id ORDER BY count(*) DESC";
		$yes_courses_all = $conn->query($yesql_all);
		$yesary_all = array();
		while ($row = $yes_courses_all->fetch_assoc()) {
    		$yesary[$row['course_id']] = $row["count(*)"];
		}

		$nosql_all = "SELECT course_id,count(*) FROM `Action` WHERE `year` = '$year' AND `choice` = '2'  AND `session_id` < '$user_id' GROUP by course_id ORDER BY count(*) DESC";
		$no_courses_all = $conn->query($nosql_all);
		$noary_all = array();
		while ($row = $no_courses_all->fetch_assoc()) {
    		$noary_all[$row['course_id']] = $row["count(*)"];
		}
		//echo '<h4>'.$nosql_all.'<h4>';

		$allsql = "SELECT * FROM `Courses`";
		$allCourses = $conn->query($allsql);

		//$allCourses = mysqli_fetch_all($conn->query($allsql),MYSQLI_ASSOC);
		//In order to make the initial work easier, we will decrease the score for every "no" it gets.

		$ary = array();
		while ($row = $allCourses->fetch_assoc()) {
    		$ary[$row['id']] = 0;
    		//echo "(".$row['id']." | ".$row['id'][0].")";
    		//echo $this->department_id;
    		if($row['department_id'] == $this->department_id){
    			$ary[$row['id']] +=190;
    		}
    		if($row['number'][0] == 1){
    			$ary[$row['id']] +=200;
    		}
    		if($row['number'][0] == 2){
    			$ary[$row['id']] +=100;
    		}
    		if($row['number'][0] == 4){
    			$ary[$row['id']] +=10;
    		}
    		if($row['number'][0] == 6){
    			$ary[$row['id']] -=1000;
    		}
    		if($row['number'][0] == 9){
    			$ary[$row['id']] -=1000;
    		}
    		$ary[$row['id']]+=$row['number']%100/10;

    		//echo "<h2>".(($row['number']%100)/10)."</h2>";


    		//echo "<h5>".$this->id.",".$this->department_id."(".$row['id'].",".$row['name'].",".$row['department_id'].",".$ary[$row['id']].")</h5>";
		}
		

		

		
		foreach($yesary as $course => $score){
			$ary[$course] += $score*5; 
		}
		foreach($yesary_all as $course => $score){
			$ary[$course] += 2*$score; 
		}

		foreach($noary as $course => $score){
			$ary[$course] -= $score*5; 
		}
		foreach($noary_all as $course => $score){
			$ary[$course] -= 2*$score; 
		}


		
		arsort($ary);

		$this->priorityQueue = $ary;

		//foreach($ary as $key => $value){
		//	echo "<h4>(".$key."|". $value.")</h4>";
	    //}
		

		return $ary;

	}

	public function print_this($conn){

		//echo "<h1>".$this->sql."current_user: ".$this->id.",".$this->major.",".$this->year.",".$this->department_id."</h1>";
        
        echo "<h2>NO:";
        foreach($this->courses_no as $id){
        	echo "<h3>".$this->request_course_name($conn,$id)."</h3>";
        }
        echo "</h2>";

        echo "<h2>YES:";
        foreach($this->courses_yes as $id){
        	echo "<h3>".$this->request_course_name($conn,$id)."</h3>";
        }
        echo "</h2>";

        echo "<h2>TAKEN:";
        foreach($this->courses_taken as $id){
        	echo "<h3>".$this->request_course_name($conn,$id)."</h3>";
        }
        echo "</h2>";
        //DEBUGGER!!!
	}
	public function get_courses_yes(){
		return $this->courses_yes;
	}
	public function get_courses_took(){
		return $this->courses_took;
	}
	public function get_courses_no(){
		return $this->courses_no;
	}
}
?>