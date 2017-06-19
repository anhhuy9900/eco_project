<?php

namespace Drupal\my_base\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\oo_common\Helper\EntityHelper;
use Drupal\oo_common\Helper\FileHelper;
use Drupal\oo_common\Helper\CommonHelper;
use Drupal\my_base\Functions\CommonFunc;

class HomeController extends ControllerBase
{
  /**
   * {@inheritdoc}
   */
  public function get_data($entity){

    $language = CommonHelper::func_get_current_lang();
    $project_categories = CommonHelper::func_get_taxonomy_term('project_categories', $language);
    $arr_projects = CommonFunc::get_project_articles_by_cid($language);

    $banner_image = FileHelper::getImageInfoByFid(EntityHelper::getFieldValue($entity, 'field_banner', array( 'type' => 'image')));
    $home_sliders_collections = EntityHelper::getFieldValue($entity, 'field_home_sliders',  array('type' => 'collection'), '', 10);
    $home_sliders = [];
    foreach ($home_sliders_collections as $key => $collection_id) {
      $home_sliders_entity =  EntityHelper::getFieldCollectionById($collection_id);
      $home_sliders[] = array(
      'title' => EntityHelper::getFieldValue($home_sliders_entity, 'field_home_slider_title', array( 'type' => 'string')),
      'summary' => EntityHelper::getFieldValue($home_sliders_entity, 'field_home_slider_summary', array( 'type' => 'string')),
      'image' => FileHelper::getImageInfoByFid( EntityHelper::getFieldValue($home_sliders_entity, 'field_home_slider_image', array( 'type' => 'image')) , array('home_slider')),
      );
    }

    $home_slogan = EntityHelper::getFieldValue($entity, 'field_home_slogan',  array('type' => 'string'));
    $title_box_project = EntityHelper::getFieldValue($entity, 'field_title_box_project',  array('type' => 'string'));
    $des_box_project = EntityHelper::getFieldValue($entity, 'field_description_box_project',  array('type' => 'string'));
    $title_box_news = EntityHelper::getFieldValue($entity, 'field_title_box_news',  array('type' => 'string'));

    $element = array(
      '#theme' => 'home_page',
      '#params' => array(
        'home_banner_url'             => $banner_image['origin_url'],
        'home_sliders'                => $home_sliders,
        'why_choose_us'               => self::get_why_choose_us_data($entity),
        'improve_your_life'           => self::get_improve_your_life_data($entity),
        'project_categories'          => $project_categories,
        'arr_projects'                => $arr_projects,
        'articles_news_latest'        => self::get_2_article_news_lastest($language),
        'home_slogan'                 => $home_slogan,
        'title_box_project'           => $title_box_project,
        'des_box_project'             => $des_box_project,
        'title_box_news'              => $title_box_news,
      ),
    );

    return $element;
  }

  public static function get_2_article_news_lastest($langcode) {
    $entity_manager = \Drupal::entityTypeManager();
    $conn = Database::getConnection();
    $query = $conn->select('node_field_data', 'pk');
    $query->fields('pk', array('nid', 'title'));
    $query->condition('type', 'news', '=');
    $query->condition('langcode', $langcode, '=');
    $query->condition('status', 1);
    $query->orderBy('nid', 'DESC');
    $query->range(0, 2);
    $data = $query->execute()->fetchAll();
    if($data){
      foreach ($data as $key => $value) {
        $entity = $entity_manager->getStorage('node')->load($value->nid);
        $url = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' => $value->nid], ['absolute' => TRUE]);
        $value->url = $url->toString();
        $image = FileHelper::getImageInfoByFid( EntityHelper::getFieldValue($entity, 'field_image', array( 'type' => 'image')) , array('news_thumbnail'));
        $value->image = $image;
        $value->description = EntityHelper::getFieldValue($entity, 'field_description', array( 'type' => 'string'));
      }
    }

    return $data;
  }

  public static function get_why_choose_us_data($entity) {
    $why_choose_us = new \stdClass();
    $why_choose_us_cid = EntityHelper::getFieldValue($entity, 'field_why_choose_us_box',  array('type' => 'collection'));
    $field_why_choose_us_box =  EntityHelper::getFieldCollectionById($why_choose_us_cid);
    $why_choose_us->title = EntityHelper::getFieldValue($field_why_choose_us_box, 'field_title', array( 'type' => 'string'));
    $why_choose_us->summary = EntityHelper::getFieldValue($field_why_choose_us_box, 'field_summary', array( 'type' => 'string'));
    $why_choose_us->link = EntityHelper::getFieldValue($field_why_choose_us_box, 'field_link', array( 'type' => 'string'));

    $choose_us_box = [];
    $field_choose_us_box = EntityHelper::getFieldValue($field_why_choose_us_box, 'field_choose_us_box',  array('type' => 'collection'), '', 3);
    foreach ($field_choose_us_box as $key => $collection_id) {
      $collection_entity =  EntityHelper::getFieldCollectionById($collection_id);
      $choose_us_box[] = array(
      'title' => EntityHelper::getFieldValue($collection_entity, 'field_title', array( 'type' => 'string')),
      'summary' => EntityHelper::getFieldValue($collection_entity, 'field_summary', array( 'type' => 'string')),
      'image' => FileHelper::getImageInfoByFid( EntityHelper::getFieldValue($collection_entity, 'field_image', array( 'type' => 'image')) , array('choose_us_thumb')),
      );
    }
    $why_choose_us->choose_us_box = $choose_us_box;
    return $why_choose_us;
  }

  public static function get_improve_your_life_data($entity) {
    $improve_your_life = new \stdClass();
    $improve_your_life_cid = EntityHelper::getFieldValue($entity, 'field_improve_your_life_box',  array('type' => 'collection'));
    $field_improve_your_life_box =  EntityHelper::getFieldCollectionById($improve_your_life_cid);
    $improve_your_life->title = EntityHelper::getFieldValue($field_improve_your_life_box, 'field_title', array( 'type' => 'string'));
    $improve_your_life->summary = EntityHelper::getFieldValue($field_improve_your_life_box, 'field_summary', array( 'type' => 'string'));
    $improve_your_life->image = FileHelper::getImageInfoByFid( EntityHelper::getFieldValue($field_improve_your_life_box, 'field_image', array( 'type' => 'image')) , array('improve_your_life_image'));

    $images_your_life_box = [];
    $field_images_your_life_box = EntityHelper::getFieldValue($field_improve_your_life_box, 'field_images_your_life_box',  array('type' => 'collection'), '', 3);
    foreach ($field_images_your_life_box as $key => $collection_id) {
      $collection_entity =  EntityHelper::getFieldCollectionById($collection_id);
      $images_your_life_box[] = array(
        'title' => EntityHelper::getFieldValue($collection_entity, 'field_title', array( 'type' => 'string')),
        'image' => FileHelper::getImageInfoByFid( EntityHelper::getFieldValue($collection_entity, 'field_image', array( 'type' => 'image')) , array('image_your_life_box_thumb')),
        'link' => EntityHelper::getFieldValue($collection_entity, 'field_link', array( 'type' => 'string'))
      );
    }
    $improve_your_life->images_your_life_box = $images_your_life_box;
    return $improve_your_life;
  }

}
