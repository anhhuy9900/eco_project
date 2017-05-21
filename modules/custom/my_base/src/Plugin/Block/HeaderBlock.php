<?php

namespace Drupal\my_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\oo_common\Helper\CommonHelper;

/**
 * Provides 'Header' block.
 *
 * @Block(
 *   id = "header_block",
 *   admin_label = @Translation("Header block"),
 * )
 */
class HeaderBlock extends BlockBase {
  
  private $language;
  function __construct(array $configuration, $plugin_id, $plugin_definition){
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->language = CommonHelper::func_get_current_lang();
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $language = CommonHelper::func_get_current_lang();
    $menu_tree = \Drupal::menuTree();
    $parameters = new MenuTreeParameters();
    $parameters->setMaxDepth(2);
    $parameters->addCondition('langcode', $language, '=');
    $tree = $menu_tree->load('main', $parameters);

    $this->generateSubMenuTree($menu, $tree);

    return [
      '#theme' => 'header_block',
      '#params' => [
        'log_site' => file_url_transform_relative(file_create_url(theme_get_setting('logo.url'))),
        'top_menu' => $menu,
        'is_front_page' => \Drupal::service('path.matcher')->isFrontPage()
      ],
    ];

  }

  private function generateSubMenuTree(&$output, &$input, $parent = FALSE) {

    $input = array_values($input);
    foreach($input as $key => $item) {
      //If menu element disabled skip this branch
      if ($item->link->isEnabled()) {
        $name = $item->link->getTitle();
        $url = $item->link->getUrlObject();
        $url_string = $url->toString() ? $url->toString() : '#';

        //If not root element, add as child
        if ($parent === FALSE) {
          $output[$key] = [
            'name' => $name,
            'url' => $url_string
          ];
        } else {
          $output['child'][$key] = [
            'name' => $name,
            'url' => $url_string
          ];
        }

        if ($item->hasChildren) {
          if ($item->depth == 1) {
            $this->generateSubMenuTree($output[$key], $item->subtree, $key);
          } else {
            $this->generateSubMenuTree($output['child'][$key], $item->subtree, $key);
          }
        }
      }
    }
  }


}
