<?php

namespace Drupal\my_base\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\oo_common\Helper\EntityHelper;
use Drupal\oo_common\Helper\FileHelper;
use Drupal\oo_common\Helper\CommonHelper;
use Drupal\my_base\Functions\CommonFunc;

class ContentManagementController extends ControllerBase
{
  /**
   * {@inheritdoc}
   */
  public function index(){
    global $base_url;
    $language = CommonHelper::func_get_current_lang();
    $curent_base_url = $base_url . '/' . $language ;

    $list_menus = CommonFunc::eco_render_menu_management_admin();
    $element = array(
      '#theme' => 'content_management',
      '#params' => array(
        'curent_base_url'     => $curent_base_url,
        'list_menus'     => $list_menus
      ),
    );

    return $element;
  }

}
