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

  // private $language;
  // function __construct(array $configuration, $plugin_id, $plugin_definition){
  //   parent::__construct($configuration, $plugin_id, $plugin_definition);
  //   $this->language = CommonHelper::func_get_current_lang();
  // }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $block_language_switcher =$this->generate_switcher_languages();

    $language = CommonHelper::func_get_current_lang();
    $breadcrumb = $this->get_breadcrumb($language);

    $menu_tree = \Drupal::menuTree();
    $parameters = new MenuTreeParameters();
    $parameters->setMaxDepth(2);
    $parameters->addCondition('langcode', $language, '=');
    $tree = $menu_tree->load('main', $parameters);

    $this->generateSubMenuTree($menu, $tree);

    return [
      '#theme' => 'header_block',
      '#params' => [
        'home_url' => \Drupal::request()->getSchemeAndHttpHost() . '/' . $language,
        'log_site' => file_url_transform_relative(file_create_url(theme_get_setting('logo.url'))),
        'top_menu' => $menu,
        'is_front_page' => \Drupal::service('path.matcher')->isFrontPage(),
        'breadcrumb' => $breadcrumb,
        'block_language_switcher' => $block_language_switcher
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

  private function get_breadcrumb($language) {
    $node = \Drupal::routeMatch()->getParameter('node');
    if(empty($node)) {
      return [];
    }
    $arr_menu = [
      [
        'title' => t('Home')->__toString(),
        'url' => \Drupal::request()->getSchemeAndHttpHost() . '/' . $language,
      ],
      [
        'title' => $node->getTitle(),
        'url' => '',
      ]
    ];

    return $arr_menu;
  }

  private function generate_switcher_languages() {
    $block = \Drupal\block\Entity\Block::load('languageswitcher');
    $block_content = \Drupal::entityManager()
      ->getViewBuilder('block')
      ->view($block);
    $block_language_switcher= drupal_render($block_content);
    
    //preg_match_all("/<ul class=\"links\">(.*)<\/ul>/", $block_language_switcher, $matches);
    //pr($matches);
    
    /*libxml_use_internal_errors(true);
    $dom = new \DOMDocument();
    @$dom->loadHTML();
    libxml_clear_errors();
    $xpath = new \DOMXpath($dom);
    $nodes = $xpath->query(".//li ");
    //$nodes = $xml->getElementsByTagName('li');
    foreach ($nodes as $node) {
      pr($node);
    }*/
    
    return $block_language_switcher;
  }
}
