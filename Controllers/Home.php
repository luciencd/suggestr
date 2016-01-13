<?php
ini_set('display_errors', 1);
// Page Controller for the Index Page
require_once('Controllers/suggestions.php');
//error_reporting(0);
class HomeController extends PageController {
	public $pageTemplate = "Home";

	public function process($get, $post) {
		//$_COOKIE['sessionId'] = 1;
		$this->pageData["Title"] = "Home";
		$this->pageData["session"] = $_COOKIE['sessionId'];
		//Generate the data from mysql.

		//NEED TO FIGURE OUT WHY COOKIE IS NOT WORKING. 

	

		
		$Data = new Database();
		
		$student = $Data->getStudent($_COOKIE['sessionId']);
		$studentCourses = $student->getTaken();

		//echo " Session: ".$student->getId()." major: ".$student->getMajor()." year: ".$student->getYear();
		
		foreach($studentCourses as $course){
	        $result = new Course();
	        $result->findById($course);
	        //echo "<h4>".$result->get('name')."<h4>";
		}

		//Here's the array of courses generated by the Jaccard index.
		//Don't suggest squat if no courses are in history.
		$JaccardCourses = array();
		//if(Count($studentCourses) != 0){
		$JaccardCourses = $Data->getSuggestedCourses($studentCourses);
		//}
			
		



		// Select all of the courses that this user is already added or ignored
		$query = new Query('action');
		$result = $query->select('*', array(array('session_id', '=', $_COOKIE['sessionId'])));
		$idsAlreadyAdded = array();
		foreach($result as $action){
			array_push($idsAlreadyAdded, $action->get('course_id'));
		}

		// Generate all of the courses (for testing)

		$allCourses = array();
		$query = new Query('courses');



		//Grabs the Id's from the Jaccard Array and 
		foreach($JaccardCourses as $class => $score){
			$result = new Course();
			$result->findById($class);
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
												  'description' => ((strlen($course->get('description'))==0)?'No description':$course->get('description')),
												  'allTags' => array(array()),/*$Data->courseTags($course->get('id'))*/
												  'ratings' =>$Data->rating($course->get('id'))///Should contain 3 ratings.
												  ));

				}//var_dump($Data->courseTags($course->get('id')));
			}catch(Exception $e){}
		}

		//Populate webpage with all the different courses that were predicted.
		if(count($studentCourses) == 0){
			$this->pageData['numResults'] = (String)count($allNewCourses);
			$this->pageData['description'] = "Here are popular courses to get started!";
			$this->pageData['allCourses'] = $allNewCourses;
		}else{
			$this->pageData['numResults'] = (String)count($allNewCourses);
			$this->pageData['description'] = "Here are suggestions based on your course history!";
			$this->pageData['allCourses'] = $allNewCourses;
		}
		
		
		
		
		/*
		IMPORTANT NODE


		*/
		//////////
		
		// Select all of the courses that this user HAS TAKEN IN THE PAST
		$query = new Query('action');
		$result = $query->select('*', array(array('session_id', '=', $_COOKIE['sessionId']),
											array('choice', '=', 1)));//1 means taken
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
		//Pushes all the new courses to the HAS TAKEN part of the view.
		
		
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

		$this->pageData['futureUsersCourses'] = $usersCourses;
		//Pushes all the new courses to the ADDING LIST view.
		
	}
}

?>