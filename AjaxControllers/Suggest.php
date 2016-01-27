<?php

/**
 * NEED advice on how to make this MVC compatible. Currently it doesn't seem to be so.
 * Given that I require the model(suggestions.php), yet I have to reload all the data from initial refresh.
 */
require_once('Controllers/suggestions.php');

class SuggestController extends AjaxController {
	public $template = "Suggest";//Identical to search one.
	public function process($get,$post) {
		//Remove this once we solved sessionId
		
		$Data = new Database();//Find a way to make this local to suggestr.php or something.
		
		$student = $Data->getStudent($_COOKIE['sessionId']);
		$studentCourses = $student->getTaken();


		foreach($studentCourses as $course){
	        $result = new Course();
	        $result->findById($course);
		}

		//Here's the array of courses generated by the Jaccard index.
		//When we press suggest, it is different as it is explicitly stated we want a suggestion.
		$JaccardCourses = array();
		$JaccardCourses = $Data->getSuggestedCourses($studentCourses);
		
		

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
												  'allTags' => array(array()),/*$Data->courseTags($course->get('id')),*///Should contain 5 tags.
												  'ratings' => $Data->rating($course->get('id'))
												  ));

				}//var_dump($Data->courseTags($course->get('id')));
			}catch(Exception $e){}
		}

		//Populate webpage with all the different courses that were predicted.
		if(count($studentCourses) === 0){
			$this->pageData['numResults'] = (String)count($allNewCourses);
			$this->pageData['description'] = "Here are popular courses to get started!";
			$this->pageData['allCourses'] = $allNewCourses;
		}else{
			$this->pageData['numResults'] = (String)count($allNewCourses);
			$this->pageData['description'] = "Here are suggestions based on your course history!";
			$this->pageData['allCourses'] = $allNewCourses;
		}
		return true;
	}
}

?>