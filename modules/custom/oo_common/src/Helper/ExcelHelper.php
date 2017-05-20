<?php
namespace Drupal\oo_common\Helper;

class ExcelHelper {

  /**
   * Create PHPExcel object.
   *
   * @param array $rows
   *   The excel rows data.
   * @param array $props
   *   The properties.
   *
   * @return object
   *   The PHPExcel object.
   */
  public static function createPhpExcelObject($rows, $props = array()) {
    require_once DRUPAL_ROOT . '/libraries/PHPExcel/PHPExcel.php';

    // Create report.
    $objPHPExcel = new PHPExcel();

    // Set document properties.
    $objPHPExcel->getProperties()
      ->setCreator(OoUtl::getArrVal($props, 'creator'))
      ->setLastModifiedBy(OoUtl::getArrVal($props, 'last_modified_by'))
      ->setTitle(OoUtl::getArrVal($props, 'title'))
      ->setSubject(OoUtl::getArrVal($props, 'subject'))
      ->setDescription(OoUtl::getArrVal($props, 'description'))
      ->setKeywords(OoUtl::getArrVal($props, 'keywords'))
      ->setCategory(OoUtl::getArrVal($props, 'category'));

    // Set default columns.
    $styleArray = array(
      'font'  => array(
        'bold'  => TRUE,
        'color' => array('rgb' => 'FFFFFF'),
      ),
      'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN,
        ),
      ),
    );

    $sheet1 = $objPHPExcel->getActiveSheet(0);
    // Fill worksheet from values in array.
    $sheet1->fromArray($rows);
    // Auto column width.
    $cellIterator = $sheet1->getRowIterator()->current()->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(TRUE);
    foreach ($cellIterator as $cell) {
      $sheet1->getColumnDimension($cell->getColumn())->setAutoSize(TRUE);
    }

    $headerRange = 'A1:' . $sheet1->getHighestColumn() . '1';
    $sheet1->getStyle($headerRange)->applyFromArray($styleArray);
    // Set cell background color.
    self::setCellBackground($objPHPExcel, $headerRange, '0489B1');
    // Set Column Alignment.
    $sheet1->getStyle($headerRange)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    return $objPHPExcel;
  }

  /**
   * Set cell background color.
   *
   * @param object $objPHPExcel
   *   The PHPExcel object.
   * @param string $cells
   *   The cells to be processed, eg: A1, A2:I2, etc.
   * @param string $color
   *   The cell background color.
   */
  public static function setCellBackground(&$objPHPExcel, $cells, $color) {
    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()
      ->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('rgb' => $color),
      )
    );
  }

  /**
   * Php array to excel.
   *
   * @param array $arr
   *   The php array to be processed.
   * @param string $filename
   *   The default file name display on download box
   *   OR the path on to save file on disk.
   * @param string $title
   *   The title of the excel file.
   *
   * @code
   * $rows = array(
   *   array('Name', 'Age', 'Height'),
   *   array('Jack', 24, '6ft 5'),
   *   array('Jim', 22, '5ft 5'),
   *   array('Jess', 54, '4ft'),
   * );
   * OoExcel::arrayToExcel($rows, '/tmp/people.xlsx', 'People');
   * @endcode
   */
  public static function arrayToExcel($arr, $filename = 'report.xlsx', $title = '') {
    $objPHPExcel = self::createPhpExcelObject($arr);

    if (!strpos($filename, '/') !== FALSE) {
      $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
      $objWriter->save($filename);
      exit();
    }
    else {
      self::download($objPHPExcel, $filename, $title);
    }
  }

  /**
   * Download excel file.
   *
   * @param object $objPHPExcel
   *   The PHPExcel object.
   * @param string $filename
   *   The default file name display on download box.
   * @param string $title
   *   The title of the excel file.
   */
  public static function download($objPHPExcel, $filename = 'report.xlsx', $title = '') {

    $styleArray = array(
      'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN,
          'color' => array('rgb' => '333333'),
        ),
      ),
    );
    $activeSheet = $objPHPExcel->getActiveSheet();
    $objPHPExcel->getActiveSheet()->getStyle(
      'A1:' . $activeSheet->getHighestColumn() .
      $activeSheet->getHighestRow()
    )->applyFromArray($styleArray);
    // Rename worksheet.
    $objPHPExcel->getActiveSheet()->setTitle(empty($title) ? t('Report') : $title);
    // Set active sheet index to the first sheet,
    // so Excel opens this as the first sheet.
    $objPHPExcel->setActiveSheetIndex(0);
    // Download file.
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $writer->save('php://output');
    exit();
  }

}
