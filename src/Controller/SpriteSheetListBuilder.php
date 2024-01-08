<?php

namespace Drupal\bidasoa_sprite_sheets\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Language\LanguageInterface;

/**
 * Provides a listing of sprite sheet entities.
 *
 */
class SpriteSheetListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'bidasoa_sprite_sheets';
  }
  /**
   * {@inheritdoc}
   */
  public function load() {
    $entities = parent::load();
    ksort($entities);
    return $entities;
  }
  /**
   * Builds the header row for the entity listing.
   *
   * @return array
   *   A render array structure of header strings.
   *
   * @see \Drupal\Core\Entity\EntityListController::render()
   */
  public function buildHeader() {
    $header['machine_name'] =
      $this->t('Key');
    $header['label'] =
      $this->t('Label');
    $header['path'] =
      $this->t('Path');
    return $header + parent::buildHeader();
  }

  /**
   * Builds a row for an entity in the entity listing.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity for which to build the row.
   *
   * @return array
   *   A render array of the table row for displaying the entity.
   *
   * @see \Drupal\Core\Entity\EntityListController::render()
   */
  public function buildRow(EntityInterface $entity) {
    $row['machine_name'] = strtolower($entity->id());
    $row['label'] = $entity->label();
    $row['path'] = $entity->path();

    return $row + parent::buildRow($entity);
  }
}
