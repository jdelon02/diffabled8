<?php

namespace Drupal\slick_test;

use Drupal\slick\SlickSkinInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Implements SlickSkinInterface as registered via hook_slick_skins_info().
 */
class SlickSkinTest implements SlickSkinInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function skins() {
    $path = base_path() . drupal_get_path('module', 'slick_test');
    $skins = [
      'test' => [
        'name' => 'Test',
        'description' => $this->t('Test slick skins.'),
        'group' => 'main',
        'provider' => 'slick_test',
        'css' => [
          'theme' => [
            $path . '/css/slick.theme--test.css' => [],
          ],
        ],
        'options' => [
          'zoom' => TRUE,
        ],
      ],
    ];

    return $skins;
  }

  /**
   * {@inheritdoc}
   */
  public function arrows() {
    $path = base_path() . drupal_get_path('module', 'slick_test');
    $skins = [
      'arrows' => [
        'name' => 'Arrows',
        'description' => $this->t('Test slick arrows.'),
        'provider' => 'slick_test',
        'group' => 'arrows',
        'css' => [
          'theme' => [
            $path . '/css/slick.theme--arrows.css' => [],
          ],
        ],
      ],
    ];

    return $skins;
  }

  /**
   * {@inheritdoc}
   */
  public function dots() {
    $path = base_path() . drupal_get_path('module', 'slick_test');
    $skins = [
      'dots' => [
        'name' => 'Dots',
        'description' => $this->t('Test slick dots.'),
        'provider' => 'slick_test',
        'group' => 'dots',
        'css' => [
          'theme' => [
            $path . '/css/slick.theme--dots.css' => [],
          ],
        ],
      ],
    ];

    return $skins;
  }

}
