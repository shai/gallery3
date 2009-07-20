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
class Albums_Controller extends Items_Controller {

  /**
   *  @see REST_Controller::_show($resource)
   */
  public function _show($album) {
    $page_size = module::get_var("gallery", "page_size", 9);
    if (!access::can("view", $album)) {
      if ($album->id == 1) {
        $view = new Theme_View("page.html", "login");
        $view->page_title = t("Log in to Gallery");
        $view->content = user::get_login_form("login/auth_html");
        print $view;
        return;
      } else {
        access::forbidden();
      }
    }

    $show = $this->input->get("show");

    if ($show) {
      $index = $album->get_position($show);
      $page = ceil($index / $page_size);
      if ($page == 1) {
        url::redirect("albums/$album->id");
      } else {
        url::redirect("albums/$album->id?page=$page");
      }
    }

    $page = $this->input->get("page", "1");
    $children_count = $album->viewable()->children_count();
    $offset = ($page - 1) * $page_size;
    $max_pages = max(ceil($children_count / $page_size), 1);

    // Make sure that the page references a valid offset
    if ($page < 1) {
      url::redirect("albums/$album->id");
    } else if ($page > $max_pages) {
      url::redirect("albums/$album->id?page=$max_pages");
    }

    $template = new Theme_View("page.html", "album");
    $template->set_global("page_size", $page_size);
    $template->set_global("item", $album);
    $template->set_global("children", $album->viewable()->children($page_size, $offset));
    $template->set_global("children_count", $children_count);
    $template->set_global("parents", $album->parents());
    $template->content = new View("album.html");

    // We can't use math in ORM or the query builder, so do this by hand.  It's important
    // that we do this with math, otherwise concurrent accesses will damage accuracy.
    Database::instance()->query(
      "UPDATE {items} SET `view_count` = `view_count` + 1 WHERE `id` = $album->id");

    print $template;
  }

  /**
   * @see REST_Controller::_create($resource)
   */
  public function _create($album) {
    access::verify_csrf();
    access::required("view", $album);
    access::required("add", $album);

    switch ($this->input->post("type")) {
    case "album":
      return $this->_create_album($album);

    case "photo":
      return $this->_create_photo($album);

    default:
      access::forbidden();
    }
  }

  private function _create_album($album) {
    access::required("view", $album);
    access::required("add", $album);

    $form = album::get_add_form($album);
    if ($form->validate()) {
      $new_album = album::create(
        $album,
        $this->input->post("name"),
        $this->input->post("title", $this->input->post("name")),
        $this->input->post("description"),
        user::active()->id);

      log::success("content", "Created an album",
               html::anchor("albums/$new_album->id", "view album"));
      message::success(
        t("Created album %album_title", array("album_title" => p::clean($new_album->title))));

      print json_encode(
        array("result" => "success",
              "location" => url::site("albums/$new_album->id"),
              "resource" => url::site("albums/$new_album->id")));
    } else {
      print json_encode(
        array(
          "result" => "error",
          "form" => $form->__toString() . html::script("modules/gallery/js/albums_form_add.js")));
    }
  }

  private function _create_photo($album) {
    access::required("view", $album);
    access::required("add", $album);

    // If we set the content type as JSON, it triggers saving the result as
    // a document in the browser (well, in Chrome at least).
    // @todo figure out why and fix this.
    $form = photo::get_add_form($album);
    if ($form->validate()) {
      $photo = photo::create(
        $album,
        $this->input->post("file"),
        $_FILES["file"]["name"],
        $this->input->post("title", $this->input->post("name")),
        $this->input->post("description"),
        user::active()->id);

      log::success("content", "Added a photo", html::anchor("photos/$photo->id", "view photo"));
      message::success(
        t("Added photo %photo_title", array("photo_title" => p::clean($photo->title))));

      print json_encode(
        array("result" => "success",
              "resource" => url::site("photos/$photo->id"),
              "location" => url::site("photos/$photo->id")));
    } else {
      print json_encode(
        array("result" => "error",
              "form" => $form->__toString()));
    }
  }

  /**
   * @see REST_Controller::_update($resource)
   */
  public function _update($album) {
    access::verify_csrf();
    access::required("view", $album);
    access::required("edit", $album);

    $form = album::get_edit_form($album);
    if ($valid = $form->validate()) {
      // Make sure that there's not a conflict
      if ($album->id != 1 &&
          Database::instance()
          ->from("items")
          ->where("parent_id", $album->parent_id)
          ->where("id <>", $album->id)
          ->where("name", $form->edit_item->dirname->value)
          ->count_records()) {
        $form->edit_item->dirname->add_error("conflict", 1);
        $valid = false;
      }
    }

    if ($valid) {
      $album->title = $form->edit_item->title->value;
      $album->description = $form->edit_item->description->value;
      $album->sort_column = $form->edit_item->sort_order->column->value;
      $album->sort_order = $form->edit_item->sort_order->direction->value;
      if ($album->id != 1) {
        $album->rename($form->edit_item->dirname->value);
      }
      $album->save();
      module::event("item_edit_form_completed", $album, $form);

      log::success("content", "Updated album", "<a href=\"albums/$album->id\">view</a>");
      message::success(
        t("Saved album %album_title", array("album_title" => p::clean($album->title))));

      print json_encode(
        array("result" => "success",
              "location" => url::site("albums/$album->id")));
    } else {
      print json_encode(
        array("result" => "error",
              "form" => $form->__toString()));
    }
  }

  /**
   *  @see REST_Controller::_form_add($parameters)
   */
  public function _form_add($album_id) {
    $album = ORM::factory("item", $album_id);
    access::required("view", $album);
    access::required("add", $album);

    switch ($this->input->get("type")) {
    case "album":
      print album::get_add_form($album) .
        html::script("modules/gallery/js/albums_form_add.js");
      break;

    case "photo":
      print photo::get_add_form($album);
      break;

    default:
      kohana::show_404();
    }
  }

  /**
   *  @see REST_Controller::_form_add($parameters)
   */
  public function _form_edit($album) {
    access::required("view", $album);
    access::required("edit", $album);

    print album::get_edit_form($album);
  }
}
