<?php
/**
 * SendCloud 的邮件消息类，用户和PHPMailer进行组装邮件内容。
 * @package SendCloud
 */
namespace SendCloud;

/**
 * 邮件信息类, 加入发送邮件所需要的信息, 包括收、发信人, 邮件的标题, 正文, 附件, 以及可选的信息
 * 
 * <a href="http://sendcloud.sohu.com/sendcloud/api-doc/x-smtpapi">
 * X-SMTPAPI</a>扩展字段。
 *
 * @author delong
 */
class Message {
			/** 发送者称呼  */
	private $fromName,
			/** 发送者地址  */
			$fromAddress, 
			/** 接收者地址  */
			$recipients = array(), 
			/** 主題  */
			$subject = "欢迎使用SendCloud",
			/** 正文的纯文字形式 */
			$altBody,
			/** 正文的html形式 */
			$body,
			/** 抄送者地址 */
			$cc_list = array(),
			/** 密送者地址 */
			$bcc_list = array(),
			/** 回复地址 */
			$reply_to,
			/** X-SMTPAPI的array形式 */
			$xSmtpApiHeaderJsonArray = array(),
			/** X-SMTPAPI的JSON字符串形式 */
			$xSmtpApiJsonString,
			/** 定制的邮件头部 */
			$headers_list = array(),
			/** 附件列表 */
			$attachment_list = array();

	/**
	 * Message 构造函数。
	 */
	public function __construct(){
	}

	/**
	 * 取得发件人姓名。
	 * @return string
	 */
	public function getFromName(){
		return $this->fromName;
	}

	/**
	 * 设置发件人姓名。
	 * @param string $fromName 发件人姓名
	 * @return self \SendCloud\Message 对象
	 */
	public function setFromName($fromName) {
		$this->fromName = $fromName;
		return $this;
	}

	/**
	 * 取得邮件发送地址。
	 * @return string 邮件发送者地址
	 */
	public function getFromAddress(){
		return $this->fromAddress;
	}

	/**
	 * 设置发送者地址。
	 * @param string $fromAddress
	 * @return self \SendCloud\Message 对象
	 */
	public function setFromAddress($fromAddress) {
		$this->fromAddress = $fromAddress;
		return $this;
	}

	/**
	 * 设置邮件的主题。
	 * @param string $subject 邮件主题
	 * @return self \SendCloud\Message 对象
	 */
	public function setSubject($subject) {
		$this->subject = $subject;
		return $this;
	}

	/**
	 * 取得邮件的主题。
	 * @return string 邮件主题
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * 设置html形式的邮件正文。
	 * @param string $body 邮件正文
	 * @return self \SendCloud\Message 对象
	 */
	public function setBody($body) {
		$this->body = $body;
		return $this;
	}

	/**
	 * 取得html形式的邮件正文。
	 * @return string html邮件正文
	 */
	public function getBody(){
		return $this->body;
	}

	/**
	 * 设置纯文本的邮件正文。这在某些客户端可以实现只显示纯文本。
	 * 如<a href="www.mutt.org">The Mutt E-Mail Client</a>
	 * @param string $altBody 纯文本邮件正文
	 * @return self \SendCloud\Message 对象
	 */
	public function setAltBody($altBody) {
		$this->altBody = $altBody;
		return $this;
	}

	/**
	 * 取得纯文本的邮件正文。
	 * @return string 邮件正文
	 */
	public function getAltBody(){
		return $this->altBody;
	}

	/**
	 * 设置邮件抄送者地址(之前的会被覆盖)。
	 * @param string $address 抄送者地址
	 * @return self \SendCloud\Message 对象
	 */
	public function setCc($address) {
		$this->cc_list = array($address);
		return $this;
	}

	/**
	 * 设置多个邮件抄送者地址(之前的会被覆盖)。
	 * @param array $addressList 邮件抄送者地址列表
	 * @return self \SendCloud\Message 对象
	 */
	public function setCcs($addressList) {
		$this->cc_list = $addressList;
		return $this;
	}

