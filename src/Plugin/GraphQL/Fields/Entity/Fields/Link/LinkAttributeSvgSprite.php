<?php

namespace Drupal\bidasoa_sprite_sheets\Plugin\GraphQL\Fields\Entity\Fields\Link;

use Drupal\Component\Utility\NestedArray;
use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\link\LinkItemInterface;
use Drupal\graphql\Plugin\GraphQL\Fields\FieldPluginBase;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Retrieve specific attributes of a menu link.
 *
 * @GraphQLField(
 *   id = "link_item_attribute_svg_sprite",
 *   secure = true,
 *   name = "svg_sprite",
 *   type = "Map",
 *   field_types = {"link"},
 *   deriver = "Drupal\graphql_core\Plugin\Deriver\Fields\EntityFieldPropertyDeriver"
 * )
 */
class LinkAttributeSvgSprite extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  protected function resolveValues($value, array $args, ResolveContext $context, ResolveInfo $info) {
    if ($value instanceof LinkItemInterface) {
      $options = $value->getUrl()->getOptions();

      $attributeValue = NestedArray::getValue($options, ['attributes', 'icon']);
      yield $attributeValue ? ['icon' => $attributeValue] : [];

    }
  }

}
