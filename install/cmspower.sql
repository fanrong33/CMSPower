/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50500
Source Host           : localhost:3306
Source Database       : 2014_test

Target Server Type    : MYSQL
Target Server Version : 50500
File Encoding         : 65001

Date: 2014-03-07 15:24:07
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `t_admin`
-- ----------------------------
DROP TABLE IF EXISTS `t_admin`;
CREATE TABLE `t_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL COMMENT '用户名',
  `password` varchar(32) DEFAULT NULL COMMENT '密码',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `role_id` int(11) unsigned DEFAULT NULL COMMENT '角色ID',
  `last_login_ip` char(15) DEFAULT '' COMMENT '上次登录IP',
  `last_login_time` int(10) DEFAULT '0' COMMENT '上次登录时间',
  `login_times` int(11) DEFAULT '0' COMMENT '登录次数',
  `is_lock` enum('0','1') DEFAULT '0' COMMENT '0为正常，1为禁用',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_admin
-- ----------------------------
INSERT INTO `t_admin` VALUES ('1', 'admin', '1f9ab63495d62739cd06c8412ba27c4b', '', '1', '', '0', '0', '0', '1394176959');

-- ----------------------------
-- Table structure for `t_app`
-- ----------------------------
DROP TABLE IF EXISTS `t_app`;
CREATE TABLE `t_app` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT '应用英文名，系统唯一',
  `title` varchar(50) DEFAULT NULL COMMENT '应用名',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `structure` text COMMENT '安装文件结构',
  `code` int(10) DEFAULT NULL COMMENT '应用版本代码，整数',
  `version` varchar(20) DEFAULT NULL COMMENT '应用版本中文名',
  `update_time` int(10) DEFAULT NULL COMMENT '应用最后更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=150 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_app
-- ----------------------------
INSERT INTO `t_app` VALUES ('78', 'Admin', '管理员管理', '维护网站后台的管理人员，为不同的管理人员分配不同的角色权限', 'Lib/Action/AdminAction.class.php\nLib/Model/AdminViewModel.class.php\nTpl/Admin/index.html\nTpl/Admin/edit.html\n[db]t_admin', '5', '1.1.5', '1393248485');
INSERT INTO `t_app` VALUES ('74', 'SystemBase', '基础配置', '用于管理维护站点的系统配置信息', 'Admin/Lib/Action/SystemAction.class.php\nAdmin/Lib/Model/SystemModel.class.php\nAdmin/Tpl/System/index.html\n[db]t_system', '2', '1.0.1', '1392617147');
INSERT INTO `t_app` VALUES ('75', 'Resource', '资源管理', '权限系统中，管理角色拥有的权限资源，没有资源权限拒绝访问，不在资源列表中的则为白名单', 'Lib/Action/ResourceAction.class.php\nTpl/Resource/index.html\nTpl/Resource/edit.html\n[db]t_resource', '3', '1.0.4', '1393234275');
INSERT INTO `t_app` VALUES ('73', 'UpdateCache', '更新缓存', '更新系统的缓存文件，默认删除Home、Admin、Api项目目录下的缓存文件，请根据项目需要进行修改', 'Lib/Action/UpdateCacheAction.class.php\nTpl/UpdateCache/index.html', '3', '1.0.3', '1392111609');
INSERT INTO `t_app` VALUES ('130', 'AboutUs', '关于我们', '展示系统信息和开发团队成员', 'Lib/Action/AboutUsAction.class.php\nTpl/AboutUs/index.html', '5', '1.0.4', '1392033719');
INSERT INTO `t_app` VALUES ('70', 'EditPassword', '修改个人密码', '对登录管理员的个人密码进行修改', ' Lib/Action/EditPassword.class.php\nTpl/EditPassword/index.html', '2', '1.0.1', '1392036606');
INSERT INTO `t_app` VALUES ('145', 'Category', '分类管理', '用于管理维护站点所有的分类信息', 'Lib/Action/CategoryAction.class.php\nTpl/Category/index.html\nTpl/Category/edit.html\nTpl/Category/tree.html', '6', '1.0.6', '1394014761');
INSERT INTO `t_app` VALUES ('146', 'Channel', '栏目管理', '用于管理网站的核心栏目\nTODO\n1、后台前端模板不知道哪个栏目有作为导航进行显示\n2、Channel依赖Module和Category模型，对开发者未做容错友好提示\n3、表单验证还不够完善', 'Lib/Action/ChannelAction.class.php\nTpl/Channel/index.html\nTpl/Channel/tree.html\nTpl/Channel/edit_1.html\nTpl/Channel/edit_2.html\nTpl/Channel/edit_3.html\n[db]t_channel', '5', '1.3.1', '1394075545');
INSERT INTO `t_app` VALUES ('149', 'Role', '角色管理', '基于角色访问控制的系统安全策略，针对不同职责权限，定义不同的管理员角，依赖资源管理应用', 'Lib/Action/RoleAction.class.php\nTpl/Role/index.html\nTpl/Role/edit.html\n[db]t_role', '4', '1.2.5', '1394173913');

-- ----------------------------
-- Table structure for `t_category`
-- ----------------------------
DROP TABLE IF EXISTS `t_category`;
CREATE TABLE `t_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(50) DEFAULT NULL COMMENT '分类英文名',
  `title` char(255) DEFAULT NULL COMMENT '分类名称',
  `pid` int(11) DEFAULT '0' COMMENT '父ID',
  `level` tinyint(2) DEFAULT '1' COMMENT '层级',
  `is_show` enum('0','1') DEFAULT '1' COMMENT '是否显示',
  `orderid` int(11) DEFAULT '0' COMMENT '排序',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_category
