<?php 
require_once("config.php");
class SessionModel {

	public function __construct(){

	}
	public function isInSession(){
		
		if(isset($_COOKIE['sessionId'])){
			$query = new Query('sessions');
			$cookieSessions = $query->select('*',array(array('id','=',$_COOKIE['sessionId'])),'','',true);
			if(count($cookieSessions) > 0){
				return true;
			}else{
				return false;
			}
		} 
		return false;
	}
	public function getCurrentSessionId(){
		if(isset($_COOKIE['sessionId'])){
			return intval($_COOKIE['sessionId']);
		} else{
			return -1;
		}
	}

	public function createNewSession(){
		$query = new Query('sessions');
		$newSessionId = $query->nextId();
		#echo "new session id:",$newSessionId;
		if(is_numeric($newSessionId)){
			
			$session = new Session();
			$session->set('major_id',0);
			$session->set('year_id',0);
			$session->set('major_input',"");
			$session->set('amount', 0);
			$session->set('ip',$_SERVER['REMOTE_ADDR']);

			$inserted = $session->save();

			header('Location: /');
			setcookie('sessionId', $newSessionId, time()+315360000, '/'); // Shouldn't expire for 10 years
			ob_end_flush();
		}
		echo $this->getCurrentSessionId();
		
	}
	public function handleSession(){
		if(SessionModel::isInSession()){
			return $this->getCurrentSessionId();
		}else{
			$this->createNewSession();
		}
	}
}
?>