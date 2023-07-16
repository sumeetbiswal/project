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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Render\FormattableMarkup;

class BranchController extends ControllerBase {

 public function display() {

 	$brnobj = \Drupal::service('branch.service');
	$encrypt = \Drupal::service('encrypt.service');
    $result = $brnobj->getAllBranchDetails();
    global $base_url;
    $asset_url = $base_url.'/'.\Drupal::theme()->getActiveTheme()->getPath();
    $sl = 0;
	  $rows = [];

   $edit_access = FALSE;
   if (\Drupal::currentUser()->hasPermission('branch edit')) {
     $edit_access = TRUE;
   }
    foreach ($result as $row => $content) {
      $sl++;
	    $codepk_encoded = $encrypt->encode($content->codepk);

      $edit = '';
      if ($edit_access) {
        $url = $base_url.'/branch/edit/'.$codepk_encoded;
        $name = new FormattableMarkup('<i class="icon-note" title="" data-toggle="tooltip" data-original-title="Edit"></i>', []);
        $edit = new FormattableMarkup('<a href=":link" style="text-align:center" >@name</a>', [':link' => $url, '@name' => $name]);
      }

      $rows[] =   array(
                'data' =>	  array( $sl, $content->codevalues, $content->location, $content->ct_name, $content->name, $edit)
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
		$filename = 'branch_details_'.date('ymds');
		$result = $xcel->generateExcel($filename, $dataRow);

	 }

 }
