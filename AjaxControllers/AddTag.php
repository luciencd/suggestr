<?php
//Working on tags.
class AddTagController extends AjaxController {
	public function process($get,$post) {
		/*if(!isset($post['tag_id'])||!is_numeric($post['tag_id'])||
			!isset($post['course_id'])||!is_numeric($post['course_id'])||
		   !isset($_COOKIE['sessionId'])||!is_numeric($_COOKIE['sessionId'])){
			$this->failureReason = 'Sorry, there was an error.';
			return false;
		}*/
		//$this->failureReason = 'Sorry, there was an error.';
		//$query = new Query('tagaction');
		//Check if already added this course to tag action.
		//echo "<h4> will add TAG </h4>"
		// Now add this course to the user's model
		
		$action = new TagAction();
		$action->set('tag_id', $post['tag_id']);
		$action->set('tag_name', $post['tag_name']);
		$action->set('course_id', $post['course_id']);
		$action->set('session_id', "1");
		$action->save();

		return true;
	}
}

?>