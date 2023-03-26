<?php

namespace Drupal\tagging\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\library\Controller\Encrypt;
use Drupal\Component\Render\FormattableMarkup;

class Tagging extends ControllerBase {

 public function getlist() {

   $tagging = \Drupal::service('tagging.service');
   $encrypt = \Drupal::service('encrypt.service');

   $result = $tagging->getUnEmployeeList();

   $rows = [];

   foreach($result AS $item){

     $name = $item->firstname.' '.$item->lastname;
     $project = ($item->won) ? $item->won : '';
     $supervisor = ($item->supervisor) ? $item->supervisor : '';
     $hr = ($item->hr) ? $item->hr : '';

     $icon_color = empty($project) ? 'icon-red' : '';

     $tagpk_encoded = $encrypt->encode($item->tagpk);

     $edit = array('data' => new FormattableMarkup('<a href=":link" style="text-align:center">
      <i class="mdi mdi-tag ' . $icon_color . '" title="" data-toggle="tooltip" data-original-title="Tag to a team"></i></a>',
         [':link' => '/tagging/edit/' . $tagpk_encoded])
       );

     $rows[] = array(
       'data' =>	  array( '1364945', $name , $project, $supervisor, $hr, $edit)
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
