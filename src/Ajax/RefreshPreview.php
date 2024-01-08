<?php

namespace Drupal\bidasoa_sprite_sheets\Ajax;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormStateInterface;

/**
 *
 */
class RefreshPreview {

  /**
   *
   */
  public static function render(array &$form, FormStateInterface $form_state) {
    $triggeringElement = $form_state->getTriggeringElement();
    $spriteParts = explode('.',$triggeringElement['#value']);

    $spriteSheet = \Drupal::service('bidasoa_sprite_sheets.manager')->getSpriteSheetFromId($spriteParts[0]);
    $spriteElement = \Drupal::service('bidasoa_sprite_sheets.renderer')->getRenderArray($spriteParts[1], $spriteSheet, $triggeringElement['#attributes']['data-drupal-selector']);
    $response = new AjaxResponse();
    $response->addCommand(new ReplaceCommand("svg." . $triggeringElement['#attributes']['data-drupal-selector'], $spriteElement));

    return $response;
  }

}
