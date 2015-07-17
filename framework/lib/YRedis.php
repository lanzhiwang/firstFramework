<?php

/**
 * Redis֧����
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Library
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=framework&type=license
 */

namespace sy\lib;
use Sy;
use \sy\base\SYDBException;

class YRedis {
	private $link = null;
	private $connect_info = null;
	static $_instance = null;
	static public function _i() {
		if (self::$_instance === null) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	/**
	 * ���캯�����Զ�����
	 * @access public
	 */
	public function __construct() {
		if (!class_exists('Redis', false)) {
			throw new SYException('������Redis��', '10007');
		}
		if (isset(Sy::$app['redis'])) {
			$this->setParam(Sy::$app['redis']);
		}
	}
	/**
	 * ���ӵ�Redis
	 * @access private
	 */
	private function connect() {
		$this->link = new Redis;
		$this->link->connect($this->connect_info['host'], $this->connect_info['port']);
	}
	/**
	 * ����Server
	 * @access public
	 * @param array $param Redis����������
	 */
	public function setParam($param) {
		$this->connect_info = $param;
		$this->link = null;
		$this->connect();
	}
	/**
	 * ����Key
	 * @access private
	 * @param string $sql
	 * @return string
	 */
	private function setQuery($key) {
		return $this->connect_info['prefix'] . $key;
	}
	/**
	 * Get
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		return $this->link->get($this->setQuery($key));
	}
	/**
	 * �����������Զ��ر�
	 * @access public
	 */
	public function __destruct() {
		if ($this->link !== null) {
			@$this->link->close();
		}
	}
}
