<?php

namespace Drupal\bidasoa_sprite_sheets\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class SpriteSheetAddForm.
 *
 * Provides the add form for our SpriteSheet entity.
 *
 */
class SpriteSheetAddForm extends SpriteSheetFormBase {

  /**
   * Returns the actions provided by this form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   An associative array containing the current state of the form.
   *
   * @return array
   *   An array of supported actions for the current entity form.
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Add sprite sheet');
    return $actions;
  }

}
