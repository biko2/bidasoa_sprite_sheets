<?php
namespace Drupal\bidasoa_sprite_sheets\Services;


use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;

class SpriteSheetLoader {

    protected ConfigFactoryInterface $configFactory;
    protected MessengerInterface $messenger;

    public function __construct( ConfigFactoryInterface $configFactory, MessengerInterface $messenger ) {
      $this->configFactory = $configFactory;
      $this->messenger = $messenger;
    }

    public function getFilePath($spriteSheet) : Url {
      return Url::fromUserInput( $spriteSheet->path);
    }

    public function getFileContent($path) : ?String {
      if(!file_exists($path)){
        $this->messenger->addError(t('SVG Sprite file not found.'));
        return null;
      }
      return file_get_contents($path);
    }
}

