/*
Navicat MySQL Data Transfer

Source Server         : mysql
Source Server Version : 50090
Source Host           : localhost:3306
Source Database       : insistblog

Target Server Type    : MYSQL
Target Server Version : 50090
File Encoding         : 65001

Date: 2015-12-19 19:59:54
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for usar_article
-- ----------------------------
DROP TABLE IF EXISTS `usar_article`;
CREATE TABLE `usar_article` (
  `article_id` bigint(20) NOT NULL auto_increment,
  `article_authorId` int(20) NOT NULL,
  `article_title` text,
  `article_author` text NOT NULL COMMENT '作者',
  `article_content` longtext,
  `article_originpic` varchar(255) NOT NULL COMMENT '存放图片路径等',
  `article_smallpic` blob NOT NULL,
  `article_categoryId` int(11) NOT NULL default '1',
  `article_createdate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `article_readnum` int(20) NOT NULL default '0' COMMENT '查看次数',
  `article_cmtnum` int(10) NOT NULL default '0' COMMENT '评论次数',
  `article_baned` tinyint(1) NOT NULL default '0' COMMENT '审核禁止文章，默认为通过',
  PRIMARY KEY  (`article_id`),
  KEY `authorId` (`article_authorId`)
) ENGINE=MyISAM AUTO_INCREMENT=159 DEFAULT CHARSET=utf8 COMMENT='存放文章的表，Extra 是存放图片的路径的';

-- ----------------------------
-- Table structure for usar_articleimg
-- ----------------------------
DROP TABLE IF EXISTS `usar_articleimg`;
CREATE TABLE `usar_articleimg` (
  `article_id` int(20) NOT NULL,
  `path` varchar(256) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for usar_article_tag
-- ----------------------------
DROP TABLE IF EXISTS `usar_article_tag`;
CREATE TABLE `usar_article_tag` (
  `at_id` int(11) NOT NULL auto_increment,
  `article_id` int(20) NOT NULL,
  `tag_id` int(10) NOT NULL,
  PRIMARY KEY  (`at_id`)
) ENGINE=MyISAM AUTO_INCREMENT=201 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for usar_category
-- ----------------------------
DROP TABLE IF EXISTS `usar_category`;
CREATE TABLE `usar_category` (
  `category_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `category_name` varchar(30) NOT NULL,
  `category_size` int(10) NOT NULL,
  PRIMARY KEY  (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for usar_comment
-- ----------------------------
DROP TABLE IF EXISTS `usar_comment`;
CREATE TABLE `usar_comment` (
  `comment_id` bigint(10) NOT NULL auto_increment,
  `comment_articleid` bigint(10) NOT NULL,
  `comment_userid` int(20) unsigned NOT NULL,
  `comment_username` varchar(255) NOT NULL,
  `comment_content` text NOT NULL,
  `comment_createdate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `comment_checked` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`comment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COMMENT='这是存放评论的表，Checked是判断此评论是否被管理员通过';

-- ----------------------------
-- Table structure for usar_follow
-- ----------------------------
DROP TABLE IF EXISTS `usar_follow`;
CREATE TABLE `usar_follow` (
  `user_id` int(11) NOT NULL,
  `follow_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`follow_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for usar_message
-- ----------------------------
DROP TABLE IF EXISTS `usar_message`;
CREATE TABLE `usar_message` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `fromid` int(11) NOT NULL,
  `toid` int(11) NOT NULL,
  `type` int(11) NOT NULL default '1' COMMENT '类别（1通知、2留言、3评论）',
  `content` text NOT NULL,
  `checked` tinyint(1) NOT NULL default '0' COMMENT '标记消息未读',
  `createtime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for usar_tag
-- ----------------------------
DROP TABLE IF EXISTS `usar_tag`;
CREATE TABLE `usar_tag` (
  `tag_id` int(5) NOT NULL auto_increment,
  `tag_name` varchar(20) NOT NULL,
  `tag_icon` varchar(255) default NULL,
  PRIMARY KEY  (`tag_id`),
  UNIQUE KEY `tagName` (`tag_name`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for usar_user
-- ----------------------------
DROP TABLE IF EXISTS `usar_user`;
CREATE TABLE `usar_user` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `user_name` varchar(30) character set ascii NOT NULL,
  `user_pwd` varchar(255) character set ascii NOT NULL,
  `user_headicon` varchar(255) NOT NULL default '',
  `user_email` varchar(30) NOT NULL,
  `user_signature` varchar(1024) default NULL,
  `user_createdate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `user_head` blob,
  `user_checked` tinyint(1) default '0',
  `user_visited` int(11) default '0',
  `user_integral` int(11) NOT NULL default '0',
  `user_level` int(11) NOT NULL default '1',
  `user_rank` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_name` (`user_name`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='用户的表';
