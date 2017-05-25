<?php
namespace Drupal\my_config\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\my_config\MyConfigInterface;

/**
 * Defines the MyConfig entity.
 *
 * @ConfigEntityType(
 *   id = "my_config",
 *   label = @Translation("My Config"),
 *   handlers = {
 *     "list_builder" = "Drupal\my_config\Controller\MyConfigListBuilder",
 *     "form" = {
 *       "add" = "Drupal\my_config\Form\MyConfigForm",
 *       "edit" = "Drupal\my_config\Form\MyConfigForm",
 *       "delete" = "Drupal\my_config\Form\MyConfigDeleteForm",
 *     }
 *   },
 *   config_prefix = "my_config",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   links = {
 *     "edit-form" = "/admin/my_config/{my_config}",
 *     "delete-form" = "/admin/my_config/{my_config}/delete",
 *   }
 * )
 */
class MyConfig extends ConfigEntityBase implements MyConfigInterface {

  /**
   * The MyConfig ID.
   *
   * @var string
   */
  public $id;

  /**
   * The MyConfig label.
   *
   * @var string
   */
  public $label;

  // Your specific configuration property get/set methods go here,
  // implementing the interface.
}
