<?php
class MController extends CController {
    public $layout = 'm_main';
    
    protected function beforeAction($action) {
        // TODO   
	Yii::setPathOfAlias('bootstrap', dirname(__FILE__).'/../extensions/bootstrap'); 
        return true;
    }
}
