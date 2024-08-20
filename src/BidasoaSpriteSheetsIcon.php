<?php

namespace Drupal\bidasoa_sprite_sheets;

class BidasoaSpriteSheetsIcon {
  protected string $sprite;
  protected string $sheet;
  public function __construct(string $sprite, string $sheet) {
    $this->sprite = $sprite;
    $this->sheet = $sheet;
  }
  public function getSprite(): string {
    return $this->sprite;
  }
  public function getSheet(): string {
    return $this->sheet;
  }
}
