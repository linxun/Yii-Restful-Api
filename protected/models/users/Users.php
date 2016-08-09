<?php
/**
 * users model
 * Authors: linxun
 * Date: 2014-03-20
 */
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
