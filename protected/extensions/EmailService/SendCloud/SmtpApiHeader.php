<?php
/**
 * 该类用于辅助生成<a href="http://sendcloud.sohu.com/sendcloud/api-doc/x-smtpapi">
 * X-SMTPAPI</a>的字符串。
 * @package SendCloud
 */
namespace SendCloud;

/**
 * <a href="http://sendcloud.sohu.com/sendcloud/api-doc/x-smtpapi">
 * X-SMTPAPI</a>的扩展字段操作类。用于生成的X-SMTPAPI JSON字符串。
 *
 * @author delong
 *
 */
class SmtpApiHeader {
	/** X-SMTPAPI字段的aary 形式 */
	private $xHeaderArray; 
	
	/**  X-SMTPAPI构造函数 */
	public function __construct() {
		$this->xHeaderArray = array();
	}

	/**
	 * 获取SmtpApiHeader 的Array形式表示对象，如果需要使用JSON形式，使用toJsonString() 方法.
	 * @return array 
	 */
	public function getSmtpApiHeaderArray() {
		return $this->xHeaderArray;
	}

	/**
	 * 添加接收者。
	 * @param string $address 接收者地址
	 */
	public function addRecipient($address) {
		if (!isset($this->xHeaderArray['to'])) {
			$this->xHeaderArray['to'] = array();
		}

		$this->xHeaderArray['to'][] = $address;
		return $this;
	}
	
	/**
	 * 获取X-SMTPAPI的to字段。
	 * @return array X-SMTPAPI的to字段array
	 */
	public function getSmtpApiRecipients() {
		if (!isset($this->xHeaderArray['to'])) {
			return array();
		}
		return $this->xHeaderArray['to'];
	}

	/**
	 * 添加多个接收者。 <b>注意，使用该方法时，将使得Message设置的收件人变得无效。</b>
	 * @param array $addresses e.g. array("example@sendcloud.com", "example1@sendcloud.com")
	 */
	public function addRecipients($addresses){
		if (!isset($this->xHeaderArray['to'])) {
			$this->xHeaderArray['to'] = array();
		}

		$this->xHeaderArray['to'] = array_merge($this->xHeaderArray['to'], (array)$addresses);

		return $this;
	}

	/**
	 * 设置邮件正文中占位符将要被替换的内容。 每一个占位符对应一组替换值,
	 * 每一个收件人按其在收件人数组中出现的位置使用替换值数组中相应位置的值进行替换。
	 * @param string $fromValue 例如： %code%
	 * @param array $toValues 例如： array("11", "12")
	 * @return self \SendCloud\SmtpApiHeader
	 */
	public function addSubstitution($fromValue, array $toValues) {
		$this->xHeaderArray['sub'][$fromValue] = $toValues;
		return $this;
	}

	/**
	 * 设置替换内容（之前的设置将会被覆盖）。
	 * @param array $keyValuePairs
	 * 			如： array("%code%" => array("11", "12"),
	 * 				%name% => array("examplename1", "examplename2"))
	 * @return self \SendCloud\SmtpApiHeader
	 */
	public function setSubstitutions(array $keyValuePairs) {
		$this->xHeaderArray['sub'] = $keyValuePairs;
		return $this;
	}

	/**
	 * 设置邮件正文中占位符将要被替换的内容。 每一个占位符对应一组替换值,
	 * 每一个收件人按其在收件人数组中出现的位置使用替换值数组中相应位置的值进行替换。
	 * 例如：
	 * <em>原来的替换内容为</em>
	 * array('sub' = >  array("%code%" => array("11", "22"),
	 * 				  %name% => array("examplename1", "examplename2")))
	 * 新增替换内容:
	 * array("%code%" => array("33", "44"), %name% => array("examplename3", "examplename4"))
	 * 替换内容变为：
	 * array('sub' = >  array("%code%" => array("11", "22", "33", "44"),
	 *  %name% => array("examplename1", "examplename2","examplename3", "examplename4")))
	 *
	 * @param array $keyValuePairs 例如： array("%code%" => array("33", "44"),
	 * 					%name% => array("examplename3", "examplename4"))
	 * @return self \SendCloud\SmtpApiHeader
	 */
	public function addSubstitutions(array $keyValuePairs) {
		if (!isset($this->xHeaderArray['sub'])) {
			$this->xHeaderArray['sub'] = array();
		}

		foreach ($keyValuePairs as $key => $value) {
			if (!isset($this->xHeaderArray['sub'][$key])) {
				$this->xHeaderArray['sub'][$key] = $value;
			}else{
				$this->xHeaderArray['sub'][$key] = array_merge($this->xHeaderArray['sub'][$key], $value);
			}
		}

		return $this;
	}

