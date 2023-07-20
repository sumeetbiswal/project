<?php

namespace Drupal\login\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\user\UserInterface;
use Drupal\user\Entity\User;
use Drupal\Core\Password\PasswordInterface;
class ChangePassword extends FormBase
{
    public function getFormId()
    {
        return 'changepwd_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {  
  
        $form['changepwd']['#prefix'] = '<div class="row"><h3 class="box-title m-b-0">Change Password</h3>';
        $form['changepwd']['currpwd'] = array(
        '#type'          => 'password',
        '#title'         => t('Current Password:'),
        '#required'      => true,
        '#attributes'    => ['class' => ['form-control']],
        '#prefix' => '<div class="row">',
        '#suffix' => '</div>',

        ); 
    
        $form['changepwd']['newpwd'] = array(
        '#type'          => 'password',
        '#title'         => t('New Password:'),
        '#required'      => true,
        '#attributes'    => ['class' => ['form-control']],
        '#prefix' => '<div class="row">',
        '#suffix' => '</div>',

        ); 
        $form['changepwd']['confirmpwd'] = array(
        '#type'          => 'password',
        '#title'         => t('Confirm password:'),
        '#required'      => true,
        '#attributes'    => ['class' => ['form-control']],
        '#prefix' => '<div class="row">',
        '#suffix' => '</div>',
        );
   
        $form['changepwd']['#type'] = 'actions';
        $form['changepwd']['submit'] = array(
        '#type'          => 'submit',
        '#default_value' => $this->t('Submit'),
        '#button_type' => 'primary',
        '#attributes' => ['class' => ['btn btn-info']],
        '#prefix' => '<br/><div class="col-md-1"></div><div class="col-md-1">',
        '#suffix' => '</div>',
        );

        $form['changepwd']['cancel'] = array(
        '#type'                     => 'submit',
        '#value'                    => t('Cancel'),
        '#attributes' => ['class' => ['btn btn-default']],
        '#limit_validation_errors'  => array(),
        '#url' => \Drupal\Core\Url::fromRoute('dashboard.dash'),
        '#suffix' => '</div>',


        );
        $form['changepwd']['cancel']['#submit'][] = '::ActionCancel';
        $form['changepwd']['#suffix'] = '</div>';
        return $form;

    }
 
    public function validateForm(array &$form, FormStateInterface $form_state)
    { 
        $field = $form_state->getValues();
        $currpwd = $field['currpwd'];
        $currentAccount = \Drupal::currentUser();
        $user = \Drupal\user\Entity\User::load($currentAccount->id());
        $password_hasher = \Drupal::service('password');
        $match = $password_hasher->check($currpwd, $user->getPassword());
        if($match == false) {
            $form_state->setErrorByName('currpwd', $this->t('Current password is wrong'));
        }
        $nwpwd = $field['newpwd'];
        $confirmpwd = $field['confirmpwd'];
        if($nwpwd != $confirmpwd) {
            $form_state->setErrorByName('confirmpwd', $this->t('New password does not match with confirm password')); 
        }
    }
  
    public function ActionCancel(array &$form, FormStateInterface $form_state)
    {      
        $form_state->setRedirect('dashboard.dash');
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
    
        $currentAccount = \Drupal::currentUser();
        $field = $form_state->getValues();
        $pwd = $field['confirmpwd'];
        $user = \Drupal\user\Entity\User::load($currentAccount->id());
        $user->setPassword($pwd);
        $user->save();
        drupal_set_message("Password has been changed successfully!");

    }
}
?>