/*
Navicat MySQL Data Transfer

Source Server         : Localhost
Source Server Version : 50516
Source Host           : localhost:3306
Source Database       : zebra

Target Server Type    : MYSQL
Target Server Version : 50516
File Encoding         : 65001

Date: 2012-09-01 13:08:34
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
INSERT INTO zebra_roles VALUES ('1', 'user', 'Standard User');
INSERT INTO zebra_roles VALUES ('2', 'admin', 'Administrator');
INSERT INTO zebra_roles VALUES ('3', 'super_admin', 'Super Administrator');

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
INSERT INTO zebra_sessions VALUES ('33f2cc43b69dcf4a617870532dde982e', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.83 Safari/537.1', '1346468340', '');

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zebra_stories
-- ----------------------------
INSERT INTO zebra_stories VALUES ('1', '1', 'How We Deploy At Github', 'how-we-deploy-at-github', null, 'https://github.com/blog/1241-deploying-at-github', '22', '4', '0', '1346366020', '0');
INSERT INTO zebra_stories VALUES ('2', '1', 'How Tracking Down My Stolen Computer Triggered a Drug Bust', 'how-tracking-down-my-stolen-computer-triggered-a-drug-bust', null, 'http://blog.makezine.com/2012/08/31/how-tracking-down-my-stolen-computer-triggered-a-drug-bust/', '1', '0', '0', '1346464971', '0');
INSERT INTO zebra_stories VALUES ('3', '1', 'Building Atari With CreateJS', 'building-atari-with-createjs', null, 'http://atari.com/arcade/developers/building-atari-createjs', '1', '0', '0', '1346465070', '0');
INSERT INTO zebra_stories VALUES ('4', '1', 'A Lesson In Timing Attacks', 'a-lesson-in-timing-attacks', null, 'http://codahale.com/a-lesson-in-timing-attacks/', '1', '0', '0', '1346465121', '0');
INSERT INTO zebra_stories VALUES ('5', '1', 'Valve Finds Value In Open-Source Drivers', 'valve-finds-value-in-open-source-drivers', null, 'http://www.phoronix.com/scan.php?page=article&item=intel_valve_linux&num=1', '1', '0', '0', '1346465182', '0');
INSERT INTO zebra_stories VALUES ('6', '1', 'What Powers Etsy', 'what-powers-etsy', null, 'http://codeascraft.etsy.com/2012/08/31/what-hardware-powers-etsy-com/', '1', '0', '0', '1346465205', '0');
INSERT INTO zebra_stories VALUES ('7', '1', 'What Is Good API Design?', 'what-is-good-api-design', null, 'http://richardminerich.com/2012/08/what-is-good-api-design/', '1', '0', '0', '1346465234', '0');
INSERT INTO zebra_stories VALUES ('8', '1', 'Open WebOS Beta Officially Released', 'open-webos-beta-officially-released', null, 'http://blog.openwebosproject.org/post/30593510898/open-webos-august-edition', '1', '0', '0', '1346465259', '0');
INSERT INTO zebra_stories VALUES ('9', '1', 'Moving From Heroku To Hardware', 'moving-from-heroku-to-hardware', null, 'http://justcramer.com/2012/08/30/how-noops-works-for-sentry/', '1', '0', '0', '1346288196', '0');

-- ----------------------------
-- Table structure for `zebra_stories_topics`
-- ----------------------------
DROP TABLE IF EXISTS `zebra_stories_topics`;
CREATE TABLE `zebra_stories_topics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `story_id` bigint(20) unsigned NOT NULL,
  `topic_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`,`story_id`,`topic_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zebra_stories_topics
-- ----------------------------
INSERT INTO zebra_stories_topics VALUES ('1', '1', '10');
INSERT INTO zebra_stories_topics VALUES ('2', '1', '1');
INSERT INTO zebra_stories_topics VALUES ('3', '2', '6');
INSERT INTO zebra_stories_topics VALUES ('4', '3', '3');
INSERT INTO zebra_stories_topics VALUES ('5', '0', '7');
INSERT INTO zebra_stories_topics VALUES ('6', '4', '6');
INSERT INTO zebra_stories_topics VALUES ('7', '5', '7');
INSERT INTO zebra_stories_topics VALUES ('8', '5', '8');
INSERT INTO zebra_stories_topics VALUES ('9', '5', '11');
INSERT INTO zebra_stories_topics VALUES ('10', '6', '1');
INSERT INTO zebra_stories_topics VALUES ('11', '6', '5');
INSERT INTO zebra_stories_topics VALUES ('12', '6', '12');
INSERT INTO zebra_stories_topics VALUES ('13', '7', '13');
INSERT INTO zebra_stories_topics VALUES ('14', '7', '4');
INSERT INTO zebra_stories_topics VALUES ('15', '8', '14');
INSERT INTO zebra_stories_topics VALUES ('16', '8', '8');
INSERT INTO zebra_stories_topics VALUES ('17', '9', '12');
INSERT INTO zebra_stories_topics VALUES ('18', '9', '5');

-- ----------------------------
-- Table structure for `zebra_topics`
-- ----------------------------
DROP TABLE IF EXISTS `zebra_topics`;
CREATE TABLE `zebra_topics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `slug` varchar(155) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zebra_topics
-- ----------------------------
INSERT INTO zebra_topics VALUES ('1', 'Web', 'web');
INSERT INTO zebra_topics VALUES ('2', 'Startups', 'startups');
INSERT INTO zebra_topics VALUES ('3', 'Javascript', 'javascript');
INSERT INTO zebra_topics VALUES ('4', 'Api\'s', 'api');
INSERT INTO zebra_topics VALUES ('5', 'Scaling', 'scaling');
INSERT INTO zebra_topics VALUES ('6', 'Security', 'security');
INSERT INTO zebra_topics VALUES ('7', 'Gaming', 'gaming');
INSERT INTO zebra_topics VALUES ('8', 'Open Source', 'open-source');
INSERT INTO zebra_topics VALUES ('9', 'E-commerce', 'ecommerce');
INSERT INTO zebra_topics VALUES ('10', 'Source Control', 'source-control');
INSERT INTO zebra_topics VALUES ('11', 'Linux', 'linux');
INSERT INTO zebra_topics VALUES ('12', 'Hosting', 'hosting');
INSERT INTO zebra_topics VALUES ('13', 'Programming', 'programming');
INSERT INTO zebra_topics VALUES ('14', 'Mobile', 'mobile');
INSERT INTO zebra_topics VALUES ('15', 'HTML5', 'html5');
INSERT INTO zebra_topics VALUES ('16', 'CSS', 'css');

-- ----------------------------
-- Table structure for `zebra_users`
-- ----------------------------
DROP TABLE IF EXISTS `zebra_users`;
CREATE TABLE `zebra_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(5) NOT NULL,
  `username` varchar(80) NOT NULL,
  `email` varchar(80) NOT NULL,
  `password` varchar(140) NOT NULL,
  `register_date` int(11) NOT NULL DEFAULT '0',
  `activation_key` varchar(120) NOT NULL,
  `user_status` enum('active','pending','banned') NOT NULL,
  `remember_me` text,
  PRIMARY KEY (`id`,`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of zebra_users
-- ----------------------------
INSERT INTO zebra_users VALUES ('1', '3', 'Zebra', 'wolf@wolfphp.com', '1c40ed3c114c927b0c77bcea4a200f4348cd806bce9b6b641df2f586432ae8d6', '1346121757', '', 'active', null);

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
INSERT INTO zebra_user_meta VALUES ('1', '1', 'first_name', 'Wolf', '0', '0');
INSERT INTO zebra_user_meta VALUES ('2', '1', 'last_name', 'De Wolfe', '0', '0');

-- ----------------------------
-- Table structure for `zebra_votes`
-- ----------------------------
DROP TABLE IF EXISTS `zebra_votes`;
CREATE TABLE `zebra_votes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `story_id` bigint(20) unsigned NOT NULL,
  `reason_id` int(5) NOT NULL DEFAULT '0',
  `comment_id` bigint(20) unsigned NOT NULL,
  `vote_type` enum('upvote','downvote') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zebra_votes
-- ----------------------------
INSERT INTO zebra_votes VALUES ('1', '1', '1', '0', '0', 'upvote');
INSERT INTO zebra_votes VALUES ('2', '1', '2', '0', '0', 'upvote');
INSERT INTO zebra_votes VALUES ('3', '1', '3', '0', '0', 'upvote');
INSERT INTO zebra_votes VALUES ('4', '1', '4', '0', '0', 'upvote');
INSERT INTO zebra_votes VALUES ('5', '1', '5', '0', '0', 'upvote');
INSERT INTO zebra_votes VALUES ('6', '1', '6', '0', '0', 'upvote');
INSERT INTO zebra_votes VALUES ('7', '1', '7', '0', '0', 'upvote');
INSERT INTO zebra_votes VALUES ('8', '1', '8', '0', '0', 'upvote');
INSERT INTO zebra_votes VALUES ('9', '1', '9', '0', '0', 'upvote');

-- ----------------------------
-- Table structure for `zebra_vote_reasons`
-- ----------------------------
DROP TABLE IF EXISTS `zebra_vote_reasons`;
CREATE TABLE `zebra_vote_reasons` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `reason` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zebra_vote_reasons
-- ----------------------------
INSERT INTO zebra_vote_reasons VALUES ('1', 'Spam');
INSERT INTO zebra_vote_reasons VALUES ('2', 'Hateful');
INSERT INTO zebra_vote_reasons VALUES ('3', 'This made absolutely no sense');
INSERT INTO zebra_vote_reasons VALUES ('4', 'I\'m offended');
INSERT INTO zebra_vote_reasons VALUES ('5', 'Too controversial');
INSERT INTO zebra_vote_reasons VALUES ('6', 'Too many spelling and grammar errors');
INSERT INTO zebra_vote_reasons VALUES ('7', 'Too biased');
INSERT INTO zebra_vote_reasons VALUES ('8', 'Fails to make a compelling argument');
INSERT INTO zebra_vote_reasons VALUES ('9', 'Lacking facts');
INSERT INTO zebra_vote_reasons VALUES ('10', 'Quite clearly don\'t know what they\'re on about');
