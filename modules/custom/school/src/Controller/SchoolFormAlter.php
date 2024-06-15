<?php

namespace Drupal\school\Controller;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class SchoolFormAlter
 *
 * @package Drupal\school
 */
class SchoolFormAlter {

    /**
     * Alter Form.
     *
     * @param array $form
     *   Form array.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *    The current state of the form.
     * @param $form_id
     *    String representing the id of the form.
     */
    public function alterForm(array &$form, FormStateInterface $form_state, $form_id) {
      // Hiding revision log information for Teacher & Student node edit form.
      $form_exception = [
        'node_teacher_form',
        'node_student_form',
        'node_student_edit_form'
      ];

      if (in_array($form_id, $form_exception)) {
        $form['revision_information']['#access'] = FALSE;
      }


      // Alter the student form.
      if ($form_id == 'node_student_form') {

        // Adding library for master library validation
        $form['#attached']['library'][] = 'singleportal/master-validation';
        $form['#attributes']['class'] = 'form-horizontal';
        $form['#attributes']['autocomplete'] = 'off';

        //$form['field_religion']['#attributes']['class'] = 'validate[required,custom[onlyLetterSp]]';
      }
    }

    /**
     * @param array              $form
     * @param FormStateInterface $form_state
     */
    public static function form_menu_link_content_form_submit(array &$form, FormStateInterface $form_state) {
    }

}
