<?php

namespace Drupal\my_base\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\oo_common\Helper\EntityHelper;
use Drupal\oo_common\Helper\FileHelper;
use Drupal\oo_common\Helper\CommonHelper;


class HomeController extends ControllerBase
{
  /**
   * {@inheritdoc}
   */
  public function get_data($entity){

    $language = CommonHelper::func_get_current_lang();
    $project_categories = CommonHelper::func_get_taxonomy_term('project_categories', $language);
    $arr_projects = self::get_project_articles_by_cid($language);

    $banner_image = FileHelper::getImageInfoByFid(EntityHelper::getFieldValue($entity, 'field_banner', array( 'type' => 'image')));

    $home_sliders_collections = EntityHelper::getFieldValue($entity, 'field_home_sliders',  array(), array(), 10);
    $home_sliders = [];
    foreach ($home_sliders_collections as $key => $collection_id) {
      $home_sliders_entity =  EntityHelper::getFieldCollectionById($collection_id);
      $home_sliders[] = array(
      'title' => EntityHelper::getFieldValue($home_sliders_entity, 'field_home_slider_title', array( 'type' => 'string')),
      'summary' => EntityHelper::getFieldValue($home_sliders_entity, 'field_home_slider_summary', array( 'type' => 'string')),
      'image' => FileHelper::getImageInfoByFid( EntityHelper::getFieldValue($home_sliders_entity, 'field_home_slider_image', array( 'type' => 'image')) , array('home_slider')),
      );

    }

    $element = array(
      '#theme' => 'home_page',
      '#params' => array(
        'home_banner_url'     => $banner_image['origin_url'],
        'home_sliders'        => $home_sliders,
        'project_categories'  => $project_categories,
        'arr_projects'        => $arr_projects,
      ),
    );

    return $element;
  }

  private function get_project_articles_by_cid($langcode, $tid = 0){
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
      }
    }

    return $data;
  }
}
