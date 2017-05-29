<?php

namespace Drupal\my_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\image\Entity\ImageStyle;

/**
 * Provides 'Footer' block.
 *
 * @Block(
 *   id = "footer_block",
 *   admin_label = @Translation("Footer block"),
 * )
 */
class FooterBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    return [
      '#theme' => 'footer_block',
      '#params' => [
      	'contact_address' => \Drupal::state()->get('eco_contact_address'),
      	'contact_phone' => \Drupal::state()->get('eco_contact_phone'),
      	'contact_fax' => \Drupal::state()->get('eco_contact_fax'),
      	'contact_email' => \Drupal::state()->get('eco_contact_email'),
      	'working_time' => \Drupal::state()->get('eco_working_time'),
      	'slogan_footer' => \Drupal::state()->get('eco_slogan_footer'),
      ],
    ];

  }

}
