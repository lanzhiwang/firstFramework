<?php

/**
 * 入口文件
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Base
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=framework&type=license
 */

/*
 * 包含启动文件
 */
require (__DIR__ . '/framework/sy.php');

/*
 *  包含配置文件
 */
$config = __DIR__ . '/application/config.php';

/*
 * 启动文件中的启动方法
 * 包含应用文件并且执行其中的方法
 */
Sy::createApplication($config);
