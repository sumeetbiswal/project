<?php

namespace Drupal\company\Form;

use Drupal\company\Model\DepartmentModel;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\AppendCommand;

/**
 * DepartmentModalForm created modal form for department.
 */
class DepartmentModalForm extends DepartmentForm {

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'department_modal_form';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

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

    $form['department']['cancel'] = [];
    return $form;

  }

  /**
   * Helper function to submit the department form value through ajax command.
   */
  public function submitFormAjax(array &$form, FormStateInterface $form_state) {
    // $response = new AjaxResponse();
    $brnobj = new DepartmentModel();

    $fieldData = $form_state->getValues();

    $name = $fieldData['name'];
    $codename = $fieldData['code'];

    $fieldData = [
      'codevalues' => $name,
      'codename'   => $codename,
      'codetype'   => 'department',
    ];

    $brnobj->setDepartment($fieldData);
    $response = new AjaxResponse();
    $response->addCommand(new CloseModalDialogCommand());

    $response->addCommand(new AppendCommand('#edit-department', '<option value="' . $codename . '" selected>' . $name . '</option>'));
    return $response;
  }

}
