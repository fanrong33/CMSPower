/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50500
Source Host           : localhost:3306
Source Database       : 2013_cmspower2

Target Server Type    : MYSQL
Target Server Version : 50500
File Encoding         : 65001

Date: 2013-07-14 13:13:52
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `cmspower_admin`
-- ----------------------------
DROP TABLE IF EXISTS `cmspower_admin`;
CREATE TABLE `cmspower_admin` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cmspower_admin
-- ----------------------------

-- ----------------------------
-- Table structure for `cmspower_app`
-- ----------------------------
DROP TABLE IF EXISTS `cmspower_app`;
CREATE TABLE `cmspower_app` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT '应用英文名，系统唯一',
  `title` varchar(50) DEFAULT NULL COMMENT '应用名',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `code` int(10) DEFAULT NULL COMMENT '应用版本代码，整数',
  `version` varchar(20) DEFAULT NULL COMMENT '应用版本中文名',
  `update_time` int(10) DEFAULT NULL COMMENT '应用最后更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cmspower_app
-- ----------------------------
INSERT INTO `cmspower_app` VALUES ('1', 'CMSPower', 'CMSPower', '强大的云内容管理系统，云端应用直接下载安装', '2', '0.3.3', '1373698515');
INSERT INTO `cmspower_app` VALUES ('2', 'Module', '内容模块管理', '用于管理网站内容模块，为满足需求而设计展示', '3', '1.0.5', '1371390077');
INSERT INTO `cmspower_app` VALUES ('4', 'Channel', '栏目管理', '用于管理网站的核心栏目', '3', '1.2.1', '1371360858');
INSERT INTO `cmspower_app` VALUES ('5', 'Admin', '管理员管理', '维护网站后台的管理人员，为不同的管理人员分配不同的角色权限', '2', '1.1.2', '1369791411');
INSERT INTO `cmspower_app` VALUES ('8', 'SystemBase', '基础配置', '用于管理维护站点的系统配置信息', '1', '1.0.0', '1369733877');
INSERT INTO `cmspower_app` VALUES ('10', 'Role', '角色管理', '基于角色访问控制的系统安全策略，针对不同职责权限，定义不同的管理员角，依赖资源管理应用', '1', '1.2.1', '1369741591');
INSERT INTO `cmspower_app` VALUES ('11', 'Resource', '资源管理', '权限系统中，管理角色拥有的权限资源，没有资源权限拒绝访问，不在资源列表中的则为白名单', '1', '1.0.0', '1369788118');

-- ----------------------------
-- Table structure for `cmspower_channel`
-- ----------------------------
DROP TABLE IF EXISTS `cmspower_channel`;
CREATE TABLE `cmspower_channel` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(50) DEFAULT NULL COMMENT '栏目名（英文名）',
  `title` char(255) DEFAULT NULL COMMENT '栏目标题',
  `module_id` smallint(5) unsigned DEFAULT '0' COMMENT '栏目所属模块ID',
  `type` enum('1','2','3') DEFAULT '1' COMMENT '类型，1-栏目，2-单网页，3-外部链接',
  `pid` smallint(5) unsigned DEFAULT '0' COMMENT '上级栏目',
  `level` tinyint(1) DEFAULT '1' COMMENT '层级',
  `image_url` varchar(255) DEFAULT '' COMMENT '栏目图片',
  `url` varchar(255) DEFAULT '' COMMENT '外部链接',
  `is_nav` enum('0','1') DEFAULT '0' COMMENT '是否在导航显示',
  `orderid` int(10) unsigned DEFAULT '999999' COMMENT '排序',
  `setting` mediumtext COMMENT '设置',
  `create_time` int(10) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of cmspower_channel
-- ----------------------------

