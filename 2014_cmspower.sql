/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50500
Source Host           : localhost:3306
Source Database       : 2014_cmspower

Target Server Type    : MYSQL
Target Server Version : 50500
File Encoding         : 65001

Date: 2014-03-06 16:22:42
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `t_ad`
-- ----------------------------
DROP TABLE IF EXISTS `t_ad`;
CREATE TABLE `t_ad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '名称',
  `ad_position_id` int(11) DEFAULT NULL COMMENT '所属广告位ID',
  `image_url` varchar(255) DEFAULT NULL COMMENT '广告图片的地址',
  `image_url_file_id` int(11) DEFAULT NULL COMMENT '广告图片ID',
  `url` varchar(255) DEFAULT NULL COMMENT '链接地址',
  `start_date` date DEFAULT NULL COMMENT '投放开始时间',
  `end_date` date DEFAULT NULL COMMENT '结束时间',
  `is_show` enum('0','1') DEFAULT '1' COMMENT '是否显示,1-显示，0-不显示',
  `description` varchar(255) DEFAULT '' COMMENT '描述',
  `orderid` int(11) DEFAULT '999999' COMMENT '排序',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_ad
-- ----------------------------
INSERT INTO `t_ad` VALUES ('5', '2014MWC全球移动通信大会 PP助手直播专题', '1', 'uploads/sw/20140225/530c38b2a1291.jpg', '21', 'http://www.25pp.com/hotnews/19.html', '2014-02-25', null, '1', '', '1', '1393309904');
INSERT INTO `t_ad` VALUES ('6', 'PP一周最佳iOS游戏推荐', '1', 'uploads/sw/20140225/530c3914c046a.jpg', '22', 'http://www.25pp.com/news/news_56985.html', '2014-02-25', null, '1', '', '2', '1393309980');
INSERT INTO `t_ad` VALUES ('7', 'Crytek爽爆战术射击新作《精英战斗小组》', '1', 'uploads/sw/20140225/530c393516beb.jpg', '23', 'http://www.25pp.com/iphone/game/info_1231485.html', '2014-02-04', '2014-02-27', '1', '', '3', '1393310008');

-- ----------------------------
-- Table structure for `t_ad_position`
-- ----------------------------
DROP TABLE IF EXISTS `t_ad_position`;
CREATE TABLE `t_ad_position` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL COMMENT '广告位英文名，系统唯一',
  `title` varchar(50) DEFAULT NULL COMMENT '广告位名称',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `width` int(10) DEFAULT NULL COMMENT '图片宽度',
  `height` int(10) DEFAULT NULL COMMENT '图片高度',
  `orderid` int(11) DEFAULT '999999' COMMENT '排序',
  `create_time` int(10) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_ad_position
-- ----------------------------
INSERT INTO `t_ad_position` VALUES ('1', 'iphone_slide', 'iPhone图片卡盘', '', '700', '240', '0', '1363693255');
INSERT INTO `t_ad_position` VALUES ('2', 'ipad_slide', 'iPad图片卡盘', '', '700', '240', '0', '1363960990');

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
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_admin
-- ----------------------------
INSERT INTO `t_admin` VALUES ('1', 'admin', '1f9ab63495d62739cd06c8412ba27c4b', '', '1', '127.0.0.1', '1394090299', '418', '0', '1364457302');
INSERT INTO `t_admin` VALUES ('17', 'fanrong33', 'e34824c10676d67670f177c1f396ad46', '', '7', '', '0', '0', '0', '1393247591');

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
) ENGINE=MyISAM AUTO_INCREMENT=148 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_app
-- ----------------------------
INSERT INTO `t_app` VALUES ('1', 'CMSPower', 'CMSPower', '强大的云内容管理系统，云端应用直接下载安装', null, '8', '0.3.9', '1378962905');
INSERT INTO `t_app` VALUES ('78', 'Admin', '管理员管理', '维护网站后台的管理人员，为不同的管理人员分配不同的角色权限', 'Lib/Action/AdminAction.class.php\nLib/Model/AdminViewModel.class.php\nTpl/Admin/index.html\nTpl/Admin/edit.html\n[db]t_admin', '5', '1.1.5', '1393248485');
INSERT INTO `t_app` VALUES ('74', 'SystemBase', '基础配置', '用于管理维护站点的系统配置信息', 'Admin/Lib/Action/SystemAction.class.php\nAdmin/Lib/Model/SystemModel.class.php\nAdmin/Tpl/System/index.html\n[db]t_system', '2', '1.0.1', '1392617147');
INSERT INTO `t_app` VALUES ('76', 'Role', '角色管理', '基于角色访问控制的系统安全策略，针对不同职责权限，定义不同的管理员角，依赖资源管理应用', null, '2', '1.2.2', '1379169110');
INSERT INTO `t_app` VALUES ('75', 'Resource', '资源管理', '权限系统中，管理角色拥有的权限资源，没有资源权限拒绝访问，不在资源列表中的则为白名单', 'Lib/Action/ResourceAction.class.php\nTpl/Resource/index.html\nTpl/Resource/edit.html\n[db]t_resource', '3', '1.0.4', '1393234275');
INSERT INTO `t_app` VALUES ('77', 'LoginLog', '登录日志', '用于查询后台管理员最近的登录记录', 'Lib/Action/LoginLogAction.class.php\nLib/Model/LoginLogModel.class.php\nTpl/LoginLog/index.html\n[db]t_login_log', '3', '1.0.2', '1392109778');
INSERT INTO `t_app` VALUES ('73', 'UpdateCache', '更新缓存', '更新系统的缓存文件，默认删除Home、Admin、Api项目目录下的缓存文件，请根据项目需要进行修改', 'Lib/Action/UpdateCacheAction.class.php\nTpl/UpdateCache/index.html', '4', '1.0.4', '1392111609');
INSERT INTO `t_app` VALUES ('130', 'AboutUs', '关于我们', '展示系统信息和开发团队成员', 'Lib/Action/AboutUsAction.class.php\nTpl/AboutUs/index.html', '5', '1.0.4', '1392033719');
INSERT INTO `t_app` VALUES ('83', 'AdPosition', '广告位管理', '维护网站的广告位信息，包括广告位的英文名、模板文本、图片宽度和图片高度等信息', 'Lib/Action/AdPositionAction.class.php\nTpl/AdPosition/index.html\nTpl/AdPosition/edit.html\n[db]t_ad_position', '4', '1.1.3', '1393298134');
INSERT INTO `t_app` VALUES ('134', 'Link', '友情链接', '用于管理网站的友情链接', 'Lib/Action/LinkAction.class.php\nTpl/Link/index.html\nTpl/Link/edit.html\n[db]t_link', '5', '1.0.5', '1393415123');
INSERT INTO `t_app` VALUES ('70', 'EditPassword', '修改个人密码', '对登录管理员的个人密码进行修改', ' Lib/Action/EditPassword.class.php\nTpl/EditPassword/index.html', '2', '1.0.1', '1392036606');
INSERT INTO `t_app` VALUES ('147', 'MemcacheStat', 'memcache监控', '对memcache服务进行监控，目前只支持单memcache服务', 'Lib/Action/MemcacheStatAction.class.php\nTpl/MemcacheStat/index.html', '5', '1.1.2', '1393403240');
INSERT INTO `t_app` VALUES ('135', 'Sw', '广告管理', '用于管理网站不同广告位的广告，只需关心广告本身的标题、图片和链接，暂不支持文字广告，依赖广告位管理应用', 'Lib/Action/SwAction.class.php\nLib/Model/AdViewModel.class.php\nTpl/Sw/index.html\nTpl/Sw/edit.html\n[db]t_ad', '8', '1.5.1', '1393402251');
INSERT INTO `t_app` VALUES ('144', 'Member', '用户管理', '用于管理网站的注册用户', 'Lib/Action/MemberAction.class.php\nLib/ORG/Util/ExcelUtil.class.php\nTpl/Member/index.html\nTpl/Member/import.html\nTpl/Member/export.html\nTpl/Member/example.xls\n[db]t_member', '5', '1.0.5', '1393921388');
INSERT INTO `t_app` VALUES ('145', 'Category', '分类管理', '用于管理维护站点所有的分类信息', 'Lib/Action/CategoryAction.class.php\nTpl/Category/index.html\nTpl/Category/edit.html\nTpl/Category/tree.html', '6', '1.0.6', '1394014761');
INSERT INTO `t_app` VALUES ('146', 'Channel', '栏目管理', '用于管理网站的核心栏目\nTODO\n1、后台前端模板不知道哪个栏目有作为导航进行显示\n2、Channel依赖Module和Category模型，对开发者未做容错友好提示\n3、表单验证还不够完善', 'Lib/Action/ChannelAction.class.php\nTpl/Channel/index.html\nTpl/Channel/tree.html\nTpl/Channel/edit_1.html\nTpl/Channel/edit_2.html\nTpl/Channel/edit_3.html\n[db]t_channel', '5', '1.3.1', '1394075545');

-- ----------------------------
-- Table structure for `t_article`
-- ----------------------------
DROP TABLE IF EXISTS `t_article`;
CREATE TABLE `t_article` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `channel_id` int(11) unsigned DEFAULT NULL COMMENT '栏目ID',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '文章标题',
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '内容',
  `image_url` varchar(255) DEFAULT NULL COMMENT '文章图片地址',
  `image_url_file_id` int(11) DEFAULT NULL COMMENT '图片ID',
  `is_show` enum('0','1') DEFAULT '1' COMMENT '是否显示',
  `pv` int(11) unsigned DEFAULT '0' COMMENT '浏览量',
  `is_recommend` enum('0','1') DEFAULT '0' COMMENT '是否推荐，0-未推荐，1-推荐',
  `orderid` int(11) DEFAULT '0' COMMENT '排序',
  `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_article
