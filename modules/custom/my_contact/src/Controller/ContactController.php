<?php

namespace Drupal\my_contact\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\oo_common\Helper\EntityHelper;
use Drupal\oo_common\Helper\FileHelper;
use Drupal\oo_common\Helper\UtilityHelper;

/**
*
*/
class ContactController extends ControllerBase
{

  public function index(){
  	$form_class = '\Drupal\my_contact\Form\ContactForm';

    $element = array(
        '#theme' => 'contact_page',
        '#form' => \Drupal::formBuilder()->getForm($form_class),
        '#params' => array(
          'eco_fb_social' => \Drupal::state()->get('eco_fb_social'),
          'eco_google_social' => \Drupal::state()->get('eco_google_social'),
          'eco_twitter_social' => \Drupal::state()->get('eco_twitter_social')
        ),
    );

    return $element;
  }
}
