<?php

namespace Drupal\my_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Database\Database;
use Drupal\oo_common\Helper\CommonHelper;

/**
 * Provides 'RightMenu' block.
 *
 * @Block(
 *   id = "right_menu_block",
 *   admin_label = @Translation("RightMenu block"),
 * )
 */
class RightMenuBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
	$node = \Drupal::routeMatch()->getParameter('node');
	$language = CommonHelper::func_get_current_lang();
	$right_menu_items = CommonFunc::func_get_another_node($node->getType(), $node->id(), $language);
  
	return [
	  '#theme' => 'right_menu_block',
	  '#params' => [
	  	'right_menu_items' => $right_menu_items
	  ],
	];

  }

}
