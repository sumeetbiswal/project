<?php

namespace Drupal\login\Form;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Password\PasswordInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\UserStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ChangePassword creates the Form for Change Password.
 */
class ChangePassword extends FormBase {
  /**
   * Include the messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Current user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * User storage Interface.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * Password Interface.
   *
   * @var \Drupal\Core\Password\PasswordInterface
   */
  protected $passwordHasher;

  /**
   * BranchForm constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\Core\Session\AccountInterface $currentUser
   *   Current User.
   * @param \Drupal\user\UserStorageInterface $user_storage
   *   User Storage.
   * @param \Drupal\Core\Password\PasswordInterface $password_hasher
   *   Password Interface.
   */
  public function __construct(MessengerInterface $messenger,
                              AccountInterface $currentUser,
                              UserStorageInterface $user_storage,
                              PasswordInterface $password_hasher) {
    $this->messenger = $messenger;
    $this->currentUser = $currentUser;
    $this->userStorage = $user_storage;
    $this->passwordHasher = $password_hasher;
  }

  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The Drupal service container.
   *
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      $container->get('current_user'),
      $container->get('entity_type.manager')->getStorage('user'),
      $container->get('password'),
    );
  }

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'changepwd_form';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['changepwd']['#prefix'] = '<div class="row"><h3 class="box-title m-b-0">Change Password</h3>';
    $form['changepwd']['currpwd'] = [
      '#type'          => 'password',
      '#title'         => $this->t('Current Password:'),
      '#required'      => TRUE,
      '#prefix' => '<div class="row">',
      '#suffix' => '</div>',

    ];

    $form['changepwd']['newpwd'] = [
      '#type'          => 'password',
      '#title'         => $this->t('New Password:'),
      '#required'      => TRUE,
      '#prefix' => '<div class="row">',
      '#suffix' => '</div>',

    ];
    $form['changepwd']['confirmpwd'] = [
      '#type'          => 'password',
      '#title'         => $this->t('Confirm password:'),
      '#required'      => TRUE,
      '#prefix' => '<div class="row">',
      '#suffix' => '</div>',
    ];

    $form['changepwd']['#type'] = 'actions';
    $form['changepwd']['submit'] = [
      '#type'          => 'submit',
      '#default_value' => $this->t('Submit'),
      '#button_type' => 'primary',
      '#attributes' => ['class' => ['btn btn-info']],
      '#prefix' => '<br/><div class="col-md-1"></div><div class="col-md-1">',
      '#suffix' => '</div>',
    ];

    $form['changepwd']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Cancel'),
      '#attributes' => ['class' => ['btn btn-default']],
      '#prefix' => '',
      '#suffix' => '</div>',
      '#url' => Url::fromRoute('dashboard.dash'),
    ];

    $form['changepwd']['#suffix'] = '</div>';
    return $form;

  }

  /**
   * {@inheritDoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $field = $form_state->getValues();
    $currpwd = $field['currpwd'];
    $user = $this->userStorage->load($this->currentUser->id());
    $match = $this->passwordHasher->check($currpwd, $user->getPassword());
    if ($match == FALSE) {
      $form_state->setErrorByName('currpwd', $this->t('Current password is wrong'));
    }
    $nwpwd = $field['newpwd'];
    $confirmpwd = $field['confirmpwd'];
    if ($nwpwd != $confirmpwd) {
      $form_state->setErrorByName('confirmpwd', $this->t('New password does not match with confirm password'));
    }
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $field = $form_state->getValues();
    $pwd = $field['confirmpwd'];
    $user = $this->userStorage->load($this->currentUser->id());
    $user->setPassword($pwd);
    $user->save();
    $this->messenger->addMessage("Password has been changed successfully!");

  }

}
