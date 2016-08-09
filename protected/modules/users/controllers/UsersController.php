<?php

class UsersController extends Controller
{
    /**
     * 获取指定用户信息
     * @param $params 用户id
     * @return array
     */
	public function get($params)
	{
		$id = new MongoID($params['id']);
		$userInfo = Users::model()->findByPk($id);
        return $userInfo;
	}

	/**
	 * 创建用户
	 *
	 * @param  array phone, captcha, passwordHash, channelId
	 * @return array userInfo
	 *
	 * 1.检查输入是否有效
	 * 2.检查手机验证码
	 * 3.把用户信息写入数据库
	 * 4.初始化用户配置
	 * 5.推荐好友
	 */
	public function post($params, $data)
	{

	}


	public function put($params, $data)
	{

	}

	public function delete($params)
	{

	}
}
