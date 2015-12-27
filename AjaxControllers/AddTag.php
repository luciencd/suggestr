<?php
//Working on tags.
class AddTag extends AjaxController {
	public function process($get,$post) {
		/*if(!isset($post['tag_id'])||!is_numeric($post['tag_id'])||
			!isset($post['course_id'])||!is_numeric($post['course_id'])||
		   !isset($_COOKIE['sessionId'])||!is_numeric($_COOKIE['sessionId'])){
			$this->failureReason = 'Sorry, there was an error.';
			return false;
		}*/
		
		//$query = new Query('tagaction');
		//Check if already added this course to tag action.

		// Now add this course to the user's model
		$action = new TagAction();
		$action->set('id',3);
		$action->set('tag_id', $post['tag_id']);
		$action->set('tag_name', $post['tag_name']);
		$action->set('course_id', $post['course_id']);
		$action->set('session_id', 1);
		$action->save();

		return "asshole";
	}
}

?>