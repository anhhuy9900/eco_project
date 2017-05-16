<?php 

namespace Drupal\eco\Controller;

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
	
	function __construct()
	{
		# code...
	}

	public function get_data($entity){
		$title = EntityHelper::getFieldValue($entity, 'title');
		$body = EntityHelper::getFieldValue($entity, 'body');
		$data = array(
			'title' => $title,
			'body' => $body
		);

		$element = array(
      		'#theme' => 'about_us_page',
      		'#data' => $data,
    	);

    	return $element;
	}
}