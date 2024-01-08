<?php

namespace Drupal\bidasoa_sprite_sheets;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * extend Drupal's Twig_Extension class
 */
class twigSvgSprites extends AbstractExtension {

  /**
   * {@inheritdoc}
   * Let Drupal know the name of your extension
   * must be unique name, string
   */
  public function getName() {
    return 'svg_sprite';
  }

  /**
   * {@inheritdoc}
   * Return your custom twig function to Drupal
   */
  public function getFunctions() {
    return [
      'svgSprite' => new TwigFunction('svgSprite', array($this, 'svgSprite'),array('is_safe'=>array('html'))),
      'svg_sprite' => new TwigFunction('svgSprite', array($this, 'svgSprite'),array('is_safe'=>array('html')))
    ];
  }

  /**
   * returns a svg sprite
   *
   * @param string $id
   *   id of the sprite
   *
   * @param string $customClass
   *   value of the query parameter name
   *
   * @return array
   */
  public static function svgSprite($id,$customClass = null) {
    $svg_sprite_renderer = \Drupal::service('bidasoa_sprite_sheets.renderer');
    return $svg_sprite_renderer->getRenderArray($id,$customClass);
  }

}
