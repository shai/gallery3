<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2009 Bharat Mediratta
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
class gallery_installer {
  static function install() {
    $db = Database::instance();
    $db->query("CREATE TABLE {access_caches} (
                 `id` int(9) NOT NULL auto_increment,
                 `item_id` int(9),
                 PRIMARY KEY (`id`))
               ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $db->query("CREATE TABLE {access_intents} (
                 `id` int(9) NOT NULL auto_increment,
                 `item_id` int(9),
                 PRIMARY KEY (`id`))
               ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $db->query("CREATE TABLE {caches} (
                `id` int(9) NOT NULL auto_increment,
                `key` varchar(255) NOT NULL,
                `tags` varchar(255),
                `expiration` int(9) NOT NULL,
                `cache` longblob,
                PRIMARY KEY (`id`),
                KEY (`tags`))
                ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $db->query("CREATE TABLE {graphics_rules} (
                 `id` int(9) NOT NULL auto_increment,
                 `active` BOOLEAN default 0,
                 `args` varchar(255) default NULL,
                 `module_name` varchar(64) NOT NULL,
                 `operation` varchar(64) NOT NULL,
                 `priority` int(9) NOT NULL,
                 `target`  varchar(32) NOT NULL,
                 PRIMARY KEY (`id`))
               ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $db->query("CREATE TABLE {incoming_translations} (
                 `id` int(9) NOT NULL auto_increment,
                 `key` char(32) NOT NULL,
                 `locale` char(10) NOT NULL,
                 `message` text NOT NULL,
                 `revision` int(9) DEFAULT NULL,
                 `translation` text,
                 PRIMARY KEY (`id`),
                 UNIQUE KEY(`key`, `locale`),
                 KEY `locale_key` (`locale`, `key`))
               ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $db->query("CREATE TABLE {items} (
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
                 `resize_dirty` boolean default 1,
                 `resize_height` int(9) default NULL,
                 `resize_width` int(9) default NULL,
                 `right` int(9) NOT NULL,
                 `sort_column` varchar(64) default NULL,
                 `sort_order` char(4) default 'ASC',
                 `thumb_dirty` boolean default 1,
                 `thumb_height` int(9) default NULL,
                 `thumb_width` int(9) default NULL,
                 `title` varchar(255) default NULL,
                 `type` varchar(32) NOT NULL,
                 `updated` int(9) default NULL,
                 `view_count` int(9) default 0,
                 `weight` int(9) NOT NULL default 0,
                 `width` int(9) default NULL,
                 PRIMARY KEY (`id`),
                 KEY `parent_id` (`parent_id`),
                 KEY `type` (`type`),
                 KEY `random` (`rand_key`))
               ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $db->query("CREATE TABLE {logs} (
                 `id` int(9) NOT NULL auto_increment,
                 `category` varchar(64) default NULL,
                 `html` varchar(255) default NULL,
                 `message` text default NULL,
                 `referer` varchar(255) default NULL,
                 `severity` int(9) default 0,
                 `timestamp` int(9) default 0,
                 `url` varchar(255) default NULL,
                 `user_id` int(9) default 0,
                 PRIMARY KEY (`id`))
               ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $db->query("CREATE TABLE {messages} (
                 `id` int(9) NOT NULL auto_increment,
                 `key` varchar(255) default NULL,
                 `severity` varchar(32) default NULL,
                 `value` varchar(255) default NULL,
                 PRIMARY KEY (`id`),
                 UNIQUE KEY(`key`))
               ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $db->query("CREATE TABLE {modules} (
                 `id` int(9) NOT NULL auto_increment,
                 `active` BOOLEAN default 0,
                 `name` varchar(64) default NULL,
                 `version` int(9) default NULL,
                 PRIMARY KEY (`id`),
                 UNIQUE KEY(`name`))
               ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $db->query("CREATE TABLE {outgoing_translations} (
                 `id` int(9) NOT NULL auto_increment,
                 `base_revision` int(9) DEFAULT NULL,
                 `key` char(32) NOT NULL,
                 `locale` char(10) NOT NULL,
                 `message` text NOT NULL,
                 `translation` text,
                 PRIMARY KEY (`id`),
                 UNIQUE KEY(`key`, `locale`),
                 KEY `locale_key` (`locale`, `key`))
               ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $db->query("CREATE TABLE {permissions} (
                 `id` int(9) NOT NULL auto_increment,
                 `display_name` varchar(64) default NULL,
                 `name` varchar(64) default NULL,
                 PRIMARY KEY (`id`),
                 UNIQUE KEY(`name`))
               ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $db->query("CREATE TABLE {sessions} (
                `session_id` varchar(127) NOT NULL,
                `data` text NOT NULL,
                `last_activity` int(10) UNSIGNED NOT NULL,
                PRIMARY KEY (`session_id`))
               ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $db->query("CREATE TABLE {tasks} (
                `id` int(9) NOT NULL auto_increment,
                `callback` varchar(128) default NULL,
                `context` text NOT NULL,
                `done` boolean default 0,
                `name` varchar(128) default NULL,
                `owner_id` int(9) default NULL,
                `percent_complete` int(9) default 0,
                `state` varchar(32) default NULL,
                `status` varchar(255) default NULL,
                `updated` int(9) default NULL,
                PRIMARY KEY (`id`),
                KEY (`owner_id`))
               ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $db->query("CREATE TABLE {themes} (
                 `id` int(9) NOT NULL auto_increment,
                 `name` varchar(64) default NULL,
                 `version` int(9) default NULL,
                 PRIMARY KEY (`id`),
                 UNIQUE KEY(`name`))
               ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $db->query("CREATE TABLE {vars} (
                `id` int(9) NOT NULL auto_increment,
                `module_name` varchar(64) NOT NULL,
                `name` varchar(64) NOT NULL,
                `value` text,
                PRIMARY KEY (`id`),
                UNIQUE KEY(`module_name`, `name`))
               ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    foreach (array("albums", "logs", "modules", "resizes", "thumbs", "tmp", "uploads") as $dir) {
      @mkdir(VARPATH . $dir);
    }

    access::register_permission("view", "View");
    access::register_permission("view_full", "View Full Size");
    access::register_permission("edit", "Edit");
    access::register_permission("add", "Add");

    $root = ORM::factory("item");
    $root->type = "album";
    $root->title = "Gallery";
    $root->description = "";
    $root->left = 1;
    $root->right = 2;
    $root->parent_id = 0;
    $root->level = 1;
    $root->thumb_dirty = 1;
    $root->resize_dirty = 1;
    $root->sort_column = "weight";
    $root->sort_order = "ASC";
    $root->save();
    access::add_item($root);

    module::set_var("gallery", "active_site_theme", "default");
    module::set_var("gallery", "active_admin_theme", "admin_default");
    module::set_var("gallery", "page_size", 9);
    module::set_var("gallery", "thumb_size", 200);
    module::set_var("gallery", "resize_size", 640);
    module::set_var("gallery", "default_locale", "en_US");
    module::set_var("gallery", "image_quality", 75);

    // Add rules for generating our thumbnails and resizes
    graphics::add_rule(
      "gallery", "thumb", "resize",
      array("width" => 200, "height" => 200, "master" => Image::AUTO),
      100);
    graphics::add_rule(
      "gallery", "resize", "resize",
      array("width" => 640, "height" => 480, "master" => Image::AUTO),
      100);

    // Instantiate default themes (site and admin)
    foreach (array("default", "admin_default") as $theme_name) {
      $theme_info = new ArrayObject(parse_ini_file(THEMEPATH . $theme_name . "/theme.info"),
                                    ArrayObject::ARRAY_AS_PROPS);
      $theme = ORM::factory("theme");
      $theme->name = $theme_name;
      $theme->version = $theme_info->version;
      $theme->save();
    }

    block_manager::add("dashboard_sidebar", "gallery", "block_adder");
    block_manager::add("dashboard_sidebar", "gallery", "stats");
    block_manager::add("dashboard_sidebar", "gallery", "platform_info");
    block_manager::add("dashboard_sidebar", "gallery", "project_news");
    block_manager::add("dashboard_center", "gallery", "welcome");
    block_manager::add("dashboard_center", "gallery", "photo_stream");
    block_manager::add("dashboard_center", "gallery", "log_entries");

    module::set_var("gallery", "choose_default_tookit", 1);
    module::set_var("gallery", "date_format", "Y-M-d");
    module::set_var("gallery", "date_time_format", "Y-M-d H:i:s");
    module::set_var("gallery", "time_format", "H:i:s");
    module::set_var("gallery", "show_credits", 1);
    // @todo this string needs to be picked up by l10n_scanner
    module::set_var("gallery", "credits", "Powered by <a href=\"%url\">Gallery %version</a>");
    module::set_version("gallery", 7);
  }

