<?php

namespace Drupal\organisation\Controller;

use Drupal\library\Controller\ModalFormController;
use Drupal\organisation\Model\DepartmentModel;
use Drupal\library\Controller\Excel;
use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for Department.
 */
class DepartmentController extends ControllerBase {

  /**
   * Helper function to export data into excel.
   */
  public function exportToExcel() {

    $xcel = new Excel();
    $dptobj = new DepartmentModel();
    $result = $dptobj->getAllDepartmentDetails();
    $headings = ['SLNO', 'Department Name', 'Department Code'];
    $dataRow = [];
    $dataRow = [$headings];
    foreach ($result as $item) {
      static $slno = 1;
      $dataRow[] = [
        $slno,
        $item->codevalues,
        $item->codename,
      ];
      $slno++;
    }
    $filename = 'department_details_' . date('ymds');
    $result = $xcel->generateExcel($filename, $dataRow);
  }

  /**
   * Helper function to open model.
   */
  public function openDeptModal() {
    $libModal = new ModalFormController();
    $formBuild = 'Drupal\organisation\Form\DepartmentModalForm';
    $formTitle = 'Add New Department';
    $modal_width = '500';
    $modalForm = $libModal->openModalForm($formBuild, $formTitle, $modal_width);
    return $modalForm;
  }

}