	/**
	 * 添加邮件抄送者地址。
	 * @param string $address 邮件抄送者地址
	 * @return self \SendCloud\Message 对象
	 */
	public function addCc($address){
		$this->cc_list[] = $address;
		return $this;
	}

	/**
	 * 添加一组收信人邮件地址。
	 * @param array $addresses 邮件抄送者地址
	 * @return self SendCloud\Message 对象
	 */
	public function addCcs(array $addresses){
		foreach ($addresses as $address){
			$this->cc_list[] = $address;
		}

		return $this;
	}

	/**
	 * 取得邮件抄送者地址。
	 * @return array $cc_list
	 */
	public function getCcs(){
		return $this->cc_list;
	}

	/**
	 * 设置bcc地址（之前的设置会被覆盖）
	 * @param string $address Bcc接收者地址
	 * @return self \SendCloud\Message 对象.
	 */
	public function setBcc($address) {
		$this->bcc_list = array($address);
		return $this;
	}

	/**
	 * 设置bcc地址。
	 * @param array $addresses 接收者列表
	 * @return self \SendCloud\Message 对象
	 */
	public function setBccs(array $addresses) {
		$this->bcc_list = $addresses;
		return $this;
	}

	/**
	 * 添加一组bcc收信人邮件地址。
	 * @param array $addresses
	 * @return self SendCloud\Message 对象
	 */
	public function addBccs(array $addresses){
		$this->bcc_list = array_merge($this->bcc_list, $addresses);

		return $this;
	}

	/**
	 * 添加新的bcc地址。
	 * @param string $address
	 * @return self \SendCloud\Message 对象
	 */
	public function addBcc($address){
		$this->bcc_list[] = $address;
		return $this;
	}

	/**
	 * 取得bcc地址。
	 * @return array $bcc_list
	 */
	public function getBccs(){
		return $this->bcc_list;
	}

	/**
	 * 返回邮件接收者地址列表。
	 * @return array 接受者列表（Array）
	 */
	public function getRecipients(){
		return $this->recipients;
	}

	/**
	 * 添加接收者地址。
	 * @param string $address 接受者地址
	 * @return self SendCloud\Message 对象
	 */
	public function addRecipient($address){
		$this->recipients[] = $address;
		return $this;
	}

	/**
	 * 添加多个接收者地址。
	 * @param array $addresses 例如：array('example1@sendcloud.com', 'example2@sendcloud.com');
	 * @return self SendCloud\Message 对象.
	 */
	public function addRecipients(array $addresses){
		$this->recipients = array_merge($this->recipients, $addresses);
		return $this;
	}

	/**
	 * 设置回复人地址。
	 * @param string $address
	 * @return self \SendCloud\Message 对象
	 */
	public function setReplyTo($address){
		$this->reply_to = $address;
		return $this;
	}

	/**
	 * 取得回复人地址。
	 * @return string 回复人地址
	 */
	public function getReplyto(){
		return $this->reply_to;
	}
	
	/**
	 * 添加来自文件系统的文件。
	 * @param string $filePath 文件路径 例 如： /usr/文件.doc
	 * @param string $fileName 文件名  例 如：  重命名文件.doc
	 * @return self \SendCloud\Message
	 */
	public function addAttachment($filePath, $fileName = '') {
		$this->attachment_list[] = array(
				0 => $filePath,
				1 => $fileName,
				2 => false,  // 来自文件系统
		);
		 
		return $this;
	}
	 
	/**
	 * 添加来自字符串或者二进制的数据文件。
	 * @param string $string
	 * @param string $filename
	 * @return self \SendCloud\Message
	 */
	public function addStringAttachment($string, $filename){
		$this->attachment_list[] = array(
				0 => $string,
				1 => $filename,
				2 => true,  // isStringAttachment
		);
	  
		return $this;
	}

