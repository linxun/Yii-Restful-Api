<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/admin.css" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">

    <div id="topbar">
        <ul>
            <?php if($this->loginUser):?>
            <li>欢迎您：<?php h($this->loginUser->userName);?><a href="/admin/site/logout">Logout</a></li>
            <?php endif; ?>
        </ul>
    </div>


	<div id="mainmenu">
        <ul id="yw0">
            <li>
                <dl>
                    <dt>用户管理</dt>
                    <dd>
                        <ul>
                            <li><a href="/admin/user/list">用户管理</a></li>
                        </ul>
                    </dd>
                </dl>
                <a href="#"></a>
            </li>
            <li>
                <dl>
                    <dt>内容管理</dt>
                    <dd>
                        <ul>
                            <li><a href="#">feed管理</a></li>
                            <li><a href="#">图片管理</a></li>
                            <li><a href="#">语音管理</a></li>
                            <li><a href="#">关键词管理</a></li>
                        </ul>
                    </dd>
                </dl>
            </li>
            <li>
                <dl>
                    <dt>用户反馈</dt>
                    <dd>
                        <ul>
                            <li><a href="#">用户反馈</a></li>
                        </ul>
                    </dd>
                </dl>
                <a href="#"></a>
            </li>
            <li>
                <dl>
                    <dt>数据统计</dt>
                    <dd>
                        <ul>
                            <li><a href="/admin/stat/daily">日报表</a></li>
                        </ul>
                    </dd>
                </dl>
                <a href="#"></a>
            </li>
        </ul>
	</div><!-- mainmenu -->


	<?php echo $content; ?>

	<div class="clear"></div>

	<div id="footer">
		Copyright &copy; <?php echo date('Y'); ?> by 享乐.<br/>
		All Rights Reserved.<br/>
		<?php echo Yii::powered(); ?>
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>
