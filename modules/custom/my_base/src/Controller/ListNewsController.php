<?php

namespace Drupal\my_base\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\oo_common\Helper\EntityHelper;
use Drupal\oo_common\Helper\FileHelper;
use Drupal\oo_common\Helper\CommonHelper;
use Drupal\my_base\Functions\CommonFunc;


class ListNewsController extends ControllerBase
{
  /**
   * {@inheritdoc}
   */
  public function index(){

    $language = CommonHelper::func_get_current_lang();
    $items = $this->get_article_news($language, 4);

    $element = array(
      '#theme' => 'list_news',
      '#params' => array(
        'title' => t('List news all'),
        'items' => $items,
        'pager' => array(
          '#type' => 'pager'
        )
      ),
    );

    return $element;
  }

  private function get_article_news($langcode, $limit = 4){

    $entity_manager = \Drupal::entityTypeManager();
    $conn = Database::getConnection();
    $query = $conn->select('node_field_data', 'pk')->extend('Drupal\Core\Database\Query\PagerSelectExtender');
    $query->fields('pk', array('nid', 'title'));
    $query->condition('type', 'news', '=');
    $query->condition('langcode', $langcode, '=');
    $query->condition('status', 1);
    $query->limit($limit);
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

  public function title() {
    return $this->t('News page');
  }

}
