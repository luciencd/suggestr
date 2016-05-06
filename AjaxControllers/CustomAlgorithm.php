<?php

/**
 * NEED advice on how to make this MVC compatible. Currently it doesn't seem to be so.
 * Given that I require the model(suggestions.php), yet I have to reload all the data from initial refresh.
 */
//require_once('Controllers/suggestions.php');
//require_once('Controllers/suggestions.php');

class CustomAlgorithmController extends AjaxController {
	
	public $template = "Suggest";

	public function process($get,$post) {

		//header('Content-Type: application/json');
		//echo json_encode(array('foo' => 'bar'));
		$session_id = $_COOKIE['sessionId']; 
		$sql_file = $post['sql_file'];

		$lines = file($sql_file);
		// Loop through each line
		foreach ($lines as $line){
			echo $line;
		}

		$statement = //Read in from file.;
        
        $result = mysqli_query($GLOBALS['CONFIG']['mysqli'], $statement);

        if(mysqli_fetch_array($result)==null){
            return 1;
        }
        echo mysqli_fetch_array($result)[0];
		//echo $result[0];

        $allNewCourses = array();
        
		//Populate webpage with all the different courses that were predicted.
		
		$this->pageData['numResults'] = 0;
		$this->pageData['description'] = "Custom Suggestion Algorithm";
		$this->pageData['allCourses'] = $allNewCourses;

		
		return true;
	}
}

?>