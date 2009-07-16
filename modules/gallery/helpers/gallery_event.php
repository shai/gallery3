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

class gallery_event_Core {
  static function group_created($group) {
    access::add_group($group);
  }

  static function group_deleted($group) {
    access::delete_group($group);
  }

  static function item_created($item) {
    access::add_item($item);
  }

  static function item_deleted($item) {
    access::delete_item($item);
  }

  static function user_login($user) {
    // If this user is an admin, check to see if there are any post-install tasks that we need
    // to run and take care of those now.
    if ($user->admin && module::get_var("gallery", "choose_default_tookit", null)) {
      graphics::choose_default_toolkit();
      module::clear_var("gallery", "choose_default_tookit");
    }
  }
}
