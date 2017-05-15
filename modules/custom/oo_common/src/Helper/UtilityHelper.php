<?php
namespace Drupal\oo_common\Helper;

class UtilityHelper {

  /**
   * Slice a hash to include only the given keys.
   *
   * This is useful for limiting an options hash to valid keys
   *   before passing to a method.
   *
   * @param array $arr
   *   The array to be processed.
   * @param array $keys
   *   The array of keys to be kept.
   *
   * @return array
   *   The array include only the given keys
   */
  public static function sliceHash($arr, $keys) {
    if (empty($keys)) {
      return array();
    }
    $result = array();
    foreach ($keys as $key) {
      $result[$key] = $arr[$key];
    }
    return $result;
  }

  /**
   * Trim all element values in array.
   *
   * @param array $params
   *   The array to be trimmed.
   *
   * @return array
   *   The array has been trimmed.
   */
  public static function trimArray($params = array()) {
    if (is_array($params)) {
      foreach ($params as $key => $value) {
        if (is_array($value)) {
          $value = self::trimArray($value);
        }
        elseif (is_string($value)) {
          $value = trim($value);
        }

        $params[$key] = $value;
      }
    }
    return $params;
  }

  /**
   * Strip all empty elements.
   *
   * @param array $params
   *   The array to be stripped.
   * @param int $exceptNumeric
   *   Except numeric (1) or not (0).
   * @param int $trimArray
   *   Trim array (1) or not (0).
   */
  public static function stripEmptyElements($params = array(), $exceptNumeric = 1, $trimArray = 1) {
    if (is_array($params)) {
      if ($trimArray) {
        $params = self::trimArray($params);
      }

      foreach ($params as $key => $value) {
        if (is_array($value)) {
          $value = self::stripEmptyElements($value, $exceptNumeric, 0);
        }

        if (empty($value) && (!is_numeric($value) || !$exceptNumeric)) {
          unset($params[$key]);
        }
      }
    }

    return $params;
  }

  /**
   * Get video id from url.
   *
   * @param string $url
   *   The video url user copy from browser address bar.
   * @param string $service
   *   The video service (youtube, youtu.be, vimeo, dailymotion, youku).
   *
   * @return string
   *   The video id or NULL if the video service if not supported
   *     or can't find the video id.
   */
  public static function getVideoIdFromUrl($url, $service = 'youtube') {
    $vid = NULL;
    switch ($service) {
      case 'youtube':
      case 'youtu.be':
        preg_match_all('/(youtu.be\/|\/watch\?v=|\/embed\/)([a-z0-9\-_]+)/i', $url, $matches);
        $vid = $matches[2][0];
        break;

      case 'vimeo':
        preg_match_all('/vimeo.com\/(.*)/i', $url, $matches);
        $vid = $matches[1][0];
        break;

      case 'dailymotion':
        preg_match_all('/^.+dailymotion.com\/(?:video|swf\/video|embed\/video|hub|swf)\/([^&?]+)/', $url, $matches);

        $vid = $matches[1][0];
        break;

      case 'youku':
        preg_match("/\/\/v\.youku\.com\/v_show\/id_(\w+)/", $url, $matches);
        $vid = isset($matches[1]) ? $matches[1] : NULL;
        break;

      default:
        break;
    }
    return $vid;
  }

  /**
   * Get all urls from text.
   *
   * @param string $text
   *   The text to get all urls.
   *
   * @return mixed
   *   Return array if found urls or NULL if not found.
   */
  public static function getUrlsFromText($text) {
    $regEx = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

    if (preg_match($regEx, $text, $urls)) {
      return $urls;
    }
    return NULL;
  }

  /**
   * Get current page url.
   *
   * @return string
   *   The current page url.
   */
  public static function getCurrentUrl() {
    $pageUrl = 'http';
    if ($_SERVER["HTTPS"] == "on") {
      $pageUrl .= "s";
    }
    $pageUrl .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
      $pageUrl .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    }
    else {
      $pageUrl .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }

