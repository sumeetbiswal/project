<?php

namespace Drupal\company\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

class DepartmentForm extends FormBase
{
    public function getFormId()
    {
        return 'department_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {

        $libobj = \Drupal::service('library.service');
        $brnobj = \Drupal::service('department.service');
        $conobj = \Drupal::service('configuration.service');
        $encrypt = \Drupal::service('encrypt.service');
        $mode = $libobj->getActionMode();

        if($mode == 'edit') {
            $pk = $libobj->getIdFromUrl();
            $pk = $encrypt->decode($pk);
            $data = $brnobj->getDepartmentDetailsById($pk);

            $form_title = 'Edit Department - ' . $data->codevalues;
            $libobj->setPageTitle($form_title);
        }



        $form['#attached']['library'][] = 'singleportal/master-validation';
        $form['#attributes']['class'] = 'form-horizontal';
        $form['#attributes']['autocomplete'] = 'off';


        $form['department']['name'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Department Name:'),
        '#attributes'    => ['class' => ['form-control', 'validate[required,custom[onlyLetterSp]]']],
        '#prefix'        => '<div class="row">',
        '#suffix'        => '</div>',
        '#default_value' => isset($data)? $data->codevalues : '',
        );

        $dptcode_config = $conobj->getDepartmentcodeConfig();
        $dpt_conf = [];
        $dpt_conf['disabled'] = '';
        $dpt_conf['departmentcode'] = '';
        $dpt_conf['helpmsg'] = 'Mention Department Code of the person';
        if($dptcode_config->codevalues == 'off') {
            $dpt_conf['disabled'] = 'disabled';
            $dpt_conf['departmentcode'] = 'XXXXXXX';
            $dpt_conf['helpmsg'] = 'Department Code will be auto generate';
        }

        $form['department']['code'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Department Code:'),
        '#attributes'    => ['class' => ['form-control', 'validate[required,custom[onlyLetterSp]]']],
        '#prefix'        => '<div class="row">',
        '#suffix'        => '</div>',
        '#default_value' => isset($data)? $data->codename : $dpt_conf['departmentcode'],
        '#disabled'      =>  $dpt_conf['disabled'],
        '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="'.$dpt_conf['helpmsg'].'" data-toggle="tooltip"></i>',

        );

        $form['department']['submit'] = array(
        '#type'          => 'submit',
        '#default_value' => ($mode == 'add') ? $this->t('Submit') : $this->t('Update'),
        '#button_type'   => 'primary',
        '#attributes'    => ['class' => ['btn btn-info']],
        '#prefix'        => '<br/><div class="row"><div class="col-md-2"></div><div class="col-md-4">',
        '#suffix'        => '',
        );

        $form['department']['cancel'] = array(
        '#type' => 'link',
        '#title' => t('Cancel'),
        '#attributes'               => ['class'   => ['btn btn-default']],
        '#prefix'                   => '',
        '#suffix'                   => '</div></div>',
        '#url' => \Drupal\Core\Url::fromRoute('department.view'),
        );
        return $form;

    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $brnobj = \Drupal::service('department.service');
        $deptname = trim($form_state->getValue('name'));
        $dept_exist = $brnobj->deptIsExist($deptname);

        if($dept_exist) {
            $form_state->setErrorByName('name', $this->t('Department has already Exist. Duplicate is not allowed.'));
        }
    }

    public function ActionCancel(array &$form, FormStateInterface $form_state)
    {
        $form_state->setRedirect('department.view');
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $libobj = \Drupal::service('library.service');
        $brnobj = \Drupal::service('department.service');
        $encrypt = \Drupal::service('encrypt.service');
        $conobj = \Drupal::service('configuration.service');

        $field = $form_state->getValues();
        $code_config = $conobj->getDepartmentCodeConfig();

        //check codevalues OFF then auto generate the code values
        $code = ( $code_config->codevalues == 'on' ) ? $field['code'] : $libobj->generateCode('DPT', $field['name']);

        $name = $field['name'];

        $field  = array(
        'codevalues' =>  $name,
        'codename'   =>  $code,
        'codetype'   => 'department',
        );

        $mode = $libobj->getActionMode();
        if($mode == 'add' ) {
            $brnobj->setDepartment($field);
            \Drupal::messenger()->addMessage("succesfully saved.");
        }
        if($mode == 'edit' ) {
            $pk = $libobj->getIdFromUrl();
            $pk = $encrypt->decode($pk);
            $brnobj->updateDepartment($field, $pk);
            \Drupal::messenger()->addMessage("succesfully Updated.");
        }

        $form_state->setRedirect('department.view');

    }
}
?>
