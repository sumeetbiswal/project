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
use Drupal\company\Model\DepartmentModel;



class DepartmentController extends ControllerBase {
  public function display() {
    
   $dptobj = new \Drupal\company\Model\DepartmentModel;
   $result = $dptobj->getAllDepartmentDetails();
   $encrypt = new Encrypt;
   
    global $base_url;
	$asset_url = $base_url.'/'.\Drupal::theme()->getActiveTheme()->getPath();
    $rows = array();
    $sl = 0;
    foreach ($result as $row => $content) { 
      $sl++;
      $html = ['#markup' => '<a href="'.$base_url.'/department/edit/'.$content->codepk.'" style="text-align:center"> 
      <i class="icon-note" title="" data-toggle="tooltip" data-original-title="Edit"></i></a>'];
      $rows[] =   array(
                    'data' =>     array( $sl, $content->codevalues, $content->codename, render($html))
      );
    }
    $element['display']['Departmentlist'] = array(
      '#type'       => 'table',
      '#header'     =>  array(t('Sl No.'), t('Department Name'), t('Department Code'), t('Action')),      
      '#rows'       =>  $rows,
      '#attributes' => ['class' => ['text-center table table-hover table-striped table-bordered dataTable'], 'border' => '1', 'rules' => 'all', 'style'=>['text-align-last: center;']],
      '#prefix'     => '<div class="panel panel-info">
                        <h3 class="box-title  col-md-10">Department Details</h3>
					<div class=" col-md-2">
                        <a href="#" id="exportit" data-toggle="tooltip" data-original-title="Word Document"><img src="'.$asset_url.'/assets/images/icon/word.png" /></a> &nbsp;
						<a href="'.$base_url.'/department/export/excel" data-toggle="tooltip" data-original-title="Excel"><img src="'.$asset_url.'/assets/images/icon/excel.png" /></a> &nbsp;
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
	  '#empty'		=>	'No Department has been created yet.'
    );
    return $element;
  }
   public function exportToExcel()
	 {
		 
		 $xcel = new \Drupal\library\Controller\Excel;
		 $dptobj = new \Drupal\company\Model\DepartmentModel;
		 $result = $dptobj->getAllDepartmentDetails();
		 //$headings = "SLNO" . "\t" . "Department Name" . "\t" . "Department Code" . "\t"; 
		 $headings = ['SLNO', 'Department Name', 'Department Code'];
		 $dataRow = array();
		 $dataRow = array($headings);
		 foreach($result AS $item)
		 {
			 static $slno = 1;
			 
			 $dataRow[] = array(
								$slno,
								$item->codevalues,
								$item->codename,

							);
			 
			 $slno++;
		 }
		//echo "<pre/>";print_r($dataRow);die;
		$filename = 'department_details_'.date('ymds');
		$result = $xcel->generateExcel($filename, $dataRow);
		
	 }
	
  
  public function openDeptModal()
  {
	  $libModal = new \Drupal\library\Controller\ModalFormController;
	  $formBuild = 'Drupal\company\Form\DepartmentModalForm';
	  $formTitle = 'Add New Department';
	  $modal_width = '500';
	  $modalForm = $libModal->openModalForm($formBuild,  $formTitle, $modal_width);
	  return $modalForm;
  }
}
