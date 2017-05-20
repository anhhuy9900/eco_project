<?php
namespace Drupal\oo_common\Helper;

class ConvertHelper {

  /**
   * Convert array to object.
   *
   * @param array $arr
   *   The array to be converted.
   *
   * @return object
   *   The stdClass has been converted.
   */
  public static function parseObject($arr) {
    return json_decode(json_encode($arr));
  }

  /**
   * Convert object to array.
   *
   * @param object $obj
   *   The object to be converted.
   *
   * @return array
   *   The array has been converted.
   */
  public static function parseArray($obj) {
    return json_decode(json_encode($obj), TRUE);
  }

  /**
   * Convert string to integer.
   *
   * @param string $str
   *   The string to be converted.
   *
   * @return int
   *   The number has been converted.
   */
  public static function parseInt($str) {
    return intval($str);
  }

  /**
   * Convert string to float.
   *
   * @param string $str
   *   The string to be converted.
   *
   * @return float
   *   The number has been converted.
   */
  public static function parseFloat($str) {
    return floatval($str);
  }

  /**
   * Convert mixed to json.
   *
   * @param mixed $data
   *   The mixed type to be converted.
   *
   * @return string
   *   The string has been encoded.
   */
  public static function parseJson($data) {
    return json_encode($data);
  }

  /**
   * Strip 4 byte string.
   *
   * @param string $str
   *   The text to be processed.
   *
   * @return string
   *   The text without 4 byte characters
   */
  public static function strip4Byte($str) {
    return preg_replace('%(?:
          \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
        | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
        | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
    )%xs', '', $str);
  }

  /**
   * Convert string to plain text.
   *
   * @param string $str
   *   The text to be processed.
   *
   * @return string
   *   The plain text.
   */
  public static function parsePlain($str) {
    if (is_object($str) || is_array($str)) {
      return '';
    }
    $str = str_replace(array('/', '\'', '"', ';', '<', '>', '?'), '', $str);
    $str = self::strip4Byte($str);
    return htmlspecialchars(strip_tags($str));
  }

  /**
   * Removes backslashes recursive.
   *
   * @param mixed $data
   *   The mixed data to be converted.
   *
   * @return mixed
   *   The mixed data has been converted
   */
  public static function stripslashesRecursive($data) {
    if (is_array($data)) {
      $data = array_map('self::stripslashesRecursive', $data);
    }
    elseif (is_object($data)) {
      $vars = get_object_vars($data);
      foreach ($vars as $key => $value) {
        $data->{$key} = self::stripslashesRecursive($value);
      }
    }
    elseif (is_string($data)) {
      $data = stripslashes($data);
    }

    return $data;
  }

  /**
   * Strip the string from HTML tags recursive.
   *
   * @param mixed $data
   *   The mixed data to be converted.
   *
   * @return mixed
   *   The mixed data has been converted
   */
  public static function stripTagsRecursive($data) {
    if (is_array($data)) {
      $data = array_map('self::stripTagsRecursive', $data);
    }
    elseif (is_object($data)) {
      $vars = get_object_vars($data);
      foreach ($vars as $key => $value) {
        $data->{$key} = self::stripTagsRecursive($value);
      }
    }
    elseif (is_string($data)) {
      $data = strip_tags($data);
    }

    return $data;
  }

  /**
   * Remove double white space.
   *
   * @param string $param
   *   The string to be trimmed.
   *
   * @return string
   *   The string has been trimmed.
   */
  public static function trimDoubleWhiteSpace($param) {
    return preg_replace('/[ \t\r\n][ \t\r\n]+/', ' ', $param);
  }

  /**
   * Remove double white space and trim.
   *
   * @param string $str
   *   The string to be processed.
   *
   * @return string
   *   The string has been processed.
   */
  public static function trimComplete($str) {
    return trim(self::trimDoubleWhiteSpace($str));
  }

  /**
   * Remove double white space and trim for all value of an array.
   *
   * @param array $params
   *   The array to be processed.
   *
   * @return array
   *   The array has been processed.
   */
  public static function trimCompleteArray($params = array()) {
    if (is_array($params)) {
      foreach ($params as $key => $value) {
        if (is_array($value)) {
          $value = self::trimCompleteArray($value);
        }
        elseif (is_string($value)) {
          $value = self::trimComplete($value);
        }

        $params[$key] = $value;
      }
    }
    return $params;
  }

  /**
   * Remove special characters.
   *
   * @param string $str
   *   The text to be processed.
   *
   * @return string
   *   The text without special characters.
   */
  public static function removeSpecialCharacters($str) {
    return preg_replace('/[^a-zA-Z0-9.]/s', '', $str);
  }

  /**
   * Split text by max characters.
   *
   * @param string $text
   *   The text to be processed.
   * @param int $maxChars
   *   The length of new text.
   * @param string $end
   *   The text end with.
   *
   * @return string
   *   The text has been processed.
   */
  public static function splitString($text, $maxChars = 140, $end = '...') {

    if (strlen($text) > $maxChars || $text == '') {
      $words = preg_split('/\s/', $text);
      $output = '';
      $i = 0;
      while (1) {
        $length = strlen($output) + strlen($words[$i]);
        if ($length > $maxChars) {
          break;
        }
        else {
          $output .= " " . $words[$i];
          ++$i;
        }
      }
      $output .= $end;
    }
    else {
      $output = $text;
    }
    return $output;
  }

  /**
   * Convert array to XML.
   *
   * @param array $array
   *   The array to be processed.
   * @param bool|XMLElement $xml
   *   The XML element or FALSE if you want to create root element.
   *
   * @return string
   *   The XML string.
   */
  public static function arrayToXml($array, $xml = FALSE) {
    if ($xml === FALSE) {
      $xml = new SimpleXMLElement('<root/>');
    }
    foreach ($array as $key => $value) {
      if (is_array($value)) {
        self::arrayToXml($value, $xml->addChild($key));
      }
      else {
        $xml->addChild($key, $value);
      }
    }
    return $xml->asXML();
  }

  /**
   * Xss clean data input.
   *
   * @param string $data
   *   The text to be processed.
   *
   * @return string
   *   The clean string.
   */
  public static function xssClean($data) {
    if (is_array($data) || is_object($data)) {
      return '';
    }
    // Fix &entity\n;.
    $data = str_replace(
      array('&amp;', '&lt;', '&gt;'),
      array('&amp;amp;', '&amp;lt;', '&amp;gt;'),
      $data
    );
    $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
    $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
    $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

    // Remove any attribute starting with "on" or xmlns.
    $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

    // Remove javascript: and vbscript: protocols.
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
    $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

    // Only works in IE:
    // <span style="width: expression(alert('Ping!'));"></span>.
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
    $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

    // Remove namespaced elements (we do not need them)
    $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

    do {
      // Remove really unwanted tags.
      $old_data = $data;
      $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
    } while ($old_data !== $data);

    // We are done...
    return $data;
  }

}
