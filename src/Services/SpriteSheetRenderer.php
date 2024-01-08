<?php

namespace Drupal\bidasoa_sprite_sheets\Services;

use Drupal\Core\Url;

/**
 *
 */
class SpriteSheetRenderer {
  protected $svgSpriteLoader;

  /**
   *
   */
  public function __construct(SpriteSheetLoader $svgSpriteLoader) {
    $this->svgSpriteLoader = $svgSpriteLoader;
  }

  /**
   *
   */
  public function getIds($spriteSheet) : array {
    $svgSpriteData = $this->svgSpriteLoader->getFileContent($spriteSheet->path());
    if (is_null($svgSpriteData)) {
      return [];
    }
    $serializedData = new \SimpleXMLElement($svgSpriteData);
    $ids = [];
    foreach ($serializedData->symbol as $symbol) {
      $ids[] = (string) $symbol->attributes()['id'];
    }
    return $ids;
  }

  /**
   *
   */
  public function getRenderArray($spriteSheet, $id, $customId = '', $size = 96) : array {
    $sprite = [
      '#theme' => 'svg_sprite',
      '#id' => $id,
      '#sheet'=> !empty($spriteSheet) ? $spriteSheet->id() : '',
      '#svgSpritePath' => !empty($spriteSheet) ? Url::fromUserInput( '/' . $spriteSheet->path() ): '',
      '#customClass' => '',
      '#size' => $size,
      '#attributes' => [],

    ];
    if(!empty($customId)){
      $sprite['#prefix'] = '<div id="' . $customId .'">';
      $sprite['#suffix'] = '</div>';
    }

    return $sprite;
  }

}
