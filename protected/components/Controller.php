<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
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
    protected $accessUid = false;
    protected $token;
    protected  $allowAllAction = array(
                                      );

    public function actionRest() {
        try {

            $method = strtolower($_SERVER['REQUEST_METHOD']);
            //$params = new DefaultArray($this->getActionParams());
            $params = $this->getActionParams();
            $data = file_get_contents('php://input');    //input stream only can be read once
            $controller_method=get_called_class().'::'.$method;
            $oauth = new OAuthServer();
            $token=$oauth->verifyRequest($_SERVER['HTTP_AUTHORIZATION'], $data);
            if ($token)
            {
                $this->accessUid=$token['uid'];
                $this->token=$token['token'];
            }
            if(!in_array($controller_method, $this->allowAllAction ) && !$token)
            {
                $errMsg = @$_SERVER['HTTP_AUTHORIZATION'].'  '.@$_SERVER['HTTP_REQUESTID'];
                InstantCommand::log($errMsg, 'trace', 'oauth');
                $oauth=0;
                throw APIException::invalidToken();
            }

            $oauth=1;

            if ($method == 'get' || $method == 'delete') {
                $result = $this->$method($params);
            } elseif ($method == 'post' || $method == 'put') {
                $post_arr = json_decode($data, true);
			    $result = $this->$method($params, $post_arr);
            }
            $ret = array('code'=>APIException::OK, 'result'=>$result);
        } catch (APIException $e) {
            $ret = array('code'=>$e->getCode(), 'result'=>$e->getOriginsMessage());
        } catch (Exception $e) {
            InstantCommand::log($e->getMessage(), CLogger::LEVEL_ERROR, __CLASS__."::".__FUNCTION__);
            $ret = array('code'=>APIException::UNKNOWN, 'result'=>array("default"=>$e->getMessage()));
        }
        $ret['requestId'] = @$_SERVER['HTTP_REQUESTID'];
        $this->render('json',$ret);
    }

    public function render($view, $data = null, $return = false) {
        if ($data['code'] != APIException::OK) {
            $head = APIException::getHttpCode($data['code']);
            header("HTTP/1.1 $head");
        }
        header("Content-Type: application/json");
        $data = $this->cleanupData($data);
        
        if ($data['result'] !== true) {
            echo json_encode($data, JSON_UNESCAPED_UNICODE), "\n";
        } else {
            $data['result'] = (object)null;
        	echo json_encode($data), "\n";
        }
    }

    public function cleanupData($data, $removeNull = true) {
        $ret = array();
        foreach ($data as $k=>$v) {
            if ($k === '_id') {
                $k = 'id';
            }
            if ($v instanceOf MongoID) {
                $v = (string)$v;
            } elseif (is_array($v)) {
                $v = $this->cleanupData($v);
            } elseif ($v instanceof EMongoDocument) {
                $v = $this->cleanupData($v->getAttributes());
            } elseif (is_null($v) && $removeNull) {
                continue;
            }
            $ret[$k] = $v;
        }
        return $ret;
    }

    protected function geneReqId() {
        return uniqid();
    }

    public function post($params, $data) {
    }

    public function get($params) {
    }

    public function put($params, $data) {
    }

    public function delete($params) {
    }

    public function getInput()
    {
        return json_decode(file_get_contents("php://input"), TRUE);
    }

    public function getParam($key = null, $default = null) {
        if($key) {
            return isset($_GET[$key]) ? $_GET[$key] : $default;
        }
        return $_GET;
    }

}

class APIException extends Exception {
    protected $code;

