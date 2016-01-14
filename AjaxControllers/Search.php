<?php
require_once('Controllers/suggestions.php');
class SearchController extends AjaxController {
	public $template = "Search";
	public function process($get,$post) {
		//Remove this once we solved sessionId
		
		$Data = new Database();//not sure if this follows mvc protocol.
		//Removing 
		//$student = $Data->getStudent($_COOKIE['sessionId']);
		

		// Select all of the courses that this user is already added
		$query = new Query('action');
		$result = $query->select('*', array(array('session_id', '=', $_COOKIE['sessionId'])));
		$idsAlreadyAdded = array();
		foreach($result as $action){
			array_push($idsAlreadyAdded, $action->get('course_id'));
		}
		// Select all the courses where the query string is a sub-string of the name or id
		$query = new Query('courses');
		$result = $query->select('*', array(array('name', 'LIKE', '%'.$post['q'].'%'), 'OR', array('number', 'LIKE', '%'.$post['q'].'%')), array('number', 'ASC'), 30);
		$courses = array();
		foreach($result as $course){
			if(!in_array($course->get('id'), $idsAlreadyAdded)){ // Check that this course has not been added by the user yet
				array_push($courses, array('id' => $course->get('id'),
											  'name' => ucwords(strtolower($course->get('name'))),
											  'department_id' => $course->get('department_id'),
											  'number' => $course->get('number'),
											  'description' => ((strlen($course->get('description'))==0)?'No description':$course->get('description')),
											  'allTags' => array(array()),/*$Data->courseTags($course->get('id'))*/ //Should contain 5 tags.
											  'ratings' => $Data->rating($course->get('id'))
											));
			}//will need to add tags. Make a new function taking in an array and returning the classes as an array in this form.
		}
		$this->pageData['numResults'] = (String)count($courses);
		$this->pageData['term'] = $post['q'];
		$this->pageData['description'] = "Here are courses corresponding to search term '".$post['q']."'";
		$this->pageData['allCourses'] = $courses;
		return true;
	}
}

?>