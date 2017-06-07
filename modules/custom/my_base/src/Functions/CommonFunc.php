<?php 

namespace Drupal\my_base\Functions;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\oo_common\Helper\EntityHelper;
use Drupal\oo_common\Helper\FileHelper;
use Drupal\oo_common\Helper\CommonHelper;

/**
* 
*/
class CommonFunc 
{
	
  public static function get_project_articles_by_cid($langcode, $tid = 0) {
    $entity_manager = \Drupal::entityTypeManager();

    $conn = Database::getConnection();
    $query = $conn->select('node_field_data', 'pk');
    $query->join('node__field_categories', 'fk', 'fk.entity_id = pk.nid');
    $query->fields('pk', array('nid', 'title', 'type'));
    $query->addField('fk', 'field_categories_target_id', 'category_id');
    $query->condition('pk.type', 'projects', '=');
    //$query->condition('fk.field_categories_target_id', $tid, '=');
    $query->condition('pk.langcode', $langcode, '=');
    $query->condition('fk.langcode', $langcode, '=');
    $query->condition('pk.status', 1);
    $data = $query->execute()->fetchAll();

    if($data){
      foreach ($data as $key => $value) {
        $entity = $entity_manager->getStorage('node')->load($value->nid);
        $image = FileHelper::getImageInfoByFid( EntityHelper::getFieldValue($entity, 'field_image', array( 'type' => 'image')) , array('home_project_thumb'));
        $value->image = $image;
        $url = Url::fromRoute('entity.node.canonical', ['node' => $value->nid], ['absolute' => TRUE]);
        $value->url = $url->toString();
        $value->height = self::project_image_random();
      }
    }

    return $data;
  }

  public static function func_get_another_node($node_type, $nid, $langcode){

    $conn = Database::getConnection();
    $data = $conn->select('node_field_data', 'pk')
    ->fields('pk', array('nid', 'title'))
    ->condition('nid', $nid, '!=')
    ->condition('type', $node_type, '=')
    ->condition('langcode', $langcode, '=')
    ->condition('status', 1)
    ->execute()
    ->fetchAll();
    if($data){
    foreach ($data as $key => $value) {
      $url = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' => $value->nid], ['absolute' => TRUE]);
      $value->url = $url->toString();
    }
    }

    return $data;
  }

  public static function project_image_random() {
      $array_height = array(
        'rd-height-445', 
        'rd-height-413', 
        'rd-height-367', 
        'rd-height-349', 
        'rd-height-284', 
        'rd-height-250', 
        'rd-height-200', 
      );
      $key = rand(0, 6);
      return $array_height[$key];
  }

  public static function eco_render_menu_management_admin() {
    $list_menus = array(
      array(
        'title' => 'Quản lý Menu',
        'description' => 'Quản lý bài viết phần Menu',
        'url' => '/admin/structure/menu/manage/main'
      ),
      array(
        'title' => 'Quản lý Mục Trang chủ',
        'description' => 'Quản lý bài viết phần Trang chủ',
        'url' => '/admin/home-management'
      ),
      array(
        'title' => 'Quản lý Giới Thiệu',
        'description' => 'Quản lý bài viết Giới Thiệu',
        'url' => '/admin/about-us-management'
      ),
      array(
        'title' => 'Quản lý Tuyển dụng',
        'description' => 'Quản lý bài viết Tuyển dụng',
        'url' => '/admin/recruiment-management'
      ),
      array(
        'title' => 'Quản lý Lĩnh vực hoạt động',
        'description' => 'Quản lý bài viết Lĩnh vực hoạt động',
        'url' => '/admin/field-operation-management'
      ),
      array(
        'title' => 'Quản lý Dự án',
        'description' => 'Quản lý bài viết Dự án',
        'url' => '/admin/projects-management'
      ),
      array(
        'title' => 'Quản lý Thành viên và đối tác',
        'description' => 'Quản lý bài viết Thành viên và đối tác',
        'url' => '/admin/partners-management'
      ),
      array(
        'title' => 'Quản lý Năng lực',
        'description' => 'Quản lý bài viết Năng lực',
        'url' => '/admin/talent-management'
      ),
      array(
        'title' => 'Quản lý Tin Tức',
        'description' => 'Quản lý bài viết Tin Tức',
        'url' => '/admin/news-management'
      ),
    );

    return $list_menus;
  }

  public static function eco_get_menu_link_management() {
    $list_menus = CommonFunc::eco_render_menu_management_admin();
    foreach ($list_menus as $key => $value) {
      $links[] = $value['url'];
    }

    return $links;
  }
}