<?php
/**
 * 默认服务器正式环境配置
 */
if (!defined('THINK_PATH')) exit();
return array(

	/* 数据库设置 */
    'DB_TYPE'               => 'mysql',     	// 数据库类型
	'DB_HOST'               => 'localhost', 	// 服务器地址
	'DB_NAME'               => '2014_cmspower', // 数据库名
	'DB_USER'               => 'root', 			// 用户名
	'DB_PWD'                => 'root',    	// 密码
	'DB_PORT'               => 3305,        	// 端口
	'DB_PREFIX'             => 't_',    		// 数据库表前缀
	'DB_SUFFIX'             => '',          	// 数据库表后缀

	/* 数据缓存设置 */
    'DATA_CACHE_TIME'       => 0,      		// 数据缓存有效期 0表示永久缓存
    'DATA_CACHE_COMPRESS'   => false,   	// 数据缓存是否压缩缓存
    'DATA_CACHE_CHECK'      => false,   	// 数据缓存是否校验缓存
    'DATA_CACHE_PREFIX'     => 'cmspower',	// 缓存前缀，不使用域名，否则可能被别人破解
    'DATA_CACHE_TYPE'       => 'Memcache',  // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator

	/* Memcache设置 */
    'MEMCACHE_HOST'			=> '127.0.0.1',	// memcache服务的IP地址
    'MEMCACHE_PORT'			=> 11211,		// memcache服务的端口
	
    'PAGE_LISTROWS'			=> 10,
    'PAGE_ROLLPAGE'			=> 5,
    
	/* URL设置 */
    'URL_CASE_INSENSITIVE'  => true,		// 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'             => 1,       	// URL访问模式，0-普通模式，1-PATHINFO模式，2-REWRITE模式
    'URL_HTML_SUFFIX'       => '.html',  	// URL伪静态后缀设置
    
    'VAR_PAGE'				=> 'p',
    
    
   	/********* 服务器正式环境配置 *********/
   	
	/* 日志设置 */
    'LOG_RECORD'            => false,   // 默认不记录日志
	/* 模板引擎设置 */
	'TMPL_CACHE_ON'    		=> true,  	// 开启模板编译缓存,设为false则每次都会重新编译
	/* 数据库设置 */
    'DB_SQL_LOG'			=> false, 	// 不记录SQL信息
    'DB_FIELDS_CACHE'		=> true, 	// 字段缓存信息
    /* 项目设定 */
    'APP_FILE_CASE'  		=> false, 	// 关闭检查文件的大小写 对Windows平台有效
);
?>