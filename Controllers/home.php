<?php
// Page Controller for the Index Page

class HomeController extends PageController {
	public $pageTemplate = "Home";
	
	public function process($get, $post) {
		$this->pageData["Title"] = "Home";

		// Generate all of the courses (for testing)
		$allCourses = array();
		$query = new Query('courses');
		$result = $query->select('*', '', '', '', false);
		while($row=mysqli_fetch_array($result)){
			$course = new Course();
			$course->findById($row['id']);
			array_push($allCourses, array('id' => $course->get('id'),
										  'name' => ucwords(strtolower($course->get('name'))),
										  'department_id' => $course->get('department_id'),
										  'number' => $course->get('number')));
		}
		$this->pageData['allCourses'] = $allCourses;

		// Get all of the courses in this user's session
		

	}
}