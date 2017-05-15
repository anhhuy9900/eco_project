<?php

namespace Drupal\eco\Controller;
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
