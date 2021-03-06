<?php

/**
 * MySQLi支持类
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
use \mysqli;
use \sy\lib\YHtml;
use \sy\base\SYException;
use \sy\base\SYDBException;

class YMysqli {
	protected $dbtype = 'MySQL';
	protected $link = NULL;
	protected $connect_info = NULL;
	protected $result;
	static $_instance = NULL;
	static public function _i() {
		if (self::$_instance === NULL) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	/**
	 * 构造函数，自动连接
	 * @access public
	 */
	public function __construct() {
		if (!class_exists('mysqli', FALSE)) {
			throw new SYException('Class "MySQLi" is required', '10020');
		}
		if (isset(Sy::$app['mysql'])) {
			$this->setParam(Sy::$app['mysql']);
		}
	}
	/**
	 * 设置Server
	 * @access public
	 * @param array $param MySQL服务器参数
	 */
	public function setParam($param) {
		$this->connect_info = $param;
		$this->link = NULL;
		$this->connect();
	}
	/**
	 * 连接到MySQL
	 * @access protected
	 */
	protected function connect() {
		$this->link = new mysqli($this->connect_info['host'], $this->connect_info['user'], $this->connect_info['password'], $this->connect_info['name'], $this->connect_info['port']);
		if ($mysqli->connect_error) {
			throw new SYDBException(YHtml::encode($this->link->connect_error), $this->dbtype, 'NULL');
		}
		$this->link->set_charset(strtolower(str_replace('-', '', Sy::$app['charset'])));
	}
	/**
	 * 处理Key
	 * @access protected
	 * @param string $sql
	 * @return string
	 */
	protected function setQuery($sql) {
		return str_replace('#@__', $this->connect_info['prefix'], $sql);
	}
	/**
	 * 获取所有结果
	 * @access public
	 * @param string $key
	 * @return array
	 */
	public function getAll($key) {
		$rs = $this->result[$key];
		$rs = $rs->fetch_all(MYSQLI_ASSOC);
		return $rs;
	}
	/**
	 * 释放结果
	 * @access public
	 * @param string $key
	 */
	public function free($key) {
		$this->result[$key]->free();
		$this->result[$key] = NULL;
	}
	/**
	 * 获取最后产生的ID
	 * @access public
	 * @return int
	 */
	public function getLastId() {
		return intval($this->link->insert_id);
	}
	/**
	 * 获取一个结果
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function getArray($key) {
		if (!isset($this->result[$key]) || empty($this->result[$key])) {
			return NULL;
		}
		$rs = $this->result[$key];
		$rs = $rs->fetch_array(MYSQLI_ASSOC);
		return $rs;
	}
	/**
	 * 执行查询
	 * @access public
	 * @param string $key
	 * @param string $sql SQL语句
	 * @param array $data 参数
	 */
	public function query($key, $sql, $data = NULL) {
		$sql = $this->setQuery($sql);
		if (is_array($data)) {
			foreach ($data as $v) {
				$v = str_replace("'", "\\'", $v);
				$sql = str_replace('?', "'$v'", $sql, 1);
			}
		}
		$r = $this->link->query($sql);
		//执行失败
		if ($r !== TRUE) {
			throw new SYDBException(YHtml::encode($this->link->error), $this->dbtype, YHtml::encode($sql));
		}
		$this->result[$key] = $r;
	}
	/**
	 * 查询并返回一条结果
	 * @access public
	 * @param string $sql SQL语句
	 * @param array $data 参数
	 * @return array
	 */
	public function getOne($sql, $data = NULL) {
		if (!preg_match('/limit ([0-9,]+)$/', strtolower($sql))) {
			$sql .= ' LIMIT 0,1';
		}
		$this->query('one', $sql, $data);
		$r = $this->getArray('one');
		$this->free('one');
		return $r;
	}
	/**
	 * 事务：开始
	 * @access public
	 */
	public function beginTransaction() {
		$this->link->autocommit(FALSE);
	}
	/**
	 * 事务：添加一句
	 * @access public
	 * @param string $sql
	 */
	public function addOne($sql) {
		$this->link->query($this->setQuery($aql));
	}
	/**
	 * 事务：提交
	 * @access public
	 */
	public function commit() {
		$this->link->commit();
	}

	/**
	 * 事务：回滚
	 * @access public
	 */
	public function rollback() {
		$this->link->rollback();
	}
	/**
	 * 析构函数，自动关闭
	 * @access public
	 */
	public function __destruct() {
		if ($this->link !== NULL && method_exists($this->link, 'close')) {
			@$this->link->close();
		}
	}
}
