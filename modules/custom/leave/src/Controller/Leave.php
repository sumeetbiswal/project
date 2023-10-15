<?php

namespace Drupal\leave\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;

class Leave extends ControllerBase
{
    public function leavelist()
    {

   global $base_url;
  $asset_url = $base_url.'/'.\Drupal::theme()->getActiveTheme()->getPath();
   $rows = [];
  $element['display']['Leavelist'] = array(
      '#type' 	    => 'table',
      '#header' 	  =>  array(t('Request Type.'), t('Leave request'), t('Leave type'),t('Start date'), t('End date'),  t('Number of days'), t('Status'), t('Submit date')),
      '#rows'		    =>  $rows,
      '#attributes' => ['class' => ['table text-center table-hover table-striped table-bordered dataTable'], 'style'=>['text-align-last: center;']],
	  '#empty'		=>	'No Leaves applied yet.'
    );
    return $element;

    }
}
