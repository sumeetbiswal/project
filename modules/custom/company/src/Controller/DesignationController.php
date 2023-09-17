<?php

namespace Drupal\company\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Control Designation module.
 */
class DesignationController extends ControllerBase {

  /**
   * The excel service.
   *
   * @var \Drupal\library\Controller\Excel
   */
  protected $excel;

  /**
   * The designation service.
   *
   * @var \Drupal\company\Model\DesignationModel
   */
  protected $designation;

  /**
   * DesignationController constructor.
   *
   * @param \Drupal\library\Controller\Excel $excel
   *   The excel generator.
   * @param \Drupal\company\Model\DesignationModel $designation
   *   Helper service for Designation.
   */
  public function __construct($excel, $designation) {
    $this->excel = $excel;
    $this->designation = $designation;
  }

  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The Drupal service container.
   *
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('excel.service'),
      $container->get('designation.service')
    );
  }

  /**
   * Generate Excel.
   */
  public function exportToExcel() {

    $result = $this->designation->getAllDesignationDetails();

    $headings = ['SLNO', 'Designation Name', 'Designation Code', 'Department'];
    $dataRow = [];
    $dataRow = [$headings];
    foreach ($result as $item) {
      static $slno = 1;

      $dataRow[] = [
        $slno,
        $item->codevalues,
        $item->codename,
        $item->codevalues,
      ];
      $slno++;
    }
    $filename = 'designation_details_' . date('ymds');
    $result = $this->excel->generateExcel($filename, $dataRow);

  }

}
