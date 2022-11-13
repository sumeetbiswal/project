<?php

namespace Drupal\company\Controller;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\AfterCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\MainContent\AjaxRenderer;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\library\Controller\Encrypt;
use Drupal\company\Model\DesignationModel;
use Drupal\company\Model\DepartmentModel;

class DesignationController extends ControllerBase {
  public function Designationcontent() {
    
   $dptobj = \Drupal::service('designation.service');
   $depobj = \Drupal::service('department.service');
   $result = $dptobj->getAllDesignationDetails();
   $encrypt = new Encrypt;
   
    global $base_url;
	$asset_url = $base_url.'/'.\Drupal::theme()->getActiveTheme()->getPath();
    $rows = array();
    $sl = 0;
    foreach ($result as $row => $content) { 
       $dept = $depobj->getDepartmentDetailsById($content->parent);
      $sl++;
      $html = ['#markup' => '<a href="'.$base_url.'/designation/edit/'.$content->codepk.'" style="text-align:center"> 
      <i class="icon-note" title="" data-toggle="tooltip" data-original-title="Edit"></i></a>'];
      $rows[] =   array(
                    'data' =>  array( $sl, $content->codevalues, $content->codename, $dept->codevalues, render($html))
      );
    }
    $element['display']['Designationlist'] = array(
      '#type'       => 'table',
      '#header'     =>  array(t('Sl No.'), t('Designation Name'), t('Designation Code'),t('Department'), t('Action')),      
      '#rows'       =>  $rows,
      '#attributes' => ['class' => ['text-center table table-hover table-striped table-bordered dataTable'], 'border' => '1', 'rules' => 'all', 'style'=>['text-align-last: center;']],
      '#prefix'     => '<div class="panel panel-info">
                        <h3 class="box-title col-md-10">Designation List</h3>
						<div class=" col-md-2">
                        <a href="#" id="exportit" data-toggle="tooltip" data-original-title="Word Document"><img src="'.$asset_url.'/assets/images/icon/word.png" /></a> &nbsp;
						<a href="'.$base_url.'/designation/export/excel" data-toggle="tooltip" data-original-title="Excel"><img src="'.$asset_url.'/assets/images/icon/excel.png" /></a> &nbsp;
						<a id="" data-toggle="tooltip" data-original-title="PDF"><img src="'.$asset_url.'/assets/images/icon/pdf.png" /></a> &nbsp;
						<a id="printit" data-toggle="tooltip" data-original-title="Print"><img src="'.$asset_url.'/assets/images/icon/print.png" /></a> 
						</div>
                        <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                        <hr>						
                        <div id="editable-datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row"><div class="col-sm-6"><a href ="add"><span  type="button" class="btn btn-info">
                        <i class="mdi mdi-plus"></i> Add </span></a></div> <br><br><br></div></div><div class="row"><div class="col-sm-12" id="printable">',
      '#suffix'     => '</div></div></div></div></div></div>',
	  '#empty'		=>	'No Designation has been created yet.'
    );
    return $element;
  }
  public function exportToExcel()
	 {
		
		 $xcel =  \Drupal::service('excel.service');
		 $dptobj = \Drupal::service('designation.service');
     $depobj = \Drupal::service('department.service');
      $result = $dptobj->getAllDesignationDetails();
   
		 //$headings = "SLNO" . "\t" . "Department Name" . "\t" . "Department Code" . "\t"; 
		 $headings = ['SLNO', 'Designation Name', 'Designation Code', 'Department'];
		 $dataRow = array();
		 $dataRow = array($headings);
		 foreach($result AS $item)
		 {
			 static $slno = 1;
			 
			 $dataRow[] = array(
								$slno,
								$item->codevalues,
								$item->codename,
								$item->codevalues,

							);
			 
			 $slno++;
		 }
		//echo "<pre/>";print_r($dataRow);die;
		$filename = 'designation_details_'.date('ymds');
		$result = $xcel->generateExcel($filename, $dataRow);
		
	 }
  
}