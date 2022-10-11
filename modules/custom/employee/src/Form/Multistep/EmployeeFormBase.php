<?php
/**
 * @file
 * Contains \Drupal\employee\Form\Multistep\EmployeeFormBase.
 */

namespace Drupal\employee\Form\Multistep;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;


abstract class EmployeeFormBase extends FormBase {

  /**
   * @var \Drupal\user\PrivateTempStoreFactory
   */
  protected $tempStoreFactory;

  /**
   * @var \Drupal\Core\Session\SessionManagerInterface
   */
  private $sessionManager;

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  private $currentUser;

  /**
   * @var \Drupal\user\PrivateTempStore
   */
  protected $store;

  /**
   * Constructs a \Drupal\employee\Form\Multistep\MultistepFormBase.
   *
   * @param \Drupal\user\PrivateTempStoreFactory $temp_store_factory
   * @param \Drupal\Core\Session\SessionManagerInterface $session_manager
   * @param \Drupal\Core\Session\AccountInterface $current_user
   */
  public function __construct(PrivateTempStoreFactory $temp_store_factory, SessionManagerInterface $session_manager, AccountInterface $current_user) {
    $this->tempStoreFactory = $temp_store_factory;
    $this->sessionManager = $session_manager;
    $this->currentUser = $current_user;

    $this->store = $this->tempStoreFactory->get('multistep_data');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('user.private_tempstore'),
      $container->get('session_manager'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
	 
    // Start a manual session for anonymous users.
    /*if ($this->currentUser->isAnonymous() && !isset($_SESSION['multistep_form_holds_session'])) {
      $_SESSION['multistep_form_holds_session'] = true;
      $this->sessionManager->start();
    }*/

    $form = array();
	$form['#attached']['library'][] = 'singleportal/multistep-style';
	$form['#attributes']['class'] = 'msform form-horizontal';
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
      '#weight' => 10,
    );

    return $form;
  }

  /**
   * Saves the data from the multistep form.
   */
  protected function saveData() {
    // Logic for saving data goes here...
	//$this->deleteStore();
	
	$empmodel = new \Drupal\employee\Model\EmployeeModel;
	$libobj = new \Drupal\library\Lib\LibController;
	
	$default_password = $libobj->generateDefaultPassword($this->store->get('id'), $this->store->get('dob'));
	
	$data = $this->getDataFromSession();
	
	$userdata = array(
						'username'	=>	$this->store->get('id'),
						'email'		=>	$this->store->get('officialemail'),
						'password' 	=>	$default_password,
						'role'		=>	$this->store->get('role'),
						'image'		=>	$this->store->get('image')
				);
	
	
	$user = $empmodel->createUser($userdata);
	
	
	$batch = array(
		  'title' => t('Creating Employee...'),
		  'init_message' => $this->t('Creating user'),
		  'progress_message' => $this->t('Completed @current step of @total.'),
		  'error_message' => $this->t('Creating user has encountered an error.'),
		  'operations' => array(
							array('\Drupal\employee\Model\EmployeeModel::setPersonalInfo', array($user, $data, 'Setting Up Personal info') ),
							
						),
		  'finished' => '\Drupal\employee\Model\EmployeeModel::finishOperation',
		  
		);
		batch_set($batch);
	    
    drupal_set_message($this->t('Employee has been created...'));
	
  }

  /**
   * Helper method that removes all the keys from the store collection used for
   * the multistep form.
   */
  protected function deletePersonalStore() {
	 //$this->store->set('personal_bypass', FALSE);
    $keys = ['firstname', 'lastname', 'fname', 'mname', 'gender', 'dob', 'marital', 'blood', 'religion', 'nationality', 'personal_bypass'];
    foreach ($keys as $key) {
      $this->store->delete($key);
    }
  }
  
  protected function deleteContactStore() {
    $keys = ['phoneno', 'altphoneno', 'emergencyno', 'relationship', 'email', 'image',
						'address1', 'address2', 'state', 'city', 'country', 'pincode', 'addresscopy',
						'permanentaddress1', 'permanentaddress2', 'permanentaddress1', 'permanentaddress2', 
						'permanentstate', 'permanentcity', 'permanentcountry', 'permanentpincode'];
    foreach ($keys as $key) {
      $this->store->delete($key);
    }
  }
  
  protected function deleteAcademicStore() {
    $keys = ['qualification', 'experience'];
    foreach ($keys as $key) {
      $this->store->delete($key);
    }
  }
  
   protected function deleteOfficialStore() {
    $keys = ['id', 'department', 'branch', 'designation', 'role', 'jobnature', 'officialemail', 'doj', 'jobtype', 'shifttime'];
    foreach ($keys as $key) {
      $this->store->delete($key);
    }
  }
  
  protected function getDataFromSession()
  {
	$libobj = new \Drupal\library\Lib\LibController;
	$data = array();
	$persnl_keys = ['firstname', 'lastname', 'fname', 'mname', 'gender', 'dob', 'marital', 'blood', 'religion', 'nationality', 'personal_bypass'];
    foreach ($persnl_keys as $key) {
      $data['personal'][$key] = $this->store->get($key);
    }
	
	$dob = $libobj->getDbDateFormat($this->store->get('dob'));
	$data['personal']['dob'] = $dob;
	
	
	$contact_keys = ['phoneno', 'altphoneno', 'emergencyno', 'relationship', 'email', 'image',
						'address1', 'address2', 'state', 'city', 'country', 'pincode', 'addresscopy',
						'permanentaddress1', 'permanentaddress2', 'permanentaddress1', 'permanentaddress2', 
						'permanentstate', 'permanentcity', 'permanentcountry', 'permanentpincode'];
	
	 foreach ($contact_keys as $key) {
      $data['contact'][$key] = $this->store->get($key);
    }
			
		
	$official_keys = ['id', 'department', 'branch', 'designation', 'role', 'jobnature', 'officialemail', 'doj', 'jobtype', 'shifttime'];
	
	foreach ($official_keys as $key) {
      $data['official'][$key] = $this->store->get($key);
    }
	
	$doj = $libobj->getDbDateFormat($this->store->get('doj'));
	$data['official']['doj'] = $doj;
	
	
	$data['qualification'] = $this->store->get('qualification');
	$data['experience'] = $this->store->get('experience');
	
	return $data;
  }
  
}
