# Yii-Restful-API (with MongoDB)
基于Yii的 REST API (演示地址）
* REST API
* 通过JSON来提交和返回数据

## 几个示例说明
```
创建用户:         curl -X POST -d '{"name":"Tom", "phone":"18612345678"}'   http://example.com/v1/users
获取指定用户:     curl -X GET http://example.com/v1/users/53c0eac036756bc97a34db62
修改指定用户信息: curl -X PUT -d {"name": "Tom.Mason"} http://example.com/v1/users/53c0eac036756bc97a34db62
删除指定用户:     curl -X DELETE http://example.com/v1/users/53c0eac036756bc97a34db62
```

## 环境要求
* PHP 5.4.0+
* Yii Framework 1.1.14+
* MongoDB 2.6+
 
## 安装
* 下载Yii-Restful-Api后，修改 protected/config/dev/main.php中的"mongodb"参数,改为符合本地的用户名和密码等信息
*  

## 配置
Nginx(在server下面加上这一段)
```
        if (!-e $request_filename) {
                rewrite ^.* /index.php?m=$1 last;
        }
```
## MongoDB
** 采用Yii框架的yii-mongo-suite扩展来操作mongodb
** 该扩展的主页 http://www.yiiframework.com/extension/yiimongodbsuite/
** 该扩展的文档 http://canni.github.io/YiiMongoDbSuite/xhtml_single/index.html

## 主要的目录结构
```
+ src
  |+ protected
     |+ components // 组件
        |- Controller.php 主控制器
     |+ config     // 配置
     |+ extensions // 扩展
     |+ models     // 模型
     |+ modules    // 功能模块
     |+ 
  |+ themes
  |- index.php     // 入口文件

```

## 用法（以用户模块，获取指定用户信息为例）
1. 创建用户模型文件 protected/models/users/Users.php
```
class Users extends EMongoDoc
{

    public $phone;
    public $name;
    public $createdTime;

    public function rules()
    {
        return [
                ['phone', 'match',  'pattern'=>'/^1\d{10}$/', 'message'=>'手机号码不能为空且应符合格式'],
                ['name', 'match', 'pattern'=>'/(*UTF8)^.{2,15}$/', 'message'=>'昵称为2到15个字符或汉字'],
               ];
    }

    public function indexes()
    {
        return [
            'phone_1' => [
                'key' => ['phone' => EMongoCriteria::SORT_ASC],
            ],
        ];
    }


    public function beforeSave() {
        if ($this->getIsNewRecord()) {
            $this->createdTime =  intval(microtime(true) * 1000);
        }
		$this->areaCode = "86";
        return parent::beforeSave();
    }

    public function id() {
        if ($this->attributes['_id'] instanceOf MongoID) {
            return (string)$this->attributes['_id'];
        }
        return $this->attributes['_id'];
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
```

2. 创建用户模块与用户控制器 protected/modules/users/controllers/UsersController.php
```
class UsersController extends Controller
{
	public function get($params)
	{
		$id = new MongoID($params['id']);
		$userInfo = Users::model()->findByPk($id);
        return $userInfo;
	}
}
```


3. 配置路由规则（url) protected/config/dev/main.php
```
'urlManager'=>array(
            'urlFormat'=>'path',
            "showScriptName"=>false,
            'rules'=>array(
                '/v1/<module:(feeds|users)>'=>'<module>/<module>/rest',
                '/v1/<module:(feeds|users)>/<id:[0-9a-f]+>'=>'<module>/<module>/rest',
            ),
        ),
```


