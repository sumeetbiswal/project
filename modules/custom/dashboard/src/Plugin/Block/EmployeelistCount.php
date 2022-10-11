<?php

namespace Drupal\dashboard\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "employeelist_count_block",
 *   admin_label = @Translation("Employee List Count block"),
 * )
 */
class EmployeelistCount extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
	global $base_url;
	$emp_obj = new \Drupal\employee\Model\EmployeeModel();
	$count = $emp_obj->getEmployeeCount();
	
    return [
	  '#theme' => 'employee_count_card',
	  '#emp_count' =>	$count
    ];
  }


}