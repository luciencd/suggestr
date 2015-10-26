<?php

class SearchController extends AjaxController {
	public $template = "Search";
	public function process($get,$post) {
		$query = new Query('courses');
		// Select all the courses where the query string is a sub-string of the name or id
		$result = $query->select('*', array(array('name', 'LIKE', '%'.$post['q'].'%'), 'OR', array('id', 'LIKE', '%'.$post['q'].'%')), '', 100);
		$courses = array();
		foreach($result as $course){
			array_push($courses, array('id' => $course->get('id'),
										  'name' => ucwords(strtolower($course->get('name'))),
										  'department_id' => $course->get('department_id'),
										  'number' => $course->get('number')));
		}
		$this->pageData['courseResults'] = $courses;
		return true;
	}
}

?>