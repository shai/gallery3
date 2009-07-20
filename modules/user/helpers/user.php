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

/**
 * This is the API for handling users.
 *
 * Note: by design, this class does not do any permission checking.
 */
class user_Core {
  static function get_edit_form($user) {
    $form = new Forge("users/$user->id?_method=put", "", "post", array("id" => "gEditUserForm"));
    $group = $form->group("edit_user")->label(t("Edit User: %name", array("name" => $user->name)));
    $group->input("full_name")->label(t("Full Name"))->id("gFullName")->value($user->full_name);
    self::_add_locale_dropdown($group, $user);
    $group->password("password")->label(t("Password"))->id("gPassword");
    $group->password("password2")->label(t("Confirm Password"))->id("gPassword2")
      ->matches($group->password);
    $group->input("email")->label(t("Email"))->id("gEmail")->value($user->email);
    $group->input("url")->label(t("URL"))->id("gUrl")->value($user->url);
    $group->submit("")->value(t("Save"));
    $form->add_rules_from($user);
    return $form;
  }

  static function get_edit_form_admin($user) {
    $form = new Forge("admin/users/edit_user/$user->id", "", "post", array("id" => "gEditUserForm"));
    $group = $form->group("edit_user")->label(t("Edit User"));
    $group->input("name")->label(t("Username"))->id("gUsername")->value($user->name);
    $group->inputs["name"]->error_messages(
      "in_use", t("There is already a user with that username"));
    $group->input("full_name")->label(t("Full Name"))->id("gFullName")->value($user->full_name);
    self::_add_locale_dropdown($group, $user);
    $group->password("password")->label(t("Password"))->id("gPassword");
    $group->password("password2")->label(t("Confirm Password"))->id("gPassword2")
      ->matches($group->password);
    $group->input("email")->label(t("Email"))->id("gEmail")->value($user->email);
    $group->input("url")->label(t("URL"))->id("gUrl")->value($user->url);
    $group->checkbox("admin")->label(t("Admin"))->id("gAdmin")->checked($user->admin);
    $group->submit("")->value(t("Modify User"));
    $form->add_rules_from($user);
    $form->edit_user->password->rules("-required");
    return $form;
  }

  static function get_add_form_admin() {
    $form = new Forge("admin/users/add_user", "", "post", array("id" => "gAddUserForm"));
    $group = $form->group("add_user")->label(t("Add User"));
    $group->input("name")->label(t("Username"))->id("gUsername")
      ->error_messages("in_use", t("There is already a user with that username"));
    $group->input("full_name")->label(t("Full Name"))->id("gFullName");
    $group->password("password")->label(t("Password"))->id("gPassword");
    $group->password("password2")->label(t("Confirm Password"))->id("gPassword2")
      ->matches($group->password);
    $group->input("email")->label(t("Email"))->id("gEmail");
    $group->input("url")->label(t("URL"))->id("gUrl");
    self::_add_locale_dropdown($group);
    $group->checkbox("admin")->label(t("Admin"))->id("gAdmin");
    $group->submit("")->value(t("Add User"));
    $user = ORM::factory("user");
    $form->add_rules_from($user);
    return $form;
  }

  private static function _add_locale_dropdown(&$form, $user=null) {
    $locales = locales::installed();
    if (count($locales) > 1) {
      // Put "none" at the first position in the array
      $locales = array_merge(array("" => t("« none »")), $locales);
      $selected_locale = ($user && $user->locale) ? $user->locale : "";
      $form->dropdown("locale")
        ->label(t("Language Preference"))
        ->options($locales)
        ->selected($selected_locale);
    }
  }

  static function get_delete_form_admin($user) {
    $form = new Forge("admin/users/delete_user/$user->id", "", "post",
                      array("id" => "gDeleteUserForm"));
    $group = $form->group("delete_user")->label(
      t("Are you sure you want to delete user %name?", array("name" => $user->name)));
    $group->submit("")->value(t("Delete user %name", array("name" => $user->name)));
    return $form;
  }

  static function get_login_form($url) {
    $form = new Forge($url, "", "post", array("id" => "gLoginForm"));
    $group = $form->group("login")->label(t("Login"));
    $group->input("name")->label(t("Username"))->id("gUsername")->class(null);
    $group->password("password")->label(t("Password"))->id("gPassword")->class(null);
    $group->inputs["name"]->error_messages("invalid_login", t("Invalid name or password"));
    $group->submit("")->value(t("Login"));
    return $form;
  }

  /**
   * Make sure that we have a session and group_ids cached in the session.
   */
  static function load_user() {
    $session = Session::instance();
    if (!($user = $session->get("user"))) {
      $session->set("user", $user = user::guest());
    }

    // The installer cannot set a user into the session, so it just sets an id which we should
    // upconvert into a user.
    if ($user === 2) {
      $user = model_cache::get("user", 2);
      user::login($user);
      $session->set("user", $user);
    }

    if (!$session->get("group_ids")) {
      $ids = array();
      foreach ($user->groups as $group) {
        $ids[] = $group->id;
      }
      $session->set("group_ids", $ids);
    }
  }

