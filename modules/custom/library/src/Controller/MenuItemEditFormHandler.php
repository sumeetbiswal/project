<?php

namespace Drupal\library\Controller;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class MenuItemEditFormHandler
 *
 * @package Drupal\library
 */
class MenuItemEditFormHandler {

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

    $menu_link_content = $form_state->getFormObject()->getEntity();

    // get link options
    $menu_link_options = $menu_link_content->link ? $menu_link_content->link->first()->options : [];

    // add custom fields classes
    $form['classes'] = [
      '#type' => 'textfield',
      '#title' => t('Icon classes '),
      '#description' => t('Enter css classes for icon. Separate multiple classes by spaces.'),
      '#default_value' => isset($menu_link_options['classes']) ? $menu_link_options['classes'] : '',
      '#weight' => 30,
    ];

    $form['permissions'] = [
      '#type' => 'textfield',
      '#title' => t('Permissions '),
      '#description' => t('Enter the permissions name for the menu item.'),
      '#default_value' => isset($menu_link_options['permissions']) ? $menu_link_options['permissions'] : '',
      '#weight' => 31,
    ];

    $form['sub_menu_class'] = [
      '#type' => 'textfield',
      '#description' => 'Enter css classes for displaying no of columns in sub menu. Example:- for 2 columns "two-li". For 3 columns "three-li" ',
      '#default_value' => isset($menu_link_options['sub_menu_class']) ? $menu_link_options['sub_menu_class'] : '',
      '#title' => 'Sub menu display class',
      '#weight' => 32,
    ];

    // add custom submit handler
    $form['actions']['submit']['#submit'][] = 'Drupal\library\Controller\MenuItemEditFormHandler::form_menu_link_content_form_submit';
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public static function form_menu_link_content_form_submit(array &$form, FormStateInterface $form_state)
  {
    // get classes values
    $classes = $form_state->getValue('classes');
    $permissions = $form_state->getValue('permissions');
    $two_ul = $form_state->getValue('sub_menu_class');

    // get menu link entity
    $menuLinkEntity = $form_state->getBuildInfo()['callback_object']->getEntity();

    // get defaul link options
    $menu_link_options = $menuLinkEntity->link->first()->options;

    // add classes to link options
    $options = array_merge($menu_link_options, ['classes' => $classes, 'permissions' => $permissions, 'sub_menu_class' => $two_ul]);
    // change new link options
    $menuLinkEntity->link->first()->options = $options;
    $menuLinkEntity->save();
  }

}
