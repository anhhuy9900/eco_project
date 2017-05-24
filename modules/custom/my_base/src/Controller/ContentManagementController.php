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
    $list_menus = array(
      array(
        'title' => 'Quản lý Giới Thiệu',
        'description' => 'Quản lý nội dung phần Giới Thiệu',
        'url' => 'admin/about-us-management'
      )
    );
    $element = array(
      '#theme' => 'content_management',
      '#params' => array(
        'list_menus'     => $list_menus
      ),
    );

    return $element;
  }

}
