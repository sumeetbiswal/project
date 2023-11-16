<?php

namespace Drupal\leave\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for the leave module.
 */
class Leave extends ControllerBase {

  /**
   * Helper function to get Leave list.
   */
  public function leavelist() {

    $rows = [];
    $element['display']['Leavelist'] = [
      '#type'         => 'table',
      '#header'       => [
        $this->t('Request Type.'),
        $this->t('Leave request'),
        $this->t('Leave type'),
        $this->t('Start date'),
        $this->t('End date'),
        $this->t('Number of days'),
        $this->t('Status'),
        $this->t('Submit date'),
      ],
      '#rows'            => $rows,
      '#attributes' => [
        'class' => [
          'table text-center table-hover table-striped table-bordered dataTable',
        ],
        'style' => ['text-align-last: center;'],
      ],
      '#empty'        => 'No Leaves applied yet.',
    ];
    return $element;

  }

}
