<?php

/**
 * 应用设置
 * 
 * @author ShuangYa
 * @package Demo
 * @category Config
 * @link http://www.sylingd.com/
 */

return [

	/*
	 * BaseSY.php createApplication()
	 */

	//编码
	'charset' => 'utf-8',

	//Cookie相关
	'cookie' => [
		'prefix' => '',
		'expire' => 7200,
		'path' => '@app/',
		'domain' => $_SERVER['HTTP_HOST']
	],

	//是否默认开启CSRF验证
	'csrf' => FALSE,

	//App根目录，相对于framework目录
	'dir' => '../application/',

	//调试模式
	'debug' => TRUE,

	/*
	 * ============================================================================================
	 * BaseSY.php router()
	 */

	//默认的Router
	'defaultRouter' => 'document/hello',

	//虚拟路由表
	'alias' => [
		'doc' => 'document'
	],

	//Controller列表
	'controller' => [
		'document'
	],

	/*
	 * ============================================================================================
	 * BaseSY.php autoload()
	 */

	//会被Autoload加载的class列表
	'class' => [
		'demo\libs\option' => '@app/libs/option.php'
	],

	/*
	 * ============================================================================================
	 * BaseSY.php createUrl()
	 */

	//是否启用URL重写
	'rewrite' => FALSE,

	//自定义重写规则
	'rewriteRule' => [
		'article/view' => '@root/article/view/{{id}}.html',
		'article/list' => '@root/article/list/{{id}}-{{page}}.html',
		'user/view' => 'member/view-{{id}}.html'
	],

	//URL后缀，仅rewrite启用时有效
	'rewriteExt' => 'html',


	/*
	 * ============================================================================================
	 * 待定
	 */
	'appName' => 'Demo',

	//默认语言
	'language' => 'zh-CN',

	//加密Key，被YSecurity::securityCode使用
	'cookieKey' => 'test',

	//加密Key，被YSecurity::password使用
	'securityKey' => 'test',

	//Redis支持
	'redis' => [
		'host' => '127.0.0.1',
		'port' => '6379',
		'password' => '',
		'prefix' => 'pre_'
	],

	//MySQL支持
	'mysql' => [
		'host' => '127.0.0.1',
		'port' => '3306',
		'user' => 'root',
		'password' => 'root',
		'name' => 'test',
		'prefix' => 'pre_'
	],

	//SQLite支持
	'sqlite' => [
		'version' => 'sqlite3',
		'path' => '@app/data/db.sq3'
	]

];