-- ----------------------------
-- Table structure for `cmspower_file`
-- ----------------------------
DROP TABLE IF EXISTS `cmspower_file`;
CREATE TABLE `cmspower_file` (
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
-- Records of cmspower_file
-- ----------------------------

-- ----------------------------
-- Table structure for `cmspower_module`
-- ----------------------------
DROP TABLE IF EXISTS `cmspower_module`;
CREATE TABLE `cmspower_module` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(255) DEFAULT NULL COMMENT '模块name，ArticleAction对应Article',
  `title` char(255) DEFAULT NULL COMMENT '模块标题，中文名称',
  `description` varchar(255) DEFAULT NULL COMMENT '模块功能描述',
  `code` int(10) DEFAULT NULL COMMENT '模块版本代码，整数',
  `version` varchar(20) DEFAULT NULL COMMENT '模块版本中文名',
  `update_time` int(10) DEFAULT NULL COMMENT '模块最后更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cmspower_module
-- ----------------------------
INSERT INTO `cmspower_module` VALUES ('1', 'Empty', '空模型', '利用空模块对系统设置URL的优化', '2', '1.0.1', '1369920291');
INSERT INTO `cmspower_module` VALUES ('2', 'Page', '单网页模型', '用于维护单页内容和展示单页内容', '1', '1.0.0', '1371387527');

-- ----------------------------
-- Table structure for `cmspower_nav`
-- ----------------------------
DROP TABLE IF EXISTS `cmspower_nav`;
CREATE TABLE `cmspower_nav` (
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
) ENGINE=MyISAM AUTO_INCREMENT=227 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of cmspower_nav
-- ----------------------------
INSERT INTO `cmspower_nav` VALUES ('2', '0', 'navbar', 'develop', '开发', '&#x32;', '0', '6', '0');
INSERT INTO `cmspower_nav` VALUES ('6', '2', 'header', '', '开发 DEVELOP', '', '0', '1', '0');
INSERT INTO `cmspower_nav` VALUES ('7', '2', 'nav', 'App', '应用管理', '', '0', '2', '0');
INSERT INTO `cmspower_nav` VALUES ('162', '0', 'navbar', 'Content', '内容', '&#x2f;', '0', '2', '1370402234');
INSERT INTO `cmspower_nav` VALUES ('219', '162', 'nav', 'Channel', '栏目管理', '', '0', '999999', '1373771228');
INSERT INTO `cmspower_nav` VALUES ('164', '162', 'header', '', '内容 CONTENT', '', '0', '1', '1370402316');
INSERT INTO `cmspower_nav` VALUES ('165', '162', 'divider', '', '', '', '0', '2', '1370402318');
INSERT INTO `cmspower_nav` VALUES ('217', '2', 'nav', 'Module', '内容模块管理', '', '0', '999999', '1373770196');
INSERT INTO `cmspower_nav` VALUES ('173', '0', 'navbar', 'Member', '用户', '&#x2e;', '0', '3', '1370402641');
INSERT INTO `cmspower_nav` VALUES ('174', '173', 'header', '', '用户 MEMBER', '', '0', '1', '1370402654');
INSERT INTO `cmspower_nav` VALUES ('176', '173', 'divider', '', '', '', '0', '2', '1370402728');
INSERT INTO `cmspower_nav` VALUES ('225', '173', 'nav', 'Role', '角色管理', '', '0', '5', '1373772769');
INSERT INTO `cmspower_nav` VALUES ('180', '0', 'navbar', 'System', '系统', '&#x30;', '0', '5', '1370402995');
INSERT INTO `cmspower_nav` VALUES ('223', '180', 'nav', 'SystemBase', '基础配置', '', '0', '2', '1373772706');
INSERT INTO `cmspower_nav` VALUES ('182', '180', 'header', '', '系统管理 SYSTEM', '', '0', '1', '1370403024');
INSERT INTO `cmspower_nav` VALUES ('183', '180', 'divider', '', '', '', '0', '3', '1370403048');
INSERT INTO `cmspower_nav` VALUES ('199', '162', 'header', '', '内容设置 CONTENT SETTING', '', '0', '3', '1371352974');
INSERT INTO `cmspower_nav` VALUES ('200', '173', 'header', '', '管理员 ADMINISTRATOR', '', '0', '3', '1371354103');
INSERT INTO `cmspower_nav` VALUES ('220', '173', 'nav', 'Admin', '管理员管理', '', '0', '4', '1373772273');
INSERT INTO `cmspower_nav` VALUES ('226', '173', 'nav', 'Resource', '资源管理', '', '0', '6', '1373772775');

-- ----------------------------
-- Table structure for `cmspower_page`
-- ----------------------------
DROP TABLE IF EXISTS `cmspower_page`;
CREATE TABLE `cmspower_page` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `channel_id` int(11) unsigned DEFAULT NULL COMMENT '栏目ID',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '单页标题',
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '内容',
  `pv` int(11) unsigned DEFAULT '0' COMMENT '阅读量 pageviews',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cmspower_page
-- ----------------------------

-- ----------------------------
-- Table structure for `cmspower_resource`
-- ----------------------------
DROP TABLE IF EXISTS `cmspower_resource`;
CREATE TABLE `cmspower_resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '资源名称',
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '控制器模型',
  `methods` varchar(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '控制器方法',
  `orderid` int(11) DEFAULT '999999' COMMENT '排序',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cmspower_resource
-- ----------------------------
INSERT INTO `cmspower_resource` VALUES ('2', 'Member', '用户管理', 'index:查询用户', '999999', '1364649061');
INSERT INTO `cmspower_resource` VALUES ('3', 'Admin', '管理员管理', 'index:查询管理员|add:添加管理员|edit:编辑管理员|delete:删除管理员', '999999', '1364877572');
INSERT INTO `cmspower_resource` VALUES ('4', 'Role', '权限管理', 'index:查询角色|add:添加角色|edit:编辑角色|delete:删除角色', '999999', '1364883982');
INSERT INTO `cmspower_resource` VALUES ('5', 'Channel', '栏目管理', 'index:查询栏目', '999999', '1365074754');

-- ----------------------------
-- Table structure for `cmspower_role`
-- ----------------------------
DROP TABLE IF EXISTS `cmspower_role`;
CREATE TABLE `cmspower_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '角色名称',
  `description` varchar(255) DEFAULT '' COMMENT '角色描述',
  `type` enum('1','2','3') DEFAULT '3' COMMENT '角色类型，1-为超级管理员，2-为FORBIDDEN，3-为普通管理员',
  `resources` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '资源',
  `channels` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '栏目',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cmspower_role
-- ----------------------------
INSERT INTO `cmspower_role` VALUES ('7', '人力资源经理', '', '3', 'a:4:{s:7:\"Channel\";a:1:{s:5:\"index\";a:1:{s:9:\"has_right\";s:1:\"1\";}}s:6:\"Member\";a:1:{s:5:\"index\";a:1:{s:9:\"has_right\";s:1:\"1\";}}s:5:\"Admin\";a:4:{s:5:\"index\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:3:\"add\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:4:\"edit\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:6:\"delete\";a:1:{s:9:\"has_right\";s:1:\"1\";}}s:4:\"Role\";a:4:{s:5:\"index\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:3:\"add\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:4:\"edit\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:6:\"delete\";a:1:{s:9:\"has_right\";s:1:\"1\";}}}', 'a:2:{i:5;a:1:{s:9:\"has_right\";s:1:\"1\";}i:6;a:1:{s:9:\"has_right\";s:1:\"1\";}}', '1369742633');
INSERT INTO `cmspower_role` VALUES ('1', '超级管理员', '拥有所有后台权限', '1', null, null, '1364453446');
INSERT INTO `cmspower_role` VALUES ('2', '禁止访问', '禁止用户访问后台所有功能', '2', null, null, '1364453446');

-- ----------------------------
-- Table structure for `cmspower_system`
-- ----------------------------
DROP TABLE IF EXISTS `cmspower_system`;
CREATE TABLE `cmspower_system` (
  `type` varchar(30) NOT NULL,
  `key` varchar(30) NOT NULL,
  `value` text,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`type`,`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cmspower_system
-- ----------------------------
INSERT INTO `cmspower_system` VALUES ('base', 'copyright', '©2013 厦门尚科网络科技有限责任公司', '版权信息');
INSERT INTO `cmspower_system` VALUES ('base', 'site_name', 'CMSPower', '网站名称');
INSERT INTO `cmspower_system` VALUES ('base', 'site_keywords', 'CMSPower内容管理系统', '关键词');
INSERT INTO `cmspower_system` VALUES ('base', 'site_description', '强大的云内容管理系统，云端应用直接下载安装', '描述');
