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
use Drupal\company\Model\DepartmentModel;

class DepartmentController extends ControllerBase
{
    public function display()
    {

        $dptobj = \Drupal::service('department.service');
        $encrypt = \Drupal::service('encrypt.service');

        $result = $dptobj->getAllDepartmentDetails();

        global $base_url;
        $asset_url = $base_url.'/'.\Drupal::theme()->getActiveTheme()->getPath();
        $rows = array();
        $sl = 0;

        $edit_access = false;
        if (\Drupal::currentUser()->hasPermission('dept edit')) {
            $edit_access = true;
        }

        foreach ($result as $row => $content) {
            $sl++;
            $codepk_encoded = $encrypt->encode($content->codepk);
            $edit = '';
            if ($edit_access) {
                $url = $base_url.'/department/edit/'.$codepk_encoded;
                $name = new FormattableMarkup('<i class="icon-note" title="" data-toggle="tooltip" data-original-title="Edit"></i>', []);
                $edit = new FormattableMarkup('<a href=":link" style="text-align:center" >@name</a>', [':link' => $url, '@name' => $name]);
            }


            $rows[] =   array(
                    'data' =>     array( $sl, $content->codevalues, $content->codename, $edit)
            );
        }
        $element['display']['Departmentlist'] = array(
        '#type'       => 'table',
        '#header'     =>  array(t('Sl No.'), t('Department Name'), t('Department Code'), t('Action')),
        '#rows'       =>  $rows,
        '#empty'        =>    'No Department has been created yet.'
        );
        return $element;
    }

    public function exportToExcel()
    {

        $xcel = new \Drupal\library\Controller\Excel;
        $dptobj = new \Drupal\company\Model\DepartmentModel;
        $result = $dptobj->getAllDepartmentDetails();
        $headings = ['SLNO', 'Department Name', 'Department Code'];
        $dataRow = array();
        $dataRow = array($headings);
        foreach($result AS $item) {
            static $slno = 1;
            $dataRow[] = array(
            $slno,
            $item->codevalues,
            $item->codename,
            );
            $slno++;
        }
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