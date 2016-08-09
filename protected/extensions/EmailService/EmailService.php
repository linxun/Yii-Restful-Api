<?php
require_once(dirname(__FILE__) . '/' . 'SendCloudLoader.php');
require_once(dirname(__FILE__) . '/' . 'SendCloud.php');

class EmailService {
    public $sendCloud;
    function init(){}
    
    const TYPE_VALIDATE_EMAIL = 'validateEmail';
    const TYPE_RESET_PASSWORD = 'resetPassword';
        
    function getConfig() {
        return $this->sendCloud;
    }
    
    public static function getValidateKey($email, $uid) {
    	$slat = '#ynaI08Oi6MEQB9ObjMUthg#';
    	return md5($email.$slat.$uid);
    }
    
    
    public static function sendEmail($type, $uid, $recipient) {
        $conf = Yii::app()->emailService->getConfig();
        $domain = $conf['domain'];
        
        try {
            	$sendCloud = new SendCloud($conf['api_user'], $conf['api_key']);
            	$message = new SendCloud\Message();
            	
            	$user = Users::model()->findByPk(new MongoID($uid));
            	
                if ($type == self::TYPE_VALIDATE_EMAIL) {
                    $key = self::getValidateKey($user->email, $uid);
                    $url = "http://{$domain}/m/users/email?key={$key}&uid={$uid}";
                    
                    $body = <<<EOF
<p>尊敬的用户：</p><p>您好！</p>
<p>感谢您注册Wode，请点击以下链接完成注册 <a href="{$url}">{$url}</a> </p>
<p>&nbsp;</p>
<p>我的团队</p>
EOF;
                    $altBody = <<<EOF
尊敬的用户：您好！
感谢您注册Wode，请点击以下访问完成注册 $url
                    
我的团队
EOF;
            
                    $message->addRecipient($recipient) // 添加第一个收件人
                    ->setReplyTo('help@wode.im') // 添加回复地址
                    ->setFromName('我的') // 添加发送者称呼
                    ->setFromAddress('noreply@wode.im') // 添加发送者地址
                    ->setSubject('注册新用户')  // 邮件主题
                    ->setBody($body); // 邮件正文html形式
                    $message->setAltBody($altBody);// 邮件正文纯文本形式，这个不是必须的。
                } elseif ($type == self::TYPE_RESET_PASSWORD) {
                    // ...
                }
            	
            	$sendCloud->send($message);
            	//var_dump($sendCloud->getEmailIdList());// 取得emailId列表
        } catch (Exception $e) {
        		$message = @$_SERVER['HTTP_REQUESTID']."  ".$e->getMessage();
        		//Yii::log($message, CLogger::LEVEL_ERROR, __CLASS__."::".__FUNCTION__);
        		InstantCommand::log($message, CLogger::LEVEL_ERROR, __CLASS__."::".__FUNCTION__);
        }
    }
}
