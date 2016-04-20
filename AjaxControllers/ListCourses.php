<?php

/**
 * NEED advice on how to make this MVC compatible. Currently it doesn't seem to be so.
 * Given that I require the model(suggestions.php), yet I have to reload all the data from initial refresh.
 */
//require_once('Controllers/suggestions.php');
require_once('Controllers/suggestions.php');


/*
All this does is take in a list and print out the classes that it needs.
*/

class ListCoursesController extends AjaxController {
	//public $template = "tdidf";//Identical to search one.
	public $template = "Suggest";
	public function process($get,$post) {

		//header('Content-Type: application/json');
		//echo json_encode(array('foo' => 'bar'));
		$allCourses = $post['courses'];
		

		$Data = new Database();
		$Data->load();

	
		//$allcourses only contains the course id's
		$student = $Data->getStudent($_COOKIE['sessionId']);


		$advisoryArray = array();

		$query = new Query('sliders');
		// Select all the courses that are in this user's session and have this course id
		$result = $query->select('*', array(array('type', '=', "advised")), '');
		foreach($result as $advisory){
			array_push($advisoryArray,array('slider_id' => $advisory->get('id'),
			                                'slider_name' => $advisory->get('name'),
			                                'slider_type' => $advisory->get('type')
					  						)
			);
		}
		//Create new array containing all the course details based on what is in Allcourses, 
		//in a format that will be taken in by the view.
		$allNewCourses = array();
		
		foreach($allCourses as $id){
			try{
				$course = $Data->getReturnArray($id,'course');
				array_push($allNewCourses, array('id' => $course['id'],
											  'name' => $course['name'],
											  'department_id' => $course['department_id'],
											  'number' => $course['number'],
											  'description' => $course['description'],//((strlen($course['description']==0)?'No description':$course['description'])),
											  'allTags' => array(array()),//$Data->courseTags($course->get('id')),//Should contain 5 tags.
											  'ratings' => $Data->rating($course['id']),
											  'stars' => $Data->requirement($course['id']),
											  'stack' => $advisoryArray
											  )
							);
			}catch(Exception $e){}
		}
		
		//Populate webpage with all the different courses that were predicted.
		if(!$Data->testConnection('courses')){
			$this->pageData['numResults'] = 0;
			$this->pageData['description'] = "No suggestions, Database Failed!";
			$this->pageData['allCourses'] = $allNewCourses;
			return true;
		}
		
		$this->pageData['numResults'] = (String)count($allCourses);
		$this->pageData['description'] = "Here are the ranked courses!";
		$this->pageData['allCourses'] = $allNewCourses;
		
		return true;
	}
}

?>