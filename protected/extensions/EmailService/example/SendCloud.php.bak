<?php
/**
 * 注意:需要PHP 5.3版本或以上 
 */

/**
 * SendCloud 邮件发送客户端，
 * 可以进行邮件发送，获取SendCloud内部的邮件唯一ID。
 * 
 * 例子1： 进行普通邮件的发送
 * @example
 * <pre>
 *		include '/path/to/sendcloud_php/SendCloudLoader.php';//或者 导入SendCloud依赖
 *		
 *		try {
 *			$sendCloud = new SendCloud('username', 'password');
 *			$message = new SendCloud\Message();
 *			$message->addRecipient('to1@sendcloud.com') // 添加第一个收件人
 *					->addRecipients(array('to2@sendcloud.com', 'to3@sendcloud.com')) // 添加批量接受地址
 *					->setReplyTo('replyto@sendcloud.com') // 添加回复地址
 *					->addCc('cc@sendcloud.com') // 添加cc地址
 *					->addBcc('bcc@sendcloud.com') // 添加bcc地址
 *					->setFromName('搜狐SendCloud') // 添加发送者称呼
 *					->setFromAddress('from@sendcloud.com') // 添加发送者地址
 *					->setSubject('SendCloud PHP SDK测试')  // 邮件主题
 *					->setBody("<strong>SendCloud PHP SDK 测试正文，请参考</strong> <a href='http://sendcloud.sohu.com'>SendCloud</a>"); // 邮件正文html形式
 *			$message->setAltBody('SendCloud PHP SDK 测试正文，请参考');// 邮件正文纯文本形式，这个不是必须的。
 *			
 *			// 二进制或者字符串流附件
 *			// $data = file_get_contents(iconv('UTF-8', 'GBK', 'E:\path\SendCloud使用指南.pdf'));		
 *			//$message->addStringAttachment($data, 'SendCloud使用指南.pdf');
 *			// 普通附件
 *			$message->addAttachment('E:\path\SendCloud测试.xls')
 *					->addAttachment('E:\path\SendCloud测试.pdf', 'SendCloud测试--重命名.pdf');
 *			
 *	 		echo $sendCloud->send($message);
 *			print '<br>emailIdList:';
 *			var_dump($sendCloud->getEmailIdList());// 取得emailId列表
 *		} catch (Exception $e) {
 *				print "出现错误:";
 *				print $e->getMessage();
 *		}
 * </pre>
 *
 * 例子2： 使用X-SMTPAPI定制化邮件的发送
 * @example
 * <pre>
 *	 include '/path/to/sendcloud_php/SendCloudLoader.php';//或者 导入SendCloud依赖
 *	
 *	// X-SMTPAPI 请参照 @link http://sendcloud.sohu.com/sendcloud/api-doc/x-smtpapi
 *	try {
 *		$sendCloud = new SendCloud('username', 'password');
 * // 		$sendCloud->setDebug(true); // 设置SMTP Debug, 默认关闭
 *  // 		$sendCloud->setServer('smtp.qq.com',25); // 设置发送服务器，默认使用smtpcloud.sohu.com:25, 这样才会有X-SMTPAPI的功能
 *
 *		// 使用SmtpApiHeader辅助类，生成X-SMTPAPI字段的json形式。
 *		$xSmtpApiHeader = new SendCloud\SmtpApiHeader();
 *		// 设置开启取消订阅，打开跟踪，点击链接跟踪
 *		$xSmtpApiHeader->addFilterSetting(SendCloud\AppFilter::$ADD_UNSUBSCRIBE, 'enable', '1') //取消订阅
 *						->addFilterSetting(SendCloud\AppFilter::$ADD_HIDDEN_IMAGE, 'enable', '1') //打开跟踪
 *						->addFilterSetting(SendCloud\AppFilter::$PROCESS_URL_REPLACE, 'enable', '1'); //点击链接跟踪
 *		
 *		// 设置接受者和相应的内容替换
 *		$recipients = array('to11@sendcloud.com', 'to2@sendcloud.com');
 *		$xSmtpApiHeader->addRecipients($recipients);
 *		$xSmtpApiHeader->addSubstitution('%name%', array("第一个接收者姓名", "第二个接收者姓名")) // 保证sub的替换内容和$recipients的个数相等
 *						->addSubstitution("%code%", array("第一个接收者代码", "第二个接收者代码")); // 保证sub的替换内容和$recipients的个数相等
 *		
 *		$message = new SendCloud\Message();
 *		$message->setXsmtpApiJsonString($xSmtpApiHeader->toJsonString()); // 设置X-SMTPAPI字符串
 *		//$message->setXsmtpApiHeaderArray($xSmtpApiHeader->getSmtpApiHeaderArray()); // 这种效果和上面一句相同
 *		$message->addHeader('header_test', 'header_test_value') // 头部必须是ascii码
 *			->setReplyTo('replyto@sendcloud.com') // 添加回复地址
 *			->addCc('cc@sendcloud.com') // 添加cc地址
 *			->addBcc('bcc@sendcloud.com') // 添加bcc地址
 *			->setFromName('搜狐SendCloud') // 添加发送者称呼
 *			->setFromAddress('from@sendcloud.com') // 添加发送者地址
 *			->setSubject('SendCloud PHP SDK测试')  // 邮件主题				
 *			->setBody("<strong>您好， %name%,您的代码是：%code%，SendCloud PHP SDK 测试正文，请参考</strong>
 *					 <a href='http://sendcloud.sohu.com'>SendCloud</a>"); // 邮件正文html形式
 *	
 *		echo $sendCloud->send($message);
 *		var_dump($sendCloud->getEmailIdList());// 取得emailIdList
 *	} catch (Exception $e) {
 *		echo "出现错误:".$e->getMessage();
 *	}
 * </pre>
 * @author SendCloud Team
 *
 */
