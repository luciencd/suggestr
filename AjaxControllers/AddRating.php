<?php
//Working on tags.
class AddRatingController extends AjaxController {
	public function process($get,$post) {
		
		//Checking the inputs //Currently sessions don't work.
		if(!isset($post['slider_id'])||!is_numeric($post['slider_id'])||
			!isset($post['course_id'])||!is_numeric($post['course_id'])||
		   !isset($_COOKIE['sessionId'])||!is_numeric($_COOKIE['sessionId'])){
			$this->failureReason = 'Sorry, there was an error.';
			return false;
		}

		//Check if the vote is positive or negative(future version)
		
		//Checking if particular session has already pressed this tag button.
		$query = new Query('slideraction');
		$result = $query->select('*', array(array('session_id', '=', $_COOKIE['sessionId']), 
											array('slider_id', '=', $post['slider_id']),
											array('course_id', '=', $post['course_id']),
											array('slider_type', '=', $post['slider_type'])), '', 2,true);

		if(count($result)!=0){
			// Abort because this tag has already been voted on by this session on this course.
			$this->failureReason = 'you already gave a rating nerd.';
			$result[0]->delete();
			
		}
		

		//Adding TagAction to the table

		$action = new SliderAction();
		
		$action->set('slider_id', $post['slider_id']);
		$action->set('slider_type', $post['slider_type']);
		$action->set('course_id', $post['course_id']);
		$action->set('session_id', $_COOKIE['sessionId']);
		$action->set('vote',$post['vote']);
		$action->save();

		
		return true;
	}
}

?>