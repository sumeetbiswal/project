<?php
/**
 * @file
 * Contains \Drupal\company\Form\DepartmentForm\DepartmentModalForm.
 */
 
namespace Drupal\company\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\AppendCommand;


class DepartmentModalForm extends DepartmentForm
{
    public function getFormId()
    {
        return 'department_modal_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {  
  
        $form = parent::buildForm($form, $form_state);
    
        $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
        $form['#attached']['library'][] = 'core/jquery.form';
    
        $form['department']['#prefix'] = '';
        $form['company']['#suffix'] = '';
    
        $form['department']['submit']['#attributes']['class'][] = 'use-ajax';
        $form['department']['submit']['#default_value'] = 'Submit';
        $form['department']['submit']['#ajax'] = [
        'callback' => [$this, 'submitFormAjax'],
        'event' => 'click',
        ];
    
    
        $form['department']['cancel'] = array();
        return $form;

    }
 

    public function submitFormAjax(array &$form, FormStateInterface $form_state)
    {
        //$response = new AjaxResponse();
        
        $libobj = new \Drupal\library\Lib\LibController;
        $brnobj = new \Drupal\company\Model\DepartmentModel;

        $fieldData = $form_state->getValues();
    
        $name = $fieldData['name'];
        $codename = $fieldData['code'];

        $fieldData  = array(
        'codevalues' =>  $name,
        'codename'   =>  $codename,
        'codetype'   => 'department',             
        );

        $brnobj->setDepartment($fieldData);
        $renderer = \Drupal::service('renderer');
        $response = new AjaxResponse();
        $response->addCommand(new CloseModalDialogCommand());
    
        $response->addCommand(new AppendCommand('#edit-department', '<option value="'.$codename.'" selected>'.$name.'</option>'));
        //\Drupal::formBuilder()->doBuildForm($form['#multistep-form-four'], $field, $form_state);
        //$response->addCommand(new ReplaceCommand("#edit-department", $renderer->render($form['employee']['department'])));
        return $response;
    }
}
?>