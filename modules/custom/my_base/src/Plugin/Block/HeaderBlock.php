<?php

namespace Drupal\my_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\image\Entity\ImageStyle;

/**
 * Provides 'Header' block.
 *
 * @Block(
 *   id = "header_block",
 *   admin_label = @Translation("Header block"),
 * )
 */
class HeaderBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    return [
      '#theme' => 'header_block',
      '#params' => [
        'is_front_page' => \Drupal::service('path.matcher')->isFrontPage()
      ],
    ];

  }

}
