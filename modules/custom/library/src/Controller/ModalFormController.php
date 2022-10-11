<?php

namespace Drupal\library\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilder;


class ModalFormController {

  public function openModalForm($formBuild, $modalTitle, $modal_width) {
    $response = new AjaxResponse();
	
	$modal_form = \Drupal::formBuilder()->getForm($formBuild);
	$response->addCommand(new OpenModalDialogCommand( $modalTitle, $modal_form, ['width' => $modal_width]));
    return $response;
  }

}