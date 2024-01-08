<?php

namespace Drupal\bidasoa_sprite_sheets;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines an access controller for the sprite_sheet entity.
 */
class SpriteSheetAccessController extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  public function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    if ($operation == 'view') {
      return AccessResult::allowed();
    }
    return parent::checkAccess($entity, $operation, $account);
  }

}
