<?php

namespace Drupal\bidasoa_sprite_sheets\Services;

use Drupal\Core\Config\CachedStorage;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SpriteSheetManager {
  private $CONFIG_PREFIX = "bidasoa_sprite_sheets.sprite_sheet";

  protected CachedStorage $configManager;
  protected EntityTypeManagerInterface $entityTypeManager;

  public function __construct(CachedStorage $configManager, EntityTypeManagerInterface $entityTypeManager) {
    $this->configManager = $configManager;
    $this->entityTypeManager = $entityTypeManager;
  }


  public function getSpriteSheets(){
    $configNames = $this->configManager->listAll($this->CONFIG_PREFIX);
    $spriteSheets =  [];
    foreach($configNames as $spriteSheetConfigKey){
      $spriteId = str_replace($this->CONFIG_PREFIX . '.', '',$spriteSheetConfigKey);
      $spriteSheet = $this->entityTypeManager->getStorage('sprite_sheet')->load($spriteId);
      $spriteSheets[] = $spriteSheet;
    }
    return $spriteSheets;
  }

  public function getSpriteSheetByName($machineName){
  }

  public function getSpriteSheetsAsOptions() {
    $options = [];
    foreach($this->getSpriteSheets() as $spriteSheet){
      $options[$spriteSheet->id()] = $spriteSheet->label();
    }
    return $options;
  }
  public function getSpriteSheetsFromIds($ids){
    return $this->entityTypeManager->getStorage('sprite_sheet')->loadMultiple($ids);
  }
  public function getSpriteSheetFromId($id){
    return $this->entityTypeManager->getStorage('sprite_sheet')->load($id);
  }


}
