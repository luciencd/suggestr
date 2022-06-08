<?php

class TookCourseController extends AjaxController {
	public function process($get,$post) {
		if(parent::process($get,$post) == false){
			return false;
		}
		if(!isset($post['course_id'])||!is_numeric($post['course_id'])){
			$this->failureReason = 'Sorry, there was an error.';
			return false;
		}

		//No duplicate Took Courses
		$query = new Query('action');
		$result = $query->select('*', array(array('course_id', '=', $post['course_id']), 
											array('session_id', '=', $this->session_id)), '', 1);
		if(count($result)!=0){
			// Abort because this course is already in the user's model
			$this->failureReason = 'Sorry, there was an error';
			return false;
		}

		// Took course to DB
		$action = new Action();
		$action->set('course_id', $post['course_id']);
		$action->set('session_id', $this->session_id);
		$action->set('choice', 1);
		$action->save();
		return true;
	}
}

?>