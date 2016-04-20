<?php

class AdvisoryQueryController extends AjaxController {
	public $template = "AdvisoryQuery";//Identical to search one.
	public function process($get,$post) {

		$course_id = $post['course_id'];
		
		//Global variables are clearly bad, but I don't know enough about the base AjaxController to extend it with 
		//a reference to the model or something.
		$Data = new Database();
		

		$Data->load();
		$student = $Data->getStudent($_COOKIE['sessionId']);
		$myMajor = $student->getMajor();
		$sqlDump = array();

		$key = $Data->RatingsList[$course_id];
            
            //Get the total amount of ratins for this particular course
        foreach($key as $k => &$target){
        	if($k == 4 || $k == 5 || $k == 6){
        		$weightedVoteSum = 0.0;
	            $weightSum = 0.0;
	            $totalVotes = 0;
	            foreach($target as $major => &$values){
	            	//echo "major:(".$major.") ";
	                $totalCourseTypeRatings = $key['advised'][$major];
	                //echo $Data->getClassNameById($course_id)." ".$course_id."Major(".$major.") -> totalcount(".$totalCourseTypeRatings.')<br>';
	                

	                //$values contains the major similarity, (coefficient)
	                //The amount of votes cast by these students in this major
	                //The total sum of those votes. 
	                // 1 + 1 + 1  =  $values[1] = $numVotes = 3
	                // 1 + 1 + 1 =  $values[2] = $sumVotes = 3 
	                // 3 + 1 + 1  = $key['advisory']['major'] = 5
	                // 3/5 = $actualVote
	                // average rating for this major should be $avgVotes = 3/1.5 = 0.5
	                // the weight of this rating will be $values[4] = $weight = .40
	                $weight = $values[0];
	                $sumVotes = $values[1];
	                $numVotes = $values[2];
	                //echo $sumVotes."/".$totalCourseTypeRatings;
	                //$actualVote = ((float)$sumVotes)/((float)$totalCourseTypeRatings);
	                //echo "vote: ".$k." weight: ".$weight." vote major:(".$major.") my major (".$myMajor.")<br>";
	                //echo 'advised: '.$k.' '.$actualVote.'<br>';
	                $totalVotes += $sumVotes;

	                $weightedVoteSum += $weight*($sumVotes);// .44(3/5)
	                $weightSum += $weight;// .44
	            }

	            if($weightSum == 0){
	                $target[0] = 0;
	            }else{
	                $target[0] = ((float)$weightedVoteSum)/((float)$totalVotes);
	            }

	            array_push($sqlDump,array('advisoryname' => $k, 'advisoryvotes' => $totalVotes,'advisoryscore' => $weightedVoteSum));
	            //echo $target[0].'<br>';
	            //$target[0] = 1;
	        }
        }
        $this->pageData['name'] = $Data->getClassNameById($course_id);
        $this->pageData['SQL'] = $sqlDump;
        return true;


	}
}