    return $pageUrl;
  }

  /**
   * Add hyperlink to text.
   *
   * @param string $text
   *   The text contains url will be converted to hyperlink.
   *
   * @return string
   *   The text has been converted if has url or origin text if not.
   */
  public static function addHyperlink($text) {
    $regEx = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

    if (preg_match($regEx, $text, $urls)) {
      return preg_replace($regEx, '<a href="' . $urls[0] . '" target="_blank">' . $urls[0] . '</a> ', $text);
    }
    return $text;
  }

  /**
   * Set cookie value.
   *
   * @param string $name
   *   The name of the cookie.
   * @param string $value
   *   The value of the cookie. This value is stored on the clients computer.
   * @param int $expire
   *   The time the cookie expires.
   *   This is a Unix timestamp so is in number of seconds since the epoch.
   */
  public static function setCookie($name, $value, $expire = 0) {
    $expire = (int) $expire;

    if (!empty($expire)) {
      $expire = time() + $expire;
    }

    if (defined("COOKIE_DOMAIN") && FALSE !== strpos($_SERVER['HTTP_HOST'], COOKIE_DOMAIN)) {
      setcookie($name, $value, $expire, '/', COOKIE_DOMAIN);
    }
    else {
      setcookie($name, $value, $expire, '/');
    }
  }

  /**
   * Remove cookie value.
   *
   * @param string $key
   *   The cookie name.
   */
  public static function unsetCookie($key) {
    self::setCookie($key, "", time() - 3600);
  }

  /**
   * Remove all cookies.
   */
  public static function destroyCookies() {
    foreach ($_COOKIE as $name => $value) {
      self::unsetCookie($name);
    }
  }

  /**
   * Check the password strong or not.
   *
   * @param string $password
   *   The password to be checked.
   *
   * @return bool
   *   TRUE if the password is strong, FALSE if not.
   */
  public static function isStrongPassword($password) {
    $matchLowerChar = preg_match("/[a-z]+/", $password);
    $matchUpperChar = preg_match("/[A-Z]+/", $password);
    $matchNumber = preg_match("/[0-9]+/", $password);
    $matchSpecialChar = preg_match("/[\W]+/", $password);

    $successCount = 0;

    $successCount += $matchLowerChar ? 1 : 0;
    $successCount += $matchUpperChar ? 1 : 0;
    $successCount += $matchNumber ? 1 : 0;
    $successCount += $matchSpecialChar ? 1 : 0;

    if ($successCount < 4) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Check the mobile number valid or not.
   *
   * @param string $param
   *   The mobile number to be checked.
   *
   * @return bool
   *   TRUE if the mobile number is valid, FALSE if not.
   */
  public static function isValidMobileNumber($param) {
    $regex = "/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{1,3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i";
    return preg_match($regex, $param);
  }

  /**
   * Check the country code valid or not.
   *
   * @param string $param
   *   The country code to be checked.
   *
   * @return bool
   *   TRUE if the country code is valid, FALSE if not.
   */
  public static function isValidCountryCode($param) {
    $trashed = '';
    $check = FALSE;
    if (substr_count($param, "+") > 0) {
      if (strpos($param, "+") == 0) {
        $param = substr($param, 1, strlen($param));
      }
      $result = ereg("^[0-9]+$", $param, $trashed);
      if ($result) {
        $check = TRUE;
      }
    }
    return $check;
  }

  /**
   * Check the json string valid or not.
   *
   * @param string $jsonStr
   *   The json string to be checked.
   *
   * @return bool
   *   TRUE if the json string is valid, FALSE if not.
   */
  public static function isJson($jsonStr) {
    return is_string($jsonStr) && is_object(json_decode($jsonStr)) && (json_last_error() == JSON_ERROR_NONE) ? TRUE : FALSE;
  }

  /**
   * Check the email valid or not.
   *
   * @param string $email
   *   The email to be checked.
   *
   * @return bool
   *   TRUE if the email is valid, FALSE if not.
   */
  public static function isValidEmail($email) {

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Insert element(s) to an array.
   *
   * @param array $array
   *   The array to be processed.
   * @param mixed $elements
   *   The element or elements (array) to be inserted.
   * @param int $position
   *   The position to be inserted.
   *
   * @return array
   *   The merged array.
   */
  public static function insertToArray($array, $elements, $position = 0) {

    if (!is_array($array)) {
      return array();
    }

    if (empty($elements)) {
      return $array;
    }

    if (!is_array($elements)) {
      $elements = (array) $elements;
    }

    $position = min($position, count($array));

    if ($position == 0) {
      return $elements + $array;
    }
    elseif ($position == count($array)) {
      return $array + $elements;
    }

    return array_splice($array, $position, 0, $elements);
  }

  /**
   * Print variable with <pre> wrap and php print_r function.
   *
   * @param mixed $variable
   *   The variable to be printed.
   * @param bool $exit
   *   Execute php exit function or not.
   */
  public static function pr($variable = '', $exit = FALSE) {
    echo '<pre>';
    print_r($variable);
    echo '</pre>';
    if ($exit) {
      exit();
    }
  }

  /**
   * Get array item value by key,
   * NULL if not found without php warning.
   *
   * @param array $arr
   *   The array to be checked.
   * @param mixed $key
   *   The key to be searched.
   *
   * @return mixed
   *   NULL if not found, mixed if found.
   */
  public static function getArrVal($arr, $key) {
    if (!isset($arr[$key])) {
      return NULL;
    }
    return $arr[$key];
  }

  /**
   * Generate random string.
   *
   * @param int $length
   *   The random string length.
   *
   * @return string
   *   The random string has been processed.
   */
  public static function generateRandomString($length = 10) {

    $characters = '-_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; ++$i) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
  }

  /**
   * Check the device is mobile or not.
   *
   * @return bool
   *   TRUE if the device is mobile, FALSE if not.
   */
  public static function isMobile() {

    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER['HTTP_USER_AGENT']);
  }

  /**
   * Check url is external or not.
   *
   * @param string $url
   *   The url to be checked.
   *
   * @return bool
   *   TRUE if the url is external, FALSE if not.
   */
  public static function isExternalUrl($url) {
    if (empty($url)) {
      return FALSE;
    }

    $linkUrl = parse_url($url);
    $homeUrl = parse_url($_SERVER['HTTP_HOST']);

    if (isset($linkUrl['host'])) {
      if ($linkUrl['host'] != $homeUrl['path']) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Remove Vietnamese and other languages accents.
   *
   * @param string $string
   *   The text to be removed accents.
   *
   * @return string
   *   The text has been removed accents.
   */
  public static function removeAccents($string) {
    if (!preg_match('/[\x80-\xff]/', $string)) {
      return $string;
    }

    if (seems_utf8($string)) {
      $chars = array(
        // Decompositions for Latin-1 Supplement.
        chr(194) . chr(170) => 'a',
        chr(194) . chr(186) => 'o',
        chr(195) . chr(128) => 'A',
        chr(195) . chr(129) => 'A',
        chr(195) . chr(130) => 'A',
        chr(195) . chr(131) => 'A',
        chr(195) . chr(132) => 'A',
        chr(195) . chr(133) => 'A',
        chr(195) . chr(134) => 'AE',
        chr(195) . chr(135) => 'C',
        chr(195) . chr(136) => 'E',
        chr(195) . chr(137) => 'E',
        chr(195) . chr(138) => 'E',
        chr(195) . chr(139) => 'E',
        chr(195) . chr(140) => 'I',
        chr(195) . chr(141) => 'I',
        chr(195) . chr(142) => 'I',
        chr(195) . chr(143) => 'I',
        chr(195) . chr(144) => 'D',
        chr(195) . chr(145) => 'N',
        chr(195) . chr(146) => 'O',
        chr(195) . chr(147) => 'O',
        chr(195) . chr(148) => 'O',
        chr(195) . chr(149) => 'O',
        chr(195) . chr(150) => 'O',
        chr(195) . chr(153) => 'U',
        chr(195) . chr(154) => 'U',
        chr(195) . chr(155) => 'U',
        chr(195) . chr(156) => 'U',
        chr(195) . chr(157) => 'Y',
        chr(195) . chr(158) => 'TH',
        chr(195) . chr(159) => 's',
        chr(195) . chr(160) => 'a',
        chr(195) . chr(161) => 'a',
        chr(195) . chr(162) => 'a',
        chr(195) . chr(163) => 'a',
        chr(195) . chr(164) => 'a',
        chr(195) . chr(165) => 'a',
        chr(195) . chr(166) => 'ae',
        chr(195) . chr(167) => 'c',
        chr(195) . chr(168) => 'e',
        chr(195) . chr(169) => 'e',
        chr(195) . chr(170) => 'e',
        chr(195) . chr(171) => 'e',
        chr(195) . chr(172) => 'i',
        chr(195) . chr(173) => 'i',
        chr(195) . chr(174) => 'i',
        chr(195) . chr(175) => 'i',
        chr(195) . chr(176) => 'd',
        chr(195) . chr(177) => 'n',
        chr(195) . chr(178) => 'o',
        chr(195) . chr(179) => 'o',
        chr(195) . chr(180) => 'o',
        chr(195) . chr(181) => 'o',
        chr(195) . chr(182) => 'o',
        chr(195) . chr(184) => 'o',
        chr(195) . chr(185) => 'u',
        chr(195) . chr(186) => 'u',
        chr(195) . chr(187) => 'u',
        chr(195) . chr(188) => 'u',
        chr(195) . chr(189) => 'y',
        chr(195) . chr(190) => 'th',
        chr(195) . chr(191) => 'y',
        chr(195) . chr(152) => 'O',
        // Decompositions for Latin Extended-A.
        chr(196) . chr(128) => 'A',
        chr(196) . chr(129) => 'a',
        chr(196) . chr(130) => 'A',
        chr(196) . chr(131) => 'a',
        chr(196) . chr(132) => 'A',
        chr(196) . chr(133) => 'a',
        chr(196) . chr(134) => 'C',
        chr(196) . chr(135) => 'c',
        chr(196) . chr(136) => 'C',
        chr(196) . chr(137) => 'c',
        chr(196) . chr(138) => 'C',
        chr(196) . chr(139) => 'c',
        chr(196) . chr(140) => 'C',
        chr(196) . chr(141) => 'c',
        chr(196) . chr(142) => 'D',
        chr(196) . chr(143) => 'd',
        chr(196) . chr(144) => 'D',
        chr(196) . chr(145) => 'd',
        chr(196) . chr(146) => 'E',
        chr(196) . chr(147) => 'e',
        chr(196) . chr(148) => 'E',
        chr(196) . chr(149) => 'e',
        chr(196) . chr(150) => 'E',
        chr(196) . chr(151) => 'e',
        chr(196) . chr(152) => 'E',
        chr(196) . chr(153) => 'e',
        chr(196) . chr(154) => 'E',
        chr(196) . chr(155) => 'e',
        chr(196) . chr(156) => 'G',
        chr(196) . chr(157) => 'g',
        chr(196) . chr(158) => 'G',
        chr(196) . chr(159) => 'g',
        chr(196) . chr(160) => 'G',
        chr(196) . chr(161) => 'g',
        chr(196) . chr(162) => 'G',
        chr(196) . chr(163) => 'g',
        chr(196) . chr(164) => 'H',
        chr(196) . chr(165) => 'h',
        chr(196) . chr(166) => 'H',
        chr(196) . chr(167) => 'h',
        chr(196) . chr(168) => 'I',
        chr(196) . chr(169) => 'i',
        chr(196) . chr(170) => 'I',
        chr(196) . chr(171) => 'i',
        chr(196) . chr(172) => 'I',
        chr(196) . chr(173) => 'i',
        chr(196) . chr(174) => 'I',
        chr(196) . chr(175) => 'i',
        chr(196) . chr(176) => 'I',
        chr(196) . chr(177) => 'i',
        chr(196) . chr(178) => 'IJ',
        chr(196) . chr(179) => 'ij',
        chr(196) . chr(180) => 'J',
        chr(196) . chr(181) => 'j',
        chr(196) . chr(182) => 'K',
        chr(196) . chr(183) => 'k',
        chr(196) . chr(184) => 'k',
        chr(196) . chr(185) => 'L',
        chr(196) . chr(186) => 'l',
        chr(196) . chr(187) => 'L',
        chr(196) . chr(188) => 'l',
        chr(196) . chr(189) => 'L',
        chr(196) . chr(190) => 'l',
        chr(196) . chr(191) => 'L',
        chr(197) . chr(128) => 'l',
        chr(197) . chr(129) => 'L',
        chr(197) . chr(130) => 'l',
        chr(197) . chr(131) => 'N',
        chr(197) . chr(132) => 'n',
        chr(197) . chr(133) => 'N',
        chr(197) . chr(134) => 'n',
        chr(197) . chr(135) => 'N',
        chr(197) . chr(136) => 'n',
        chr(197) . chr(137) => 'N',
        chr(197) . chr(138) => 'n',
        chr(197) . chr(139) => 'N',
        chr(197) . chr(140) => 'O',
        chr(197) . chr(141) => 'o',
        chr(197) . chr(142) => 'O',
        chr(197) . chr(143) => 'o',
        chr(197) . chr(144) => 'O',
        chr(197) . chr(145) => 'o',
        chr(197) . chr(146) => 'OE',
        chr(197) . chr(147) => 'oe',
        chr(197) . chr(148) => 'R',
        chr(197) . chr(149) => 'r',
        chr(197) . chr(150) => 'R',
        chr(197) . chr(151) => 'r',
        chr(197) . chr(152) => 'R',
        chr(197) . chr(153) => 'r',
        chr(197) . chr(154) => 'S',
        chr(197) . chr(155) => 's',
        chr(197) . chr(156) => 'S',
        chr(197) . chr(157) => 's',
        chr(197) . chr(158) => 'S',
        chr(197) . chr(159) => 's',
        chr(197) . chr(160) => 'S',
        chr(197) . chr(161) => 's',
        chr(197) . chr(162) => 'T',
        chr(197) . chr(163) => 't',
        chr(197) . chr(164) => 'T',
        chr(197) . chr(165) => 't',
        chr(197) . chr(166) => 'T',
        chr(197) . chr(167) => 't',
        chr(197) . chr(168) => 'U',
        chr(197) . chr(169) => 'u',
        chr(197) . chr(170) => 'U',
        chr(197) . chr(171) => 'u',
        chr(197) . chr(172) => 'U',
        chr(197) . chr(173) => 'u',
        chr(197) . chr(174) => 'U',
        chr(197) . chr(175) => 'u',
        chr(197) . chr(176) => 'U',
        chr(197) . chr(177) => 'u',
        chr(197) . chr(178) => 'U',
        chr(197) . chr(179) => 'u',
        chr(197) . chr(180) => 'W',
        chr(197) . chr(181) => 'w',
        chr(197) . chr(182) => 'Y',
        chr(197) . chr(183) => 'y',
        chr(197) . chr(184) => 'Y',
        chr(197) . chr(185) => 'Z',
        chr(197) . chr(186) => 'z',
        chr(197) . chr(187) => 'Z',
        chr(197) . chr(188) => 'z',
        chr(197) . chr(189) => 'Z',
        chr(197) . chr(190) => 'z',
        chr(197) . chr(191) => 's',
        // Decompositions for Latin Extended-B.
        chr(200) . chr(152) => 'S',
        chr(200) . chr(153) => 's',
        chr(200) . chr(154) => 'T',
        chr(200) . chr(155) => 't',
        // Euro Sign.
        chr(226) . chr(130) . chr(172) => 'E',
        // GBP (Pound) Sign.
        chr(194) . chr(163) => '',
        // Vowels with diacritic (Vietnamese)
        // unmarked.
        chr(198) . chr(160) => 'O',
        chr(198) . chr(161) => 'o',
        chr(198) . chr(175) => 'U',
        chr(198) . chr(176) => 'u',
        // Grave accent.
        chr(225) . chr(186) . chr(166) => 'A',
        chr(225) . chr(186) . chr(167) => 'a',
        chr(225) . chr(186) . chr(176) => 'A',
        chr(225) . chr(186) . chr(177) => 'a',
        chr(225) . chr(187) . chr(128) => 'E',
        chr(225) . chr(187) . chr(129) => 'e',
        chr(225) . chr(187) . chr(146) => 'O',
        chr(225) . chr(187) . chr(147) => 'o',
        chr(225) . chr(187) . chr(156) => 'O',
        chr(225) . chr(187) . chr(157) => 'o',
        chr(225) . chr(187) . chr(170) => 'U',
        chr(225) . chr(187) . chr(171) => 'u',
        chr(225) . chr(187) . chr(178) => 'Y',
        chr(225) . chr(187) . chr(179) => 'y',
        // Hook.
        chr(225) . chr(186) . chr(162) => 'A',
        chr(225) . chr(186) . chr(163) => 'a',
        chr(225) . chr(186) . chr(168) => 'A',
        chr(225) . chr(186) . chr(169) => 'a',
        chr(225) . chr(186) . chr(178) => 'A',
        chr(225) . chr(186) . chr(179) => 'a',
        chr(225) . chr(186) . chr(186) => 'E',
        chr(225) . chr(186) . chr(187) => 'e',
        chr(225) . chr(187) . chr(130) => 'E',
        chr(225) . chr(187) . chr(131) => 'e',
        chr(225) . chr(187) . chr(136) => 'I',
        chr(225) . chr(187) . chr(137) => 'i',
        chr(225) . chr(187) . chr(142) => 'O',
        chr(225) . chr(187) . chr(143) => 'o',
        chr(225) . chr(187) . chr(148) => 'O',
        chr(225) . chr(187) . chr(149) => 'o',
        chr(225) . chr(187) . chr(158) => 'O',
        chr(225) . chr(187) . chr(159) => 'o',
        chr(225) . chr(187) . chr(166) => 'U',
        chr(225) . chr(187) . chr(167) => 'u',
        chr(225) . chr(187) . chr(172) => 'U',
        chr(225) . chr(187) . chr(173) => 'u',
        chr(225) . chr(187) . chr(182) => 'Y',
        chr(225) . chr(187) . chr(183) => 'y',
        // Tilde.
        chr(225) . chr(186) . chr(170) => 'A',
        chr(225) . chr(186) . chr(171) => 'a',
        chr(225) . chr(186) . chr(180) => 'A',
        chr(225) . chr(186) . chr(181) => 'a',
        chr(225) . chr(186) . chr(188) => 'E',
        chr(225) . chr(186) . chr(189) => 'e',
        chr(225) . chr(187) . chr(132) => 'E',
        chr(225) . chr(187) . chr(133) => 'e',
        chr(225) . chr(187) . chr(150) => 'O',
        chr(225) . chr(187) . chr(151) => 'o',
        chr(225) . chr(187) . chr(160) => 'O',
        chr(225) . chr(187) . chr(161) => 'o',
        chr(225) . chr(187) . chr(174) => 'U',
        chr(225) . chr(187) . chr(175) => 'u',
        chr(225) . chr(187) . chr(184) => 'Y',
        chr(225) . chr(187) . chr(185) => 'y',
        // Acute accent.
        chr(225) . chr(186) . chr(164) => 'A',
        chr(225) . chr(186) . chr(165) => 'a',
        chr(225) . chr(186) . chr(174) => 'A',
        chr(225) . chr(186) . chr(175) => 'a',
        chr(225) . chr(186) . chr(190) => 'E',
        chr(225) . chr(186) . chr(191) => 'e',
        chr(225) . chr(187) . chr(144) => 'O',
        chr(225) . chr(187) . chr(145) => 'o',
        chr(225) . chr(187) . chr(154) => 'O',
        chr(225) . chr(187) . chr(155) => 'o',
        chr(225) . chr(187) . chr(168) => 'U',
        chr(225) . chr(187) . chr(169) => 'u',
        // Dot below.
        chr(225) . chr(186) . chr(160) => 'A',
        chr(225) . chr(186) . chr(161) => 'a',
        chr(225) . chr(186) . chr(172) => 'A',
        chr(225) . chr(186) . chr(173) => 'a',
        chr(225) . chr(186) . chr(182) => 'A',
        chr(225) . chr(186) . chr(183) => 'a',
        chr(225) . chr(186) . chr(184) => 'E',
        chr(225) . chr(186) . chr(185) => 'e',
        chr(225) . chr(187) . chr(134) => 'E',
        chr(225) . chr(187) . chr(135) => 'e',
        chr(225) . chr(187) . chr(138) => 'I',
        chr(225) . chr(187) . chr(139) => 'i',
        chr(225) . chr(187) . chr(140) => 'O',
        chr(225) . chr(187) . chr(141) => 'o',
        chr(225) . chr(187) . chr(152) => 'O',
        chr(225) . chr(187) . chr(153) => 'o',
        chr(225) . chr(187) . chr(162) => 'O',
        chr(225) . chr(187) . chr(163) => 'o',
        chr(225) . chr(187) . chr(164) => 'U',
        chr(225) . chr(187) . chr(165) => 'u',
        chr(225) . chr(187) . chr(176) => 'U',
        chr(225) . chr(187) . chr(177) => 'u',
        chr(225) . chr(187) . chr(180) => 'Y',
        chr(225) . chr(187) . chr(181) => 'y',
      );

      $string = strtr($string, $chars);
    }
    else {
      // Assume ISO-8859-1 if not UTF-8.
      $chars['in'] = chr(128) . chr(131) . chr(138) . chr(142) . chr(154) . chr(158)
      . chr(159) . chr(162) . chr(165) . chr(181) . chr(192) . chr(193) . chr(194)
      . chr(195) . chr(196) . chr(197) . chr(199) . chr(200) . chr(201) . chr(202)
      . chr(203) . chr(204) . chr(205) . chr(206) . chr(207) . chr(209) . chr(210)
      . chr(211) . chr(212) . chr(213) . chr(214) . chr(216) . chr(217) . chr(218)
      . chr(219) . chr(220) . chr(221) . chr(224) . chr(225) . chr(226) . chr(227)
      . chr(228) . chr(229) . chr(231) . chr(232) . chr(233) . chr(234) . chr(235)
      . chr(236) . chr(237) . chr(238) . chr(239) . chr(241) . chr(242) . chr(243)
      . chr(244) . chr(245) . chr(246) . chr(248) . chr(249) . chr(250) . chr(251)
      . chr(252) . chr(253) . chr(255);

      $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

      $string = strtr($string, $chars['in'], $chars['out']);
      $double_chars['in'] = array(
        chr(140),
        chr(156),
        chr(198),
        chr(208),
        chr(222),
        chr(223),
        chr(230),
        chr(240),
        chr(254),
      );
      $double_chars['out'] = array(
        'OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th',
      );
      $string = str_replace($double_chars['in'], $double_chars['out'], $string);
    }

    return $string;
  }

}
