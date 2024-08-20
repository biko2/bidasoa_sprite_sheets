<?php

namespace Drupal\bidasoa_sprite_sheets\Element;

use Drupal\bidasoa_sprite_sheets\Services\SpriteSheetManager;
use Drupal\bidasoa_sprite_sheets\Services\SpriteSheetRenderer;
use Drupal\bidasoa_sprite_sheets\SvgSpriteHelper;
use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Attribute\FormElement;
use Drupal\Core\Render\Element\FormElementBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provide an sprite sheet icon form element.
 *
 * Usage example:
 *
 * @code
 * $form['icon'] = [
 *   '#type' => 'bidasoa_sprite_sheets_icon',
 *   '#title' => t('Icon'),
 *   '#sheets' => ['sheet1','sheet2'],
 *   '#default_value' => [
 *     'icon' => '',
 *     'sheet' => ''
 *   ],
 * ];
 * @endcode
 *
 * @FormElementBase("bidasoa_sprite_sheets_icon")
 */
#[FormElement('bidasoa_sprite_sheets_icon')]
class SpriteSheetIconElement  extends FormElementBase {
    /**
   * {@inheritdoc}
   */
  public function getInfo(): array {
    $class = get_class($this);
    return [
      '#input' => TRUE,
      '#process' => [
        [$class, 'formElement'],
      ],
    ];
  }

/*  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    $value = $form_state->getValue($element['#name']);
$vals = $form_state->getValues();
    return parent::valueCallback($element, $input, $form_state); // TODO: Change the autogenerated stub
  }
*/
  /**
   *
   * @param array $element
   *   The form element to process.
   * @param FormStateInterface $form_state
   *   The current state of the form.
   * @param array $form
   *   The complete form structure.
   *
   * @return array
   *   The form element.
   */
  public static function formElement(array &$element, FormStateInterface $form_state, array &$form): array {
    $spriteSheetManager = \Drupal::service('bidasoa_sprite_sheets.manager');
    $spriteSheetRenderer = \Drupal::service('bidasoa_sprite_sheets.renderer');
    $defaultValue = $element['#default_value'];

    $element['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $element['#attached']['library'][] = 'bidasoa_sprite_sheets/ajax';

    $svg_sprite_element = [];

    $spriteDefaultValue = (isset($defaultValue['sprite'])) ? $defaultValue['sprite'] : SvgSpriteHelper::NONE_KEY;

    $sheetDefaultValue = (isset($defaultValue['sheet'])) ? $defaultValue['sheet'] : SvgSpriteHelper::NONE_KEY;
    $selectedSpriteSheet = \Drupal::service('bidasoa_sprite_sheets.manager')->getSpriteSheetFromId($sheetDefaultValue);

    $sheets = $spriteSheetManager->getSpriteSheetsFromIds($element['#sheets']);

    $sheetWidgetId = Html::getUniqueId('sheet_widget');
    $spriteWidgetId = Html::getUniqueId('sprite_widget');
    $previewWidgetId = Html::getUniqueId('preview_widget');

    $svg_sprite_element['sprite_container'] = [
      '#type' => 'fieldset',
      '#title' => $element['#title'],
      '#description' => $element['#description'],
      '#required' => $element['#required'],
    ];
    $svg_sprite_element['sprite_container']['sheet'] = [
      '#type' => 'textfield',
      '#default_value' => $sheetDefaultValue,
      '#attributes' => [
        'style' => 'display:none',
        'id' => [
          $sheetWidgetId,
        ],
      ],

    ];
    $svg_sprite_element['sprite_container']['sprite'] = [
      '#type' => 'textfield',
      '#default_value' => $spriteDefaultValue,
      '#attributes' => [
        'style' => 'display:none',
        'id' => [
          $spriteWidgetId,
        ],
      ],
    ];

    $svg_sprite_element['sprite_container']['sprite_preview'] = $spriteSheetRenderer->getRenderArray($selectedSpriteSheet,$spriteDefaultValue,$previewWidgetId);

    $svg_sprite_element['sprite_container']['actions'] = [];
    $svg_sprite_element['sprite_container']['actions']['dialog_link'] = [
      '#type' => 'link',
      '#title' => t('Browse'),
      '#url' => Url::fromRoute(
        'svg_sprite_browser.widget_form',
        [
        ],[
        'query'=>[
          'selected_sheet' => $sheetDefaultValue,
          'selected_sprite' => $spriteDefaultValue,
          'sheet_field_id' => $sheetWidgetId,
          'sprite_field_id' => $spriteWidgetId,
          'preview_field_id' => $previewWidgetId,
          'sprite_sheets' =>implode(',',$sheets)
        ]]),
      '#attributes' => [
        'class' => [
          'use-ajax',
          'button',
          'button--primary',
        ],
      ],
    ];

    $svg_sprite_element['sprite_container']['actions']['clear'] = [
      '#type' => 'link',
      '#title' => t('Clear'),
      '#url' => Url::fromRoute(
        'svg_sprite_browser.clear_form',
        [
        ],[
        'query'=>[
          'sheet_field_id' => $sheetWidgetId,
          'sprite_field_id' => $spriteWidgetId,
          'preview_field_id' => $previewWidgetId,
        ]]),
      '#attributes' => [
        'class' => [
          'use-ajax',
          'button',
        ],
      ],
    ];
    $element += $svg_sprite_element;
    return $element;

  }
}
