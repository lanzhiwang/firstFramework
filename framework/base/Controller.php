<?php

/**
 * Coltroller基本类
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Base
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=frameworkr&type=license
 */

namespace sy\base;
use Sy;
use \sy\base\SYException;

class Controller {
	protected $_m = [];
	/**
	 * 加载Model
	 * @access protected
	 * @param string $modelName
	 * @param string $loadAs
	 */
	//load_model('test', 't');
	protected function load_model($modelName, $loadAs) {
		//是否已经加载
		if (in_array($modelName, $this->_m, TRUE)) {
			return;
		}
		//load
		$appDir = Sy::$appDir;// D:/wamp/www/SYFramework/application/
		$fileName = $appDir . 'models/' . $modelName . '.php';
		if (!is_file($fileName)) {
			throw new SYException('Model ' . $fileName . ' not exists', '10010');
		}
		require ($fileName);
		$this->_m[] = $modelName;
		//Model名称
		$m_file = 'M' . ucfirst($modelName);
		$this->$loadAs = $m_file::_i();
	}
}
