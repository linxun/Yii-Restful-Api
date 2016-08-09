<?php
    include __dir__ . "/../SendCloudLoader.php"; // 导入SendCloud依赖
	//include '/path/to/sendcloud_php/SendCloudLoader.php';或者 导入SendCloud依赖
	// X-SMTPAPI 请参照 http://sendcloud.sohu.com/v2/api-doc/smtp-api-extension.jsp
	try {
		// 设置脚本执行的最长时间，以免附件较大时，需要传输比较久的时间
		// Fatal error: Maximum execution time of 30 seconds exceeded
		// http://php.net/manual/en/function.set-time-limit.php
		// set_time_limit(300);
		
		$sendCloud = new SendCloud('username', 'password');
// 		$sendCloud->setDebug(true); // 设置SMTP Debug, 默认关闭
// 		$sendCloud->setServer('smtp.qq.com',25); // 设置发送服务器，默认使用smtpcloud.sohu.com:25, 这样才会有X-SMTPAPI的功能

		// 使用SmtpApiHeader辅助类，生成X-SMTPAPI字段的json形式。
		$xSmtpApiHeader = new SendCloud\SmtpApiHeader();
		// 设置开启取消订阅，打开跟踪，点击链接跟踪
		$xSmtpApiHeader->addFilterSetting(SendCloud\AppFilter::$ADD_UNSUBSCRIBE, 'enable', '1') //取消订阅
						->addFilterSetting(SendCloud\AppFilter::$ADD_HIDDEN_IMAGE, 'enable', '1') //打开跟踪
						->addFilterSetting(SendCloud\AppFilter::$PROCESS_URL_REPLACE, 'enable', '1'); //点击链接跟踪
		
		// 设置接受者和相应的内容替换
		$recipients = array('to11@sendcloud.com', 'to2@sendcloud.com');
		$xSmtpApiHeader->addRecipients($recipients);
		$xSmtpApiHeader->addSubstitution('%name%', array("第一个接收者姓名", "第二个接收者姓名")) // 保证sub的替换内容和$recipients的个数相等
						->addSubstitution("%code%", array("第一个接收者代码", "第二个接收者代码")); // 保证sub的替换内容和$recipients的个数相等
		
		$message = new SendCloud\Message();
		$message->setXsmtpApiJsonString($xSmtpApiHeader->toJsonString()); // 设置X-SMTPAPI字符串
		//$message->setXsmtpApiHeaderArray($xSmtpApiHeader->getSmtpApiHeaderArray()); // 这种效果和上面一句相同
		$message->addHeader('header_test', 'header_test_value') // 头部必须是ascii码
			->setReplyTo('replyto@sendcloud.com') // 添加回复地址
			->addCc('cc@sendcloud.com') // 添加cc地址
			->addBcc('bcc@sendcloud.com') // 添加bcc地址
			->setFromName('搜狐SendCloud') // 添加发送者称呼
			->setFromAddress('from@sendcloud.com') // 添加发送者地址
			->setSubject('SendCloud PHP SDK测试')  // 邮件主题				
			->setBody("<strong>您好， %name%,您的代码是：%code%，SendCloud PHP SDK 测试正文，请参考</strong>
					 <a href='http://sendcloud.sohu.com'>SendCloud</a>"); // 邮件正文html形式
	
		echo $sendCloud->send($message);
		print '<br>'.var_dump($sendCloud->getEmailIdList());// 取得emailIdList
	} catch (Exception $e) {
		echo "出现错误:".$e->getMessage();
	}