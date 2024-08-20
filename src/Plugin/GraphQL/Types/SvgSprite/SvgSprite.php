<?php
namespace Drupal\bidasoa_sprite_sheets\Plugin\GraphQL\Types\SvgSprite;


use Drupal\bidasoa_sprite_sheets\BidasoaSpriteSheetsIcon;
use Drupal\Core\Entity\EntityInterface;
use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql\Plugin\GraphQL\Types\TypePluginBase;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * GraphQL type representing Svg sprites.
 *
 * @GraphQLType(
 *   id = "svg_sprite",
 *   name = "SvgSprite"
 * )
 */
class SvgSprite  extends TypePluginBase {
  public function applies($object, ResolveContext $context, ResolveInfo $info) {
    if ($object instanceof BidasoaSpriteSheetsIcon) {
      return TRUE;
    }

    return FALSE;
  }
}
