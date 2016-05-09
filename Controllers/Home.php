<?php
ini_set('display_errors', 1);
// Page Controller for the Index Page

//$Data->load();


//error_reporting(0);
class HomeController extends PageController { 
//PageModelController supposed to extend.
	
	public $pageTemplate = "Home";

	public function process($get, $post) {
		//$_COOKIE['sessionId'] = 1;
		//$this->load();
		$Data = $GLOBALS['MODEL']['Data'];
		$Data->load();
		
		//$Data->getStudent($_COOKIE['sessionId'])->getMajor();
		//$this->pageData['year_id'] = $Data->getStudent($_COOKIE['sessionId'])->getMajor();
		//$Data->loadAllClasses();
		//$Data->loadAllStudents();
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
		

		/**/





		//FINAL INFO// GET COURSES
	
		
		$student = $Data->getStudent($_COOKIE['sessionId']);
		$studentCourses = $student->getTaken();

		/*GETTING WEIGHT SLIDERS*/
		/*GETTING WEIGHT SLIDERS*/
		/*GETTING WEIGHT SLIDERS*/
		$query_weights = "SELECT * FROM sliders WHERE type = 'preference'";
		$weights = mysqli_query($GLOBALS['CONFIG']['mysqli'], $query_weights); 

		$WEIGHTSARRAY = array();
		foreach($weights as $row){
			array_push($WEIGHTSARRAY,array('type' => $row['type'],'id' =>$row['id'],'name' =>$row['name']));
			
		}



		$query_easiness = "SELECT * FROM weights WHERE session_id = ".$_COOKIE['sessionId'];
		$result = mysqli_query($GLOBALS['CONFIG']['mysqli'], $query_easiness); 
		//echo $query_easiness, Count($result);
		
		$slider_easiness = .5;
		$slider_relevance = .5;
		$slider_quality = .5;
		
		foreach($result as $row){
			if($row['slider_id'] == 1){
				$slider_easiness = $row['vote'];
			}else if($row['slider_id'] == 2){
				$slider_relevance = $row['vote'];
			}else if($row['slider_id'] == 3){
				$slider_quality = $row['vote'];
			}
		}
		$WEIGHTSARRAY[0]['percentage'] = 100*$slider_easiness;
		$WEIGHTSARRAY[1]['percentage'] = 100*$slider_relevance;
		$WEIGHTSARRAY[2]['percentage'] = 100*$slider_quality;

		//type
		//id
		//percentage
		//name

		$this->pageData['ArrayWeights'] = $WEIGHTSARRAY;
		/*GETTING WEIGHT SLIDERS*/
		/*GETTING WEIGHT SLIDERS*/
		/*GETTING WEIGHT SLIDERS*/








		////////// LEFT SIDE /////////////////////
		////////// FASTER TO GENERATE!!!//////////

		///BOTTOM HERE SHOULD BE GETTING THINGS FROM MODEL not DIRECTLY FROM DB!
		// Select all of the courses that this user HAS TAKEN IN THE PAST
		// Get all of the courses in this user's session
		$idsAlreadyAdded = $student->getTaken();
		$usersCourses = array();
		foreach($idsAlreadyAdded as $courseId){
			$Data->requirement($courseId);
			try{
				$cArray = $Data->getReturnArray($courseId,'course');
				array_push($usersCourses, array('id' => $cArray['id'],
											  'code' => $cArray['department_code'],
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
											  //'code' => $cArray['department_code'],
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

		

		//Somehow this does not work well, So I just put it into the 
		$this->pageData['percentage2'] = 100.0*$Data->semesterDifficulty($student->getAdded());
		

		$allNewCourses = array();

		
		//Populate major name
		$major = new Department();
		//echo $student->getMajor();
		try{
			$major->findById($student->getMajor());
			$this->pageData['major'] = $major->get('name');
			
		}catch(Exception $e){
			$this->pageData['major'] = 'Uncommitted';
		}
		//$major->findById($student->getMajor());

		


		if(true){//If we want to load page faster.// Perhaps include a "fake" class to teach how to use program.
			$this->pageData['numResults'] = "See";
			$this->pageData['description'] = "Press Suggest Button to get course Suggestions!";
			//$this->pageData['allCourses'] = $allNewCourses;
		}else {
			if(count($studentCoursesTaken) == 0){
			$this->pageData['numResults'] = (String)count($allNewCourses);
			$this->pageData['description'] = "Here are popular courses to get started!";
			$this->pageData['allCourses'] = $allNewCourses;
			}else{
				$this->pageData['numResults'] = (String)count($allNewCourses);
				$this->pageData['description'] = "Here are suggestions based on your course history!";
				$this->pageData['allCourses'] = $allNewCourses;
			}
		}
		$GLOBALS['MODEL']['Data'] = $Data;
	}
}

?>