<?php
namespace Drupal\oo_common\Helper;

use Drupal\Core\Entity\Entity;
use Drupal\field_collection\Entity\FieldCollectionItem;

/**
 * Entity class.
 */
class EntityHelper {

  /**
   * Get field values.
   *
   * @param object $entity
   *   The entity load by entity_load, node_load, user_load,
   *   taxonomy_term_load, field_collection_item_load, etc.
   * @param string $fieldName
   *   Machine name of the field.
   * @param int $index
   *   The index begin from zero, input less than zero to list all values.
   * @param string $key
   *   The key, eg: value, target_id.
   *
   * @return mixed
   *   String|Array|NULL
   *
   * @code
   *   $body = OoEntity::getFieldValues($entity, 'body');
   * @endcode
   */
  public static function getFieldValues(
    Entity $entity,
    $fieldName,
    $index = 0,
    $key = 'value',
    $default_value = ''
  ) {
    if (empty($entity)) {
      return NULL;
    }
    /*foreach ($entity as $key => $value) {
      _dump($key);
    }
    die;*/

    if($entity->{$fieldName}) {
      $object = $entity->{$fieldName};
    } else {
      $object = false;
    }

    if (!$object) {
      return $default_value;
    }
    else {
      $vals = $object->getValue();
      if ($index < 0) {
        $res = array();
        foreach ($vals as $entity) {
          $res[] = $entity[$key];
        }

        return $res;
      }
      if (isset($vals[$index]) && isset($vals[$index][$key])) {
        return $vals[$index][$key];
      }
    }

    return NULL;
  }

  public static function getFieldValue(
    $entity,
    $fieldName,
    $options = array(
      'type' => 'string'
    ),
    $default_value = '',
    $limit = 0
  ) {
    if (empty($entity)) {
      return NULL;
    }
    /*foreach ($entity as $key => $value) {
      _dump($key);
    }
    die;*/

    if($entity->{$fieldName}) {
      $object = $entity->{$fieldName};
    } else {
      $object = false;
    }

    if (!$object) {
      return $default_value;
    }
    else {
      $vals = $object->getValue();
      if(isset($options['type'])) {
        $type = $options['type'];
      } else {
        $type = 'string';
      }
      switch ($type) {
        case 'string':
          $key = 'value';
          break;
        case 'integer':
          $key = 'target_id';
          break;
        case 'term':
          $key = 'target_id';
          break;
        case 'image':
          $key = 'target_id';
          break;
        case 'collection':
          $key = 'value';
          break;
        default:
          $key = 'value';
          break;
      }
      
      if ($limit > 0) {
        $res = array();
        foreach ($vals as $value) {
          if(isset($value[$key])) {
            $res[] = $value[$key];
          } else {
            if(isset($value['target_id'])) {
              $res[] = $value['target_id'];
            } else {
              $res[] = $default_value;
            }
          }
        }
        return $res;
      } else {
        if (isset($vals[0])) {
          if(isset($vals[0][$key])) {
            return $vals[0][$key];
          } else {
            if(isset($vals[0]['target_id'])) {
              return $vals[0]['target_id'];
            } else {
              return $default_value;
            }
          }
        } else {
          return $default_value;
        }
      }
    }
  }


  /**
   * Get collections.
   *
   * @param object $entity
   *   The entity load by entity_load.
   * @param string $fieldName
   *   Machine name of the field.
   * @param int $index
   *   The index begin from zero, input less than zero to list all values.
   *
   * @return mixed
   *   String|Array|NULL
   */
  public static function getCollections($entity, $fieldName, $index = 0) {
    $cids = self::getFieldValues($entity, $fieldName, $index);

    if (empty($cids)) {
      return NULL;
    }
    if ($index < 0) {
      return FieldCollectionItem::loadMultiple($cids);
    }
    return FieldCollectionItem::load($cids);
  }

  /**
   * Get collection field value(s)
   *
   * @param object $entity
   *   The entity load by field_collection_item_load.
   * @param string $fieldName
   *   Machine name of the field.
   * @param int $index
   *   The index begin from zero, input less than zero to list all values.
   * @param string $key
   *   The key, eg: value, target_id.
   *
   * @return mixed
   *   String|Array|NULL
   *
   * @code
   *   OoEntity::getCollectionFieldValues($collection, 'field_detail');
   * @endcode
   */
  public static function getCollectionFieldValues(
    $entity,
    $fieldName,
    $index = 0,
    $key = 'value'
  ) {
    self::getFieldValues($entity, $fieldName, $index, $key);
  }

  public static function getNodeById($id) {
    $entity_manager = \Drupal::entityTypeManager();
    $entity = $entity_manager->getStorage('node')->load($id);

    return $entity;
  }

  public static function getTermById($id) {
    $entity_manager = \Drupal::entityTypeManager();
    $entity = $entity_manager->getStorage('taxonomy_term')->load($id);
    return $entity;
  }

  public static function getFieldCollectionById($id) {
    $entity = FieldCollectionItem::load($id);
    return $entity;
  }
}
