<?php
/**
 * 注意:需要PHP 5.3版本或以上
 * 使用时只需要使用
 * include /pathto/sendcloud_php/SendCloudLoader.php即可。
 */

/** 定义目录为当前目录 */
define("ROOT_DIR", __dir__ . DIRECTORY_SEPARATOR);

/**
 * SendClou自动加载依赖类。
 * @author delong
 */
function sendCloudLoader() {
	/** PHP Mailer依赖 */
	require_once ROOT_DIR . '/lib/phpmailer/class.phpmailer.php';
	require_once ROOT_DIR . '/lib/phpmailer/class.smtp.php';
	require_once ROOT_DIR . '/lib/phpmailer/language/phpmailer.lang-zh_cn.php';

	/** SendCloud依赖 */
	require_once 'SendCloud.php';
	require_once 'SendCloud/Smtp.php';
	require_once 'SendCloud/Message.php';
	require_once 'SendCloud/AppFilter.php';
	require_once 'SendCloud/SmtpApiHeader.php';
}

spl_autoload_register("sendCloudLoader");