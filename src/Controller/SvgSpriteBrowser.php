<?php

namespace Drupal\bidasoa_sprite_sheets\Controller;

use Drupal\bidasoa_sprite_sheets\SvgSpriteHelper;
use Drupal\Core\Access\CsrfTokenGenerator;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class SvgSpriteBrowser extends ControllerBase {
  public function __construct(FormBuilder $formBuilder, CsrfTokenGenerator $csrfToken) {
    $this->formBuilder = $formBuilder;
    $this->csrfToken = $csrfToken;
  }

  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The Drupal service container.
   *
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder'),
      $container->get('csrf_token')
    );
  }
  /**
   * Callback for opening the modal form.
   */
  public function openSearchForm(Request $request) {
    $selectedSheet = $request->get('selected_sheet');
    $selectedSprite = $request->get('selected_sprite');
    $sheetFieldId = $request->get('sheet_field_id');
    $spriteFieldId = $request->get('sprite_field_id');
    $previewFieldId = $request->get('preview_field_id');

    $allowedSpriteSheets = $request->get('sprite_sheets');


    $response = new AjaxResponse();

    $allowedSpriteSheetsArray = explode(',' , $allowedSpriteSheets);
    // Get the modal form using the form builder.
    $modal_form = $this->formBuilder->getForm('Drupal\bidasoa_sprite_sheets\Form\SearchForm', $sheetFieldId,$spriteFieldId,$previewFieldId,$selectedSheet,$selectedSprite,$allowedSpriteSheetsArray);

    // Add an AJAX command to open a modal dialog with the form as the content.
    $response->addCommand(new OpenModalDialogCommand($this->t('Sprite selector'), $modal_form, ['width' => '75%', 'heigth' => '75%','classes'=> ['ui-dialog'=>'sprite-browser-modal']]));

    return $response;
  }

  public function clearForm(Request $request) {
    $selectedSheet = SvgSpriteHelper::NONE_KEY;
    $selectedSprite = SvgSpriteHelper::NONE_KEY;
    $sheetFieldId = $request->get('sheet_field_id');
    $spriteFieldId = $request->get('sprite_field_id');
    $previewFieldId = $request->get('preview_field_id');

    $response = new AjaxResponse();

    $response->addCommand(new InvokeCommand(NULL, 'svgSpriteBrowserDialogAjaxCallback', [$sheetFieldId,$spriteFieldId, $selectedSheet, $selectedSprite]));

    $spriteSheet = \Drupal::service('bidasoa_sprite_sheets.manager')->getSpriteSheetFromId($selectedSheet);
    $previewElement = \Drupal::service('bidasoa_sprite_sheets.renderer')->getRenderArray($spriteSheet,$selectedSprite,$previewFieldId);
    $response->addCommand(new ReplaceCommand("#" . $previewFieldId, $previewElement));

    return $response;
  }
}
