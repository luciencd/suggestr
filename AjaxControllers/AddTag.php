<?php
//Working on tags.
class AddTagController extends AjaxController {
	public function process($get,$post) {
		
		//Checking the inputs //Currently sessions don't work.
		if(!isset($post['tag_id'])||!is_numeric($post['tag_id'])||
			!isset($post['course_id'])||!is_numeric($post['course_id'])||
		   !isset($_COOKIE['sessionId'])||!is_numeric($_COOKIE['sessionId'])){
			$this->failureReason = 'Sorry, there was an error.';
			return false;
		}

		//Check if the vote is positive or negative(future version)

		//Checking if particular session has already pressed this tag button.
		$query = new Query('tagaction');
		$result = $query->select('*', array(array('session_id', '=', $_COOKIE['sessionId']), 
											array('tag_id', '=', $post['tag_id']),
											array('course_id', '=', $post['course_id'])), '', 2,true);

		if(count($result)!=0){
			// Abort because this tag has already been voted on by this session on this course.
			$this->failureReason = 'you already uptoked.';
			$result[0]->delete();
			return false;
		}
		

		//Adding TagAction to the table
		$action = new TagAction();
		$action->set('tag_id', $post['tag_id']);
		$action->set('tag_name', $post['tag_name']);
		$action->set('course_id', $post['course_id']);
		$action->set('session_id', $_COOKIE['sessionId']);
		$action->save();

		$query = new Query('tagaction');
		$result = $query->select('*', array(array('tag_id', '=', $post['tag_id']),
											array('course_id', '=', $post['course_id'])), '', '');
		
		
		
		return true;
	}
}

?>