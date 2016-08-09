<?php
$result = send_mail();
var_dump($result);

function send_mail() {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_URL, 'https://sendcloud.sohu.com/webapi/mail.send.json');
    //不同于登录SendCloud站点的帐号，您需要登录后台创建发信子帐号，使用子帐号和密码才可以进行邮件的发送。
    curl_setopt($ch, CURLOPT_POSTFIELDS,
    array('api_user' => 'postmaster@wode1.sendcloud.org',
    'api_key' => 'PCKkk2ax',
    'from' => 'linxun@xiangle.me',
    'fromname' => '我的',
    'to' => 'linxunsdut@163.com',
    'subject' => '注册新用户',
    'html' => '<p>尊敬的用户：</p><p>您好！</p><p>感谢您注册Wode，请点击以下链接完成注册 http://www.wode.im</p><p>&nbsp;</p><p>我的团队</p>
    ',
    //'file1' => '@/path/to/附件.png;filename=附件.png',
    //'file2' => '@/path/to/附件2.txt;filename=附件2.txt'
    ));

    $result = curl_exec($ch);

    if($result === false) //请求失败
    {
        echo 'last error : ' . curl_error($ch);
    }

    curl_close($ch);

    return $result;
}