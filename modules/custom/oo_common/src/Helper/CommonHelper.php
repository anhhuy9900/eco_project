<?php
namespace Drupal\oo_common\Helper;

class CommonHelper {

  public static function func_get_current_lang($langcode = 'vi') {
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    return $language ? $language : $langcode;
  }
}
