<?php

namespace Drupal\company\Controller;

use Drupal\Component\Render\FormattableMarkup;
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

    $edit_access = FALSE;
    if (\Drupal::currentUser()->hasPermission('desig edit')) {
      $edit_access = TRUE;
    }


    foreach ($result as $row => $content) {
       $dept = $depobj->getDepartmentDetailsById($content->parent);
      $sl++;
      $codepk_encoded = $encrypt->encode($content->codepk);
      $edit = '';
      if ($edit_access) {
        $url = $base_url.'/designation/edit/'.$codepk_encoded;
        $name = new FormattableMarkup('<i class="icon-note" title="" data-toggle="tooltip" data-original-title="Edit"></i>', []);
        $edit = new FormattableMarkup('<a href=":link" style="text-align:center" >@name</a>', [':link' => $url, '@name' => $name]);
      }


      $rows[] =   array(
                    'data' =>  array( $sl, $content->codevalues, $content->codename, $dept->codevalues, $edit)
            );
    }
    $element['display']['Designationlist'] = array(
      '#type'       => 'table',
      '#header'     =>  array(t('Sl No.'), t('Designation Name'), t('Designation Code'),t('Department'), t('Action')),
      '#rows'       =>  $rows,
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
