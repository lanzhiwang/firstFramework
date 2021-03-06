<?php

/**
 * 基本类
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Base
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=framework&type=license
 */

namespace sy;
use \sy\base\SYException;

//将系统异常封装为自有异常
set_exception_handler(function ($e) {
	@header('Content-Type:text/html; charset=utf-8');
	if ($e instanceof SYException) {
		echo $e;
	} else {
		$e = new SYException($e->getMessage(), '10000');
		echo $e;
	}
	exit;
});

class BaseSY {
	/*
	 * D:/wamp/www/SYFramework/framework/
	 */
	public static $frameworkDir;

	/*
	 * D:/wamp/www/SYFramework/
	 */
	public static $rootDir;

	/*
	 * /SYFramework/
	 */
	public static $siteDir;

	/*
	 * D:/wamp/www/
	 */
	public static $webrootDir;

	/*
	 * 所有的配置选项
	 * $app = $config;
	 */
	public static $app;

	/*
	 * D:/wamp/www/SYFramework/application/
	 */
	public static $appDir;

	/*
	 * static::$mimeTypes = require(static::$frameworkDir . 'data/mimeTypes.php');
	 */
	public static $mimeTypes = NULL;

	/*
	 * static::$httpStatus = require(static::$frameworkDir . 'data/httpStatus.php');
	 */
	public static $httpStatus = NULL;

	/*
	 * 路由参数名称
	 */
	public static $routeParam = 'r';

	/*
	 * 调试模式
	 * static::$debug = $config['debug'];
	 */
	public static $debug = TRUE;

