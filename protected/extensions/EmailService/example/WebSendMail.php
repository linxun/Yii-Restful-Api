<?php
$api_user = $_POST["api_user"];
$api_key = $_POST["api_key"];
$to = $_POST["to"];
$from = $_POST["from"];
$fromname = $_POST["fromname"];
$replyto = $_POST["replyto"];
$cc = $_POST["cc"];
$bcc = $_POST["bcc"];
$subject = $_POST["subject"];
$html = $_POST["html"];
$text = $_POST["text"];
$x_smtpapi = $_POST["x-smtpapi"];

if ($_FILES["files1"]["error"] <= 0){
	$file1_name = $_FILES['files1']['name'];
	$tmp_name1 = $_FILES['files1']['tmp_name'];
}

if ($_FILES["files2"]["error"] <= 0){
	$file2_name = $_FILES['files2']['name'];
	$tmp_name2 = $_FILES['files2']['tmp_name'];
}
$tos = array();
$ccs = array();
$bccs = array();

if ($to !== ""){
	$tos = preg_split("/;/", $to);
}
if ($cc !== ""){
	$ccs = preg_split("/;/", $cc);
}
if ($bcc !== ""){
	$bccs = preg_split("/;/", $bcc);
}

// print var_dump($tos);
// print var_dump($ccs);
// print var_dump($bccs);

require_once __dir__ . "/../SendCloudLoader.php";

try {
	// 设置脚本执行的最长时间，以免附件较大时，需要传输比较久的时间
	// Fatal error: Maximum execution time of 30 seconds exceeded
	// http://php.net/manual/en/function.set-time-limit.php
	set_time_limit(300);
	
	$sendCloud = new SendCloud($api_user, $api_key);
	$sendCloud->setServer('smtpcloud.sohu.com', 25);
	// 		$sendCloud->setDebug(true);
	$message = new SendCloud\Message();
	$message->addRecipients($tos)
	->setReplyTo($replyto)
	->addCcs($ccs)
	->addBccs($bccs)
	->setFromName($fromname)
	->setFromAddress($from)
	->setSubject($subject)
	->setBody(stripslashes($html))
	->setAltBody($text);
	// attachment
	if (isset($file1_name)) {
		$message->addAttachment($tmp_name1, $file1_name);
	}
	if (isset($file2_name)) {
		$message->addAttachment($tmp_name2, $file2_name);
	}

	// X-SMTPAPI字段
	$message->setXsmtpApiJsonString($x_smtpapi);

	echo $sendCloud->send($message);
	print '<br>emailIdList:';
	print var_dump($sendCloud->getEmailIdList());// 取得emailId
} catch (Exception $e) {
	echo "出现错误:".$e->getMessage();
}

?>