# ************************************************************
# Sequel Pro SQL dump
# Version 5446
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: logs-contents-develop.cmmdmfrzajtw.ap-northeast-1.rds.amazonaws.com (MySQL 5.7.44-log)
# Database: access_log
# Generation Time: 2024-08-07 07:20:49 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table access_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `access_log`;

CREATE TABLE `access_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `ip` varchar(15) NOT NULL COMMENT 'IPアドレス',
  `method` varchar(8) NOT NULL COMMENT 'HTTPリクエストmethod',
  `path` text NOT NULL COMMENT 'HTTPリクエストpath',
  `ua` text NOT NULL COMMENT 'ユーザエージェント',
  `locale` varchar(5) NOT NULL,
  `p_head` varchar(2) NOT NULL COMMENT 'plate ID 先頭2文字',
  `p_lot` varchar(5) NOT NULL COMMENT 'palte ID 中央5桁',
  `p_type` varchar(1) NOT NULL COMMENT 'plate ID種別',
  `p_num` varchar(7) NOT NULL COMMENT 'palte ID 末尾7桁',
  `team_id` int(10) unsigned NOT NULL COMMENT 'チームID',
  `bookmark_id` int(11) NOT NULL,
  `is_nest` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Tilesç”¨',
  `contents` varchar(255) NOT NULL COMMENT 'リダイレクト先URL',
  `app` varchar(4) NOT NULL COMMENT 'アプリケーション種別',
  `cookie` varchar(40) NOT NULL COMMENT 'Cookie',
  `location` geometry NOT NULL COMMENT '位置情報',
  `delete_flag` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0:enable 1:deleted',
  `created_at` datetime NOT NULL COMMENT '登録日時',
  PRIMARY KEY (`id`),
  KEY `created_at` (`created_at`),
  KEY `bookmark_id` (`bookmark_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12182083 DEFAULT CHARSET=utf8 COMMENT='アクセスログ';



# Dump of table additional_element
# ------------------------------------------------------------

DROP TABLE IF EXISTS `additional_element`;