	/**
	 * 和addSubstitution一样, addSection也是用来设置邮件中占位符的将要被替换的内容。 和addSubstitution不同的是,
	 * addSection中用来替换占位符的文本是所有收件人都要 收到的大段相似文本, 仅仅在个性化设置方面有所不同。
	 * 使用addSection可以简化邮件的内容。
	 *
	 * 例如： 在{"section" =>
	 *            {"%greeting% => "%name%,欢迎使用SendCloud PHP SDK"}
	 *       }中, 邮件正文中的%greeting%在发给邮件接收人的时候, 会以"xx, 欢迎使用SendCloud PHP
	 *            SDK"的形式出现, 其中xx是收信人的称呼
	 *
	 * @param string $fromValue  占位符
	 * @param string toValue 替换占为符的文本.
	 * @return self \SendCloud\SmtpApiHeader
	 */
	public function addSection($fromValue, $toValue){
		$this->xHeaderArray['section'][$fromValue] = $toValues;
		return $this;
	}

	/**
	 * 和addSubstitution一样, addSection也是用来设置邮件中占位符的将要被替换的内容。和addSubstitution不同的是,
	 * addSection中用来替换占位符的文本是所有收件人都要 收到的大段相似文本, 仅仅在个性化设置方面有所不同。
	 * 使用addSection可以简化邮件的内容。
	 *
	 * 例如： 在array"section" =>
	 *            array（"%greeting% => "%name%,欢迎使用SendCloud PHP SDK"))
	 *     中, 邮件正文中的%greeting%在发给邮件接收人的时候, 会以"xx, 欢迎使用SendCloud PHP
	 *     SDK"的形式出现, 其中xx是收信人的称呼.
	 *
	 * @param array $keyValuePairs  例如： array("%greeting% => "%name%,欢迎使用SendCloud PHP SDK")
	 * @return self \SendCloud\SmtpApiHeader
	 */
	public function setSections($keyValuePairs){
		$this->xHeaderArray['section'] = $keyValuePairs;
		return $this;
	}

	/**
	 * 设定SendCloud中应用的运行配置。
	 * 例如：array("filters" => array("addHiddenImage" => array("settings" =>
	 *            array("enable" => "1")))中, ”addHiddenImages“是应用的名字, "enable"是设置该应用是否
	 *            运行的属性, "1"是enable所取的值, 表示处理该邮件时, “addHiddenImage”这个应用要开启。
	 *
	 * @param string $filterName  应用名字 例如：SendCloud\AppFilter::$ADD_UNSUBSCRIBE
	 * @param string $setting  应用的运行配置属性 例如： 'enable'
	 * @param string $value  app的运行配置属性的值 '1'表示启用，'0'表示关闭

	 */
	public function addFilterSetting($filterName, $setting, $value){
		$this->xHeaderArray['filters'][$filterName]['settings'][$setting] = $value;
		return $this;
	}

	/**
	 * 返回{@link http://sendcloud.sohu.com/v2/api-doc/smtp-api-extension.jsp X-SMTPAPI}
         * 的JSON形式。
	 * 	@return JSON形式的表示
	 */
	public function toJsonString(){
		if (count($this->xHeaderArray) <= 0){
			return "{}";
		}

		return json_encode($this->xHeaderArray, JSON_HEX_TAG | JSON_HEX_APOS
					 | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
	}
	
}
