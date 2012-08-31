/*
Navicat MySQL Data Transfer

Source Server         : Localhost
Source Server Version : 50525
Source Host           : localhost:3306
Source Database       : zebra

Target Server Type    : MYSQL
Target Server Version : 50525
File Encoding         : 65001

Date: 2012-08-31 13:11:13
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `zebra_comments`
-- ----------------------------
DROP TABLE IF EXISTS `zebra_comments`;
CREATE TABLE `zebra_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `story_id` bigint(20) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `comment` text NOT NULL,
  `upvotes` int(5) NOT NULL DEFAULT '0',
  `downvotes` int(5) NOT NULL,
  `created` int(5) NOT NULL DEFAULT '0',
  `updated` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zebra_comments
-- ----------------------------

-- ----------------------------
-- Table structure for `zebra_permissions`
-- ----------------------------
DROP TABLE IF EXISTS `zebra_permissions`;
CREATE TABLE `zebra_permissions` (
  `permission_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_string` varchar(255) NOT NULL,
  PRIMARY KEY (`permission_id`,`permission_string`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of zebra_permissions
-- ----------------------------

-- ----------------------------
-- Table structure for `zebra_permissions_roles`
-- ----------------------------
DROP TABLE IF EXISTS `zebra_permissions_roles`;
CREATE TABLE `zebra_permissions_roles` (
  `permission_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of zebra_permissions_roles
-- ----------------------------

-- ----------------------------
-- Table structure for `zebra_roles`
-- ----------------------------
DROP TABLE IF EXISTS `zebra_roles`;
CREATE TABLE `zebra_roles` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(120) NOT NULL,
  `role_display_name` varchar(160) NOT NULL,
  PRIMARY KEY (`role_id`,`role_name`),
  UNIQUE KEY `Unique Role` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of zebra_roles
-- ----------------------------
INSERT INTO `zebra_roles` VALUES ('1', 'user', 'Standard User');
INSERT INTO `zebra_roles` VALUES ('2', 'admin', 'Administrator');
INSERT INTO `zebra_roles` VALUES ('3', 'super_admin', 'Super Administrator');

-- ----------------------------
-- Table structure for `zebra_sessions`
-- ----------------------------
DROP TABLE IF EXISTS `zebra_sessions`;
CREATE TABLE `zebra_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zebra_sessions
-- ----------------------------
INSERT INTO `zebra_sessions` VALUES ('bdacf17bba11e1d7283d8ddf0c71d914', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.83 Safari/537.1', '1346382336', '');

-- ----------------------------
-- Table structure for `zebra_stories`
-- ----------------------------
DROP TABLE IF EXISTS `zebra_stories`;
CREATE TABLE `zebra_stories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` tinytext NOT NULL,
  `description` text,
  `external_link` varchar(255) DEFAULT NULL,
  `upvotes` int(5) NOT NULL DEFAULT '0',
  `downvotes` int(5) NOT NULL DEFAULT '0',
  `views` int(10) NOT NULL DEFAULT '0',
  `created` int(5) NOT NULL DEFAULT '0',
  `updated` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zebra_stories
-- ----------------------------
INSERT INTO `zebra_stories` VALUES ('1', '1', 'How We Deploy At Github', '', null, 'http://www.github.com', '7', '0', '0', '1346366020', '0');

-- ----------------------------
-- Table structure for `zebra_topics`
-- ----------------------------
DROP TABLE IF EXISTS `zebra_topics`;
CREATE TABLE `zebra_topics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `slug` varchar(155) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zebra_topics
-- ----------------------------

-- ----------------------------
-- Table structure for `zebra_users`
-- ----------------------------
DROP TABLE IF EXISTS `zebra_users`;
CREATE TABLE `zebra_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(5) NOT NULL,
  `email` varchar(80) NOT NULL,
  `password` varchar(140) NOT NULL,
  `register_date` int(11) NOT NULL DEFAULT '0',
  `activation_key` varchar(120) NOT NULL,
  `user_status` enum('active','pending','banned') NOT NULL,
  `remember_me` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of zebra_users
-- ----------------------------
INSERT INTO `zebra_users` VALUES ('1', '3', 'wolf@wolfphp.com', '1c40ed3c114c927b0c77bcea4a200f4348cd806bce9b6b641df2f586432ae8d6', '1346121757', '', 'active', null);

-- ----------------------------
-- Table structure for `zebra_user_meta`
-- ----------------------------
DROP TABLE IF EXISTS `zebra_user_meta`;
CREATE TABLE `zebra_user_meta` (
  `umeta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `umeta_key` varchar(255) NOT NULL,
  `umeta_value` longtext NOT NULL,
  `created` int(10) NOT NULL DEFAULT '0',
  `updated` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`umeta_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of zebra_user_meta
-- ----------------------------
INSERT INTO `zebra_user_meta` VALUES ('1', '1', 'first_name', 'Wolf', '0', '0');
INSERT INTO `zebra_user_meta` VALUES ('2', '1', 'last_name', 'De Wolfe', '0', '0');

-- ----------------------------
-- Table structure for `zebra_votes`
-- ----------------------------
DROP TABLE IF EXISTS `zebra_votes`;
CREATE TABLE `zebra_votes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `story_id` bigint(20) unsigned NOT NULL,
  `comment_id` bigint(20) unsigned NOT NULL,
  `vote_type` enum('upvote','downvote') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zebra_votes
-- ----------------------------
