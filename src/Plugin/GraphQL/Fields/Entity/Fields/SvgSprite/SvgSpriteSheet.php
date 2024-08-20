<?php

namespace Drupal\bidasoa_sprite_sheets\Plugin\GraphQL\Fields\Entity\Fields\SvgSprite;

use Drupal\bidasoa_sprite_sheets\BidasoaSpriteSheetsIcon;
use Drupal\Core\Menu\MenuLinkTreeElement;
use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql\Plugin\GraphQL\Fields\FieldPluginBase;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Retrieve sheet attribute of a svg sprite.
 *
 * @GraphQLField(
 *   id = "svg_sprite_sheet",
 *   secure = true,
 *   name = "sheet",
 *   type = "string",
 *   parents = {"SvgSprite"},
 * )
 */
class SvgSpriteSheet extends FieldPluginBase {
  public function resolveValues($value, array $args, ResolveContext $context, ResolveInfo $info) {
    if ($value instanceof BidasoaSpriteSheetsIcon) {
      yield $value->getSheet();
    }
  }
}
