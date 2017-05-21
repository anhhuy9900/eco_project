<?php

namespace Drupal\my_base\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\oo_common\Helper\EntityHelper;
use Drupal\oo_common\Helper\FileHelper;

class HomeController extends ControllerBase
{
    /**
     * {@inheritdoc}
     */
    public function get_data($entity){

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
                'home_banner_url' => $banner_image['origin_url'],
                'home_sliders' => $home_sliders
            ),
        );

        return $element;
    }
}
