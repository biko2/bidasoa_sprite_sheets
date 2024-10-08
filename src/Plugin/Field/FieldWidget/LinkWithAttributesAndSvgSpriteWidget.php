<?php

namespace Drupal\bidasoa_sprite_sheets\Plugin\Field\FieldWidget;

use Drupal\bidasoa_sprite_sheets\Services\SpriteSheetManager;
use Drupal\bidasoa_sprite_sheets\Services\SpriteSheetRenderer;
use Drupal\bidasoa_sprite_sheets\SvgSpriteHelper;
use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\link_attributes\LinkAttributesManager;
use Drupal\link_attributes\Plugin\Field\FieldWidget\LinkWithAttributesWidget;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'link' widget.
 *
 * @FieldWidget(
 *   id = "link_attributes_and_svg_sprite_widget",
 *   label = @Translation("Link (with attributes and svg sprite)"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class LinkWithAttributesAndSvgSpriteWidget extends LinkWithAttributesWidget {

  protected SpriteSheetRenderer $spriteSheetRenderer;

  protected SpriteSheetManager $spriteSheetManager;

  /**
   * Constructs a LinkWithAttributesWidget object.
   *
   * @param string $plugin_id
   *   The plugin_id for the widget.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the widget is associated.
   * @param array $settings
   *   The widget settings.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\link_attributes\LinkAttributesManager $link_attributes_manager
   *   The link attributes manager.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, LinkAttributesManager $link_attributes_manager, SpriteSheetRenderer $spriteSheetRenderer, SpriteSheetManager $spriteSheetManager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings, $link_attributes_manager);
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
      $container->get('plugin.manager.link_attributes'),
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

  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    $item_value = $items[$delta]->getValue();

    $svg_sprite_element = [
      '#type' => 'bidasoa_sprite_sheets_icon',
      '#title' => $this->fieldDefinition->getLabel(),
      '#description' => $this->fieldDefinition->getDescription(),
      '#required' => $this->fieldDefinition->isRequired(),
      '#sheets' => $this->getSetting('sheets'),
      '#default_value' => $item_value['options']['attributes']['icon'] ?? ['sheet' => SvgSpriteHelper::NONE_KEY, 'sprite' => SvgSpriteHelper::NONE_KEY],
    ];
    $element += $svg_sprite_element;
    return $element;
  }

  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $sheets = $this->spriteSheetManager->getSpriteSheetsFromIds($this->getSetting('sheets'));
    if (empty($sheets)) {
      $summary[] = $this->t('No sprite sheet selected.');
    }

    $labels = [];
    foreach ($sheets as $sheet) {
      $labels[] = $sheet->label();
    }
    if (!empty($labels)) {
      $summary[] = $this->t('Sprite sheets: @sheetLabels', ['@sheetLabels' => implode(', ', $labels)]);
    }
    return $summary;
  }
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as $delta => $value){
      $values[$delta]['options']['attributes']['icon'] = [
        'sheet' => $value['sprite_container']['sheet'],
        'sprite' => $value['sprite_container']['sprite'],
      ];
      /*$values[$delta]['attributes']['icon'] = [
        'sheet' => $value['sprite_container']['sheet'],
        'sprite' => $value['sprite_container']['sprite'],
      ];*/

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