-- ----------------------------
INSERT INTO `t_article` VALUES ('56', '13', '万众期待！回合制大型幻想RPG《碧蓝幻想》3月10日来袭', '<p>\n	　　曾在去年年底宣布上架的超大型幻想RPG<strong>《碧蓝幻想》(グランブルーファンタジー /Granblue Fantas)</strong>近日放出最新消息，该作将于3月10日上架Mobage 平台。\n</p>\n<p style=\"text-align:center;\">\n	<img alt=\"万众期待！回合制大型幻想RPG《碧蓝幻想》3月10日来袭\" src=\"http://img.25pp.com/uploadfile/news/2014/0306/20140306100846972.jpg\" style=\"height:241px;width:500px;\" />\n</p>\n<p>\n	　　该作由植松伸夫担任音乐监督，Cygames及CyDesignation共同制作开发。游戏的玩法与常见的RPG一样，有主线任务与支线任务两种。\n随着剧情与任务的进行，还会有新的伙伴加入与更多的任务出现。玩家通过战斗会获得各类报酬以及装备品，把各类装备品装到喜欢的角色上，可强化角色能力，玩\n家还可以让系统帮玩家选择装备，方便各位玩家选择。\n</p>\n<p style=\"text-align:center;\">\n	<img alt=\"万众期待！回合制大型幻想RPG《碧蓝幻想》3月10日来袭\" src=\"http://img.25pp.com/uploadfile/news/2014/0306/20140306100848434.jpg\" style=\"height:282px;width:500px;\" />\n</p>\n<p>\n	　　游戏战斗系统采用了回合制，玩家队伍最多6人，参与战斗最多4人，由于游戏正式版仍未发布，所以暂时不知道能不能在战斗途中换人。战斗画面敌方与我方\n分左右阵营，我方阵型在右方。玩家可以根据敌方的弱点进行突击喔！除了普通攻击，玩家还能使用“奥义”。刚刚说过的“星晶兽”，玩家可以利用召唤石召唤各\n式各样的星晶兽。每个角色都有自己的特有能力，只要在适合的时间使用特技，甚至可以一发逆转困局！\n</p>\n<p>\n	&nbsp;\n</p>\n<p>\n	　　在这个奇幻的世界中，我们的主人公(玩家)与一名拥有驾驭星之民的遗产“星晶兽”能力的少女露莉雅相遇了，露莉雅因自己的能力而被帝国的士兵追捕，看\n不过眼的主人公毅然选择了保护露莉雅，但是因为实力的差距而陷入了危机。这时露莉雅与主人公的灵魂产生了共鸣，奇迹发生了……摆脱了危机的露莉雅与主人\n公，还有为了露莉雅而背叛了帝国的女骑士卡塔丽娜一同行动，究竟主人公与露莉雅的命运会走向何方呢？\n</p>\n<br />', 'uploads/article/20140306/531818cb48afd.jpg', '46', '1', '0', '0', '0', '1394089448', '1394088144');

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
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_category
-- ----------------------------
INSERT INTO `t_category` VALUES ('1', 'games', '游戏资讯', '0', '1', '1', '1', '1394008448', '1394006494');
INSERT INTO `t_category` VALUES ('6', 'games1', '游戏评测', '1', '2', '1', '101', '1394008415', '1394007140');
INSERT INTO `t_category` VALUES ('3', 'games2', '游戏攻略', '1', '2', '1', '104', '1394008437', '1394006743');
INSERT INTO `t_category` VALUES ('4', 'games3', '新游预告', '1', '2', '1', '103', '1394008434', '1394006751');
INSERT INTO `t_category` VALUES ('5', 'games4', '游戏热点', '1', '2', '1', '102', '1394008431', '1394006767');

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
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of t_channel
-- ----------------------------
INSERT INTO `t_channel` VALUES ('4', 'about', '关于', '7', '1', '0', '1', '0', '', '', '0', '2', 'a:7:{s:8:\"tpl_mode\";s:1:\"1\";s:11:\"tpl_channel\";s:12:\"channel.html\";s:9:\"tpl_index\";s:10:\"index.html\";s:10:\"tpl_detail\";s:11:\"detail.html\";s:10:\"meta_title\";s:0:\"\";s:13:\"meta_keywords\";s:0:\"\";s:16:\"meta_description\";s:0:\"\";}', '1394154010', '1394069712');
INSERT INTO `t_channel` VALUES ('5', 'about_us', '关于我们', '12', '2', '4', '2', '0', '', '', '0', '201', 'a:4:{s:8:\"tpl_page\";s:9:\"page.html\";s:10:\"meta_title\";s:0:\"\";s:13:\"meta_keywords\";s:12:\"关于我们\";s:16:\"meta_description\";s:48:\"关于我们关于我们关于我们关于我们\";}', '1394155361', '1394069879');
INSERT INTO `t_channel` VALUES ('6', 'intro', '公司介绍', '12', '2', '4', '2', '0', '', '', '0', '202', 'a:4:{s:8:\"tpl_page\";s:9:\"page.html\";s:10:\"meta_title\";s:0:\"\";s:13:\"meta_keywords\";s:12:\"公司介绍\";s:16:\"meta_description\";s:36:\"公司介绍公司介绍公司介绍\";}', '1394155384', '1394069996');
INSERT INTO `t_channel` VALUES ('7', 'thinkphp', 'thinkphp', '0', '3', '0', '1', '0', '', 'http://www.thinkphp.cn', '1', '3', 'N;', '1394073784', '1394071536');
INSERT INTO `t_channel` VALUES ('12', 'games', '游戏资讯', '56', '1', '0', '1', '0', '', '', '1', '1', 'a:7:{s:8:\"tpl_mode\";s:1:\"2\";s:11:\"tpl_channel\";s:12:\"channel.html\";s:9:\"tpl_index\";s:10:\"index.html\";s:10:\"tpl_detail\";s:11:\"detail.html\";s:10:\"meta_title\";s:0:\"\";s:13:\"meta_keywords\";s:47:\"iphone游戏 ，iPhone5S游戏  、苹果游戏\";s:16:\"meta_description\";s:183:\"PP助手苹果资源站-提供最热门的iPhone游戏/iPhone软件/iPhone4/iPhone4s/iPhone5S游戏软件等。最好玩最好用的苹果手机游戏、iPhone软件任您免费下载\";}', '1394112081', '1394111983');
INSERT INTO `t_channel` VALUES ('14', 'games2', '游戏攻略', '56', '1', '12', '2', '0', '', '', '0', '102', 'a:7:{s:8:\"tpl_mode\";s:1:\"2\";s:11:\"tpl_channel\";s:12:\"channel.html\";s:9:\"tpl_index\";s:10:\"index.html\";s:10:\"tpl_detail\";s:11:\"detail.html\";s:10:\"meta_title\";s:0:\"\";s:13:\"meta_keywords\";s:0:\"\";s:16:\"meta_description\";s:0:\"\";}', '0', '1394069884');
INSERT INTO `t_channel` VALUES ('13', 'games1', '游戏评测', '56', '1', '12', '2', '0', '', '', '0', '101', 'a:7:{s:8:\"tpl_mode\";s:1:\"2\";s:11:\"tpl_channel\";s:12:\"channel.html\";s:9:\"tpl_index\";s:10:\"index.html\";s:10:\"tpl_detail\";s:11:\"detail.html\";s:10:\"meta_title\";s:0:\"\";s:13:\"meta_keywords\";s:0:\"\";s:16:\"meta_description\";s:0:\"\";}', '1394115008', '1394112043');
INSERT INTO `t_channel` VALUES ('15', 'games3', '新游预告', '56', '1', '12', '2', '0', '', '', '0', '103', 'a:7:{s:8:\"tpl_mode\";s:1:\"2\";s:11:\"tpl_channel\";s:12:\"channel.html\";s:9:\"tpl_index\";s:10:\"index.html\";s:10:\"tpl_detail\";s:11:\"detail.html\";s:10:\"meta_title\";s:0:\"\";s:13:\"meta_keywords\";s:0:\"\";s:16:\"meta_description\";s:0:\"\";}', '0', '1394069906');
INSERT INTO `t_channel` VALUES ('16', 'games4', '游戏热点', '56', '1', '12', '2', '0', '', '', '0', '104', 'a:7:{s:8:\"tpl_mode\";s:1:\"2\";s:11:\"tpl_channel\";s:12:\"channel.html\";s:9:\"tpl_index\";s:10:\"index.html\";s:10:\"tpl_detail\";s:11:\"detail.html\";s:10:\"meta_title\";s:0:\"\";s:13:\"meta_keywords\";s:0:\"\";s:16:\"meta_description\";s:0:\"\";}', '0', '1394069932');
INSERT INTO `t_channel` VALUES ('17', 'contact', '联系我们', '12', '2', '4', '2', '0', '', '', '0', '203', 'a:4:{s:8:\"tpl_page\";s:9:\"page.html\";s:10:\"meta_title\";s:0:\"\";s:13:\"meta_keywords\";s:0:\"\";s:16:\"meta_description\";s:0:\"\";}', '0', '1394069964');

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
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_file
-- ----------------------------
INSERT INTO `t_file` VALUES ('23', '20140224114226353.jpg', 'uploads/sw/20140225/', '530c393516beb.jpg', '195420', 'jpg', '1393310005');
INSERT INTO `t_file` VALUES ('22', '20140224030533741.jpg', 'uploads/sw/20140225/', '530c3914c046a.jpg', '80469', 'jpg', '1393309972');
INSERT INTO `t_file` VALUES ('21', '20140224115138120.jpg', 'uploads/sw/20140225/', '530c38b2a1291.jpg', '61749', 'jpg', '1393309874');
INSERT INTO `t_file` VALUES ('46', '351651651651651.jpg', 'uploads/article/20140306/', '531818cb48afd.jpg', '58424', 'jpg', '1394088139');

