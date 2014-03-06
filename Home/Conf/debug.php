<?php
/**
 * 本地开发环境配置
 */

if (!defined('THINK_PATH')) exit();
return  array(

	/* 数据库设置 */
    'DB_TYPE'               => 'mysql',     	// 数据库类型
	'DB_HOST'               => 'localhost', 	// 服务器地址
	'DB_NAME'               => '2014_cmspower', // 数据库名
	'DB_USER'               => 'root', 	// 用户名
	'DB_PWD'                => 'root',    	// 密码
	'DB_PORT'               => 3306,        	// 端口
	'DB_PREFIX'             => 't_',    		// 数据库表前缀
	'DB_SUFFIX'             => '',          	// 数据库表后缀

	/* Memcache设置 */
    'MEMCACHE_HOST'			=> '127.0.0.1',	// memcache服务的IP地址
    'MEMCACHE_PORT'			=> 11211,		// memcache服务的端口


	/********* 本地开发环境配置 *********/
	
    'LOG_RECORD'			=>	true, 	// 开启日志记录
    'LOG_EXCEPTION_RECORD'  => 	true,   // 记录异常信息日志
    'LOG_LEVEL'       		=>  'EMERG,ALERT,CRIT,ERR,WARN,NOTIC,DEBUG,SQL',  // 允许记录的日志级别
    'DB_SQL_LOG'			=>	true, 	// 记录SQL信息
    'DB_FIELDS_CACHE'		=> 	false, 	// 关闭字段缓存信息
    'APP_FILE_CASE'  		=>  true, 	// 检查文件的大小写 对Windows平台有效
    'TMPL_CACHE_ON'    		=> 	false,  // 关闭模板编译缓存,设为false则每次都会重新编译 TODO!!!
    'TMPL_STRIP_SPACE'      => 	false,  // 关闭去除模板文件里面的html空格与换行 TODO!!!
//    'TMPL_EXCEPTION_FILE'	=>  TMPL_PATH.'Public/think_exception.tpl',
    'SHOW_ERROR_MSG'        => 	true,   // 显示错误信息
);
?>