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
use Symfony\Component\DependencyInjection\ContainerInterface;

class BranchController extends ControllerBase {

 public function display() {

 	$brnobj = \Drupal::service('branch.service');
	$encrypt = new Encrypt;
    $result = $brnobj->getAllBranchDetails();
    global $base_url;
    $asset_url = $base_url.'/'.\Drupal::theme()->getActiveTheme()->getPath();
    $sl = 0;
	$rows = [];
    foreach ($result as $row => $content) {
      $sl++;
	  $codepk_encoded = $encrypt->encode($content->codepk);

      $edit = '';
      if (\Drupal::currentUser()->hasPermission('branch edit')) {
        $edit = '<a href="'.$base_url.'/branch/edit/'.$codepk_encoded.'" style="text-align:center">
      <i class="icon-note" title="" data-toggle="tooltip" data-original-title="Edit"></i></a>';
      }
      
      $html = ['#markup' => $edit];


      $rows[] =   array(
                'data' =>	  array( $sl, $content->codevalues, $content->location, $content->ct_name, $content->name, render($html))
            );
    }

    $element['display']['branchlist'] = array(
      '#type' 	    => 'table',
      '#header' 	  =>  array(t('Sl no.'), t('Branch Name'),t('Location'), t('City'), t('State'), t('Action')),
      '#rows'		    =>  $rows,
      '#empty'		=>	'No Branch has been created yet.'
    );
    return $element;
  }


	 public function exportToExcel()
	 {
		 $xcel =  \Drupal::service('excel.service');
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