	/**
	 * 取得附件列表
	 * @return 附件列表
	 */
	public function getAttachments() {
		return $this->attachment_list;
	}

	/**
	 * 在邮件头中添加一个扩展字段。字段必须为ascii码，如果不是，请使用base64或者QP 进行编码。
	 * @param string $key 邮件头中扩展字段名称, 例如："X-SendCloud"
	 * @param string $value 邮件头中扩展字段内容, 例如："SendCloud rocks"
	 * @return self \SendCloud\Message 对象
	 */
	public function addHeader($key, $value){
		$this->headers_list[$key]= $value;
		return $this;
	}

	/**
	 * 在邮件头中添加扩展字段, 和addHeader功能相同，用于批量添加。
	 * @param array $keyValuePairs 例如： array('X-Sendcloud', 'Sendcloud rocks')
	 * @return self \SendCloud\Message 对象
	 */
	public function setHeaders($keyValuePairs){
		$this->header_list = $keyValuePairs;
		
		return $this;
	}
	
	/**
	 * 取得邮件头部信息。
	 * @return array 邮件头部列表
	 */
	public function getHeaders(){
		return $this->headers_list;
	}
	
	//////////////////////////////////////////////////////////
	//X-SMTPAPI操作
	/////////////////////////////////////////////////////////
	/**
	 * 设置SmtpApiHeader array 形式表示
	 *
	 * @param array $keyValuePairs 如 array('to'=>array('example@sendcloud.com'))
	 * @return self \SendCloud\Message Message消息对象
	 */
	public function setXsmtpApiHeaderArray($keyValuePairs) {
		$this->xSmtpApiHeaderJsonArray = $keyValuePairs;
		
		if (isset($this->xSmtpApiHeaderJsonArray['to']) && count($this->xSmtpApiHeaderJsonArray['to']) > 0) {
			unset($this->recipients);
			$this->recipients[] = reset($this->xSmtpApiHeaderJsonArray['to']); // 设置第一个为接收者，跳过服务器必须有接收者的认证
			//TODO 检查保证to 和sub中array数组相等
		}
		// set X-SMTPAPI JSON
		if (count($this->xSmtpApiHeaderJsonArray) <= 0){
			$this->xSmtpApiJsonString = "{}";
		}

		$this->xSmtpApiJsonString = json_encode($this->xSmtpApiHeaderJsonArray, JSON_HEX_TAG | JSON_HEX_APOS
					 | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
		
		return $this;
	}
	
	/**
	 * 取得SmtpApiHeader Array
	 * @return SmtpApiHeader array的形式
	 */
	public function getXsmtpApiHeaderArray(){
		return $this->xSmtpApiHeaderJsonArray;
	}

	/**
	 * 设置X-SMTPAPI的JSON字符串。
	 * @param string $xSmtpApiJsonString JSON形式的xsmtpapi字段
	 * @return self \SendCloud\Message
	 */
	public function setXsmtpApiJsonString($xSmtpApiJsonString){
		// 进行简单的参数检查
		if (!isset($xSmtpApiJsonString) || trim($xSmtpApiJsonString) == false)return $this;
		
		$this->xSmtpApiHeaderJsonArray = (array)json_decode($xSmtpApiJsonString);
		if (isset($this->xSmtpApiHeaderJsonArray['to']) && count($this->xSmtpApiHeaderJsonArray['to']) > 0) {
			unset($this->recipients);
			$this->recipients[] = reset($this->xSmtpApiHeaderJsonArray['to']); // 设置第一个为接受者，跳过服务器必须有接受者的认证
			//TODO  检查保证to 和sub中array数组相等
		}

		$this->xSmtpApiJsonString = $xSmtpApiJsonString;
		return $this;
	}
	
	/**
	 * 获取X-SMTPAPI JSON字符串。
	 * @return string $xSmtpJsonString JSON形式的X-SMTPAPI字段
	 */
	public function getXsmtpApiJsonString(){
		return $this->xSmtpApiJsonString;
	}
}
