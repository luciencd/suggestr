<?php
ini_set('display_errors', 1);
// Page Controller for the Index Page

//error_reporting(0);
class HomeController extends PageController {
	public $pageTemplate = "Home";

	public function process($get, $post) {
		//$_COOKIE['sessionId'] = 1;

		$Data = $GLOBALS['MODEL']['Data'];
		//$Data->load();
		$Data->loadAllClasses();
		$Data->loadAllStudents();
		//$Data = $GLOBALS['MODEL']['Data'];


		if(!isset($_COOKIE['sessionId'])){return;}
		$this->pageData["Title"] = "Home";

		$this->pageData["session"] = $_COOKIE['sessionId'];


		//Need to reset cookie if does not appear in database.
		try{
			if(isset($_COOKIE['sessionId'])){
				$Session = new Session();
				$Session->findById($_COOKIE['sessionId']);

				$major_id = $Session->get('major_id');
				$year_id = $Session->get('year_id');
				if($major_id != 0){
					//This don't actually matter doe
					$Department = new Department();
					$Department->findById($major_id);
					$major_name = $Department->get('name');
					$this->pageData['major_name'] = $major_name;
					//$this->pageData["major_icon"] = "<icon showing success!>"
				}else{
					//$this->pageData["major_icon"] = "<icon showing fail>"
				}
				//ADDING YEAR HERE:
				$this->pageData['year_id'] = $year_id;
			}
		} catch (Exception $e){
			$this->pageData["Title"] = "Error reset cache";
		}
		
	
		//FINAL INFO// GET COURSES
	
		
		$student = $Data->getStudent($_COOKIE['sessionId']);
		$studentCourses = $student->getTaken();


		////////// LEFT SIDE /////////////////////
		////////// FASTER TO GENERATE!!!//////////

		///BOTTOM HERE SHOULD BE GETTING THINGS FROM MODEL not DIRECTLY FROM DB!
		// Select all of the courses that this user HAS TAKEN IN THE PAST
		// Get all of the courses in this user's session
		$idsAlreadyAdded = $student->getTaken();
		$usersCourses = array();
		foreach($idsAlreadyAdded as $courseId){
			try{
				$cArray = $Data->getReturnArray($courseId,'course');
				array_push($usersCourses, array('id' => $cArray['id'],
											  'name' => $cArray['name'],
											  'department_id' => $cArray['department_id'],
											  'number' => $cArray['number']));
			}catch(Exception $e){}
		}
		$this->pageData['usersCourses'] = $usersCourses;
		//Pushes all the new courses to the HAS TAKEN part of the view.
		
		
		// Select all of the courses that this user is already added
		// Get all of the courses in this user's session
		$idsAlreadyAdded = $student->getAdded();
		$usersCourses = array();
		foreach($idsAlreadyAdded as $courseId){
			try{
				$cArray = $Data->getReturnArray($courseId,'course');
				array_push($usersCourses, array('id' => $cArray['id'],
											  'name' => $cArray['name'],
											  'department_id' => $cArray['department_id'],
											  'number' => $cArray['number']));
			}catch(Exception $e){}
		}
		$this->pageData['futureUsersCourses'] = $usersCourses;
		//Pushes all the new courses to the ADDING LIST view.


		////////////////////////////////////////////
		//////////////ADDING END SLIDERS////////////
		////////////////////////////////////////////

		
		$this->pageData['percentage2'] = 100*$Data->semesterDifficulty($idsAlreadyAdded);
		

		////////////////////////////////////////////
		//////////ADDING COURSE SUGGESTIONS/////////
		////////////////////////////////////////////

		//No predictions at first.

		/*
		foreach($studentCourses as $course){
	        $result = new Course();
	        $result->findById($course);
	        //echo "<h4>".$result->get('name')."<h4>";
		}

		//Here's the array of courses generated by the Jaccard index.
		//Don't suggest squat if no courses are in history.
		$JaccardCourses = array();
		//if(Count($studentCourses) != 0){
		$JaccardCourses = $Data->getSuggestedCourses($student,$studentCourses);
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
		*/
		
		//Create new array containing all the course details based on what is in Allcourses.
		$allNewCourses = array();

		/*

		//Perhaps add course that serves as an example to teach. 
		//Would have a bool var in html to say it was not legit.
		foreach($allCourses as $course){
			try{
				if(!in_array($course->get('id'), $idsAlreadyAdded)){ // Check that this course has not been added by the user yet
					array_push($allNewCourses, array('id' => $course->get('id'),
												  'name' => ucwords(strtolower($course->get('name'))),
												  'department_id' => $course->get('department_id'),
												  'number' => $course->get('number'),
												  'description' => ((strlen($course->get('description'))==0)?'No description':$course->get('description')),
												  'allTags' => array(array()),//$Data->courseTags($course->get('id'))
												  'ratings' =>$Data->rating($course->get('id'))///Should contain 3 ratings.
												  ));

				}//var_dump($Data->courseTags($course->get('id')));
			}catch(Exception $e){}
		}
		*/
		//Populate webpage with all the different courses that were predicted.
		
		if(true){//If we want to load page faster.// Perhaps include a "fake" class to teach how to use program.
			$this->pageData['numResults'] = "See";
			$this->pageData['description'] = "Press Suggest Button to get course Suggestions!";
			//$this->pageData['allCourses'] = $allNewCourses;
		}else {
			if(count($studentCourses) == 0){
			$this->pageData['numResults'] = (String)count($allNewCourses);
			$this->pageData['description'] = "Here are popular courses to get started!";
			$this->pageData['allCourses'] = $allNewCourses;
			}else{
				$this->pageData['numResults'] = (String)count($allNewCourses);
				$this->pageData['description'] = "Here are suggestions based on your course history!";
				$this->pageData['allCourses'] = $allNewCourses;
			}
		}		
	}
}

?>