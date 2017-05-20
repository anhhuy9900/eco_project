<?php
namespace Drupal\oo_common\Helper;

class DateHelper {

  /**
   * Validate datetime.
   *
   * Format: dd.mm.yyyy or mm/dd/yyyy or dd-mm-yyyy or yyyy-mm-dd.
   *
   * @param string $date
   *   The date to check.
   *
   * @return bool
   *   Return TRUE when the date is valid, FALSE if not.
   */
  public static function isValidDate($date) {
    if (strlen($date) == 10) {
      // . or / or -.
      $pattern = '/\.|\/|-/i';
      preg_match($pattern, $date, $char);

      $array = preg_split($pattern, $date, -1, PREG_SPLIT_NO_EMPTY);

      if (strlen($array[2]) == 4) {
        // dd.mm.yyyy || dd-mm-yyyy.
        if ($char[0] == "." || $char[0] == "-") {
          $month = $array[1];
          $day = $array[0];
          $year = $array[2];
        }
        // mm/dd/yyyy    # Common U.S. writing.
        if ($char[0] == "/") {
          $month = $array[0];
          $day = $array[1];
          $year = $array[2];
        }
      }
      // yyyy-mm-dd    # iso 8601.
      if (strlen($array[0]) == 4 && $char[0] == "-") {
        $month = $array[1];
        $day = $array[2];
        $year = $array[0];
      }
      // Validate Gregorian date.
      if (checkdate($month, $day, $year)) {
        return TRUE;
      }
      else {
        return FALSE;
      }
    }
    else {
      // More or less 10 chars.
      return FALSE;
    }
  }

  /**
   * Get the date diff for human read.
   *
   * @param string $datePart
   *   The display of the result.
   * @param mixed $dateStart
   *   The start date
   *   int if use timestamp
   *   string if in YYYY-MM-DD format.
   * @param mixed $dateEnd
   *   The end date
   *   int if use timestamp
   *   string if in YYYY-MM-DD format.
   *
   * @return string
   *   Some examples:
   *   '%y Year %m Month %d Day %h Hours %i Minute %s Seconds'
   *    => 1 Year 3 Month 14 Day 11 Hours 49 Minute 36 Seconds
   *   '%y Year %m Month %d Day' => 1 Year 3 Month 14 Days
   *   '%m Month %d Day' => 3 Month 14 Day
   *   '%d Day %h Hours' => 14 Day 11 Hours
   *   '%d Day' =>  14 Days
   *   '%h Hours %i Minute %s Seconds' =>  11 Hours 49 Minute 36 Seconds
   *   '%i Minute %s Seconds' => 49 Minute 36 Seconds
   *   '%h Hours => 11 Hours
   *   '%a Days => 468 Days.
   *
   *   (PHP 5 >= 5.3.0)
   */
  public static function dateDiff($datePart, $dateStart, $dateEnd = '') {

    if (is_int($dateStart)) {
      $dateStart = date_create(date('Y-m-d H:i:s', $dateStart));
    }

    if (empty($dateEnd)) {
      $dateEnd = date_create(date('Y-m-d H:i:s'));
    }

    if (is_int($dateEnd)) {
      $dateEnd = date_create(date('Y-m-d H:i:s', $dateEnd));
    }

    $diff = date_diff($dateStart, $dateEnd);

    return $diff->format("{$datePart}");
  }

}
