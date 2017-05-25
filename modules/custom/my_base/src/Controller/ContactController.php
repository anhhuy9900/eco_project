<?php

namespace Drupal\my_base\Controller;

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


    $element = array(
          '#theme' => 'contact_page',
          '#params' => array(),
      );

    return $element;
  }
}
