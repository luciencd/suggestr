<?php
//Working on tags.
class AddWeightsController extends AjaxController {
	public function process($get,$post) {
		//echo "sliderid:".$post['slider_id']." courseid:".$post['course_id']."slidertype:".$post['slider_type'];
		//Checking the inputs //Currently sessions don't work.
		if(!isset($_COOKIE['sessionId'])||!is_numeric($_COOKIE['sessionId'])){
			$this->failureReason = 'Sorry, there was an error.';
			return false;
		}

		$query = new Query('weights');
		$result = $query->select('*', array(array('session_id', '=', $_COOKIE['sessionId']), 
											array('slider_id', '=', $post['slider_id']),
											array('slider_type', '=', $post['slider_type'])), '', 2,true);

		#echo count($result);
		if(count($result)!=0){
			// Abort because this tag has already been voted on by this session on this course.

			$this->failureReason = 'you already gave a rating nerd.';
			$result[0]->delete();
			
		}
		$action = new Weights();
		
		
		$action->set('session_id', $_COOKIE['sessionId']);
		$action->set('slider_id', $post['slider_id']);
		$action->set('slider_type', $post['slider_type']);
		$action->set('vote', $post['vote']);
		$action->save();

		
		return true;
	}
}

?>