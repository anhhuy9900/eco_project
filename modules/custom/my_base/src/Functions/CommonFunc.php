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
}