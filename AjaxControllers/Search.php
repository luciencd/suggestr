<?php
require_once('Controllers/suggestions.php');
class SearchController extends AjaxController {
	public $template = "Search";
	public function process($get,$post) {
		//Remove this once we solved sessionId
		
		$Data = $GLOBALS['MODEL']['Data'];

		$Data->load();
		//Again we have to load in the data so that the ratings are correct. 


		$student = $Data->getStudent($_COOKIE['sessionId']);
		//not sure if this follows mvc protocol.
		//Removing 
		//$student = $Data->getStudent($_COOKIE['sessionId']);
		$idsAlreadyAdded = array_merge($student->getTaken(),$student->getAdded());


		// Select all of the courses that this user is already added
		$advisoryArray = array();

		$query = new Query('sliders');
		// Select all the courses that are in this user's session and have this course id
		$result = $query->select('*', array(array('type', '=', "advised")), '');
		
		///echo count($result);
		foreach($result as $advisory){
			//echo $advisory->get('slider_id');
			//echo "<-<";
			array_push($advisoryArray,array('slider_id' => $advisory->get('id'),
			                                'slider_name' => $advisory->get('name'),
			                                'slider_type' => $advisory->get('type')
					  						)
			);
		}
		// Select all the courses where the query string is a sub-string of the name or id
		$query = new Query('courses');
		$result = $query->select('*', array(array('name', 'LIKE', '%'.$post['q'].'%'), 'OR', array('number', 'LIKE', '%'.$post['q'].'%')), array('number', 'ASC'), 30);
		$courses = array();


		
		
		foreach($result as $course){

			$c = $Data->getReturnArray($course->get('id'),'course');
			if(!in_array($c['id'], $idsAlreadyAdded)){ // Check that this course has not been added by the user yet
				array_push($courses, array('id' => $c['id'],
											  'name' => ucwords(strtolower($c['name'])),
											  'department_id' => $c['department_id'],
											  'number' => $c['number'],
											  'description' => $c['description'],
											  'allTags' => array(array()),//$Data->courseTags($course->get('id')) //Should contain 5 tags.
											  'ratings' => $Data->rating($c['id']),
											  'stars' => $Data->requirement($c['id']),
											  'stack' => $advisoryArray
											)
							);
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