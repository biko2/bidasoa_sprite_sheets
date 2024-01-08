<?php

namespace Drupal\bidasoa_sprite_sheets\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\InvokeCommand;

/**
 * ModalForm class.
 *
 * To properly inject services, override create() and use the setters provided
 * by the traits to inject the needed services.
 *
 * @code
 * public static function create($container) {
 *   $form = new static();
 *   // In this example we only need string translation so we use the
 *   // setStringTranslation() method provided by StringTranslationTrait.
 *   $form->setStringTranslation($container->get('string_translation'));
 *   return $form;
 * }
 * @endcode
 */
class SearchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'svg_sprite_browser_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $sheet_field_id = '', $sprite_field_id = '',$preview_field_id = '',$selected_sheet = '', $selected_sprite = '', $paramSpriteSheets = []) {
    // Do nothing after the form is submitted.
    if (!empty($form_state->getValues())) {
      return [];
    }

    $form['#attached']['library'][] = 'bidasoa_sprite_sheets/browser';
    $form['#attached']['library'][] = 'bidasoa_sprite_sheets/browser_styles';

    $form['status_messages'] = [
      '#type' => 'status_messages',
      '#weight' => -10,
    ];

    $form['selected_sheet'] = [
      '#type' => 'hidden',
      '#default_value' => $selected_sheet,
      '#attributes' => [
        'id' => [
          'svg-sprite-browser-selected-sheet',
        ],
      ],
    ];
    $form['selected_sprite'] = [
      '#type' => 'hidden',
      '#default_value' => $selected_sprite,
      '#attributes' => [
        'id' => [
          'svg-sprite-browser-selected-sprite',
        ],
      ],
    ];



    $form['sheet_field_id'] = [
      '#type' => 'hidden',
      '#value' => $sheet_field_id,
    ];
    $form['sprite_field_id'] = [
      '#type' => 'hidden',
      '#value' => $sprite_field_id,
    ];

    $form['preview_field_id'] = [
      '#type' => 'hidden',
      '#value' => $preview_field_id,
    ];
    // Search filter box.
    $form['sprite_search'] = [
      '#type' => 'search',
      '#title' => $this
        ->t('Search'),
      '#size' => 60,
      '#attributes' => [
        'id' => [
          'svg-sprite-browser-search',
        ],
      ],
    ];


    $spriteSheetRenderer= \Drupal::service('bidasoa_sprite_sheets.renderer');
    $spriteSheetManager = \Drupal::service('bidasoa_sprite_sheets.manager');

    $elements = [];

    $spriteSheets = $spriteSheetManager->getSpriteSheetsFromIds($paramSpriteSheets);
    forEach($spriteSheets as $spriteSheet){
      foreach ($spriteSheetRenderer->getIds($spriteSheet) as $id){
        $elements[] =[
          '#theme' => 'svg_sprite_browser_grid_item',
          '#id' => $id,
          '#sheet' => $spriteSheet->id(),
          '#content' => $spriteSheetRenderer->getRenderArray($spriteSheet, $id),
        ];
      }
    }

    $form['sprite_grid'] = [
      '#theme' => 'svg_sprite_browser_grid',
      '#elements' => $elements,
    ];

    // Submit button.
    $form['actions'] = ['#type' => 'actions'];

    $form['actions']['send'] = [
      '#type' => 'submit',
      '#value' => $this->t('Select'),
      '#attributes' => [
        'class' => [
          'use-ajax',
        ],
      ],
      '#ajax' => [
        'callback' => [$this, 'submitForm'],
        'event' => 'click',
      ],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    // If there are any form errors, re-display the form.
    if ($form_state->hasAnyErrors()) {
      $response->addCommand(new ReplaceCommand('#entity_reference_tree_wrapper', $form));
    }
    else {
      $response->addCommand(new InvokeCommand(NULL, 'svgSpriteBrowserDialogAjaxCallback', [$form_state->getValue('sheet_field_id'),$form_state->getValue('sprite_field_id'), $form_state->getValue('selected_sheet'),  $form_state->getValue('selected_sprite')]));

      $spriteSheet = \Drupal::service('bidasoa_sprite_sheets.manager')->getSpriteSheetFromId($form_state->getValue('selected_sheet'));
      $previewElement = \Drupal::service('bidasoa_sprite_sheets.renderer')->getRenderArray($spriteSheet,$form_state->getValue('selected_sprite'),$form_state->getValue('preview_field_id'));
      $response->addCommand(new CloseModalDialogCommand());
      $response->addCommand(new ReplaceCommand("#" . $form_state->getValue('preview_field_id'), $previewElement));

      $response->addCommand(new ReplaceCommand($form_state->getValue('sprite_field_id'), $form));
    //  $response->addCommand(new ReplaceCommand(NULL, $form));

    }

    return $response;
  }
  public function ajaxChange(){

  }
}