-- ----------------------------

-- ----------------------------
-- Table structure for `t_channel`
-- ----------------------------
DROP TABLE IF EXISTS `t_channel`;
CREATE TABLE `t_channel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(50) DEFAULT NULL COMMENT '栏目名（英文名）',
  `title` char(255) DEFAULT NULL COMMENT '栏目标题',
  `module_id` int(11) unsigned DEFAULT '0' COMMENT '栏目所属模块ID',
  `type` enum('1','2','3') DEFAULT '1' COMMENT '类型，1-栏目，2-单网页，3-外部链接',
  `pid` int(11) unsigned DEFAULT '0' COMMENT '上级栏目',
  `level` tinyint(1) DEFAULT '1' COMMENT '层级',
  `category_pid` int(11) DEFAULT '0' COMMENT '绑定分类，只对1-栏目类型有效，发布内容时可按该分类下的子分类归类',
  `image_url` varchar(255) DEFAULT '' COMMENT '栏目图片',
  `url` varchar(255) DEFAULT '' COMMENT '外部链接',
  `is_nav` enum('0','1') DEFAULT '0' COMMENT '是否在导航显示',
  `orderid` int(10) unsigned DEFAULT '0' COMMENT '排序',
  `setting` mediumtext COMMENT '设置',
  `update_time` int(10) DEFAULT '0' COMMENT '修改时间',
  `create_time` int(10) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of t_channel
-- ----------------------------

-- ----------------------------
-- Table structure for `t_file`
-- ----------------------------
DROP TABLE IF EXISTS `t_file`;
CREATE TABLE `t_file` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '上传文件原来的名字',
  `save_path` varchar(255) NOT NULL COMMENT '保存的目录',
  `save_name` varchar(255) DEFAULT NULL COMMENT '保存的文件名,按新的命名规则',
  `size` varchar(255) DEFAULT NULL COMMENT '文件字节数',
  `extension` varchar(20) DEFAULT NULL COMMENT '文件后缀',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_file
-- ----------------------------

-- ----------------------------
-- Table structure for `t_module`
-- ----------------------------
DROP TABLE IF EXISTS `t_module`;
CREATE TABLE `t_module` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(255) DEFAULT NULL COMMENT '模块name，ArticleAction对应Article',
  `title` char(255) DEFAULT NULL COMMENT '模块标题，中文名称',
  `description` varchar(255) DEFAULT NULL COMMENT '模块功能描述',
  `structure` text COMMENT '安装文件结构',
  `code` int(10) DEFAULT NULL COMMENT '模块版本代码，整数',
  `version` varchar(20) DEFAULT NULL COMMENT '模块版本中文名',
  `update_time` int(10) DEFAULT NULL COMMENT '模块最后更新时间',
  `orderid` int(10) DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_module
-- ----------------------------
INSERT INTO `t_module` VALUES ('7', 'Empty', '空模型', '利用空模块对系统设置URL的优化', null, '2', '1.0.1', '1369920291', '1');
INSERT INTO `t_module` VALUES ('12', 'Page', '单页模型', '用于维护单页内容和展示单页内容', 'Lib/Action/PageAction.class.php\nTpl/Page/index.html\n[db]t_page', '3', '1.0.2', '1394076087', '2');

-- ----------------------------
-- Table structure for `t_nav`
-- ----------------------------
DROP TABLE IF EXISTS `t_nav`;
CREATE TABLE `t_nav` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT '0',
  `type` varchar(10) DEFAULT NULL COMMENT '''header'',''nav'',''divider''',
  `name` varchar(50) DEFAULT '' COMMENT 'type为''nav''的,使用该name组装url',
  `title` varchar(50) DEFAULT '',
  `icon` varchar(50) DEFAULT '',
  `disabled` enum('0','1') DEFAULT '0',
  `orderid` int(11) DEFAULT NULL,
  `create_time` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=360 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of t_nav
-- ----------------------------
INSERT INTO `t_nav` VALUES ('2', '0', 'navbar', 'develop', '开发', '&#x32;', '0', '6', '0');
INSERT INTO `t_nav` VALUES ('6', '2', 'header', '', '开发 DEVELOP', '', '0', '1', '0');
INSERT INTO `t_nav` VALUES ('7', '2', 'nav', 'App', '应用管理', '', '0', '2', '0');
INSERT INTO `t_nav` VALUES ('162', '0', 'navbar', 'Content', '内容管理', '&#x2f;', '0', '2', '1370402234');
INSERT INTO `t_nav` VALUES ('353', '162', 'nav', 'Channel', '栏目管理', '', '0', '3', '1394075595');
INSERT INTO `t_nav` VALUES ('164', '162', 'header', '', '内容管理', '', '0', '1', '1370402316');
INSERT INTO `t_nav` VALUES ('173', '0', 'navbar', 'Member', '用户管理', '&#x2e;', '0', '4', '1370402641');
INSERT INTO `t_nav` VALUES ('359', '173', 'nav', 'Role', '角色管理', '', '0', '5', '1394174279');
INSERT INTO `t_nav` VALUES ('276', '173', 'nav', 'Resource', '资源管理', '', '0', '6', '1393234306');
INSERT INTO `t_nav` VALUES ('180', '0', 'navbar', 'NavbarSystem', '系统配置', '&#x30;', '0', '5', '1370402995');
INSERT INTO `t_nav` VALUES ('274', '180', 'nav', 'SystemBase', '基础配置', '', '0', '2', '1392617179');
INSERT INTO `t_nav` VALUES ('182', '180', 'header', '', '基本设置', '', '0', '1', '1370403024');
INSERT INTO `t_nav` VALUES ('186', '0', 'navbar', 'Dashboard', '管理首页', '&#x33;', '0', '1', '1370403218');
INSERT INTO `t_nav` VALUES ('199', '162', 'header', '', '内容设置', '', '0', '2', '1371352974');
INSERT INTO `t_nav` VALUES ('342', '173', 'header', '', '管理员管理', '', '0', '3', '1393898002');
INSERT INTO `t_nav` VALUES ('352', '162', 'nav', 'Category', '分类管理', '', '0', '5', '1394014855');
INSERT INTO `t_nav` VALUES ('218', '2', 'nav', 'Module', '内容模块管理', '', '0', '3', '1373779937');
INSERT INTO `t_nav` VALUES ('334', '186', 'nav', 'AboutUs', '关于我们', '', '0', '3', '1393401825');
INSERT INTO `t_nav` VALUES ('270', '186', 'nav', 'EditPassword', '修改个人密码', '', '0', '2', '1392036688');
INSERT INTO `t_nav` VALUES ('279', '173', 'nav', 'Admin', '管理员管理', '', '0', '4', '1393248522');
INSERT INTO `t_nav` VALUES ('273', '180', 'nav', 'UpdateCache', '更新缓存', '', '0', '4', '1392111274');
INSERT INTO `t_nav` VALUES ('243', '186', 'header', '', '管理首页', '', '0', '1', '1383663716');
INSERT INTO `t_nav` VALUES ('284', '180', 'header', '', '工具', '', '0', '3', '1393294502');

-- ----------------------------
-- Table structure for `t_page`
-- ----------------------------
DROP TABLE IF EXISTS `t_page`;
CREATE TABLE `t_page` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `channel_id` int(11) unsigned DEFAULT NULL COMMENT '栏目ID',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '单页标题',
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '内容',
  `pv` int(11) unsigned DEFAULT '0' COMMENT '阅读量 pageviews',
  `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_page
