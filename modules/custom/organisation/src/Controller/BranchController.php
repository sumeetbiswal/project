<?php

namespace Drupal\organisation\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\library\Controller\Excel;
use Drupal\organisation\Model\BranchModel;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for Branch Module.
 */
class BranchController extends ControllerBase {

  /**
   * Include the branch service.
   *
   * @var \Drupal\organisation\Model\BranchModel
   */
  protected $branch;

  /**
   * Include the Excel service.
   *
   * @var \Drupal\library\Controller\Excel
   */
  protected $excel;

  /**
   * BranchForm constructor.
   *
   * @param \Drupal\organisation\Model\BranchModel $branch
   *   The branch service.
   * @param \Drupal\library\Controller\Excel $excel
   *   The branch service.
   */
  public function __construct(BranchModel $branch, Excel $excel) {
    $this->branch = $branch;
    $this->excel = $excel;
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
      $container->get('branch.service'),
      $container->get('excel.service'),
    );
  }

  /**
   * Helper function export data to excel.
   */
  public function exportToExcel() {

    $result = $this->branch->getAllBranchDetails();

    $headings = [
      'SLNO',
      'Branch Name',
      'State',
      'City',
      'Location',
      'Pincode',
    ];
    $dataRow = [];
    $dataRow = [$headings];
    foreach ($result as $item) {
      static $slno = 1;

      $dataRow[] = [
        $slno,
        $item->codevalues,
        $item->name,
        $item->ct_name,
        $item->location,
        $item->pincode,
      ];

      $slno++;
    }
    $filename = 'branch_details_' . date('ymds');
    $result = $this->excel->generateExcel($filename, $dataRow);

  }

}
