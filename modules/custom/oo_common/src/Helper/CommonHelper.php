<?php
namespace Drupal\oo_common\Helper;

use Drupal\Core\Database\Database;

class CommonHelper {

  public static function func_get_current_lang($langcode = 'vi') {
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    return $language ? $language : $langcode;
  }

  public static function func_get_taxonomy_term($term, $langcode = 'vi') {
    $conn = Database::getConnection();
    $data = $conn->select('taxonomy_term_field_data', 'pk')
    ->fields('pk', array('tid', 'name'))
    ->condition('vid', $term, '=')
    ->condition('langcode', $langcode)
    ->orderBy('weight', 'ASC')
    ->execute()
    ->fetchAll();
    $arr = array();

    foreach ($data as $term) {
      $obj = new \stdClass();
      $obj->tid = $term->tid;
      $obj->name = $term->name;
      $arr[] = $obj;
    }

    return $arr;
  }

  /**
   *
   * Validate Email format
   *
   */
  public static function func_is_valid_email($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) { // The email address is valid
      return TRUE;
    }
    else { // The email address is not valid
      return FALSE;
    }
  }

  /**
   *
   * Validate Email format
   *
   */
  public static function func_is_valid_phonenumber($phone_number) {
    if (filter_var(
      $phone_number,
      FILTER_VALIDATE_EMAIL
    )) { // The email address is valid
      return TRUE;
    }
    else { // The email address is not valid
      return FALSE;
    }
  }
  
}
