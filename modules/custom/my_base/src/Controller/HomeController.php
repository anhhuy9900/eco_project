<?php

namespace Drupal\my_base\Controller;
use Drupal\Core\Controller\ControllerBase;

class HomeController extends ControllerBase
{
    /**
     * {@inheritdoc}
     */
    function index(){
        $data = [
            '#theme' => 'home_page',
            '#params' => [],
        ];
        return $data;
    }
}
