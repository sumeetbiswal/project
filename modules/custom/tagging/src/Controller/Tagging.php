<?php

namespace Drupal\tagging\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\library\Controller\Encrypt;

class Tagging extends ControllerBase {

 public function getlist() {

   $tagging = \Drupal::service('tagging.service');
   $result = $tagging->getUnEmployeeList();

   $rows = [];

   foreach($result AS $item){
     $rows[] = array(
       'data' =>	  array( '1364945', $item->firstname.' '.$item->lastname , $item->won, $item->supervisor, $item->hr, 'Edit')
     );
   }

   $headers = array(
              t('Employee ID'),
              t('Name'),
              t('Project'),
              t('SuperVisor'),
              t('HR Manager'),
              t('Action')
          );

   $element['display']['employeelist'] = array(
     '#type' 	    => 'table',
     '#header' 	  =>  $headers,
     '#rows'      =>  $rows,
     '#empty'     =>  'No Un Allocated Employee are there.',
     '#caption'   =>  'Un Allocated List'
   );

   return $element;

 }
}
