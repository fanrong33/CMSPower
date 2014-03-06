<?php
// 开启调试模式
define('APP_DEBUG', true); // 正式环境注释 TODO!!!

//定义项目名称和路径
define('APP_NAME'	, 'Admin');
define('APP_PATH'	, 'Admin/');

// 定义项目根路径
define('ROOT_PATH'	, rtrim(dirname(__FILE__), '/\\') . DIRECTORY_SEPARATOR);
define('THINK_PATH'	, 'ThinkPHP/');

// 定义资源路径
define('HTDOC_PATH'	, '/htdoc/');
define('CSS_PATH'	, '/htdoc/admin/images/');
define('IMG_PATH'	, '/htdoc/admin/images/');
define('JS_PATH'	, '/htdoc/admin/scripts/');

// 加载框架入口文件
require(THINK_PATH."ThinkPHP.php");
?>