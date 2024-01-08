<?php

namespace Drupal\bidasoa_sprite_sheets\Plugin\Field\FieldWidget;

use Drupal\bidasoa_sprite_sheets\Services\SpriteSheetManager;
use Drupal\bidasoa_sprite_sheets\Services\SpriteSheetRenderer;
use Drupal\bidasoa_sprite_sheets\SvgSpriteHelper;
use Drupal\Component\Utility\Html;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 * @FieldWidget(
 *   id = "field_svg_sprite_browser_widget",
 *   module = "bidasoa_sprite_sheets",
 *   label = @Translation("Svg sprite browser"),
 *   field_types = {
 *     "field_svg_sprite"
 *   }
 * )
 */
class FieldSvgSpriteBrowserWidget extends WidgetBase {

  protected SpriteSheetRenderer $spriteSheetRenderer;
  protected SpriteSheetManager $spriteSheetManager;

  /**
   *
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, SpriteSheetRenderer $spriteSheetRenderer, SpriteSheetManager $spriteSheetManager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->spriteSheetRenderer = $spriteSheetRenderer;
    $this->spriteSheetManager = $spriteSheetManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('bidasoa_sprite_sheets.renderer'),
      $container->get('bidasoa_sprite_sheets.manager')
    );
  }
  public static function defaultSettings() {
    return [
        'sheets' => [],
      ] + parent::defaultSettings();
  }

  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['sheets'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Sprite sheets'),
      '#options' => $this->spriteSheetManager->getSpriteSheetsAsOptions(),
      '#default_value' => $this->getSetting('sheets'),
      '#description' => $this->t('Allowed sprite sheets.'),
    ];
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $sheets = $this->spriteSheetManager->getSpriteSheetsFromIds($this->getSetting('sheets'));
    if(empty($sheets)){
      $summary[] = $this->t('No sprite sheet selected.');
    }

    $labels = [];
    foreach($sheets as $sheet){
      $labels[] = $sheet->label();
    }
    if(!empty($labels)){
      $summary[] = $this->t('Sprite sheets: @sheetLabels', ['@sheetLabels' => implode(', ',$labels)]);
    }
    return $summary;
  }
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $item_value = $items[$delta]->getValue();

    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $form['#attached']['library'][] = 'bidasoa_sprite_sheets/ajax';

    $svg_sprite_element = [];

    $spriteDefaultValue = (isset($item_value['sprite'])) ? $item_value['sprite'] : SvgSpriteHelper::NONE_KEY;

    $sheetDefaultValue = (isset($item_value['sheet'])) ? $item_value['sheet'] : SvgSpriteHelper::NONE_KEY;
    $selectedSpriteSheet = \Drupal::service('bidasoa_sprite_sheets.manager')->getSpriteSheetFromId($sheetDefaultValue);

    $sheets = $this->spriteSheetManager->getSpriteSheetsFromIds($this->getSetting('sheets'));

    $sheetWidgetId = Html::getUniqueId('sheet_widget');
    $spriteWidgetId = Html::getUniqueId('sprite_widget');
    $previewWidgetId = Html::getUniqueId('preview_widget');

    $svg_sprite_element['sprite_container'] = [
      '#type' => 'fieldset',
      '#title' => $this->fieldDefinition->getLabel(),
      '#description' => $this->fieldDefinition->getDescription(),
      '#required' => $this->fieldDefinition->isRequired(),
      '#element_validate' => [
        [$this, 'validate'],
      ],
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

    $svg_sprite_element['sprite_container']['sprite_preview'] = $this->spriteSheetRenderer->getRenderArray($selectedSpriteSheet,$spriteDefaultValue,$previewWidgetId);

    $svg_sprite_element['sprite_container']['actions'] = [];
    $svg_sprite_element['sprite_container']['actions']['dialog_link'] = [
      '#type' => 'link',
      '#title' => $this->t('Browse'),
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
      '#title' => $this->t('Clear'),
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

  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as &$value){
      $value = [
        'sheet' => $value['sprite_container']['sheet'],
        'sprite' => $value['sprite_container']['sprite'],
      ];
    }

    return parent::massageFormValues($values, $form, $form_state);
  }

  public static function addMoreAjax(array $form, FormStateInterface $form_state) {
    $form_state->setRebuild();
    return parent::addMoreAjax($form, $form_state); // TODO: Change the autogenerated stub
  }
  public function validate($element, FormStateInterface $form_state) {
    $value = [
      'sheet' => $element['sheet']['#value'],
      'sprite' => $element['sprite']['#value'],
    ];
    $form_state->setValueForElement($element, $value);
  }

}
