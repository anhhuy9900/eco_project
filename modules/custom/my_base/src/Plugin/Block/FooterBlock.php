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
        'log_site' => file_url_transform_relative(file_create_url(theme_get_setting('logo.url'))),
      	'contact_address' => \Drupal::state()->get('eco_contact_address'),
      	'contact_phone' => \Drupal::state()->get('eco_contact_phone'),
      	'contact_fax' => \Drupal::state()->get('eco_contact_fax'),
      	'contact_email' => \Drupal::state()->get('eco_contact_email'),
      	'working_time' => \Drupal::state()->get('eco_working_time'),
        'slogan_footer' => \Drupal::state()->get('eco_slogan_footer'),
        'eco_fb_social' => \Drupal::state()->get('eco_fb_social'),
        'eco_google_social' => \Drupal::state()->get('eco_google_social'),
        'eco_twitter_social' => \Drupal::state()->get('eco_twitter_social'),
      	'eco_footer_summary' => \Drupal::state()->get('eco_footer_summary'),
      ],
    ];

  }

}