	/**
	 * 初始化：创建Application
	 * @param null $config
	 * @throws SYException
	 */
	public static function createApplication($config = NULL) {
		if ($config === NULL) {
			throw new SYException('Configuration is required', '10001');
		} elseif (is_string($config)) {
			if (is_file($config)) {
				$config = require($config);
			} else {
				throw new SYException('Config file ' . $config . ' not exists', '10002');
			}
		} elseif (!is_array($config)) {
			throw new SYException('Config can not be recognised', '10003');
		}

		//框架所在的绝对路径
		/*
		 * echo __DIR__;// D:\wamp\www\SYFramework\framework
		 * echo str_replace('\\', '/', __DIR__ );// D:/wamp/www/SYFramework/framework
		 * echo rtrim(str_replace('\\', '/', __DIR__ ), '/');// D:/wamp/www/SYFramework/framework
		 * echo rtrim(str_replace('\\', '/', __DIR__ ), '/') . '/'; //D:/wamp/www/SYFramework/framework/
		*/
		static::$frameworkDir =  rtrim(str_replace('\\', '/', __DIR__ ), '/') . '/';
		/*
		 * echo realpath(static::$frameworkDir . '../');// D:\wamp\www\SYFramework
		 * echo str_replace('\\', '/', realpath(static::$frameworkDir . '../')) . '/';// D:/wamp/www/SYFramework/
		 */
		static::$rootDir = str_replace('\\', '/', realpath(static::$frameworkDir . '../')) . '/';

		//程序相对网站根目录所在
		/*
		 * echo $_SERVER['PHP_SELF'];
		 * echo dirname($now);
		 *
		 * http://localhost/SYFramework/index.php -> /SYFramework/index.php -> /SYFramework
		 *
		 * http://localhost/SYFramework/index.php#start -> /SYFramework/index.php -> /SYFramework
		 *
		 * http://localhost/SYFramework/index.php?r=doc/start&title=HelloWorld -> /SYFramework/index.php -> /SYFramework
		 */
		$now = $_SERVER['PHP_SELF'];
		$dir = str_replace('\\', '/', dirname($now));// /SYFramework
		$dir !== '/' && $dir = rtrim($dir, '/') . '/';
		static::$siteDir = $dir;// /SYFramework/
		//网站根目录
		static::$webrootDir = substr(static::$rootDir, 0, strlen(static::$rootDir) - strlen(static::$siteDir)) . '/';// D:/wamp/www/

		//基本信息

		//echo $config['cookie']['path'] . "\n";// @app/
		$config['cookie']['path'] = str_replace('@app/', $dir, $config['cookie']['path']);
		//echo $config['cookie']['path'];// /SYFramework/
		static::$app = $config;

		//应用的绝对路径
		static::$appDir = str_replace('\\', '/', realpath(static::$frameworkDir . $config['dir'])) . '/';// D:/wamp/www/SYFramework/application/
		if (isset($config['debug'])) {
			static::$debug = $config['debug'];
		}

		/*
		 * mb_internal_encoding — 设置/获取内部字符编码
		 */
		mb_internal_encoding($config['charset']);
		//是否启用CSRF验证
		if ($config['csrf']) {
			\sy\lib\YSecurity::csrfSetCookie();
		}
		//加载App的基本函数
		if (is_file(static::$appDir . 'common.php')) {
			require(static::$appDir . 'common.php');
		}
		//开始路由分发
		static::router();
	}
	/**
	 * 报错：HTTP状态
	 * @access public
	 * @param string $status 状态码
	 * @param boolean $end 是否自动结束当前请求
	 */
	public static function httpStatus($status, $end = FALSE) {
		if (static::$httpStatus === NULL) {
			static::$httpStatus = require(static::$frameworkDir . 'data/httpStatus.php');
		}
		$version = ((isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.0') ? '1.0' : '1.1');
		if (isset(static::$httpStatus[$status])) {
			$statusText = static::$httpStatus[$status];
			@header("HTTP/$version $status $statusText");
		} else {
			@header("HTTP/$version $status");
		}
		if ($end) {
			echo isset($statusText) ? $statusText : $status . ' error';
			exit;
		}
	}
	/**
	 * 简单Router
	 * @access public
	 */
	public static function router() {
		/*
		 * http://localhost/SYFramework/index.php?r=doc/start&title=HelloWorld
		 */
		$r = trim($_GET[static::$routeParam]);// doc/start
		if (empty($r)) {
			$r = static::$app['defaultRouter'];// document/hello
		}
		$r = explode('/', $r);// Array ( [0] => document [1] => hello )
		if (count($r) !== 2) {
			static::httpStatus('404', TRUE);
		}
		list($controllerName, $actionName) = $r;
		//var_dump($controllerName, $actionName);// 'document' 'hello'

		//Alias路由表
		if (isset(static::$app['alias'][$controllerName])) {
			$controllerName = static::$app['alias'][$controllerName];
		}
		//controller列表
		if (!in_array($controllerName, static::$app['controller'], TRUE)) {
			static::httpStatus('404', TRUE);
		}
		$fileName = static::$appDir . 'controllers/' . $controllerName . '.php';
		$className = 'C' . ucfirst($controllerName);
		//echo $className;// CDocument
		if (!is_file($fileName)) {
			throw new SYException('Controller ' . $controllerName . ' not exists', '10004');
		}
		if (!class_exists($className, FALSE)) {
			require ($fileName);
		}
		//初始化Controller
		$controller = new $className;
		$actionName = 'action' . ucfirst($actionName);
		if (!method_exists($controller, $actionName)) {
			static::httpStatus('404', TRUE);
		}
		$controller->$actionName();
	}
	/**
	 * 自动加载类
	 * @access public
	 * @param string $className
	 */
	public static function autoload($className) {
		//判断是否为框架的class
		if (strpos($className, 'sy\\') === FALSE) {
			//是否为App自有class
			if (isset(static::$app['class'][$className])) {
				$fileName = str_replace('@app/', static::$appDir, static::$app['class'][$className]);
			} else {
				return;
			}
		} elseif (strpos($className, 'sy\\') === 0) {
			$fileName = substr($className, 3) . '.php';
			$fileName = static::$frameworkDir . str_replace('\\', '/', $fileName);
		} else {
			return;
		}
		if (is_file($fileName)) {
			require ($fileName);
		}
	}
	/**
	 * 创建URL
	 * @access public
	 * @param mixed $param URL参数
	 * @param string $ext 自定义扩展名
	 * @return string
	 */
	// createUrl(['document/start', 'title' => 'Router'])
	public static function createUrl($param = '', $ext = NULL) {
		$param = (array)$param;
		$router = $param[0];// document/start

		/*
		 * 增加锚点
		 */
		$anchor = isset($param['#']) ? '#' . $param['#'] : '';
		unset($param[static::$routeParam], $param['#']);
		//基本URL
		$url = ($_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
		if ($param[0] === '') {
			return $url . static::$siteDir;//http://localhost/SYFramework/
		}
		unset($param[0]);
		//Alias路由表
		list($controllerName, $actionName) = explode('/', $router);
		if (in_array($controllerName, static::$app['alias'], TRUE)) {
			$controllerName = array_search($controllerName, static::$app['alias']);
		}
		//是否启用了Rewrite
		if (static::$app['rewrite'] && isset(static::$app['rewriteRule'][$router])) {
			$url .= str_replace('@root/', static::$siteDir, static::$app['rewriteRule'][$router]);
			foreach ($param as $k => $v) {
				$k_tpl = '{{' . $k . '}}';
				if (strpos($url, $k_tpl) === FALSE) {
					continue;
				}
				$url = str_replace($k_tpl, urlencode($v), $url);
				//去掉此参数，防止后面http_build_query重复
				unset($param[$k]);
			}
		} elseif (static::$app['rewrite']) {
			// http://localhost/SYFramework/document/start.html
			$url .= static::$siteDir . $controllerName . '/' . $actionName . '.' . ($ext === NULL ? static::$app['rewriteExt'] : $ext);
		} else {
			// http://localhost/SYFramework/index.php?r=document/start
			$url .= static::$siteDir . 'index.php?r=' . $controllerName . '/' . $actionName;
		}
		if (count($param) > 0) {
			if (strpos($url, '?') === FALSE) {
				$url .= '?';
			} else {
				$url .= '&';
			}
			// http://localhost/SYFramework/document/start.html?title=Router
			$url .= http_build_query($param);
		}
		$url .= $anchor;
		return $url;
	}
	/**
	 * 发送Content-type的header，也就是mimeType
	 * @access public
	 * @param string $type 可为文件扩展名，或者Content-type的值
	 */
	// setMimeType('html')
	public static function setMimeType($type) {
		$mimeType = static::getMimeType($type);
		if ($mimeType === NULL) {
			$mimeType = $type;
		}
		$header = 'Content-type:' . $mimeType . ';';
		if (in_array($type, ['js', 'json', 'atom', 'rss', 'xhtml'], TRUE) || substr($mimeType, 0, 5) === 'text/') {
			$header .= ' charset=' . static::$app['charset'];
		}
		@header($header);
	}
	/**
	 * 获取扩展名对应的mimeType
	 * @access public
	 * @param string $ext
	 * @return string
	 */
	public static function getMimeType($ext) {
		if (static::$mimeTypes === NULL) {
			static::$mimeTypes = require(static::$frameworkDir . 'data/mimeTypes.php');
		}
		$ext = strtolower($ext);
		return isset(static::$mimeTypes[$ext]) ? (static::$mimeTypes[$ext]) : null;
	}

	/**
	 * @param $tpl
	 * @return string
	 */
	// viewPath('document/hello')
	public static function viewPath($tpl) {
		return static::$appDir . 'views/' . $tpl . '.php';
	}
	/**
	 * 引入模板
	 * @access public
	 * @param string $_tpl 模板文件
	 * @param array $_param 参数
	 */
	// view('document/hello', ['url' => $url_to_css]);
	// view('document/start/' . $_GET['title'])
	public static function view($_tpl, $_param = NULL) {
		//是否启用CSRF验证
		if (static::$app['csrf']) {
			$_csrf_token = \sy\lib\YSecurity::csrfGetHash();
		}
		if (is_array($_param)) {
			unset($_param['_tpl'], $_param['_csrf_token']);
			/*
			 * extract — 从数组中将变量导入到当前的符号表
			 *
			 * $url = $url_to_css
			 */
			extract($_param);
		}
		include (static::viewPath($_tpl));
	}
}