-- ----------------------------
-- Table structure for `t_link`
-- ----------------------------
DROP TABLE IF EXISTS `t_link`;
CREATE TABLE `t_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '名称',
  `url` varchar(255) DEFAULT NULL COMMENT '链接地址',
  `description` varchar(255) DEFAULT '' COMMENT '描述',
  `is_show` enum('0','1') DEFAULT '1' COMMENT '是否显示',
  `orderid` int(11) DEFAULT '0' COMMENT '排序',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_link
-- ----------------------------
INSERT INTO `t_link` VALUES ('1', '厦门尚科网络', 'http://www.suncco.com', '', '1', '0', '1393410013');
INSERT INTO `t_link` VALUES ('3', '百度', 'http://www.baidu.com', '', '1', '0', '1393414882');

-- ----------------------------
-- Table structure for `t_login_log`
-- ----------------------------
DROP TABLE IF EXISTS `t_login_log`;
CREATE TABLE `t_login_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `username` varchar(30) NOT NULL DEFAULT '' COMMENT '登录帐号',
  `login_time` int(10) NOT NULL COMMENT '登录时间',
  `login_ip` varchar(20) NOT NULL DEFAULT '' COMMENT '登录IP',
  `status` enum('1','2') NOT NULL DEFAULT '1' COMMENT '状态,1-登录成功，2-登录失败',
  `ext` varchar(255) NOT NULL DEFAULT '0' COMMENT '其他说明',
  `http_user_agent` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_login_log
