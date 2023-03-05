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

   $element['display']['employeelist'] = array(
     '#type' 	    => 'table',
     '#header' 	  =>  array(t('Employee ID.'), t('Name'),t('Date of joining'), t('Designation'),  t('Department'), t('Action')),
     '#rows'		    =>  $rows,
     //'#attributes' => ['class' => ['table text-center table-hover table-striped table-bordered dataTable'], 'style'=>['text-align-last: center;']],
     '#prefix'     => '<div class="panel panel-info">
                        <h3 class="box-title col-md-10">Employees List</h3>
                        <div class=" col-md-2">
                        <a href="#" id="exportit" data-toggle="tooltip" data-original-title="Word Document"><img src="'.$asset_url.'/assets/images/icon/word.png" /></a> &nbsp;
						<a href="'.$base_url.'/employee/export/excel" data-toggle="tooltip" data-original-title="Excel"><img src="'.$asset_url.'/assets/images/icon/excel.png" /></a> &nbsp;
						<a id="" data-toggle="tooltip" data-original-title="PDF"><img src="'.$asset_url.'/assets/images/icon/pdf.png" /></a> &nbsp;
						<a id="printit" data-toggle="tooltip" data-original-title="Print"><img src="'.$asset_url.'/assets/images/icon/print.png" /></a>
						</div>
                        <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                        <hr>
                        <div id="editable-datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row"><div class="col-sm-6"><a href ="'.$base_url.'/employee/add/personal"><span  type="button" class="btn btn-info" style="background-color: #4c5667">
                        <i class="mdi mdi-plus"></i> Add </span></a></div> <br><br><br></div></div><div class="row"><div class="col-sm-12" id="printable">',
     '#suffix'     => '</div></div></div></div></div></div>',
     '#empty'		=>	'No Employee has been added yet.'
   );
   return $element;

 }
}
