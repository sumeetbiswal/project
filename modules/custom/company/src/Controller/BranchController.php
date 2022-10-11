<?php

namespace Drupal\company\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\AfterCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\MainContent\AjaxRenderer;
use Drupal\library\Controller\Encrypt;
use Drupal\company\Model\BranchModel;

class BranchController extends ControllerBase {
  
 public function display() {
 	$brnobj = new BranchModel;
	$encrypt = new Encrypt;
    $result = $brnobj->getAllBranchDetails();
    global $base_url;
    $asset_url = $base_url.'/'.\Drupal::theme()->getActiveTheme()->getPath();
    $sl = 0;
	$rows = [];
    foreach ($result as $row => $content) {
      $sl++;
	  $codepk_encoded = $encrypt->encode($content->codepk);
      $html = ['#markup' => '<a href="'.$base_url.'/branch/edit/'.$codepk_encoded.'" style="text-align:center"> 
      <i class="icon-note" title="" data-toggle="tooltip" data-original-title="Edit"></i></a>'];
      $rows[] = 	array(
                    'data' =>	  array( $sl, $content->codevalues, $content->location, $content->ct_name, $content->name, render($html))
      );
    }

    $element['display']['branchlist'] = array(
      '#type' 	    => 'table',
      '#header' 	  =>  array(t('Sl no.'), t('Branch Name'),t('Location'), t('City'), t('State'), t('Action')),
      '#rows'		    =>  $rows,
      '#attributes' => ['class' => ['table text-center table-hover table-striped table-bordered dataTable'], 'border' => '1', 'rules' => 'all', 'style'=>['text-align-last: center;']],
      '#prefix'     => '<div class="panel panel-info">
                        <h3 class="box-title col-md-10">Branch List</h3>
                    <div class=" col-md-2">
                        <a href="#" id="exportit" data-toggle="tooltip" data-original-title="Word Document"><img src="'.$asset_url.'/assets/images/icon/word.png" /></a> &nbsp;
						<a href="'.$base_url.'/branch/export/excel" data-toggle="tooltip" data-original-title="Excel"><img src="'.$asset_url.'/assets/images/icon/excel.png" /></a> &nbsp;
						<a id="" data-toggle="tooltip" data-original-title="PDF"><img src="'.$asset_url.'/assets/images/icon/pdf.png" /></a> &nbsp;
						<a id="printit" data-toggle="tooltip" data-original-title="Print"><img src="'.$asset_url.'/assets/images/icon/print.png" /></a> 
						</div>
                        <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">	
                        <hr>
                        <div id="editable-datatable_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row"  id="headingtxt"><div class="col-sm-6 "><a href ="add"><span type="button" class="btn btn-info" style="background-color: #4c5667">
                        <i class="mdi mdi-plus"></i> Add </span></a></div> <br><br><br></div></div><div class="row"><div class="col-sm-12" id="printable">',
      '#suffix'     => '</div></div></div></div></div></div>',
	  '#empty'		=>	'No Branch has been created yet.'
    );
    return $element;
  }
	
    
	 public function exportToExcel()
	 {
		 $xcel = new \Drupal\library\Controller\Excel;
		 $brnobj = new BranchModel;
		 $result = $brnobj->getAllBranchDetails();
		 //$headings = "SLNO" . "\t" . "Branch Name" . "\t" . "State" . "\t" . "City" . "\t" . "Location" . "\t" . "Pincode" . "\t"; 
		 $headings = ['SLNO', 'Branch Name', 'State', 'City', 'Location', 'Pincode'];
		 $dataRow = array();
		 $dataRow = array($headings);
		 foreach($result AS $item)
		 {
			 static $slno = 1;
			 
			 $dataRow[] = array(
								$slno,
								$item->codevalues,
								$item->name,
								$item->ct_name,
								$item->location,								
								$item->pincode,								
							);
			 
			 $slno++;
		 }
		//echo "<pre/>";print_r($dataRow);die;
		$filename = 'branch_details_'.date('ymds');
		$result = $xcel->generateExcel($filename, $dataRow);
		
	 }
	
 }