  static function upgrade($version) {
    $db = Database::instance();
    if ($version == 1) {
      module::set_var("gallery", "date_format", "Y-M-d");
      module::set_var("gallery", "date_time_format", "Y-M-d H:i:s");
      module::set_var("gallery", "time_format", "H:i:s");
      module::set_version("gallery", $version = 2);
    }

    if ($version == 2) {
      module::set_var("gallery", "show_credits", 1);
      module::set_version("gallery", $version = 3);
    }

    if ($version == 3) {
      $db->query("CREATE TABLE {caches} (
                 `id` varchar(255) NOT NULL,
                 `tags` varchar(255),
                 `expiration` int(9) NOT NULL,
                 `cache` text,
                 PRIMARY KEY (`id`),
                 KEY (`tags`))
                 ENGINE=InnoDB DEFAULT CHARSET=utf8;");
      module::set_version("gallery", $version = 4);
    }

    if ($version == 4) {
      Cache::instance()->delete_all();
      $db->query("ALTER TABLE {caches} MODIFY COLUMN `cache` LONGBLOB");
      module::set_version("gallery", $version = 5);
    }

    if ($version == 5) {
      Cache::instance()->delete_all();
      $db->query("ALTER TABLE {caches} DROP COLUMN `id`");
      $db->query("ALTER TABLE {caches} ADD COLUMN `key` varchar(255) NOT NULL");
      $db->query("ALTER TABLE {caches} ADD COLUMN `id` int(9) NOT NULL auto_increment PRIMARY KEY");
      module::set_version("gallery", $version = 6);
    }

    if ($version == 6) {
      module::clear_var("gallery", "version");
      module::set_version("gallery", $version = 7);
    }
  }

  static function uninstall() {
    $db = Database::instance();
    $db->query("DROP TABLE IF EXISTS {access_caches}");
    $db->query("DROP TABLE IF EXISTS {access_intents}");
    $db->query("DROP TABLE IF EXISTS {graphics_rules}");
    $db->query("DROP TABLE IF EXISTS {incoming_translations}");
    $db->query("DROP TABLE IF EXISTS {items}");
    $db->query("DROP TABLE IF EXISTS {logs}");
    $db->query("DROP TABLE IF EXISTS {modules}");
    $db->query("DROP TABLE IF EXISTS {outgoing_translations}");
    $db->query("DROP TABLE IF EXISTS {permissions}");
    $db->query("DROP TABLE IF EXISTS {sessions}");
    $db->query("DROP TABLE IF EXISTS {tasks}");
    $db->query("DROP TABLE IF EXISTS {themes}");
    $db->query("DROP TABLE IF EXISTS {vars}");
    $db->query("DROP TABLE IF EXISTS {caches}");
    foreach (array("albums", "resizes", "thumbs", "uploads",
                   "modules", "logs", "database.php") as $entry) {
      system("/bin/rm -rf " . VARPATH . $entry);
    }
  }
}
