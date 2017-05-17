<?php

namespace Drupal\my_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\image\Entity\ImageStyle;

/**
 * Provides 'TopCommon' block.
 *
 * @Block(
 *   id = "topcommon_block",
 *   admin_label = @Translation("TopCommon block"),
 * )
 */
class TopCommonBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    return [
      '#theme' => 'topcommon_block',
      '#params' => [],
    ];

  }

}
