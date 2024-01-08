<?php

namespace Drupal\bidasoa_sprite_sheets\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Sprite sheet entity.
 *
 *
 * @see annotation
 * @see Drupal\Core\Annotation\Translation
 *
 *
 * @ConfigEntityType(
 *   id = "sprite_sheet",
 *   label = @Translation("Sprite sheet"),
 *   label_collection = @Translation("Sprite sheets"),
 *   label_singular = @Translation("Sprite sheet"),
 *   label_plural = @Translation("Sprite sheets"),
 *   label_count = @PluralTranslation(
 *     singular = "@count sprite sheet",
 *     plural = "@count sprite sheets",
 *   ),
 *   admin_permission = "administer sprite sheets",
 *   handlers = {
 *     "access" = "Drupal\bidasoa_sprite_sheets\SpriteSheetAccessController",
 *     "list_builder" = "Drupal\bidasoa_sprite_sheets\Controller\SpriteSheetListBuilder",
 *     "form" = {
 *       "add" = "Drupal\bidasoa_sprite_sheets\Form\SpriteSheetAddForm",
 *       "edit" = "Drupal\bidasoa_sprite_sheets\Form\SpriteSheetEditForm",
 *       "delete" = "Drupal\bidasoa_sprite_sheets\Form\SpriteSheetDeleteForm"
 *     }
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/content/sprite-sheets/{sprite_sheet}",
 *     "delete-form" = "/admin/config/content/sprite-sheets/{sprite_sheet}/delete"
 *   },
 *   config_export = {
 *     "id",
 *     "uuid",
 *     "label",
 *     "path"
 *   }
 * )
 */
class SpriteSheet extends ConfigEntityBase {

  /**
   * The key.
   *
   * @var string
   */
  public $id;

  /**
   *
   * @var string
   */
  public $path;

  /**
   *
   * @var string
   */
  public $uuid;

  /**
   *
   * @var string
   */
  public $label;


  public function path(): string {
    if($this->path)
      return $this->path;
    return "";
  }

  public function __toString(): string {
    return $this->id();
  }

}