-- ----------------------------
INSERT INTO `t_login_log` VALUES ('1', 'admin', '1393242091', '127.0.0.1', '2', '帐号密码错误', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('2', 'admin', '1393242094', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('3', 'admin', '1393247675', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('4', 'admin', '1393252186', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('5', 'admin', '1393255067', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('6', 'admin', '1393255721', '127.0.0.1', '2', '密码为空', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('7', 'admin', '1393255725', '127.0.0.1', '2', '帐号密码错误', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('8', 'admin', '1393255727', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('9', 'admin', '1393255780', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('10', 'admin', '1393256139', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('11', 'admin', '1393290824', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('12', 'admin', '1393298488', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('13', 'admin', '1393308655', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('14', 'admin', '1393375992', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('15', 'admin', '1393376004', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('16', 'admin', '1393423242', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('17', 'admin', '1393462300', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('18', 'admin', '1393464444', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('19', 'admin', '1393467386', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('20', 'admin', '1393467386', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('21', 'admin', '1393467386', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('22', 'admin', '1393467387', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('23', 'admin', '1393469111', '127.0.0.1', '1', '', 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)');
INSERT INTO `t_login_log` VALUES ('24', 'admin', '1393479557', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('25', 'admin', '1393492610', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('26', 'admin', '1393500882', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('27', 'admin', '1393550091', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('28', 'admin', '1393585826', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('29', 'admin', '1393897374', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('30', 'admin', '1393935241', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('31', 'admin', '1393935704', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('32', 'admin', '1393947250', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('33', 'admin', '1394012515', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('34', 'admin', '1394068257', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('35', 'admin', '1394075680', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('36', 'admin', '1394077367', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('37', 'admin', '1394081339', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('38', 'admin', '1394089044', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('39', 'admin', '1394093589', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('40', 'admin', '1394112426', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('41', 'admin', '1394076562', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');
INSERT INTO `t_login_log` VALUES ('42', 'admin', '1394083567', '127.0.0.1', '1', '', 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Mobile/9B176 MicroMessenger/4.3.2');
INSERT INTO `t_login_log` VALUES ('43', 'admin', '1394090299', '127.0.0.1', '1', '', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0');

-- ----------------------------
-- Table structure for `t_member`
-- ----------------------------
DROP TABLE IF EXISTS `t_member`;
CREATE TABLE `t_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(18) DEFAULT NULL COMMENT '用户名',
  `password` varchar(32) DEFAULT NULL COMMENT '密码',
  `email` varchar(100) DEFAULT NULL COMMENT '电子邮箱',
  `login_ip` varchar(15) DEFAULT NULL COMMENT '当前登录IP地址',
  `login_time` int(10) DEFAULT NULL COMMENT '当前登录时间',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `dateline` date DEFAULT NULL COMMENT '日期，统计用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_member
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
INSERT INTO `t_module` VALUES ('12', 'Page', '单页模型', '用于维护单页内容和展示单页内容', 'Lib/Action/PageAction.class.php\nTpl/Page/index.html\n[db]t_page', '2', '1.0.2', '1394076087', '2');
INSERT INTO `t_module` VALUES ('56', 'Article', '文章模型', '用于维护文章内容和展示文章内容', 'Lib/Action/ArticleAction.class.php\nLib/Model/ArticleModel.class.php\nTpl/Article/index.html\nTpl/Article/edit.html\n[db]t_article', '4', '1.1.2', '1393919832', '3');

-- ----------------------------
-- Table structure for `t_module_store`
-- ----------------------------
DROP TABLE IF EXISTS `t_module_store`;
CREATE TABLE `t_module_store` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(255) DEFAULT NULL COMMENT '模块name，ArticleAction对应Article',
  `title` char(255) DEFAULT NULL COMMENT '模块标题，中文名称',
  `description` varchar(255) DEFAULT NULL COMMENT '模块功能描述',
  `code` int(10) DEFAULT NULL COMMENT '模块版本代码，整数',
  `version` varchar(20) DEFAULT NULL COMMENT '模块版本中文名',
  `history` text COMMENT '更新历史记录说明',
  `zip_url` varchar(255) DEFAULT NULL COMMENT '安装包地址',
  `zip_url_file_id` int(11) DEFAULT NULL COMMENT '安装包文件ID',
  `file_size` int(11) DEFAULT '0' COMMENT '文件字节数，用于前端判断是否下载成功',
  `structure` text COMMENT '安装文件结构',
  `sql` text COMMENT '应用表结构sql',
  `update_time` int(10) DEFAULT NULL COMMENT '模块最后更新时间',
  `orderid` int(10) DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_module_store
-- ----------------------------
INSERT INTO `t_module_store` VALUES ('3', 'Article', '文章模型', '用于维护文章内容和展示文章内容', '6', '1.1.5', '1、新增排序、批量删除功能\n2、新增文章标题模糊搜索', 'uploads/module/20140306/53181f29b0557.zip', '47', '5249', 'Lib/Action/ArticleAction.class.php\nLib/Model/ArticleModel.class.php\nTpl/Article/index.html\nTpl/Article/edit.html\n[db]t_article', 'CREATE TABLE `t_article` (\n  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,\n  `channel_id` int(11) unsigned DEFAULT NULL COMMENT \'栏目ID\',\n  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT \'文章标题\',\n  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT \'内容\',\n  `image_url` varchar(255) DEFAULT NULL COMMENT \'文章图片地址\',\n  `is_show` enum(\'0\',\'1\') DEFAULT \'1\' COMMENT \'是否显示\',\n  `pv` int(11) unsigned DEFAULT \'0\' COMMENT \'浏览量\',\n  `is_recommend` enum(\'0\',\'1\') DEFAULT \'0\' COMMENT \'是否推荐，0-未推荐，1-推荐\',\n  `orderid` int(11) DEFAULT \'999999\' COMMENT \'排序\',\n  `create_time` int(10) unsigned DEFAULT NULL COMMENT \'创建时间\',\n  PRIMARY KEY (`id`)\n) ENGINE=MyISAM;', '1394089846', '3');
INSERT INTO `t_module_store` VALUES ('2', 'Page', '单页模型', '用于维护单页内容和展示单页内容', '3', '1.0.2', '1、优化单页模型，新增栏目路径和update_time字段', 'uploads/module/20140306/5317e9b25baf3.zip', '45', '2086', 'Lib/Action/PageAction.class.php\nTpl/Page/index.html\n[db]t_page', 'CREATE TABLE `t_page` (                                                                                 \n  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,\n  `channel_id` int(11) unsigned DEFAULT NULL COMMENT \'栏目ID\',\n  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT \'单页标题\',\n  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT \'内容\',\n  `pv` int(11) unsigned DEFAULT \'0\' COMMENT \'阅读量 pageviews\',\n  `create_time` int(10) unsigned DEFAULT NULL COMMENT \'创建时间\',\n  PRIMARY KEY (`id`)\n) ENGINE=MyISAM;', '1394076087', '2');
INSERT INTO `t_module_store` VALUES ('1', 'Empty', '空模型', '利用空模块对系统设置URL的优化', '2', '1.0.1', null, 'http://cmspower.com/cloudstore/module/Empty.zip', null, '842', null, null, '1369920291', '1');

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
) ENGINE=MyISAM AUTO_INCREMENT=355 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of t_nav
-- ----------------------------
INSERT INTO `t_nav` VALUES ('2', '0', 'navbar', 'develop', '开发', '&#x32;', '0', '6', '0');
INSERT INTO `t_nav` VALUES ('6', '2', 'header', '', '开发 DEVELOP', '', '0', '1', '0');
INSERT INTO `t_nav` VALUES ('7', '2', 'nav', 'App', '应用管理', '', '0', '2', '0');
INSERT INTO `t_nav` VALUES ('162', '0', 'navbar', 'Content', '内容管理', '&#x2f;', '0', '2', '1370402234');
INSERT INTO `t_nav` VALUES ('353', '162', 'nav', 'Channel', '栏目管理', '', '0', '3', '1394075595');
INSERT INTO `t_nav` VALUES ('164', '162', 'header', '', '内容管理', '', '0', '1', '1370402316');
INSERT INTO `t_nav` VALUES ('341', '173', 'header', '', '会员管理', '', '0', '1', '1393897993');
INSERT INTO `t_nav` VALUES ('248', '0', 'navbar', 'Trade', '网站运营', '&#x34;', '0', '3', '1385423006');
INSERT INTO `t_nav` VALUES ('173', '0', 'navbar', 'Member', '用户管理', '&#x2e;', '0', '4', '1370402641');
INSERT INTO `t_nav` VALUES ('277', '173', 'nav', 'Role', '角色管理', '', '0', '5', '1393241139');
INSERT INTO `t_nav` VALUES ('276', '173', 'nav', 'Resource', '资源管理', '', '0', '6', '1393234306');
INSERT INTO `t_nav` VALUES ('180', '0', 'navbar', 'NavbarSystem', '系统配置', '&#x30;', '0', '5', '1370402995');
INSERT INTO `t_nav` VALUES ('274', '180', 'nav', 'SystemBase', '基础配置', '', '0', '2', '1392617179');
INSERT INTO `t_nav` VALUES ('182', '180', 'header', '', '基本设置', '', '0', '1', '1370403024');
INSERT INTO `t_nav` VALUES ('186', '0', 'navbar', 'Dashboard', '管理首页', '&#x33;', '0', '1', '1370403218');
INSERT INTO `t_nav` VALUES ('199', '162', 'header', '', '内容设置', '', '0', '2', '1371352974');
INSERT INTO `t_nav` VALUES ('342', '173', 'header', '', '管理员管理', '', '0', '3', '1393898002');
INSERT INTO `t_nav` VALUES ('352', '162', 'nav', 'Category', '分类管理', '', '0', '5', '1394014855');
INSERT INTO `t_nav` VALUES ('218', '2', 'nav', 'Module', '内容模块管理', '', '0', '3', '1373779937');
INSERT INTO `t_nav` VALUES ('286', '162', 'nav', 'AdPosition', '广告位管理', '', '0', '4', '1393298205');
INSERT INTO `t_nav` VALUES ('334', '186', 'nav', 'AboutUs', '关于我们', '', '0', '3', '1393401825');
INSERT INTO `t_nav` VALUES ('270', '186', 'nav', 'EditPassword', '修改个人密码', '', '0', '2', '1392036688');
INSERT INTO `t_nav` VALUES ('351', '173', 'nav', 'Member', '用户管理', '', '0', '2', '1393934300');
INSERT INTO `t_nav` VALUES ('279', '173', 'nav', 'Admin', '管理员管理', '', '0', '4', '1393248522');
INSERT INTO `t_nav` VALUES ('278', '180', 'nav', 'LoginLog', '登录日志', '', '0', '5', '1393242036');
INSERT INTO `t_nav` VALUES ('273', '180', 'nav', 'UpdateCache', '更新缓存', '', '0', '4', '1392111274');
INSERT INTO `t_nav` VALUES ('243', '186', 'header', '', '管理首页', '', '0', '1', '1383663716');
INSERT INTO `t_nav` VALUES ('284', '180', 'header', '', '工具', '', '0', '3', '1393294502');
INSERT INTO `t_nav` VALUES ('249', '248', 'header', '', '网站运营', '', '0', '1', '1385423033');
INSERT INTO `t_nav` VALUES ('339', '248', 'nav', 'Sw', '广告管理', '', '0', '3', '1393463854');
INSERT INTO `t_nav` VALUES ('338', '248', 'nav', 'Link', '友情链接', '', '0', '4', '1393415987');
INSERT INTO `t_nav` VALUES ('354', '180', 'nav', 'MemcacheStat', 'memcache监控', '', '0', '6', '1394076352');

-- ----------------------------
-- Table structure for `t_order`
-- ----------------------------
DROP TABLE IF EXISTS `t_order`;
CREATE TABLE `t_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `number` char(50) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `code` char(255) DEFAULT NULL,
  `thumb_image_url` varchar(255) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `price` int(10) DEFAULT NULL,
  `status` enum('1','2','3') DEFAULT '1' COMMENT '订单状态，1-未付款，2-已付款，3-已取消',
  `create_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_page
-- ----------------------------
INSERT INTO `t_page` VALUES ('7', '17', '联系我们', '<p>\r\n	我们的联系方式：\r\n</p>\r\n<p>\r\n	QQ群：303462914\r\n</p>', '0', '0', '1394072976');
INSERT INTO `t_page` VALUES ('8', '5', '关于我们', '<p>\n	CMSPower是一个开源的内容管理开发框架，基于ThinkPHP3.1框架开发，遵循Apache2开源协议发布，并且提供免费使用，致力于为您提供极致的开发体验。\n</p>\n<p>\n	通过社群智慧，让CMSPower成为一个强大而高效的云内容管理系统，节省人类的时间，拒绝重复的劳动，做更有价值的事情，专注创新，这就是CMSPower的使命。\n</p>\n<p>\n	CMSPower在构建一个为更少更快更好的专业社群的同时，也希望帮助用户分享实践中的经验与知识，构建一个围绕CMS的互动的开源的知识库，极具价值且能不断完善。\n</p>', '0', '1394075911', '1394073503');

-- ----------------------------
-- Table structure for `t_position`
-- ----------------------------
DROP TABLE IF EXISTS `t_position`;
CREATE TABLE `t_position` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '推荐位ID',
  `name` varchar(50) DEFAULT NULL COMMENT '推荐位名称',
  `module_id` int(11) DEFAULT NULL COMMENT '所属模型',
  `max_number` int(11) DEFAULT NULL COMMENT '最大保存条数',
  `orderid` int(11) DEFAULT '999999' COMMENT '排序',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_position
-- ----------------------------
INSERT INTO `t_position` VALUES ('8', '文章热门推荐', '5', '10', '999999', '1378963810');
INSERT INTO `t_position` VALUES ('9', '热门推荐', '5', '5', '999999', '1378967302');

-- ----------------------------
-- Table structure for `t_position_data`
-- ----------------------------
DROP TABLE IF EXISTS `t_position_data`;
CREATE TABLE `t_position_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `item_id` int(11) DEFAULT NULL COMMENT '产品ID',
  `channel_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `position_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '推荐位ID',
  `module` char(20) DEFAULT NULL COMMENT '模型',
  `module_id` int(11) unsigned DEFAULT '0' COMMENT '模型ID',
  `data` mediumtext COMMENT '数据信息',
  `orderid` int(11) DEFAULT '999999' COMMENT '排序',
  `create_time` int(11) DEFAULT NULL COMMENT '推荐时间',
  PRIMARY KEY (`id`),
  KEY `posid` (`position_id`),
  KEY `listorder` (`orderid`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COMMENT='推荐位数据表';

-- ----------------------------
-- Records of t_position_data
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
) ENGINE=MyISAM AUTO_INCREMENT=101 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_resource
-- ----------------------------
INSERT INTO `t_resource` VALUES ('15', 'Version', '版本管理', 'index:查询版本', '36', '1383663255');
INSERT INTO `t_resource` VALUES ('16', 'EditPassword', '修改个人密码', 'index:修改个人密码', '11', '1383663566');
INSERT INTO `t_resource` VALUES ('13', 'Brand', '品牌介绍', 'index:查询品牌介绍|sort:保存排序|add:添加品牌介绍|edit:修改品牌介绍|delete:删除品牌介绍', '32', '1383663184');
INSERT INTO `t_resource` VALUES ('83', 'AboutUs', '关于我们', 'index:关于我们', '0', '1393401825');
INSERT INTO `t_resource` VALUES ('18', 'AdPosition', '广告位管理', 'index:查询广告位|sort:保存排序|add:添加广告位|edit:修改广告位|delete:删除广告位', '34', '1383663663');
INSERT INTO `t_resource` VALUES ('98', 'Category', '分类管理', 'index:查询分类|tree:显示分类树|sort:保存排序|add:添加分类|edit:修改分类|delete:删除分类', '0', '1394014855');
INSERT INTO `t_resource` VALUES ('100', 'MemcacheStat', '监控', 'index:查询', '0', '1394076352');
INSERT INTO `t_resource` VALUES ('21', 'Admin', '管理员管理', 'index:管理员管理|add:添加管理员|edit:修改管理员|delete:删除管理员', '21', '1384510752');
INSERT INTO `t_resource` VALUES ('22', 'Role', '角色管理', 'index:角色管理|sort:保存排序|add:添加角色|edit:修改角色|delete:删除角色', '22', '1384510757');
INSERT INTO `t_resource` VALUES ('99', 'Channel', '栏目管理', 'index:查询栏目|tree:显示分类数|sort:保存排序|add:添加栏目|edit:修改栏目|delete:删除栏目', '0', '1394075595');
INSERT INTO `t_resource` VALUES ('88', 'Sw', '广告管理', 'index:广告管理|sort:保存排序|add:添加广告|edit:修改广告|delete:删除广告|toggle:上架下架', '0', '1393463854');
INSERT INTO `t_resource` VALUES ('87', 'Link', '友情链接管理', 'index:查询链接|sort:保存排序|add:添加链接|edit:修改链接|delete:删除链接|toggle:上架下架', '0', '1393415987');
INSERT INTO `t_resource` VALUES ('97', 'Member', '用户管理', 'index:查询用户|sort:保存排序|delete:删除用户|import:导入用户|export:导出全部用户', '0', '1393934300');

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
  `orderid` int(11) DEFAULT '999999' COMMENT '排序',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_role
-- ----------------------------
INSERT INTO `t_role` VALUES ('7', '内容编辑', '', '3', 'a:11:{s:12:\"EditPassword\";a:1:{s:5:\"index\";a:1:{s:9:\"has_right\";s:1:\"1\";}}s:7:\"AboutUs\";a:1:{s:5:\"index\";a:1:{s:9:\"has_right\";s:1:\"1\";}}s:3:\"Url\";a:7:{s:5:\"index\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:4:\"sort\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:3:\"add\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:4:\"edit\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:12:\"toggle_field\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:5:\"audit\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:6:\"delete\";a:1:{s:9:\"has_right\";s:1:\"1\";}}s:7:\"Publish\";a:1:{s:5:\"index\";a:1:{s:9:\"has_right\";s:1:\"1\";}}s:8:\"Category\";a:5:{s:5:\"index\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:4:\"sort\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:3:\"add\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:4:\"edit\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:6:\"delete\";a:1:{s:9:\"has_right\";s:1:\"1\";}}s:10:\"AdPosition\";a:5:{s:5:\"index\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:4:\"sort\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:3:\"add\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:4:\"edit\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:6:\"delete\";a:1:{s:9:\"has_right\";s:1:\"1\";}}s:11:\"ThreedImage\";a:5:{s:5:\"index\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:3:\"add\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:4:\"edit\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:6:\"delete\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:10:\"image_list\";a:1:{s:9:\"has_right\";s:1:\"1\";}}s:7:\"Product\";a:7:{s:5:\"index\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:4:\"sort\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:12:\"toggle_field\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:3:\"add\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:4:\"edit\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:6:\"delete\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:10:\"image_list\";a:1:{s:9:\"has_right\";s:1:\"1\";}}s:5:\"Order\";a:5:{s:5:\"index\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:6:\"detail\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:6:\"delete\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:7:\"confirm\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:6:\"cancel\";a:1:{s:9:\"has_right\";s:1:\"1\";}}s:5:\"Admin\";a:4:{s:5:\"index\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:3:\"add\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:4:\"edit\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:6:\"delete\";a:1:{s:9:\"has_right\";s:1:\"1\";}}s:4:\"Role\";a:5:{s:5:\"index\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:4:\"sort\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:3:\"add\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:4:\"edit\";a:1:{s:9:\"has_right\";s:1:\"1\";}s:6:\"delete\";a:1:{s:9:\"has_right\";s:1:\"1\";}}}', 'N;', '0', '1369742633');
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
INSERT INTO `t_system` VALUES ('base', 'nav_width', '500', '首页导航宽度');
INSERT INTO `t_system` VALUES ('base', 'nav_mini_width', '350', '迷你导航宽度');

-- ----------------------------
-- Table structure for `t_widget`
-- ----------------------------
DROP TABLE IF EXISTS `t_widget`;
CREATE TABLE `t_widget` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(255) DEFAULT NULL COMMENT 'MixWallWidget对应MixWall',
  `title` char(255) DEFAULT NULL COMMENT '标题，中文名称',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `code` int(10) DEFAULT NULL COMMENT '版本代码，整数',
  `version` varchar(20) DEFAULT NULL COMMENT '版本中文名',
  `update_time` int(10) DEFAULT NULL COMMENT '最后更新时间',
  `is_show` enum('0','1') DEFAULT '1' COMMENT '是否显示',
  `orderid` int(11) DEFAULT '999999' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of t_widget
-- ----------------------------
INSERT INTO `t_widget` VALUES ('1', 'MixWall', '混合图片墙', '可选两种展示样式，具开关灯效果', '1', '1.0.0', '1363784252', '1', '1');
INSERT INTO `t_widget` VALUES ('4', 'ImageText', '图文资讯', '以图文形式展示文章内容，左侧显示小图', '1', '1.0.0', '1363916635', '1', '3');
INSERT INTO `t_widget` VALUES ('5', 'Link', '栏目链接', '根据栏目配置自动生成链接导航', '2', '1.3.1', '1364087105', '1', '5');
INSERT INTO `t_widget` VALUES ('2', 'ItemShow', '宝贝橱窗', '以图片为主，展示宝贝内容', '1', '1.0.0', '1364094241', '1', '2');
INSERT INTO `t_widget` VALUES ('18', 'FriendLink', '友情链接', '以文本形式展示友情链接', '3', '1.0.2', '1373550150', '1', '4');