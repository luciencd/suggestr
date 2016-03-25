<?php

class CreateSessionController extends AjaxController {
	public function process($get,$post) {
		$needNewSession = true;
		//Make sure if database fails, catch that, don't create new session.
		if(isset($_COOKIE['sessionId'])){
			$query = new Query('sessions');
			$cookieSessions = $query->select('*',array(array('id','=',$_COOKIE['sessionId'])),'','',false);
			if(Count($cookieSessions) == 1){
				$needNewSession = false;
			}else{
				$needNewSession = true;
			}
		}

		if($needNewSession){ // Check if this user already has a session
			// Generate the next user id from the table

			//If new user, we get the nextId from the table.
			$query = new Query('sessions');
			$id = $query->nextId();
			//I suppose if database could not be accessed when we check for it
			//in $session->findById($_COOKIE['sessionId']);
			// it also won't work here, meaning the session_id won't actually change
			// just because the database fails.

			if(is_numeric($id)){
				
				$session = new Session();
				$session->set('amount', 0); // Just so that the ORM class thinks something's dirty and allows entry of an empty row
				$session->set('ip',$_SERVER['REMOTE_ADDR']);
				$session->save(); // Add an empty row to the Sessions table with the next session ID
				
				header('Location: /'); // Needs to reload since a cookie must be set at the start of the request.
				setcookie('sessionId', $id, time()+315360000, '/'); // Shouldn't expire for 10 years

				ob_end_flush();

				
				//now that the session_id is set, start model.
				
			}else
				throw new Exception("Error Processing New Session.", 1);
		}else{
		}
	}
}

?>