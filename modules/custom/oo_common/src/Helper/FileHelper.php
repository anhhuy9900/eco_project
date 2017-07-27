<?php
namespace Drupal\oo_common\Helper;

use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\oo_common\Helper\EntityHelper as EntityHelperFunc;

/**
 * File class.
 */
class FileHelper {

  /**
   * Call curl to get url content via get method.
   *
   * @param string $url
   *   The url to get content.
   * @param int $timeout
   *   The connection timeout in second.
   *
   * @return mixed
   *   Return the result on success, FALSE on failure.
   */
  public static function curlGet($url, $timeout = 5) {
    $ch = curl_init();
    $timeout = 60;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
  }

  /**
   * Call curl to get url content via post method.
   *
   * @param string $url
   *   The url to get content.
   * @param array $params
   *   The parameters.
   * @param int $timeout
   *   The connection timeout in second.
   *
   * @return mixed
   *   Return the result on success, FALSE on failure.
   */
  public static function curlPost($url, $params, $timeout = 5) {
    $postData = '';
    $sep = '';
    foreach ($params as $name => $value) {
      $postData .= $sep . urlencode($name) . '=' . urlencode($value);
      $sep = '&';
    }
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_POST, 1);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
  }

  /**
   * Write file content to disk.
   *
   * @param string $str
   *   Specifies the string to write to the file.
   * @param string $filename
   *   The path on disk.
   *
   * @return bool
   *   Return TRUE if can write and FALSE if can't write.
   */
  public static function writeStringToFile($str, $filename) {
    $pathinfo = pathinfo($filename);
    if (empty($pathinfo['dirname'])) {
      return FALSE;
    }
    // Makes directory if needed.
    if (!file_exists($pathinfo['dirname'])) {
      mkdir($pathinfo['dirname'], 0775, TRUE);
    }

    try {
      $fileHandle = fopen($filename, 'w');
      fwrite($fileHandle, $str);
      fclose($fileHandle);
    } catch (Exception $e) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Save image from base64 string to disk.
   *
   * @param string $base64ImageStr
   *   The image as base64 string.
   * @param string $type
   *   The MIME type.
   * @param string $filename
   *   The image filename without extension.
   *
   * @return mixed
   *   The file object if saved to disk, NULL if not.
   */
  public static function saveBase64Image(
    $base64ImageStr,
    $type = '',
    $filename = ''
  ) {
    if ($base64ImageStr) {
      switch ($type) {
        case 'image/jpeg':
          $ext = '.jpg';
          break;

        case 'image/gif':
          $ext = '.gif';
          break;

        case 'image/gif':
          $ext = '.png';
          break;

        default:
          $ext = '.jpg';
          break;

      }
      $data = base64_decode($base64ImageStr);
      if ($filename) {
        $file = file_save_data(
          $data,
          'public://' . date('Y-m-d') . '/' . $filename . $ext,
          FILE_EXISTS_RENAME
        );
      }
      else {
        $file = file_save_data(
          $data,
          'public://' . date('Y-m-d') . '/' . time() . '_' . rand(
            1000,
            9999
          ) . $ext,
          FILE_EXISTS_RENAME
        );
      }

      if (!empty($file)) {
        $file->status = 1;
        $file->save();
        db_insert('file_usage')
          ->fields(
            array(
              'fid' => $file->id(),
              'module' => 'oo_common',
              'type' => 'image_from_base64',
              'id' => 1,
              'count' => 1,
            )
          )
          ->execute();
        return $file;
      }
    }
    return NULL;
  }

  /**
   * Write a file to a directory and all sub directories.
   *
   * @param string $dir
   *   Path to the parent directory.
   * @param string $filename
   *   The file name.
   * @param string $data
   *   The file content.
   */
  public static function writeFileRecursive(
    $dir,
    $filename = 'index.php',
    $data = ''
  ) {
    $path = "{$dir}/{$filename}";
    if (!file_exists($path)) {
      try {
        file_put_contents($path, $data);
      } catch (Exception $e) {

      }
    }
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
      if (is_dir("{$dir}/{$file}")) {
        self::writeFileRecursive("{$dir}/{$file}", $filename, $data);
      }
    }
  }

  /**
   * Get image info by fid.
   *
   * @param int $fid
   *   The file managed id.
   * @param mixed $imageStyles
   *   The image style name(s) that we will use,
   *   keep empty if you don't want to get the thumbnail
   *   Array: get multiple thumnail dimensions at once time
   *   String: get only one thumnail.
   * @param array $returnFields
   *   The result fields you need.
   *
   * @return array
   *   Default will return the origin image url and the thumbnail url.
   *
   * @code
   * OoFile::getImageInfoByFid(1, 'medium');
   * @endcode
   */
  public static function getImageInfoByFid(
    $fid,
    $imageStyles = array('thumbnail'),
    $returnFields = array('origin_url', 'thumbnail')
  ) {
    $fid = ConvertHelper::parseInt($fid);
    $file = File::load($fid);
    if($file) {
      $file->origin_url = file_create_url($file->getFileUri());
      if (!empty($imageStyles)) {
        if (!is_array($imageStyles)) {
          $imageStyles = array($imageStyles);
        }
        foreach ($imageStyles as $styleName) {
//        $file->{$styleName} = image_style_url($styleName, $file->uri);
          $file->{$styleName} = ImageStyle::load($styleName)->buildUrl(
              $file->getFileUri()
          );
        }
      }
    }


    $data = array();
    if (in_array('thumbnail', $returnFields)) {
      $data['thumbnail'] = array();
      if (!empty($imageStyles)) {
        foreach ($imageStyles as $styleName) {
          $data['thumbnail'][$styleName] = $file->{$styleName};
        }
      }
      // If only get one image style, don't need to return array.
      if (count($data['thumbnail']) == 1) {
        $data['thumbnail'] = array_values($data['thumbnail']);
        $data['thumbnail'] = $data['thumbnail'][0];
      }
      unset($returnFields[array_search('thumbnail', $returnFields)]);
    }
    // Get more image information.
    if (!empty($returnFields)) {
      foreach ($returnFields as $fieldName) {
        if (isset($file->{$fieldName})) {
          $data[$fieldName] = $file->{$fieldName};
        }
      }
    }

    return $data;
  }

  public static function getImagePath(
    $file,
    $imageStyle = 'thumbnail',
    $defaultStyle = 'thumbnail'
  ) {
    if (!$file) {
      return '';
    }
    $entity = ImageStyle::load($imageStyle);
    if ($entity) {
      $imagePath = $entity->buildUrl(
        $file->getFileUri()
      );
    }
    else {
      $entity = ImageStyle::load($defaultStyle);
      if ($entity) {
        $imagePath = $entity->buildUrl(
          $file->getFileUri()
        );
      }
      else {
        $imagePath = '';
      }
    }
    return $imagePath;
  }

  public static function getImagePathByFid(
    $fid,
    $imageStyle = 'thumbnail',
    $defaultStyle = 'thumbnail'
  ) {
    $file = self::getFileById($fid);
    if (!$file) {
      return '';
    }
    $entity = ImageStyle::load($imageStyle);
    if ($entity) {
      $imagePath = $entity->buildUrl(
        $file->getFileUri()
      );
    }
    else {
      $entity = ImageStyle::load($defaultStyle);
      if ($entity) {
        $imagePath = $entity->buildUrl(
          $file->getFileUri()
        );
      }
      else {
        $imagePath = '';
      }
    }
    return $imagePath;
  }

  public static function getFileById($fid) {
    $fid = ConvertHelper::parseInt($fid);
    if (!$fid) {
      return NULL;
    }
    else {
      $file = File::load($fid);
      if ($file) {
        return $file;
      }
      else {
        return NULL;
      }
    }
  }

  public static function getFilePath($file){
    if(is_numeric($file)) {
      $file = self::getFileById($file);
    }
    if($file) {
      $file_path = file_create_url($file->getFileUri());
    } else {
      $file_path = '';
    }
    return $file_path;
  }

  public static function get_image_by_entity($entity, $data = array('field' => 'field_image_mobile', 'image_style' => '')){
    if($entity) {
      $entityId = EntityHelperFunc::getFieldValue($entity, $data['field']);
      if($entityId) {
        $image = self::getImageInfoByFid(
          $entityId,
          $data['image_style']
        );
        return $image['thumbnail'];
      }
    }
    return '';
  }

}