-- ----------------------------

-- ----------------------------
-- Table structure for `t_resource`
-- ----------------------------
DROP TABLE IF EXISTS `t_resource`;
CREATE TABLE `t_resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '资源名称',
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '控制器模型',
  `methods` varchar(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '控制器方法',
  `orderid` int(11) DEFAULT '0' COMMENT '排序',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_resource
-- ----------------------------
INSERT INTO `t_resource` VALUES ('1', 'AboutUs', '关于我们', 'index:关于我们', '0', '1394176370');
INSERT INTO `t_resource` VALUES ('2', 'EditPassword', '修改个人密码', 'index:修改个人密码', '0', '1394176379');
INSERT INTO `t_resource` VALUES ('3', 'Channel', '栏目管理', 'index:查询栏目|sort:保存排序|add:添加栏目|edit:修改栏目|delete:删除栏目', '0', '1394176415');
INSERT INTO `t_resource` VALUES ('4', 'Category', '分类管理', 'index:查询分类|sort:保存排序|add:添加分类|edit:修改分类|delete:删除分类', '0', '1394176433');
INSERT INTO `t_resource` VALUES ('5', 'Admin', '管理员管理', 'index:管理员管理|add:添加管理员|edit:修改管理员|delete:删除管理员', '0', '1394176448');
INSERT INTO `t_resource` VALUES ('6', 'Resource', '资源管理', 'index:查询资源|sort:保存排序|add:添加资源|edit:修改资源|delete:删除资源', '0', '1394176457');
INSERT INTO `t_resource` VALUES ('7', 'Role', '角色管理', 'index:角色管理|sort:保存排序|add:添加角色|edit:修改角色|delete:删除角色', '0', '1394176464');
INSERT INTO `t_resource` VALUES ('8', 'SystemBase', '基础配置', 'index:基础配置', '0', '1394176535');
INSERT INTO `t_resource` VALUES ('9', 'UpdateCache', '更新缓存', 'index:更新缓存', '0', '1394176558');

-- ----------------------------
-- Table structure for `t_role`
-- ----------------------------
DROP TABLE IF EXISTS `t_role`;
CREATE TABLE `t_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '角色名称',
  `description` varchar(255) DEFAULT '' COMMENT '角色描述',
  `type` enum('1','2','3') DEFAULT '3' COMMENT '角色类型，1-为超级管理员，2-为FORBIDDEN，3-为普通管理员',
  `resources` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '资源',
  `channels` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '栏目',
  `orderid` int(11) DEFAULT '0' COMMENT '排序',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_role
-- ----------------------------
INSERT INTO `t_role` VALUES ('1', '超级管理员', '拥有所有后台权限', '1', null, null, '0', '1364453446');
INSERT INTO `t_role` VALUES ('2', '禁止访问', '禁止用户访问后台所有功能', '2', null, null, '0', '1364453446');

-- ----------------------------
-- Table structure for `t_system`
-- ----------------------------
DROP TABLE IF EXISTS `t_system`;
CREATE TABLE `t_system` (
  `type` varchar(30) NOT NULL,
  `key` varchar(30) NOT NULL,
  `value` text,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`type`,`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_system
-- ----------------------------
INSERT INTO `t_system` VALUES ('base', 'site_name', 'CMSPower v2', '网站名称');
INSERT INTO `t_system` VALUES ('base', 'copyright', '©2013 CMSPower 版权所有', '版权信息');
INSERT INTO `t_system` VALUES ('base', 'tongji_code', '', '第三方统计代码');
INSERT INTO `t_system` VALUES ('base', 'site_keywords', '', '关键词');
INSERT INTO `t_system` VALUES ('base', 'site_description', '', '描述');
