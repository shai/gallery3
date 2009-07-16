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
 * This is the API for handling photos.
 *
 * Note: by design, this class does not do any permission checking.
 */
class photo_Core {
  /**
   * Create a new photo.
   * @param integer $parent_id id of parent album
   * @param string  $filename path to the photo file on disk
   * @param string  $name the filename to use for this photo in the album
   * @param integer $title the title of the new photo
   * @param string  $description (optional) the longer description of this photo
   * @return Item_Model
   */
  static function create($parent, $filename, $name, $title,
                         $description=null, $owner_id=null) {
    if (!$parent->loaded || !$parent->is_album()) {
      throw new Exception("@todo INVALID_PARENT");
    }

    if (!is_file($filename)) {
      throw new Exception("@todo MISSING_IMAGE_FILE");
    }

    if (strpos($name, "/")) {
      throw new Exception("@todo NAME_CANNOT_CONTAIN_SLASH");
    }

    // We don't allow trailing periods as a security measure
    // ref: http://dev.kohanaphp.com/issues/684
    if (rtrim($name, ".") != $name) {
      throw new Exception("@todo NAME_CANNOT_END_IN_PERIOD");
    }

    if (filesize($filename) == 0) {
      throw new Exception("@todo EMPTY_INPUT_FILE");
    }

    $image_info = getimagesize($filename);

    // Force an extension onto the name
    $pi = pathinfo($filename);
    if (empty($pi["extension"])) {
      $pi["extension"] = image_type_to_extension($image_info[2], false);
      $name .= "." . $pi["extension"];
    }

    $photo = ORM::factory("item");
    $photo->type = "photo";
    $photo->title = $title;
    $photo->description = $description;
    $photo->name = $name;
    $photo->owner_id = $owner_id ? $owner_id : user::active();
    $photo->width = $image_info[0];
    $photo->height = $image_info[1];
    $photo->mime_type = empty($image_info['mime']) ? "application/unknown" : $image_info['mime'];
    $photo->thumb_dirty = 1;
    $photo->resize_dirty = 1;
    $photo->sort_column = "weight";
    $photo->rand_key = ((float)mt_rand()) / (float)mt_getrandmax();

    // Randomize the name if there's a conflict
    while (ORM::factory("item")
           ->where("parent_id", $parent->id)
           ->where("name", $photo->name)
           ->find()->id) {
      // @todo Improve this.  Random numbers are not user friendly
      $photo->name = rand() . "." . $pi["extension"];
    }

    // This saves the photo
    $photo->add_to_parent($parent);

    /*
     * If the thumb or resize already exists then rename it. We need to do this after the save
     * because the resize_path and thumb_path both call relative_path which caches the
     * path. Before add_to_parent the relative path will be incorrect.
     */
    if (file_exists($photo->resize_path()) ||
        file_exists($photo->thumb_path())) {
      $photo->name = $pi["filename"] . "-" . rand() . "." . $pi["extension"];
      $photo->save();
    }

    copy($filename, $photo->file_path());

    // @todo: publish this from inside Item_Model::save() when we refactor to the point where
    // there's only one save() happening here.
    module::event("item_created", $photo);

    // Build our thumbnail/resizes
    graphics::generate($photo);

    // If the parent has no cover item, make this it.
    if (access::can("edit", $parent) && $parent->album_cover_item_id == null)  {
      item::make_album_cover($photo);
    }

    return $photo;
  }

  static function get_add_form($parent) {
    $form = new Forge("albums/{$parent->id}", "", "post", array("id" => "gAddPhotoForm"));
    $group = $form->group("add_photo")->label(
      t("Add Photo to %album_title", array("album_title" =>$parent->title)));
    $group->input("title")->label(t("Title"));
    $group->textarea("description")->label(t("Description"));
    $group->input("name")->label(t("Filename"));
    $group->upload("file")->label(t("File"))->rules("required|allow[jpg,png,gif,flv,mp4]");
    $group->hidden("type")->value("photo");
    $group->submit("")->value(t("Upload"));
    $form->add_rules_from(ORM::factory("item"));
    return $form;
  }

  static function get_edit_form($photo) {
    $form = new Forge("photos/$photo->id", "", "post", array("id" => "gEditPhotoForm"));
    $form->hidden("_method")->value("put");
    $group = $form->group("edit_photo")->label(t("Edit Photo"));
    $group->input("title")->label(t("Title"))->value($photo->title);
    $group->textarea("description")->label(t("Description"))->value($photo->description);
    $group->input("filename")->label(t("Filename"))->value($photo->name)
      ->error_messages("conflict", t("There is already a file with this name"))
      ->callback("item::validate_no_slashes")
      ->error_messages("no_slashes", t("The photo name can't contain a \"/\""))
      ->callback("item::validate_no_trailing_period")
      ->error_messages("no_trailing_period", t("The photo name can't end in \".\""));

    $group->submit("")->value(t("Modify"));
    $form->add_rules_from(ORM::factory("item"));
    return $form;
  }

  /**
   * Return scaled width and height.
   *
   * @param integer $width
   * @param integer $height
   * @param integer $max    the target size for the largest dimension
   * @param string  $format the output format using %d placeholders for width and height
   */
  static function img_dimensions($width, $height, $max, $format="width=\"%d\" height=\"%d\"") {
    if (!$width || !$height) {
      return "";
    }

    if ($width > $height) {
      $new_width = $max;
      $new_height = (int)$max * ($height / $width);
    } else {
      $new_height = $max;
      $new_width = (int)$max * ($width / $height);
    }
    return sprintf($format, $new_width, $new_height);
  }
}