    const OK = 'OK';
    const UNKNOWN = 'Unknown';
    const SERVICE_UNAVAILABLE = 'ServiceUnavailable';
    const OBJECT_NOT_FOUND = 'ObjectNotFound';
    const METHOD_NOT_ALLOWED = 'MethodNotAllowed';
    const AUTHENTICATION_FAILED = 'AuthenticationFailed';
    const NO_PERMISSION = 'NoPermission';
    const NOT_FRIEND = 'NotFriend';
    const INVALID_TOKEN = 'InvalidToken';
    const INVALID_INPUT_FORMAT = 'InvalidInputFormat';
    const DATA_CONFLICT = 'DataConflict';
    const QUOTA_NOT_ENOUGH = 'QuotaNotEnough';
    const THIRD_PLATFORM_SERVICE_ERROR = 'ThirdPlatformServiceError';

    static $codeMap = array(
        self::OK => ['200 OK', ''],
        self::INVALID_INPUT_FORMAT=> ['400 Bad Request', ' invalid input format'],
        self::AUTHENTICATION_FAILED => ['403 Forbidden',' authentication failed'],
        self::OBJECT_NOT_FOUND => ['404 Not Found', ' requested object does not exists'],
        self::METHOD_NOT_ALLOWED => ['405 Method Not Allowed', ' method not allowed'],
        self::DATA_CONFLICT=> ['409 Conflict', 'data conflict'],
        self::UNKNOWN => ['500 Internal Server Error', ' unknown error occurred'],
        self::SERVICE_UNAVAILABLE => ['503 Service Unavailable', ' service unavailable'],
        self::THIRD_PLATFORM_SERVICE_ERROR=> ['503 Forbidden', ' third platform service error'],
    );

    function __construct($code, $msg) {
        $this->code = $code;
        $this->message = $msg;
    }

    function getOriginsMessage() {
        return $this->message;
    }
    
    

    static function objectNotFound($msg = '内容不存在') {
        return new self(self::OBJECT_NOT_FOUND, self::getMsg($msg));
    }

    static function methodNotAllowed($msg = '无效方法') {
        return new self(self::METHOD_NOT_ALLOWED, self::getMsg($msg));
    }

    static function authenticationFailed($msg = '验证失败') {
        return new self(self::AUTHENTICATION_FAILED, self::getMsg($msg));
    }
    
    static function noPermission($msg = '权限不够') {
        return new self(self::NO_PERMISSION, self::getMsg($msg));
    }

    static function notFriend($msg = '陌生人不能操作') {
        return new self(self::NOT_FRIEND, self::getMsg($msg));
    }
    
    static function invalidToken($msg = '验证信息无效') {
        return new self(self::INVALID_TOKEN, self::getMsg($msg));
    }

    static function invalidInputFormat($msg = '格式错误') {
        return new self(self::INVALID_INPUT_FORMAT, self::getMsg($msg));
    }

    static function serviceUnavailable($msg = '服务器不可用') {
        return new self(self::SERVICE_UNAVAILABLE, self::getMsg($msg));
    }
    
    static function dataConflict($msg = '数据冲突') {
        return new self(self::DATA_CONFLICT, self::getMsg($msg));
    }

    static function quotaNotEnough($msg = '配额不足') {
        return new self(self::QUOTA_NOT_ENOUGH, self::getMsg($msg));
    }

    static function thirdPlatformServiceError($msg = '第三方服务不可用') {
        return new self(self::THIRD_PLATFORM_SERVICE_ERROR, self::getMsg($msg));
    }

    static function getHttpCode($code) {
        return isset(self::$codeMap[$code]) ? self::$codeMap[$code][0] : '400 Bad Request';
    }
    
    static function getMsg($msg = '') {
    	if (is_string($msg)) {
    	    return array("default"=>$msg);
    	} elseif (is_array($msg)) {
    	    $keys = array_keys($msg);
    	    return array(
    	            "default" => $msg[$keys[0]][0],
    	            "field" => $keys[0],
    	    );
    	} else {
    	    return array();
    	}
    }
}

class DefaultArray extends ArrayObject {
    function get($key, $default = null) {
        return parent::offsetExists($key) ? parent::offsetGet($key) : $default;
    }

    function offsetGet($key) {
        return $this->get($key, null);
    }
}


