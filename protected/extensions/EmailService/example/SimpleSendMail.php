<?php
include __dir__ . "/../SendCloudLoader.php"; // 导入SendCloud依赖
//include '/path/to/sendcloud_php/SendCloudLoader.php';或者 导入SendCloud依赖

try {
	// 设置脚本执行的最长时间，以免附件较大时，需要传输比较久的时间
	// Fatal error: Maximum execution time of 30 seconds exceeded
	// http://php.net/manual/en/function.set-time-limit.php
	// set_time_limit(300);
	
	$sendCloud = new SendCloud('username', 'password');
	$message = new SendCloud\Message();
	$message->addRecipient('to1@sendcloud.com') // 添加第一个收件人
			->addRecipients(array('to2@sendcloud.com', 'to3@sendcloud.com')) // 添加批量接受地址
			->setReplyTo('replyto@sendcloud.com') // 添加回复地址
			->addCc('cc@sendcloud.com') // 添加cc地址
			->addBcc('bcc@sendcloud.com') // 添加bcc地址
			->setFromName('搜狐SendCloud') // 添加发送者称呼
			->setFromAddress('from@sendcloud.com') // 添加发送者地址
			->setSubject('SendCloud PHP SDK测试')  // 邮件主题
			->setBody("<strong>SendCloud PHP SDK 测试正文，请参考</strong> <a href='http://sendcloud.sohu.com'>SendCloud</a>"); // 邮件正文html形式
	$message->setAltBody('SendCloud PHP SDK 测试正文，请参考');// 邮件正文纯文本形式，这个不是必须的。
	
	// 二进制或者字符串流附件
	// $data = file_get_contents(iconv('UTF-8', 'GBK', 'E:\path\SendCloud使用指南.pdf'));		
	//$message->addStringAttachment($data, 'SendCloud使用指南.pdf');
	// 普通附件
	$message->addAttachment('E:\path\SendCloud测试.xls')
			->addAttachment('E:\path\SendCloud测试.pdf', 'SendCloud测试--重命名.pdf');
	
	echo $sendCloud->send($message);
	print '<br>emailIdList:';
	print var_dump($sendCloud->getEmailIdList());// 取得emailId列表
} catch (Exception $e) {
		print "出现错误:";
		print $e->getMessage();
}