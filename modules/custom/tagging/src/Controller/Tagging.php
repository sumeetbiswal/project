<?php

namespace Drupal\tagging\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\library\Controller\Encrypt;

class Tagging extends ControllerBase {

 public function getlist() {


   global $base_url;
   $asset_url = $base_url.'/'.\Drupal::theme()->getActiveTheme()->getPath();
   $rows = [];
   $headers = array(
          t('Employee ID'),
          t('Name'),
          t('Date of joining'),
          t('Designation'),
          t('Department'),
          t('Action')
   );

   $element['display']['employeelist'] = array(
     '#type' 	    => 'table',
     '#header' 	  =>  $headers,
     '#rows'		    =>  $rows,
     '#empty'		=>	'No Un Allocated Employee are there.',
     '#caption' =>  'Un Allocated List'
   );
   return $element;

 }
}
