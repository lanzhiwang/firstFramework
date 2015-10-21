此框以泷涯的框架为基础仅用于学习目的

泷涯框架地址
https://git.oschina.net/sy/SYFramework.git

此项目基于[Apache License 2.0](http://opensource.org/licenses/Apache-2.0)开源

http://localhost/SYFramework/index.php

index.php
  |
config.php
sy.php -> spl_autoload_register(['Sy', 'autoload'], TRUE, TRUE);
  |
BaseSY.php -> createApplication($config = NULL)
                           |
           require(static::$appDir . 'common.php');
                   require ($fileName);
                           |
                $controller->$actionName();

http://localhost/SYFramework/index.php
http://localhost/SYFramework/index.php#start
http://localhost/SYFramework/index.php?r=doc/start&title=HelloWorld
http://localhost/SYFramework/index.php?r=doc/start&title=Base
http://localhost/SYFramework/index.php?r=doc/start&title=Router