<?php

namespace Drupal\my_base\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\oo_common\Helper\EntityHelper;
use Drupal\oo_common\Helper\FileHelper;
use Drupal\oo_common\Helper\CommonHelper;
use Drupal\my_base\Functions\CommonFunc;


class ProjectsController extends ControllerBase
{
  /**
   * {@inheritdoc}
   */
  public function index(){

    $language = CommonHelper::func_get_current_lang();
    $project_categories = CommonHelper::func_get_taxonomy_term('project_categories', $language);
    $arr_projects = CommonFunc::get_project_articles_by_cid($language);

    $element = array(
      '#theme' => 'project_all_page',
      '#params' => array(
        'title'               => t('Projects All'),
        'project_categories'  => $project_categories,
        'arr_projects'        => $arr_projects,
      ),
    );

    return $element;
  }

  public function title() {
    return $this->t('Projects page');
  }

}
