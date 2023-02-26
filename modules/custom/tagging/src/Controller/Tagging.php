<?php

namespace Drupal\tagging\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;

class Tagging extends ControllerBase {

 public function getlist() {


  $element['display']['untaglist'] = array(
    );
    return $element;

 }
}
