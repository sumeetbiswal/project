<?php

namespace Drupal\leave\Controller;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for the leaveType.
 */
class LeaveType extends ControllerBase {

  /**
   * Helper function to get Leave Type List.
   */
  public function leaveTypeList() {

    global $base_url;

    $encrypt = \Drupal::service('encrypt.service');
    $leave = \Drupal::service('leave.service');

    // $asset_url = $base_url.'/'.\Drupal::theme()->getActiveTheme()->getPath();
    $rows = [];
    $header = [
      $this->t('Leave Type.'),
      $this->t('Leave Code'),
      $this->t('Allotment'),
      $this->t('Status'),
    ];

    $edit_access = FALSE;
    if (\Drupal::currentUser()->hasPermission('leavetype edit')) {
      $edit_access = TRUE;
    }

    $result = $leave->getLeaveTypeList();

    foreach ($result as $content) {
      $codepk_encoded = $encrypt->encode($content->codepk);

      $edit = '';
      if ($edit_access) {
        $url = $base_url . '/leavetype/edit/' . $codepk_encoded;
        $name = new FormattableMarkup('<i class="icon-note" title="" data-toggle="tooltip" data-original-title="Edit"></i>', []);
        $edit = new FormattableMarkup(
          '<a href=":link" style="text-align:center" >@name</a>',
          [':link' => $url, '@name' => $name],
        );
      }

      $rows[] = [
        'data' => [
          $content->codevalues,
          $content->codename,
          $content->weight,
          $content->status,
          $edit,
        ],
      ];
    }

    $element['display']['Leavelist'] = [
      '#type'         => 'table',
      '#header'       => $header,
      '#rows'            => $rows,
      '#attributes' => [
        'class' => [
          'table text-center table-hover table-striped table-bordered dataTable',
        ],
        'style' => ['text-align-last: center;'],
      ],
      '#empty'        => 'No Leaves Types created yet.',
    ];
    return $element;

  }

}
