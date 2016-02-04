<?php

class AddCourseController extends AjaxController {

	public function process($get,$post) {
		


		if(!isset($post['course_id'])||!is_numeric($post['course_id'])||
		   !isset($_COOKIE['sessionId'])||!is_numeric($_COOKIE['sessionId'])){
			$this->failureReason = 'Sorry, there was an error. 2';
			return false;
		}

		$session = new Session();
		$session->findById($_COOKIE['sessionId']);
		$major_id = $session->get('department_id');

		$query = new Query('action');
		// Select all the courses that are in this user's session and have this course id
		$result = $query->select('*', array(array('course_id', '=', $post['course_id']), 
											array('session_id', '=', $_COOKIE['sessionId'])), '', 0);
		if(count($result)!=0){
			// Abort because this course is already in the user's model
			$this->failureReason = 'Sorry, there was an error';
			return false;
		}
		// Now add this course to the user's model
		$action = new Action();
		$action->set('course_id', $post['course_id']);
		$action->set('session_id', $_COOKIE['sessionId']);
		$action->set('major_id', $major_id);
		$action->set('choice', 0);
		$action->save();

		//$Data = $GLOBALS['MODEL']['Data'];
		//$Data->getStudent($_COOKIE['sessionId'])->addCourse($post['course_id'],0);

		return true;
		/*
		$Data = $GLOBALS['MODEL']['Data'];
		//return 33;
		$Data->getStudent($_COOKIE['sessionId'])->addCourse($post['course_id'],0);
		
		//Updating difficulty slider. maybe put in separate function?
		$query = new Query('action');
		$result = $query->select('*', array(array('session_id', '=', $_COOKIE['sessionId']),
											array('choice', '=', 0)));

		$idsAlreadyAdded = array();
		foreach($result as $action){
			array_push($idsAlreadyAdded, $action->get('course_id'));
		}
		header('Content-Type: application/json');
		echo json_encode(array('difficulty' => 100*$Data->semesterDifficulty($idsAlreadyAdded)));
		//echo 100*$Data->semesterDifficulty($idsAlreadyAdded);*/

		
	}
}

?>