  /**
   * Return the array of group ids this user belongs to
   *
   * @return array
   */
  static function group_ids() {
    return Session::instance()->get("group_ids", array(1));
  }

  /**
   * Return the active user.  If there's no active user, return the guest user.
   *
   * @return User_Model
   */
  static function active() {
    // @todo (maybe) cache this object so we're not always doing session lookups.
    $user = Session::instance()->get("user", self::guest());
    return $user;
  }

  /**
   * Return the guest user.
   *
   * @todo consider caching
   *
   * @return User_Model
   */
  static function guest() {
    return model_cache::get("user", 1);
  }

  /**
   * Change the active user.
   *
   * @return User_Model
   */
  static function set_active($user) {
    $session = Session::instance();
    $session->set("user", $user);
    $session->delete("group_ids");
    self::load_user();
  }

  /**
   * Create a new user.
   *
   * @param string  $name
   * @param string  $full_name
   * @param string  $password
   * @return User_Model
   */
  static function create($name, $full_name, $password) {
    $user = ORM::factory("user")->where("name", $name)->find();
    if ($user->loaded) {
      throw new Exception("@todo USER_ALREADY_EXISTS $name");
    }

    $user->name = $name;
    $user->full_name = $full_name;
    $user->password = $password;

    // Required groups
    $user->add(group::everybody());
    $user->add(group::registered_users());

    $user->save();
    return $user;
  }

  /**
   * Is the password provided correct?
   *
   * @param user User Model
   * @param string $password a plaintext password
   * @return boolean true if the password is correct
   */
  static function is_correct_password($user, $password) {
    $valid = $user->password;

    // Try phpass first, since that's what we generate.
    if (strlen($valid) == 34) {
      require_once(MODPATH . "user/lib/PasswordHash.php");
      $hashGenerator = new PasswordHash(10, true);
      return $hashGenerator->CheckPassword($password, $valid);
    }

    $salt = substr($valid, 0, 4);
    // Support both old (G1 thru 1.4.0; G2 thru alpha-4) and new password schemes:
    $guess = (strlen($valid) == 32) ? md5($password) : ($salt . md5($salt . $password));
    if (!strcmp($guess, $valid)) {
      return true;
    }

    // Passwords with <&"> created by G2 prior to 2.1 were hashed with entities
    $sanitizedPassword = html::specialchars($password, false);
    $guess = (strlen($valid) == 32) ? md5($sanitizedPassword)
          : ($salt . md5($salt . $sanitizedPassword));
    if (!strcmp($guess, $valid)) {
      return true;
    }

    return false;
  }

  /**
   * Create the hashed passwords.
   * @param string $password a plaintext password
   * @return string hashed password
   */
  static function hash_password($password) {
    require_once(MODPATH . "user/lib/PasswordHash.php");
    $hashGenerator = new PasswordHash(10, true);
    return $hashGenerator->HashPassword($password);
  }

  /**
   * Log in as a given user.
   * @param object $user the user object.
   */
  static function login($user) {
    $user->login_count += 1;
    $user->last_login = time();
    $user->save();

    user::set_active($user);
    module::event("user_login", $user);
  }

  /**
   * Log out the active user and destroy the session.
   * @param object $user the user object.
   */
  static function logout() {
    $user = user::active();
    if (!$user->guest) {
      try {
        Session::instance()->destroy();
      } catch (Exception $e) {
        Kohana::log("error", $e);
      }
      module::event("user_logout", $user);
    }
  }

  /**
   * Look up a user by id.
   * @param integer      $id the user id
   * @return User_Model  the user object, or null if the id was invalid.
   */
  static function lookup($id) {
    $user = model_cache::get("user", $id);
    if ($user->loaded) {
      return $user;
    }
    return null;
  }

  /**
   * Look up a user by name.
   * @param integer      $id the user name
   * @return User_Model  the user object, or null if the name was invalid.
   */
  static function lookup_by_name($name) {
    $user = model_cache::get("user", $name, "name");
    if ($user->loaded) {
      return $user;
    }
    return null;
  }

  /**
   * Create a hashed password using md5 plus salt.
   * @param string $password plaintext password
   * @param string $salt (optional) salt or hash containing salt (randomly generated if omitted)
   * @return string hashed password
   */
  private static function _md5Salt($password, $salt="") {
    if (empty($salt)) {
      for ($i = 0; $i < 4; $i++) {
        $char = mt_rand(48, 109);
        $char += ($char > 90) ? 13 : ($char > 57) ? 7 : 0;
        $salt .= chr($char);
      }
    } else {
      $salt = substr($salt, 0, 4);
    }
    return $salt . md5($salt . $password);
  }
}