CREATE TABLE `additional_element` (
  `custom_value_id` bigint(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `value` varchar(40) NOT NULL,
  KEY `fk_other_element_custom_value1_idx` (`custom_value_id`),
  CONSTRAINT `fk_other_element_custom_value1` FOREIGN KEY (`custom_value_id`) REFERENCES `custom_value` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table custom_value
# ------------------------------------------------------------

DROP TABLE IF EXISTS `custom_value`;

CREATE TABLE `custom_value` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tag` varchar(32) NOT NULL,
  `team_id` int(11) DEFAULT '0',
  `app_user_id` int(11) DEFAULT '0',
  `bookmark_id` int(11) DEFAULT '0',
  `user_token` varchar(40) DEFAULT NULL,
  `cdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=92139 DEFAULT CHARSET=utf8;



# Dump of table exclusion_lock
# ------------------------------------------------------------

DROP TABLE IF EXISTS `exclusion_lock`;

CREATE TABLE `exclusion_lock` (
  `token` varchar(64) COLLATE utf8_bin NOT NULL,
  `team_id` int(11) NOT NULL,
  `action` varchar(32) COLLATE utf8_bin NOT NULL,
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`token`,`team_id`,`action`,`cdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table geo_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `geo_data`;

CREATE TABLE `geo_data` (
  `token` varchar(64) NOT NULL,
  `team_id` int(11) NOT NULL,
  `plate_id` varchar(20) DEFAULT '',
  `bookmark_id` int(11) DEFAULT '0',
  `type` varchar(16) NOT NULL DEFAULT '',
  `geo_code` geometry NOT NULL,
  `cdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table invalid_access
# ------------------------------------------------------------

DROP TABLE IF EXISTS `invalid_access`;

CREATE TABLE `invalid_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(45) DEFAULT 'none' COMMENT 'Invalid type',
  `token` varchar(64) DEFAULT '' COMMENT 'user token',
  `path` varchar(20) DEFAULT '' COMMENT 'Ex) AZ10000N1000000',
  `short_path` varchar(8) DEFAULT '' COMMENT 'For Short URL',
  `blocking_out_time` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='management invalid access';



# Dump of table line_client
# ------------------------------------------------------------

DROP TABLE IF EXISTS `line_client`;

CREATE TABLE `line_client` (
  `project_id` bigint(11) unsigned NOT NULL,
  `channel_id_login` bigint(10) NOT NULL,
  `channel_secret_login` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `channel_access_token_login` varchar(172) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `channel_id_message` bigint(10) NOT NULL,
  `channel_secret_message` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `channel_access_token_message` varchar(172) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table linkage_with_other
# ------------------------------------------------------------

DROP TABLE IF EXISTS `linkage_with_other`;

CREATE TABLE `linkage_with_other` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(11) DEFAULT '0' COMMENT 'No Must Column',
  `linkage_data` varchar(128) NOT NULL COMMENT 'UUID etc on Other system data',
  `token` varchar(40) DEFAULT NULL,
  `extent` varchar(64) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=309 DEFAULT CHARSET=latin1 COMMENT='他システムとの連携用';



# Dump of table login
# ------------------------------------------------------------

DROP TABLE IF EXISTS `login`;

CREATE TABLE `login` (
  `cookie` varchar(40) NOT NULL COMMENT 'tap.cmのクッキー値',
  `oauth_username` varchar(128) NOT NULL COMMENT 'Cognito上のusername',
  `expire_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `last_login` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最終ログイン日時',
  `team_id` int(11) NOT NULL COMMENT 'プロジェクトID（ログイン情報はプロジェクトID毎に保持する）',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '初回作成日時',
  `ip` varchar(20) DEFAULT NULL,
  `user_agent` varchar(200) DEFAULT '',
  PRIMARY KEY (`cookie`,`oauth_username`,`team_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='クッキーのログイン状態とfederated login 情報を保持するテーブル';



# Dump of table login_connectbay
# ------------------------------------------------------------

DROP TABLE IF EXISTS `login_connectbay`;

CREATE TABLE `login_connectbay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cookie` varchar(128) NOT NULL,
  `connectbay_id` varchar(128) NOT NULL,
  `team_id` varchar(45) NOT NULL,
  `last_login` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2251 DEFAULT CHARSET=utf8;



# Dump of table login_connectbay_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `login_connectbay_log`;

CREATE TABLE `login_connectbay_log` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT COMMENT '一意なID',
  `cookie` varchar(128) NOT NULL,
  `connectbay_id` varchar(128) NOT NULL,
  `team_id` varchar(45) NOT NULL,
  `kind` varchar(10) DEFAULT NULL COMMENT '発生イベントの種類',
  `cdate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'イベント発生日時',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4976 DEFAULT CHARSET=utf8 COMMENT='LINEでログイン・ログアウト等を行った記録';



# Dump of table login_line
# ------------------------------------------------------------

DROP TABLE IF EXISTS `login_line`;

CREATE TABLE `login_line` (
  `tonariwa_id` varchar(100) NOT NULL COMMENT 'UserTokenの値',
  `team_id` varchar(11) NOT NULL COMMENT 'プロジェクトID（ログイン情報はプロジェクトID毎に保持する）',
  `last_login` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最終ログイン日時',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '初回作成日時',
  `cookie` varchar(40) NOT NULL COMMENT 'tap.cmのクッキー値',
  PRIMARY KEY (`cookie`,`team_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='LINEログイン';



# Dump of table login_line_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `login_line_log`;

CREATE TABLE `login_line_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '一意なID',
  `tonariwa_id` varchar(100) NOT NULL COMMENT 'UserTokenの値',
  `kind` varchar(10) DEFAULT NULL COMMENT '発生イベントの種類',
  `cdate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'イベント発生日時',
  `cookie` varchar(40) NOT NULL COMMENT 'tap.cmのクッキー値',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13680 DEFAULT CHARSET=utf8 COMMENT='LINEでログイン・ログアウト等を行った記録';



# Dump of table login_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `login_log`;

CREATE TABLE `login_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '一意なID',
  `cookie` varchar(40) NOT NULL COMMENT 'UAのクッキー値',
  `oauth_username` varchar(128) NOT NULL COMMENT 'cognitoで取得できるOAuthユーザー名',
  `kind` varchar(10) DEFAULT NULL COMMENT '発生イベントの種類',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'イベント発生日時',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22747 DEFAULT CHARSET=utf8 COMMENT='cognitoでログイン・ログアウト等を行った記録';



# Dump of table payment_extention
# ------------------------------------------------------------

DROP TABLE IF EXISTS `payment_extention`;

CREATE TABLE `payment_extention` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'ãƒšã‚¤ãƒ¡ãƒ³ãƒˆãƒ¦ãƒ¼ã‚¶ID',
  `acct_id` varchar(64) DEFAULT NULL COMMENT 'Stripeã‚¢ã‚«ã‚¦ãƒ³ãƒˆID',
  `session_code` varchar(64) DEFAULT NULL COMMENT 'ã‚»ãƒƒã‚·ãƒ§ãƒ³ã‚³ãƒ¼ãƒ‰',
  `price` int(11) DEFAULT '0' COMMENT 'æ”¯æ‰•ã„é‡‘é¡',
  `plate` varchar(64) DEFAULT NULL COMMENT 'ãƒ—ãƒ¬ãƒ¼ãƒˆID',
  `payment_status` tinyint(4) DEFAULT '0' COMMENT 'æ”¯æ‰•ã„ã‚¹ãƒ†ãƒ¼ã‚¿ã‚¹',
  `status_code` int(3) DEFAULT NULL COMMENT 'ã‚¹ãƒ†ãƒ¼ã‚¿ã‚¹ã‚³ãƒ¼ãƒ‰',
  `expire` datetime DEFAULT NULL COMMENT 'æœ‰åŠ¹æœŸé™',
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'ä½œæˆæ—¥',
  `charge_id` varchar(64) DEFAULT NULL COMMENT 'ãƒãƒ£ãƒ¼ã‚¸ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=721 DEFAULT CHARSET=utf8;



# Dump of table pudo_extention
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pudo_extention`;

CREATE TABLE `pudo_extention` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL,
  `plate_id` varchar(32) CHARACTER SET latin1 DEFAULT NULL,
  `status_code` int(11) NOT NULL COMMENT 'ã‚¹ãƒ†ãƒ¼ã‚¿ã‚¹ã‚³ãƒ¼ãƒ‰',
  `passcode` varchar(12) NOT NULL,
  `regist_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  `user_token` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;



# Dump of table signin_temp
# ------------------------------------------------------------

DROP TABLE IF EXISTS `signin_temp`;

CREATE TABLE `signin_temp` (
  `uid` varchar(13) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `oauth_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `session` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `redirect_url` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret_code` int(6) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table signup_temp
# ------------------------------------------------------------

DROP TABLE IF EXISTS `signup_temp`;

CREATE TABLE `signup_temp` (
  `uid` varchar(13) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `oauth_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `redirect_url` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table stripes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `stripes`;

CREATE TABLE `stripes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` varchar(64) NOT NULL COMMENT 'Stripe customer id',
  `card_id` varchar(64) NOT NULL COMMENT 'Stripe card id',
  `session_id` varchar(64) NOT NULL COMMENT 'ãƒ¦ãƒ‹ãƒ¼ã‚¯ã‚»ãƒƒã‚·ãƒ§ãƒ³ID',
  `user_token` varchar(64) DEFAULT NULL COMMENT 'ãƒ¦ãƒ¼ã‚¶ãƒˆãƒ¼ã‚¯ãƒ³',
  `mail_flg` int(1) DEFAULT '0' COMMENT 'ãƒ¡ãƒ¼ãƒ«åˆ°é”ç¢ºèªãƒ•ãƒ©ã‚° 0:æœªç¢ºèª 1:ç¢ºèªæ¸ˆã¿',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=230 DEFAULT CHARSET=utf8;



# Dump of table temp
# ------------------------------------------------------------

DROP TABLE IF EXISTS `temp`;

CREATE TABLE `temp` (
  `uid` varchar(13) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `login_data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table user_extention
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_extention`;

CREATE TABLE `user_extention` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(11) DEFAULT NULL COMMENT 'ãƒ¦ãƒ¼ã‚¶IDã¾ãŸã¯ãƒãƒ¼ãƒ ID',
  `pay_id` int(11) DEFAULT NULL COMMENT 'ãƒšã‚¤ãƒ¡ãƒ³ãƒˆã‚¿ã‚¤ãƒ—ID',
  `acct_id` varchar(64) DEFAULT NULL COMMENT 'Stripeã‚¢ã‚«ã‚¦ãƒ³ãƒˆID',
  `type` int(1) DEFAULT NULL COMMENT 'ãƒšã‚¤ãƒ¡ãƒ³ãƒˆã‚¿ã‚¤ãƒ— 1:stripe 2:amazon 3:paypal',
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'ä½œæˆæ—¥',
  `application_fee` int(3) DEFAULT NULL COMMENT 'æ‰‹æ•°æ–™',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;



# Dump of table user_prizes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_prizes`;

CREATE TABLE `user_prizes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `cookie` varchar(64) NOT NULL COMMENT 'user id',
  `team_id` int(10) unsigned NOT NULL COMMENT 'team id',
  `shop_id` varchar(32) DEFAULT NULL,
  `prize_id` int(11) unsigned NOT NULL COMMENT 'prize id',
  `seq_no` int(3) unsigned DEFAULT '0' COMMENT 'prize seq no',
  `get_at` datetime DEFAULT NULL COMMENT 'stamp get at',
  `used_at` datetime DEFAULT NULL COMMENT 'stamp used at',
  `token` varchar(10) DEFAULT NULL COMMENT 'token',
  `order_id` varchar(64) DEFAULT NULL,
  `gift_url` varchar(256) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_prizes` (`cookie`,`team_id`,`prize_id`,`seq_no`,`get_at`)
) ENGINE=InnoDB AUTO_INCREMENT=7447 DEFAULT CHARSET=latin1;



# Dump of table user_sessions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_sessions`;

CREATE TABLE `user_sessions` (
  `cookie` varchar(40) NOT NULL COMMENT 'user id',
  `last_access_log_id` bigint(20) unsigned DEFAULT NULL COMMENT 'latest access_logs.id',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`cookie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table user_stamps
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_stamps`;

CREATE TABLE `user_stamps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `cookie` varchar(64) NOT NULL COMMENT 'user id',
  `team_id` int(10) unsigned NOT NULL COMMENT 'team id',
  `bookmark_id` int(11) NOT NULL COMMENT 'bookmark id',
  `get_at` datetime DEFAULT NULL COMMENT 'stamp get at',
  `used_at` datetime DEFAULT NULL COMMENT 'stamp used at',
  `access_log_id` bigint(20) unsigned DEFAULT NULL COMMENT 'access_log_id of get stamp',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_stamps` (`cookie`,`team_id`,`bookmark_id`,`get_at`)
) ENGINE=InnoDB AUTO_INCREMENT=31254 DEFAULT CHARSET=latin1;



# Dump of table xid_temp
# ------------------------------------------------------------

DROP TABLE IF EXISTS `xid_temp`;

CREATE TABLE `xid_temp` (
  `cookie` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `redirect_url` varchar(2000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `project_id` int(11) NOT NULL,
  `env` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cookie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table xid_token
# ------------------------------------------------------------

DROP TABLE IF EXISTS `xid_token`;

CREATE TABLE `xid_token` (
  `cookie` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `access_token` varchar(90) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cookie`,`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
