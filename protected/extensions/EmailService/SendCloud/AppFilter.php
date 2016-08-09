<?php
/**
 * SendCloud 可以设置的常量。
 * @package SendCloud
 */
namespace SendCloud;

/**
 * 该类提供了SendCloud可以设置的应用常量，参看
 * <a href="http://sendcloud.sohu.com/v2/api-doc/smtp-api-extension.jsp">
 * X-SMTPAPI</a>进行使用。
 * 
 * 定义的常量为取消订阅链接，增加隐藏图片跟踪邮件打开，进行URL替换跟踪邮件点击。
 *
 * @author delong
 */
class AppFilter
{
	/**
	 * 增加取消订阅应用。 
	 * 添加之后, 在接收者接收到的邮件正文下方将出现"取消订阅"的链接。
	 */
	public static $ADD_UNSUBSCRIBE = "subscription_tracking";

	/**
	 * 添加隐藏图片应用; 这个图片用于跟踪用户打开邮件的操作。
	 */
	public static $ADD_HIDDEN_IMAGE = "open_tracking";

	/**
	 * 增加替换邮件所有URL的应用, 用于跟踪用户点击操作。
	 * 添加这个应用后, 邮件中的所有链接将被替换。
	 */
	public static $PROCESS_URL_REPLACE = "click_tracking";

}
