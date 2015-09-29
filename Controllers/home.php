<?php
// Page Controller for the Index Page

class HomeController extends PageController {
	public $pageTemplate = "home";
	
	public function process($get, $post) {
		$this->pageData["Title"] = "Home";
		
		$this->pageData["myMath"] = 24*180;
		$this->pageData["name"] = $get['i'];
		
		$this->pageData['arr'] = array(
									array('elem' => 1, 'lucien' => 100), 
									array('elem' => 2), 
									array('elem' => 3)
								);
		$user = new User();
		$user->findById(69);
		$user->set('name', 'Bob');
		$user->save();

		//if(!isset($_GET['i'])) // No event id given
		//	header('Location: /error');
		
		/*
		// Create GROUP ORM
		try{
			$event = new Event();
			$event->findById($_GET['i']);
		}catch(Exception $e){
			header($_GET["SUGGESTR_PAGE"]." 404 Not Found");
			$_SYSTEM["SUGGESTR_PAGE"] = "404 - Page Not Found";
			$controller = GetController("error");
			$controller->process($get,$post,$_FILES,$_SYSTEM);
			die($controller->render(!$_GET['AJAX']));	
		}
		*/

		//Left TIME

	}
}