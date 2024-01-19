<?php

namespace Drupal\company\Controller;

use Drupal\company\Model\CompanyModel;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\library\Controller\Encrypt;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for the Company.
 */
class CompanyController extends ControllerBase {

  /**
   * Include the branch service.
   *
   * @var \Drupal\company\Model\CompanyModel
   */
  protected $company;

  /**
   * The theme manager.
   *
   * @var \Drupal\Core\Theme\ThemeManagerInterface
   */
  protected $themeManager;

  /**
   * BranchForm constructor.
   *
   * @param \Drupal\company\Model\CompanyModel $company
   *   The branch service.
   * @param \Drupal\Core\Theme\ThemeManagerInterface $theme_manager
   *   The theme manager.
   */
  public function __construct(CompanyModel $company, ThemeManagerInterface $theme_manager) {
    $this->company = $company;
    $this->themeManager = $theme_manager;
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
      $container->get('company.service'),
      $container->get('theme.manager'),
    );
  }

  /**
   * Helper function to display organisation info.
   */
  public function display() {

    global $base_url;
    $data = $this->company->getCompanyDetailsById(1);
    $encrypt = new Encrypt();

    $asset_url = $base_url . '/' . $this->themeManager->getActiveTheme()->getPath();
    $comp_details = [];
    if (!empty($data)) {
      $comp_details = [
        'logo' => \Drupal::service('file_url_generator')->generateAbsoluteString("public://logo.png"),
        'name' => $data->companyname,
        'type' => $data->codevalues,
        'email' => $data->email,
        'phone' => $data->phone,
        'address' => $data->address1 . ', ' . $data->address2 . ', ' . $data->cityname . ', ' . $data->statename . ', ' . $data->countryname . ', ' . $data->pincode,
        'id'     => $encrypt->encode($data->companypk),
      ];
    }
    return [
      '#theme'  => 'companyview',
      '#prefix' => '<div class="panel panel-info"> <h3 class="box-title  col-md-10">Company Details</h3>
                  <div class=" col-md-2"> <a id="printit" data-toggle="tooltip" data-original-title="Print"><img src="' . $asset_url . '/assets/images/icon/print.png" /></a>
						      </div></div>',

      '#data' => $comp_details,
    ];
  }

}
