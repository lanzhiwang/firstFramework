<?php
use \sy\lib\YHtml;
use \demo\libs\option as YOp;
?>
<!DOCTYPE html>
<head>
	<title>Hello</title>
	<?=YHtml::meta()?>
	<?=YHtml::css('@root/public/css/bootstrap.min.css')?>
</head>
<body>
	<h1>Hello</h1>
	<p>欢迎来到SYFramework</p>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">目录</h3>
		</div>
		<div class="panel-body">
			<a href="#start">开始</a>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><a name="start"></a>开始</h3>
		</div>
		<div class="panel-body">
            <!--http://localhost/SYFramework/index.php?r=doc/start&title=HelloWorld-->
			<a href="<?=Sy::createUrl(['document/start', 'title' => 'HelloWorld'])?>">Hello World</a>
			<!--http://localhost/SYFramework/index.php?r=doc/start&title=Base-->
			<a href="<?=Sy::createUrl(['document/start', 'title' => 'Base'])?>">基本构架</a>
			<!--http://localhost/SYFramework/index.php?r=doc/start&title=Router-->
			<a href="<?=Sy::createUrl(['document/start', 'title' => 'Router'])?>">路由（Router）</a>
		</div>
	</div>
	<p><a href=""></a></p>
	<p><?=$url?></p>
	<p><?=YOp::_i()->set('I am libs')?></p>
	<p><?=YOp::_i()->get()?></p>
</body>