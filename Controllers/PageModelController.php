<?php

class PageModelController extends PageController {
	public static $model = $GLOBALS['MODEL']['Data'];

	public loadModel(){
		$model->load();
	}
	public getModel(){
		return $model;
	}

}

?>