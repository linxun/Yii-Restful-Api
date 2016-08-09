<?php
/**
 * 注意:需要PHP 5.3版本或以上 
 * @package SendCloud
 */
namespace SendCloud;

/**
 *该类将Message对象中的发送邮件所需要的信息进行组装发送
 *
 * @author delong
 *
 */
class Smtp {
	/** 默认smtp服务器 'smtpcloud.sohu.com'*/
	const DEFAULT_SERVER = 'smtpcloud.sohu.com';
	/** 默认端口 25*/
	const DEFAULT_PORT = 25;

			/** 用户名 */
	private $username, 
		/** 密码 */
		$password, 
		/** smtp服务器 */
		$server,   
		/** 端口 */
		$port,    
		/** SendCloud\Message */
		$message,  
		/** 服务器返回信息 */
		$server_response = ''; 
	
	/**
	 * Smtp构造函数
	 * @param string $username 用户名
	 * @param string $password 密码
	 */
	public function __construct($username, $password) {
		// 设置默认的smtp服务器和端口号
		$this->server = Smtp::DEFAULT_SERVER;
		$this->port = Smtp::DEFAULT_PORT;
		$this->username = $username;
		$this->password = $password;
		$this->message = new Message();
	}
	
	/**
	 * 设置服务器的名称和端口号
	 * @param string $server 服务器名称
	 * @param int $port 端口号
	 * @return \SendCloud\Smtp
	 * @return self
	 */
	public function setServer($server = 'smtpcloud.sohu.com', $port = 25) {
		$this->server = $server;
		$this->port = $port;
	
		return $this;
	}
	
	/**
	 * 取得smpt发送邮件之后的返回结果
	 * @return string smpt发送邮件之后的返回结果
	 */
	public function getServerResponse() {
		return $this->server_response;
	}

	/**
	 * 取得phpmailer实例
	 * @param string $server smtp服务器， 默认为'smtpcloud.sohu.com'
	 * @param string $port 端口, 默认为'25'
	 * @param boolean $is_debug 调试状态。
	 * @return \PHPMailer
	 */
	private function getPhpMailerInstance($server = 'smtpcloud.sohu.com', $port = 25, $is_debug = false) {
		// the true param means it will throw exceptions on errors, which we need to catch
		$mail = new \PHPMailer(true);
		$mail->CharSet = "UTF-8";
		$mail->IsSMTP();
		$mail->Host  = $server;
		$mail->Port  = $port;
		$mail->SMTPAuth =  true;
		$mail->Username = $this->username;
		$mail->Password =  $this->password;
		$mail->WordWrap = 76;
		// 设置调试
		$mail->SMTPDebug = $is_debug;
		
		return  $mail;
	}
		
	/**
	 * 使用PHPMailer进行邮件的发送。设置编码形式的转化PHPMailer 中文名路径或者文件的读取问题。
	 * @param \SendCloud\Message $message SendCloud\Message 对象
	 * @param string $filePathFromEncoding 需要转换文件路径的原始编码形式，默认为空。
	 * @param string $filePathToEncoding 需要转换文件路径的目标编码形式，默认为空。
	 * @param boolean/int $is_debug 调试的状态。
	 * @return boolean 发送成功，返回true；有错误信息时返回false
	 */
	public function send(Message $message, $filePathFromEncoding = '', $filePathToEncoding = '', $is_debug = false) {
		$mail = $this->getPhpMailerInstance($this->server, $this->port, $is_debug);
		
		// 接受者
		$tos = $message->getRecipients();
		foreach ($tos as $key => $value) {
			$mail->AddAddress($value);
		}
		// from
		$mail->SetFrom($message->getFromAddress(), $message->getFromName());
		// 主题
		$mail->Subject = $message->getSubject();
		// optional - MsgHTML will create an alternate automatically
		$mail->AltBody = $message->getAltBody(); 
		$mail->MsgHTML($message->getBody());
		
		if(($replyto = $message->getReplyTo())) {
			$mail->AddReplyTo($message->getReplyto());;
		}
		// ccs
		$ccs = $message->getCcs();
		foreach ($ccs as $key => $value) {
			$mail->AddCC($value);
		}
		// bccs
		$bccs = $message->getBccs();
		foreach ($bccs as $key => $value) {
			$mail->AddBCC($value);
		}
		// 附件
		$attachs = $message->getAttachments();
		if (count($attachs) > 0) {
			// 进行附件及路径名的设置转换
			$mail->convertFilePathEncoding($filePathFromEncoding, $filePathToEncoding);;
		}
		foreach ($attachs as $key => $value) {
			if ($value[2]) { // string attachment 
				$mail->AddStringAttachment($value[0], $value[1]);
			}else{
				$mail->AddAttachment($value[0], $value[1]);
			}
		}
		// 添加全部的头部字段
		foreach ($message->getHeaders() as $key => $value){
			$mail->AddCustomHeader($key, $value);
		}
		
		// X-SMTPAPI 字段
		$jsonString = $message->getXsmtpApiJsonString();
		if (isset($jsonString) && trim($jsonString) !=='' && $jsonString !== '{}') {
			$jsonDec = json_decode($jsonString);
			if($jsonDec === null) {
				throw new \Exception('x-smtpapi is not a valid json string.'); 
			}
			$mail->AddCustomHeader('X-SMTPAPI', base64_encode($jsonString));
		} else if ($message->getXsmtpApiJsonString()!== '{}') {
			$mail->AddCustomHeader('X-SMTPAPI', base64_encode($message->getXsmtpApiJsonString()));
		}
	   
		$code = $mail->Send();
		if ($code){
			$this->server_response = $mail->getDataCommandRply();
		}
		return $code;
	}
}
