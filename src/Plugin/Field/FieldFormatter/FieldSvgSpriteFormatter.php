<?php

namespace Drupal\bidasoa_sprite_sheets\Plugin\Field\FieldFormatter;

use Drupal\bidasoa_sprite_sheets\Services\SpriteSheetRenderer;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\svg_sprite\Services\Renderer;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'field_example_simple_text' formatter.
 *
 * @FieldFormatter(
 *   id = "field_svg_sprite_formatter",
 *   module = "bidasoa_sprite_sheets",
 *   label = @Translation("Svg sprite formatter"),
 *   field_types = {
 *     "field_svg_sprite"
 *   }
 * )
 */
class FieldSvgSpriteFormatter extends FormatterBase {

  protected  $spriteSheetRenderer;
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, SpriteSheetRenderer $spriteSheetRenderer) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->spriteSheetRenderer = $spriteSheetRenderer;
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
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('bidasoa_sprite_sheets.renderer')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {

      $elements[$delta] = $this->svg_sprite_renderer->getRenderArray($item->getValue()['sprite']);
    }

    return $elements;
  }

}
