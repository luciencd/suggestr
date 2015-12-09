<?php
// Page Controller for the Index Page
require_once(ROOT.'Controllers/suggestions.php');

class HomeController extends PageController {
	public $pageTemplate = "Home";
	public function predictClasses(){//Take in session_id

		$command = 'python schedule.py';// . json_encode($classes)
		$result = json_decode(shell_exec('python "schedule.py"'),true);
		return $result;
	}
	public function process($get, $post) {
		$Data = new Database();
		//echo "<h4> getStudent:".$Data->getStudent(536)."</h4>";
		echo "<h4> studentExists:".$Data->studentExists(536)."</h4>";
		echo "<h4> numStudents:".$Data->numStudents()."</h4>";
		//echo "<h4> array".$Data->getStudentsTakenCourses(536)."</h4>";
		var_dump($Data->getStudentsTakenCourses(536));
		$this->pageData["Title"] = "Home";

		// Select all of the courses that this user is already added or ignored
		$query = new Query('action');
		$result = $query->select('*', array(array('session_id', '=', $_COOKIE['sessionId'])));
		$idsAlreadyAdded = array();
		foreach($result as $action){
			array_push($idsAlreadyAdded, $action->get('course_id'));
		}

		// Generate all of the courses (for testing)

		//Get list of predicted courses from Python.
		$predClasses = $this->predictClasses();

		$allCourses = array();
		$query = new Query('courses');

		//Adding predicted courses to the $allCourses array.
		foreach($predClasses as $class){
			$result = new Course();
			$result->findById($class['id']);
			array_push($allCourses,$result);
		}
		//$allcourses only contains the course id's

		
		//Create new array containing all the course details based on what is in Allcourses.
		$allNewCourses = array();
		foreach($allCourses as $course){
			try{
				if(!in_array($course->get('id'), $idsAlreadyAdded)){ // Check that this course has not been added by the user yet
					array_push($allNewCourses, array('id' => $course->get('id'),
												  'name' => ucwords(strtolower($course->get('name'))),
												  'department_id' => $course->get('department_id'),
												  'number' => $course->get('number'),
												  'description' => ((strlen($course->get('description'))==0)?'No description':$course->get('description'))));
				}
			}catch(Exception $e){}
		}

		//Populate webpage with all the different courses that were predicted.
		$this->pageData['allCourses'] = $allNewCourses;
		

		//////////
		// Select all of the courses that this user is already added
		$query = new Query('action');
		$result = $query->select('*', array(array('session_id', '=', $_COOKIE['sessionId']),
											array('choice', '=', 0)));
		$idsAlreadyAdded = array();
		foreach($result as $action){
			array_push($idsAlreadyAdded, $action->get('course_id'));
		}
		
		// Get all of the courses in this user's session
		$usersCourses = array();
		foreach($idsAlreadyAdded as $courseId){
			try{
				$course = new Course();
				$course->findById($courseId);
				array_push($usersCourses, array('id' => $course->get('id'),
											  'name' => ucwords(strtolower($course->get('name'))),
											  'department_id' => $course->get('department_id'),
											  'number' => $course->get('number')));
			}catch(Exception $e){}
		}

		$this->pageData['usersCourses'] = $usersCourses;
		//Pushes all the new courses to the view.
		
	}
}

?>