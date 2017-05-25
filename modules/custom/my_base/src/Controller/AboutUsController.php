<?php

namespace Drupal\my_base\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\oo_common\Helper\EntityHelper;
use Drupal\oo_common\Helper\FileHelper;
use Drupal\oo_common\Helper\UtilityHelper;

/**
*
*/
class AboutUsController extends ControllerBase
{

	public function get_data($entity){
		$title = EntityHelper::getFieldValue($entity, 'title');
		$body = EntityHelper::getFieldValue($entity, 'body');
		$data = array(
			'title' => $title,
			'body' => $body
		);

		$element = array(
      		'#theme' => 'about_us_page',
      		'#params' => $data,
    	);

    	return $element;
	}
}