class SendCloud
{
	/** 用户名 */ 
	private $username,
	/** 密码  */
			$password;
	/** 是否调试 */
	private $is_debug = false;
	/** SendCloud/Message 对象 */
	private $message;

	/** 附件路径的默认编码 , 默认读取文件不进行转换*/ 
	private $filePathFromEncoding = '', 
			$filePathToEncoding = '';

	/**
	 * SendCloud构造函数。
	 * @param string 用户名
	 * @param string 密码
	 */
	public function __construct($username, $password) {
		$this->username = $username;
		$this->password = $password;
		require_once 'SendCloud/Smtp.php';
		$this->smtp = new SendCloud\Smtp($this->username, $this->password);
	}

	/**
	 * 发送邮件 。
	 * @param SendCloud\Message $message SendCloud消息对象。
	 * @return boolean 成功返回true，失败返回false。
	 */
	public function send($message) {
		$this->message = $message;
		return $this->smtp->send($message, $this->filePathFromEncoding, $this->filePathToEncoding, $this->is_debug);
	}

	/**
	 * 如果server为'smtpcloud.sohu.com', 发送邮件成功将取得发送的emailId的列表。
	 * 每个接受者对应一个emailId。
	 * 有了EmailId， 可以使用SendCloud的Webhooks服务，进行邮件事件的订阅。
	 * 如： 收件人为 array('example0@sendcloud.com', 'example1@sendcloud.com', 'example2@sendcloud.com')
	 * 如果发送成功，将返回：
	 * array("1348066218100_0_16082_9090.zw_124_1990$example0@sendcloud.com",
	 * 		 "1348066218100_0_16082_9090.zw_124_1991$example1@sendcloud.com",
	 * 		 "1348066218100_0_16082_9090.zw_124_1992$example2@sendcloud.com")
	 * @return array emaild列表。
	 */
	public function getEmailIdList(){
		$rply = $this->smtp->getServerResponse();// 250 #1348047005177_0_7003_2087.zw_124_223#Queued!
		
		$emailIdList = array();
		if (isset($rply) && trim($rply) != false) {
			$resp_arr = preg_split("/#/", $rply);
			if (count($resp_arr) == 3) {
				$preEmailId = trim($resp_arr[1]);
				$jsonHeader = $this->message->getXsmtpApiHeaderArray();
				if (isset($jsonHeader['to']) && !empty($jsonHeader['to'])) { // X-SmtpApi to
					if (!isset($jsonHeader['to'])) {
						$recipients =  array();
					}else{
						$recipients = $jsonHeader['to'];
					}
				}else{
					$recipients = $this->message->getRecipients();
				}
				// to
				$i = 0 ;
				foreach ($recipients as $key => $value){
					$emailIdList[] = $preEmailId . $i . '$'. $value;
					$i = $i + 1;
				}
				
				// cc
				$ccs = $this->message->getCcs();
				if (isset($ccs)) {
					foreach ($ccs as $key => $value){
						$emailIdList[] = $preEmailId . $i . '$'. $value;
						$i = $i + 1;
					}
				}
				// bcc
				$bccs = $this->message->getBccs();
				if (isset($bccs)) {
					foreach ($bccs as $key => $value){
						$emailIdList[] = $preEmailId . $i . '$'. $value;
						$i = $i + 1;
					}
				}
			}
		}
		
		return $emailIdList;
	}
	
	/**
	 * 取得服务器的返回信息。
	 * 如：
	 * 250 #1348047005177_0_7003_2087.zw_124_223#Queued!
	 * @return string  服务器的返回信息
	 */
	public function getServerReponce(){
		return $this->smtp->getServerResponse();// 250 #1348047005177_0_7003_2087.zw_124_223#Queued!
	}

	/**
	 * 设置服务器名称和端口号，如果不设置默认使用smtpcloud.sohu.com:25。
	 * @param string $server 服务器名称， 如：smtpcloud.sohu.com
	 * @param int $port 端口号， 默认为 25
	 */
	public function setServer($server, $port = 25) {
		$this->smtp->setServer($server, $port);
	}

	/**
	 * 设置文件路径的编码转换，用于在中文路径下读取文件的错误及乱码问题。
	 * 
	 * 这个可以解决php中读取文件的问题。 默认不进行任何的转换；如果设置了$fromEncoding 和 $toEncoding 
	 * 将进行路径的编码转换。进行的转换为， $path = iconv($filePathFromEncoding, $filePathToEncoding, $path)
	 * 然后根据$path来读取文件。参见lib/class.phpmailer.php的1728行
	 *
	 * @param string $filePathFromEncoding 需要转换文件路径的原始编码形式，默认为空。
	 * @param string $filePathToEncoding 需要转换文件路径的目标编码形式，默认为空。
	 */
	public function convertFilePathEncoding($filePathFromEncoding = '', $filePathToEncoding = '') {
		$this->filePathFromEncoding = $filePathFromEncoding;
		$this->filePathToEncoding = $filePathToEncoding;
	}

	/**
	 * 设置是否打开调试模式
	 * @param boolean/int false表示不打开调试，true表示只显示消息，设置2表示显示消息及Debug信息
	 */
	public function setDebug($is_debug){
		$this->is_debug = $is_debug;
		return $this;
	}

	/**
	 * 返回是否打开了调试模式
	 * @return boolean 是否调试，调试的级别为true表示只显示消息，设置2表示显示消息及Debug信息
	 */
	public function getDebug(){
		return $this->is_debug;
	}
}
