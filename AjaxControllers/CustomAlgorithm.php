<?php

/**
 * NEED advice on how to make this MVC compatible. Currently it doesn't seem to be so.
 * Given that I require the model(suggestions.php), yet I have to reload all the data from initial refresh.
 */
//require_once('Controllers/suggestions.php');
//require_once('Controllers/suggestions.php');

class CustomAlgorithmController extends AjaxController {
	
	public $template = "Suggest";

	public function parseSQL($filename){

		$lines = file($filename);
		$statement = "";
		// Loop through each line
		foreach ($lines as $line){
			$statement .= $line;
		}
		return $statement;
	}

	public function process($get,$post) {

		//header('Content-Type: application/json');
		//echo json_encode(array('foo' => 'bar'));
		$session_id = $_COOKIE['sessionId']; 
		$sql_file = $post['sql_file'];

		

		/*
		Changing individual variables from sql function to match post requests
		from user choices (of array weights).
		That or what user it is using cookie.
		*/
		//$statement = $oldstatement;
		//Replacing user cookie
		//$this->pageData['description'] = $oldstatement;
		//return true;
		//$session_id = 4996822;
		
		$query_easiness = "SELECT * FROM weights WHERE session_id = ".$session_id;
		$result = mysqli_query($GLOBALS['CONFIG']['mysqli'], $query_easiness); 
		//echo $query_easiness, Count($result);
		
		$slider_easiness = .5;
		$slider_relevance = .5;
		$slider_quality = .5;
		
		foreach($result as $row){
			//echo $row['slider_id'];
			if($row['slider_id'] == 1){
				$slider_easiness = $row['vote'];
			}else if($row['slider_id'] == 2){
				$slider_relevance = $row['vote'];
			}else if($row['slider_id'] == 3){
				$slider_quality = $row['vote'];
			}

		}
		//echo "(",$slider_easiness,$slider_quality,$slider_relevance,")";
		/*
		mysql queries take time to load.
		*/

		$statement2 = $this->parseSQL("model/ranking_scripts/final_collaborative.sql");
		$statement2 = str_replace("e_slider",$slider_easiness,$statement2);
		$statement2 = str_replace("r_slider",$slider_relevance,$statement2);
		$statement2 = str_replace("q_slider",$slider_quality,$statement2);
	    $result2 = mysqli_query($GLOBALS['CONFIG']['mysqli'], $statement2); 

	    //if(Count(mysqli_fetch_array($result2)) == 0 || mysqli_fetch_array($result2)[0]['user_id']!= $session_id){
	    	//echo "DO IT";
    	$statement1 = $this->parseSQL("model/ranking_scripts/collaborative_filter_integrated_corrolation.sql");
		$statement1 = str_replace("user_id",$session_id,$statement1);
		

    	$result1 = mysqli_multi_query($GLOBALS['CONFIG']['mysqli'], $statement1);
	    //}
	    //$this->pageData['description'] = $statement1;
		//return true;
		
        
		//$this->pageData['description'] = Count(mysqli_fetch_array($result));
		//return true;

        /*if(mysqli_fetch_array($result)==null){
            return 1;
        }*/
        
		//echo $result[0];
		$allNewCourses = array();
		//$this->pageData['description'] = Count(mysqli_fetch_array($result));
		//return true;
		$count = 0;
		/*foreach($result as $course){
			$count+=1;
		}*/
		/*
        foreach($result2 as $course){
			try{
				$count +=1;
				//$course = $Data->getReturnArray($id,'course');
				$ratings = array();
				array_push($ratings,array('percentage' => 100*$course['easiness'],
				                                                'slider_id' => 1,
				                                                'slider_name' => 'easiness',
				                                                'slider_type' => 'preference'
				                                                	));

				array_push($ratings,array('percentage' => 100*$course['relevance'],
				                                                'slider_id' => 2,
				                                                'slider_name' => 'relevance',
				                                                'slider_type' => 'preference'
				                                                	));

				array_push($ratings,array('percentage' => 100*$course['quality'],
				                                                'slider_id' => 3,
				                                                'slider_name' => 'quality',
				                                                'slider_type' => 'preference'
				                                                	));
				//setting JSON object array.
				$allNewCourses[$course['course_id']] = array('id' => $course['course_id'],
											  'name' => $course['course_name'],
											  'description' =>$course['description'],
											  'department_code' => $course['department_code'],
											  'number' => $course['level'],
											  'ratings' => $ratings
											  );
							
				
			}catch(Exception $e){}
		}*/
		$displayArray = array();
		$count = 0;
		foreach($result2 as $course){
			try{
				$count +=1;
				//$course = $Data->getReturnArray($id,'course');
				$ratings = array();
				array_push($ratings,array('percentage' => 100*$course['easiness'],
				                                                'slider_id' => 1,
				                                                'slider_name' => 'easiness',
				                                                'slider_type' => 'preference'
				                                                	));

				array_push($ratings,array('percentage' => 100*$course['relevance'],
				                                                'slider_id' => 2,
				                                                'slider_name' => 'relevance',
				                                                'slider_type' => 'preference'
				                                                	));

				array_push($ratings,array('percentage' => 100*$course['quality'],
				                                                'slider_id' => 3,
				                                                'slider_name' => 'quality',
				                                                'slider_type' => 'preference'
				                                                	));
				//setting JSON object array.
				array_push($displayArray, array('id' => $course['course_id'],
											  'name' => $course['course_name'],
											  'description' =>$course['description'],
											  'department_code' => $course['department_code'],
											  'number' => $course['level'],
											  'ratings' => $ratings
											  )
				);
							
				
			}catch(Exception $e){}
		}

		//$allNewCourses = array();
		/* Alphabetize
        foreach($result as $course){
			try{
				//$course = $Data->getReturnArray($id,'course');
				
				array_push($allNewCourses, array('id' => $course['course_id'],
											  'name' => $course['course_name'],
											  'department_id' => $course['department_id'],
											  
											  'number' => $course['number'],
											  'description' => $course['description']//((strlen($course['description']==0)?'No description':$course['description'])),
											  )
							);
				
			}catch(Exception $e){}
		}*/
        //
        
		//Populate webpage with all the different courses that were predicted.
		
		/*$this->pageData['numResults'] = Count($allNewCourses);
		$this->pageData['numResults'] = $count;
		//$count = 2;
		//$this->pageData['numResults'] = $count;
		//$this->pageData['description'] = "Custom Suggestion Algorithm";
		//$this->pageData['description'] = $sql_file;
		
		$this->pageData['description'] = $statement2;*/
		$this->pageData['numResults'] = Count($displayArray);
		$this->pageData['description'] = "Custom Suggestion Algorithm";

		//Return just a JSON array of courses called allCourses
		//$this->pageData['allCourses'] = $allNewCourses;
		$this->pageData['allCourses'] = $displayArray;


		
		return true;
	}
}

?>