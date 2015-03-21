<?php
/*
		'sitetitle' => array('title'=>'网站标题', 'type'=>'text'	, 'default'=>'百度一下，你就知道', 'description'=>''),
		'copyright'	=> array('title'=>'版权信息', 'type'=>'textarea', 'default'=>'©2013 copyright', 'description'=>'支持html和js代码 '),
		'sms_check' => array('title'=>'短信验证', 'type'=>'radio', 'value'=>array('1'=>'启用', '0'=>'禁用'), 'default'=>'1', 'description'=>''),
		'password'	=> array('title'=>'飞信密码', 'type'=>'password'),
*/
return array(
	'base'  => array(
		'site_name' => array('title'=>'网站名称', 'type'=>'text'	, 'default'=>'CMSPower', 'description'=>''),
		'site_keywords' => array('title'=>'关键词', 'type'=>'text'	, 'default'=>'', 'description'=>''),
		'site_description' => array('title'=>'描述', 'type'=>'textarea', 'default'=>'', 'description'=>''),
		'copyright'	=> array('title'=>'版权信息', 'type'=>'textarea', 'default'=>' ©2013 CMSPower 版权所有', 'description'=>'支持html和js代码'),
		'tongji_code' => array('title'=>'第三方统计代码', 'type'=>'textarea', 'default'=>'', 'description'=>'支持html和js代码'),
	),
	'mail' => array(
		'from'		=> array('title'=>'发件人名称', 'type'=>'text'	, 'default'=>'', 'description'=>''),
		'hostname'	=> array('title'=>'SMTP 服务器', 'type'=>'text'	, 'default'=>'', 'description'=>''),
		'port'		=> array('title'=>'SMTP 服务器端口', 'type'=>'text', 'default'=>'25', 'description'=>'默认25'),
		'username'	=> array('title'=>'SMTP 身份验证用户名', 'type'=>'text', 'default'=>'', 'description'=>''),
		'password'	=> array('title'=>'SMTP 身份验证密码', 'type'=>'password', 'default'=>'', 'description'=>''),
	),
	'sms' => array(
		'username'	=> array('title'=>'短信账号', 'type'=>'text'	, 'default'=>'', 'description'=>''),
		'password'	=> array('title'=>'短信密码', 'type'=>'password'	, 'default'=>'', 'description'=>''),
	),
	'alipay' => array(
		'partner'	=> array('title'=>'合作身份者ID', 'type'=>'text'	, 'default'=>'', 'description'=>'以2088开头的16位纯数字'),
		'key'		=> array('title'=>'安全检验码', 'type'=>'text'	, 'default'=>'', 'description'=>'以数字和字母组成的32位字符'),
		'seller_email' => array('title'=>'卖家支付宝帐户', 'type'=>'text'	, 'default'=>'', 'description'=>''),
	),
	'unionpay' => array(
		'key'		=> array('title'=>'安全检验码', 'type'=>'text'	, 'default'=>'', 'description'=>''),
		'merid'		=> array('title'=>'商户ID'	, 'type'=>'text'	, 'default'=>'', 'description'=>''),
		'merabbr'	=> array('title'=>'商户简称', 'type'=>'text'	, 'default'=>'', 'description'=>''),
	),
)
?>
