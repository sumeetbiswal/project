<?php

namespace Drupal\library\Lib;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Http\RequestStack;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;

/**
 * {@inheritdoc}
 */
class LibController extends ControllerBase {

  /**
   * Request stack.
   *
   * @var \Drupal\Core\Http\RequestStack
   */
  protected $requestStack;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  private $connection;

  /**
   * Current user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The route provider.
   *
   * @var \Drupal\Core\Routing\RouteProviderInterface
   */
  protected $routeProvider;

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructor of the LibController.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   * @param \Drupal\Core\Http\RequestStack $requestStack
   *   Request stack.
   * @param \Drupal\Core\Session\AccountInterface $currentUser
   *   Current User.
   * @param \Drupal\Core\Routing\RouteProviderInterface $provider
   *   The route provider.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(Connection $connection,
                              RequestStack $requestStack,
                              AccountInterface $currentUser,
                              RouteProviderInterface $provider,
                              RouteMatchInterface $route_match,
                              EntityTypeManagerInterface $entity_type_manager) {
    $this->connection = $connection;
    $this->requestStack = $requestStack;
    $this->currentUser = $currentUser;
    $this->routeProvider = $provider;
    $this->routeMatch = $route_match;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Helper function to get Action mode.
   */
  public function getActionMode() {
    $current_path = $this->requestStack->getCurrentRequest()->getPathInfo();
    $path = explode('/', $current_path);
    return $path[2];
  }

  /**
   * Helper function to get ID from URL.
   */
  public function getIdFromUrl() {

    $current_path = $this->requestStack->getCurrentRequest()->getPathInfo();
    $path = explode('/', $current_path);
    return $path[3];
  }

  /**
   * Helper function to get State List.
   */
  public function getStateList() {
    $query = $this->connection->select(DataModel::STATE, 'n');
    $query->fields('n');
    $query->condition('name', "", "!=");
    $query->condition('country_id', "101", "=");
    $query->orderBy('name', "ASC");
    $result = $query->execute()->fetchAll();
    $res[''] = 'Select State';
    foreach ($result as $val) {
      $res[$val->id] = $val->name;
    }

    return $res;
  }

  /**
   * Helper function to get State name by ID.
   */
  public function getStateNameById($id) {
    $query = $this->connection->select(DataModel::STATE, 'n');
    $query->fields('n');
    $query->condition('id', $id, "=");
    $result = $query->execute()->fetchAll();
    foreach ($result as $content) {
      $state_name = $content->name;
    }

    return $state_name;
  }

  /**
   * Helper function to get City list by State.
   */
  public function getCityListByState($statePk) {
    $query = $this->connection->select(DataModel::CITY, 'n');
    $query->fields('n');
    $query->condition('name', "", "!=");
    $query->condition('state_id', $statePk, "=");
    $query->orderBy('name', "ASC");
    $result = $query->execute()->fetchAll();
    $res[''] = 'Select City';
    foreach ($result as $val) {
      $res[$val->id] = $val->name;
    }

    return $res;
  }

  /**
   * Helper function getCity name by ID.
   */
  public function getCityNameById($id) {
    $query = $this->connection->select(DataModel::CITY, 'n');
    $query->fields('n');
    $query->condition('id', $id, "=");
    $result = $query->execute()->fetchAll();
    foreach ($result as $content) {
      $city_name = $content->name;
    }

    return $city_name;
  }

  /**
   * Helper function get country name by id.
   */
  public function getCountryNameById($id) {
    $query = $this->connection->select(DataModel::COUNTRY, 'n');
    $query->fields('n');
    $query->condition('id', $id, "=");
    $result = $query->execute()->fetchAll();
    foreach ($result as $content) {
      $cntry_name = $content->name;
    }

    return $cntry_name;
  }

  /**
   * Helper function get code values.
   */
  public function getCodeValues($codetype, $codename) {
    $query = $this->connection->select(DataModel::CODEVAL, 'n');
    $query->fields('n');
    $query->condition('codename', $codename, "=");
    $query->condition('codetype', $codetype, "=");
    $result = $query->execute();
    foreach ($result as $content) {
      $codevalue = $content->codevalues;
    }

    return $codevalue;
  }

  /**
   * Helper function to get roles.
   */
  public function getRoles() {
    $roles = $this->entityTypeManager->getStorage('user_role')->loadMultiple();
    foreach ($roles as $val) {
      $exclude = ['anonymous', 'administrator'];
      if (in_array($val->id(), $exclude)) {
        continue;
      }
      $roleList[$val->id()] = $val->label();
    }
    return $roleList;
  }

  /**
   * Generating code for codevalues based upon entity type.
   *
   * @1st paramter entity type
   * @2nd paramter string inputed by user whcih to be used in
   * generating code
   */
  public function generateCode($codephrase, $string) {
    $codename = '';

    for ($i = 3; $i <= 8; $i++) {
      $extract_subcode_from_string = substr($string, 0, $i);
      $codename = strtoupper($codephrase . $extract_subcode_from_string);

      $query = $this->connection->select(DataModel::CODEVAL, 'n');
      $query->fields('n')
        ->condition('codename', $codename, "=");
      $result = $query->execute()->fetch();
      if (empty($result)) {
        break;
      }
    }

    return $codename;
  }

  /**
   * Generate default password.
   */
  public function generateDefaultPassword($str, $dob) {
    $dob_split_arr = explode('/', $dob);
    $dob_merge_to_string = implode('', $dob_split_arr);

    $password = $str . '@' . $dob_merge_to_string;

    return $password;
  }

  /**
   * Change the dateformat to Db date format.
   *
   * Parameters required UI @date string.
   */
  public function getDbDateFormat($dt) {
    $explode_dt = explode('/', $dt);
    $dbDate = $explode_dt[2] . '-' . $explode_dt[1] . '-' . $explode_dt[0];
    return $dbDate;
  }

  /**
   * This function helps to identify whether a path exist ?
   */
  public function isPathEnable($path, $action) {
    $path = $path . '/' . $action;
    $query = $this->connection->select('router', 'r');
    $query->fields('r', ['path']);
    $query->condition('path', '%' . $path . '%', 'LIKE');
    $results = $query->execute()->fetchAll();

    // Get current route name.
    // from that fetch route module name which
    // will use on permission on twig file.
    $current_route_permission = $this->routeMatch->getRouteObject()->getRequirements()['_permission'];
    $route_permission_name = explode(' ', $current_route_permission)[0];
    $label = '';

    // Get the route name of the ADD page and fetch title of it.
    // to be used in ADD button Label.
    // Get the route name of listing page.
    // Get the module name from route name
    // Generate add page route name of that module.
    // echo $route_name;die;.
    $route_name = $this->routeMatch->getRouteName();
    $route_name_arr = explode('.', $route_name);
    $route_name_arr = array_reverse($route_name_arr);

    foreach ($route_name_arr as $route_name_module) {
      // Generate add page route name.
      $add_route_name = $route_name_module . '.add';

      // Check if route path exist.
      $route_provider = $this->routeProvider;
      $exists = count($route_provider->getRoutesByNames([$add_route_name])) === 1;

      if (!$exists) {
        continue;
      }
      $path = '';
      // Check the Add page route details.
      if ($route = $this->routeProvider->getRouteByName($add_route_name)) {
        $title = $route->getDefault('_title');
        $label = $title;
        $path = $route->getPath();
        break;
      }
    }

    $result = [
      'exist' => FALSE,
      'permission' => FALSE,
      'label' => $label ,
      'path' => $path,
    ];
    if (count($results) > 0) {
      $result['exist'] = TRUE;
    }

    if ($this->currentUser->hasPermission($route_permission_name . ' ' . $action)) {
      $result['permission'] = TRUE;
    }

    return $result;
  }

  /**
   * This function helps to set the page title Dynamically.
   *
   * @param string $newTitle
   *   New title.
   */
  public function setPageTitle($newTitle) {
    if ($route = $this->request->attributes->get(RouteObjectInterface::ROUTE_OBJECT)) {
      $route->setDefault('_title', $newTitle);
    }
  }

}
