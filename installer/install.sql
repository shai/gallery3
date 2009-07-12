DROP TABLE IF EXISTS {access_caches};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {access_caches} (
  `id` int(9) NOT NULL auto_increment,
  `item_id` int(9) default NULL,
  `view_full_1` smallint(6) NOT NULL default '0',
  `edit_1` smallint(6) NOT NULL default '0',
  `add_1` smallint(6) NOT NULL default '0',
  `view_full_2` smallint(6) NOT NULL default '0',
  `edit_2` smallint(6) NOT NULL default '0',
  `add_2` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO {access_caches} VALUES (1,1,1,0,0,1,0,0);
DROP TABLE IF EXISTS {access_intents};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {access_intents} (
  `id` int(9) NOT NULL auto_increment,
  `item_id` int(9) default NULL,
  `view_1` tinyint(1) default NULL,
  `view_full_1` tinyint(1) default NULL,
  `edit_1` tinyint(1) default NULL,
  `add_1` tinyint(1) default NULL,
  `view_2` tinyint(1) default NULL,
  `view_full_2` tinyint(1) default NULL,
  `edit_2` tinyint(1) default NULL,
  `add_2` tinyint(1) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO {access_intents} VALUES (1,1,1,1,0,0,1,1,0,0);
DROP TABLE IF EXISTS {caches};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {caches} (
  `id` int(9) NOT NULL auto_increment,
  `key` varchar(255) NOT NULL,
  `tags` varchar(255) default NULL,
  `expiration` int(9) NOT NULL,
  `cache` longblob,
  PRIMARY KEY  (`id`),
  KEY `tags` (`tags`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS {comments};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {comments} (
  `author_id` int(9) default NULL,
  `created` int(9) NOT NULL,
  `guest_email` varchar(128) default NULL,
  `guest_name` varchar(128) default NULL,
  `guest_url` varchar(255) default NULL,
  `id` int(9) NOT NULL auto_increment,
  `item_id` int(9) NOT NULL,
  `server_http_accept_charset` varchar(64) default NULL,
  `server_http_accept_encoding` varchar(64) default NULL,
  `server_http_accept_language` varchar(64) default NULL,
  `server_http_accept` varchar(128) default NULL,
  `server_http_connection` varchar(64) default NULL,
  `server_http_host` varchar(64) default NULL,
  `server_http_referer` varchar(255) default NULL,
  `server_http_user_agent` varchar(128) default NULL,
  `server_query_string` varchar(64) default NULL,
  `server_remote_addr` varchar(32) default NULL,
  `server_remote_host` varchar(64) default NULL,
  `server_remote_port` varchar(16) default NULL,
  `state` varchar(15) default 'unpublished',
  `text` text,
  `updated` int(9) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS {graphics_rules};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {graphics_rules} (
  `id` int(9) NOT NULL auto_increment,
  `active` tinyint(1) default '0',
  `args` varchar(255) default NULL,
  `module_name` varchar(64) NOT NULL,
  `operation` varchar(64) NOT NULL,
  `priority` int(9) NOT NULL,
  `target` varchar(32) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO {graphics_rules} VALUES (1,1,'a:3:{s:5:\"width\";i:200;s:6:\"height\";i:200;s:6:\"master\";i:2;}','gallery','resize',100,'thumb');
INSERT INTO {graphics_rules} VALUES (2,1,'a:3:{s:5:\"width\";i:640;s:6:\"height\";i:480;s:6:\"master\";i:2;}','gallery','resize',100,'resize');
DROP TABLE IF EXISTS {groups};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {groups} (
  `id` int(9) NOT NULL auto_increment,
  `name` char(64) default NULL,
  `special` tinyint(1) default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO {groups} VALUES (1,'Everybody',1);
INSERT INTO {groups} VALUES (2,'Registered Users',1);
DROP TABLE IF EXISTS {groups_users};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {groups_users} (
  `group_id` int(9) NOT NULL,
  `user_id` int(9) NOT NULL,
  PRIMARY KEY  (`group_id`,`user_id`),
  UNIQUE KEY `user_id` (`user_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO {groups_users} VALUES (1,1);
INSERT INTO {groups_users} VALUES (1,2);
INSERT INTO {groups_users} VALUES (2,2);
DROP TABLE IF EXISTS {incoming_translations};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {incoming_translations} (
  `id` int(9) NOT NULL auto_increment,
  `key` char(32) NOT NULL,
  `locale` char(10) NOT NULL,
  `message` text NOT NULL,
  `revision` int(9) default NULL,
  `translation` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `key` (`key`,`locale`),
  KEY `locale_key` (`locale`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS {items};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {items} (
  `id` int(9) NOT NULL auto_increment,
  `album_cover_item_id` int(9) default NULL,
  `captured` int(9) default NULL,
  `created` int(9) default NULL,
  `description` varchar(2048) default NULL,
  `height` int(9) default NULL,
  `left` int(9) NOT NULL,
  `level` int(9) NOT NULL,
  `mime_type` varchar(64) default NULL,
  `name` varchar(255) default NULL,
  `owner_id` int(9) default NULL,
  `parent_id` int(9) NOT NULL,
  `rand_key` float default NULL,
  `relative_path_cache` varchar(255) default NULL,
  `resize_dirty` tinyint(1) default '1',
  `resize_height` int(9) default NULL,
  `resize_width` int(9) default NULL,
  `right` int(9) NOT NULL,
  `sort_column` varchar(64) default NULL,
  `sort_order` char(4) default 'ASC',
  `thumb_dirty` tinyint(1) default '1',
  `thumb_height` int(9) default NULL,
  `thumb_width` int(9) default NULL,
  `title` varchar(255) default NULL,
  `type` varchar(32) NOT NULL,
  `updated` int(9) default NULL,
  `view_count` int(9) default '0',
  `weight` int(9) NOT NULL default '0',
  `width` int(9) default NULL,
  `view_1` smallint(6) NOT NULL default '0',
  `view_2` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `type` (`type`),
  KEY `random` (`rand_key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO {items} VALUES (1,NULL,NULL,UNIX_TIMESTAMP(),'',NULL,1,1,NULL,NULL,NULL,0,NULL,'',1,NULL,NULL,2,'weight','ASC',1,NULL,NULL,'Gallery','album',UNIX_TIMESTAMP(),0,1,NULL,1,1);
DROP TABLE IF EXISTS {items_tags};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {items_tags} (
  `id` int(9) NOT NULL auto_increment,
  `item_id` int(9) NOT NULL,
  `tag_id` int(9) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `tag_id` (`tag_id`,`id`),
  KEY `item_id` (`item_id`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS {logs};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {logs} (
  `id` int(9) NOT NULL auto_increment,
  `category` varchar(64) default NULL,
  `html` varchar(255) default NULL,
  `message` text,
  `referer` varchar(255) default NULL,
  `severity` int(9) default '0',
  `timestamp` int(9) default '0',
  `url` varchar(255) default NULL,
  `user_id` int(9) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS {messages};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {messages} (
  `id` int(9) NOT NULL auto_increment,
  `key` varchar(255) default NULL,
  `severity` varchar(32) default NULL,
  `value` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS {modules};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {modules} (
  `id` int(9) NOT NULL auto_increment,
  `active` tinyint(1) default '0',
  `name` varchar(64) default NULL,
  `version` int(9) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO {modules} VALUES (1,1,'gallery',6);
INSERT INTO {modules} VALUES (2,1,'user',1);
INSERT INTO {modules} VALUES (3,1,'comment',2);
INSERT INTO {modules} VALUES (4,1,'organize',1);
INSERT INTO {modules} VALUES (5,1,'info',1);
INSERT INTO {modules} VALUES (6,1,'rss',1);
INSERT INTO {modules} VALUES (7,1,'search',1);
INSERT INTO {modules} VALUES (8,1,'slideshow',1);
INSERT INTO {modules} VALUES (9,1,'tag',1);
DROP TABLE IF EXISTS {outgoing_translations};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {outgoing_translations} (
  `id` int(9) NOT NULL auto_increment,
  `base_revision` int(9) default NULL,
  `key` char(32) NOT NULL,
  `locale` char(10) NOT NULL,
  `message` text NOT NULL,
  `translation` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `key` (`key`,`locale`),
  KEY `locale_key` (`locale`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS {permissions};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {permissions} (
  `id` int(9) NOT NULL auto_increment,
  `display_name` varchar(64) default NULL,
  `name` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO {permissions} VALUES (1,'View','view');
INSERT INTO {permissions} VALUES (2,'View Full Size','view_full');
INSERT INTO {permissions} VALUES (3,'Edit','edit');
INSERT INTO {permissions} VALUES (4,'Add','add');
DROP TABLE IF EXISTS {search_records};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {search_records} (
  `id` int(9) NOT NULL auto_increment,
  `item_id` int(9) default NULL,
  `dirty` tinyint(1) default '1',
  `data` longtext,
  PRIMARY KEY  (`id`),
  KEY `item_id` (`item_id`),
  FULLTEXT KEY `data` (`data`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO {search_records} VALUES (1,1,0,'   Gallery');
DROP TABLE IF EXISTS {sessions};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {sessions} (
  `session_id` varchar(127) NOT NULL,
  `data` text NOT NULL,
  `last_activity` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS {tags};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {tags} (
  `id` int(9) NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `count` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS {tasks};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {tasks} (
  `id` int(9) NOT NULL auto_increment,
  `callback` varchar(128) default NULL,
  `context` text NOT NULL,
  `done` tinyint(1) default '0',
  `name` varchar(128) default NULL,
  `owner_id` int(9) default NULL,
  `percent_complete` int(9) default '0',
  `state` varchar(32) default NULL,
  `status` varchar(255) default NULL,
  `updated` int(9) default NULL,
  PRIMARY KEY  (`id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS {themes};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {themes} (
  `id` int(9) NOT NULL auto_increment,
  `name` varchar(64) default NULL,
  `version` int(9) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO {themes} VALUES (1,'default',1);
INSERT INTO {themes} VALUES (2,'admin_default',1);
DROP TABLE IF EXISTS {users};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {users} (
  `id` int(9) NOT NULL auto_increment,
  `name` varchar(32) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `password` varchar(64) NOT NULL,
  `login_count` int(10) unsigned NOT NULL default '0',
  `last_login` int(10) unsigned NOT NULL default '0',
  `email` varchar(64) default NULL,
  `admin` tinyint(1) default '0',
  `guest` tinyint(1) default '0',
  `hash` char(32) default NULL,
  `url` varchar(255) default NULL,
  `locale` char(10) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO {users} VALUES (1,'guest','Guest User','',0,0,NULL,0,1,NULL,NULL,NULL);
INSERT INTO {users} VALUES (2,'admin','Gallery Administrator','',0,0,NULL,1,0,NULL,NULL,NULL);
DROP TABLE IF EXISTS {vars};
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE {vars} (
  `id` int(9) NOT NULL auto_increment,
  `module_name` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `value` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `module_name` (`module_name`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
INSERT INTO {vars} VALUES (1,'gallery','active_site_theme','default');
INSERT INTO {vars} VALUES (2,'gallery','active_admin_theme','admin_default');
INSERT INTO {vars} VALUES (3,'gallery','page_size','9');
INSERT INTO {vars} VALUES (4,'gallery','thumb_size','200');
INSERT INTO {vars} VALUES (5,'gallery','resize_size','640');
INSERT INTO {vars} VALUES (6,'gallery','default_locale','en_US');
INSERT INTO {vars} VALUES (7,'gallery','image_quality','75');
INSERT INTO {vars} VALUES (9,'gallery','blocks_dashboard_sidebar','a:4:{i:2;a:2:{i:0;s:7:\"gallery\";i:1;s:11:\"block_adder\";}i:3;a:2:{i:0;s:7:\"gallery\";i:1;s:5:\"stats\";}i:4;a:2:{i:0;s:7:\"gallery\";i:1;s:13:\"platform_info\";}i:5;a:2:{i:0;s:7:\"gallery\";i:1;s:12:\"project_news\";}}');
INSERT INTO {vars} VALUES (14,'gallery','blocks_dashboard_center','a:4:{i:6;a:2:{i:0;s:7:\"gallery\";i:1;s:7:\"welcome\";}i:7;a:2:{i:0;s:7:\"gallery\";i:1;s:12:\"photo_stream\";}i:8;a:2:{i:0;s:7:\"gallery\";i:1;s:11:\"log_entries\";}i:9;a:2:{i:0;s:7:\"comment\";i:1;s:15:\"recent_comments\";}}');
INSERT INTO {vars} VALUES (17,'gallery','version','3.0 pre beta 2 (git)');
INSERT INTO {vars} VALUES (18,'gallery','choose_default_tookit','1');
INSERT INTO {vars} VALUES (19,'gallery','date_format','Y-M-d');
INSERT INTO {vars} VALUES (20,'gallery','date_time_format','Y-M-d H:i:s');
INSERT INTO {vars} VALUES (21,'gallery','time_format','H:i:s');
INSERT INTO {vars} VALUES (22,'gallery','show_credits','1');
INSERT INTO {vars} VALUES (23,'gallery','credits','Powered by <a href=\"%url\">Gallery %version</a>');
INSERT INTO {vars} VALUES (25,'comment','spam_caught','0');
