<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class AdminController extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='dwz_data';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();


    protected function beforeAction($action) {
        if(!$this->getLoginUser()) {
            $action_name = Yii::app()->controller->action->id;
            $controller_name = Yii::app()->controller->id;
            if($controller_name != "site" || !in_array($action_name ,array("login","logout"))) {

                $url = "/admin/site/login";
                if(!empty($_SERVER['REQUEST_URI'])) {
                    $url .= "?url=". urlencode($_SERVER['REQUEST_URI']);
                }
                $this->redirect($url);
                return false;
            }
        }

        return true;
    }

    protected $loginUser = null;
    protected function getLoginUser() {

        $admin_id = isset(Yii::app()->request->cookies['admin_id']) ? Yii::app()->request->cookies['admin_id']->value : '';
        $username = isset(Yii::app()->request->cookies['username']) ? Yii::app()->request->cookies['username']->value : '';
        $token = isset(Yii::app()->request->cookies['token']) ? Yii::app()->request->cookies['token']->value : '';

        if(!$admin_id) {
            return false;
        }
        $administrator = Administrator::model()->findByPk(new MongoId($admin_id));
        if($administrator
            && $administrator->status
            && $administrator->userName === $username
            && $administrator->getCookiePassword() ===$token) {
            $this->loginUser = $administrator;
            return true;
        }


        return false;
    }

    public function renderJson($code, $msg ="操作成功！", $data =array() ) {
        //{"statusCode":"200", "message":"操作成功", "navTabId":"navNewsLi", "forwardUrl":"", "callbackType":"closeCurrent"}

        $ret = array(
            "statusCode" => $code,
            "message"   =>  $msg
        );

        $ret = array_merge($ret,$data);

        echo json_encode($ret);
    }

    public function renderDwzJson($code,$msg ="操作成功！",$closeCurrent = 1,$data = array()) {

        if($closeCurrent) {
            $data['callbackType'] = "closeCurrent";
        }
        return $this->renderJson($code,$msg,$data);
    }

}



