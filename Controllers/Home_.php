<?php
// Page Controller for the Index Page

class HomeController extends PageController {
	public $pageTemplate = "Home";
	
	public function process($get, $post) {
		$this->pageData["Title"] = "Home";

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
		$result = $query->select('*', '', array('number', 'ASC'), 20, false);
		while($row=mysqli_fetch_array($result)){
			try{
				$course = new Course();
				$course->findById($row['id']);
				if(!in_array($course->get('id'), $idsAlreadyAdded)){ // Check that this course has not been added by the user yet
					array_push($allCourses, array('id' => $course->get('id'),
												  'name' => ucwords(strtolower($course->get('name'))),
												  'department_id' => $course->get('department_id'),
												  'number' => $course->get('number'),
												  'description' => ((strlen($course->get('description'))==0)?'No description':$course->get('description'))));
				}
			}catch(Exception $e){}
		}
		$this->pageData['allCourses'] = $allCourses;

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

	}
}

?>