<?php

namespace Drupal\dashboard\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

class DashboardController extends ControllerBase
{

    public function display()
    {
        global $base_url;

        return array(
          '#markup' => $this->t(''),
        /*  <div class="row">
                              <a href="'.$base_url.'/settings/password"><div class="col-lg-3 col-sm-6 col-xs-12">
                                <div class="white-box" style="box-shadow: 0 4px 8px 0 grey;">
                                    <h3 class="box-title">Change password</h3>
                                    <ul class="list-inline two-part">
                                        <li><i class="icon-lock text-info"></i></li>
                                        <li class="text-right"><span class="counter">23</span></li>
                                    </ul>
                                </div>
                            </div></a>
                            <a href="'.$base_url.'/employee"><div class="col-lg-3 col-sm-6 col-xs-12">
                                <div class="white-box" style="box-shadow: 0 4px 8px 0 grey;">
                                    <h3 class="box-title">List Employee</h3>
                                    <ul class="list-inline two-part">
                                        <li><i class="icon-people text-purple"></i></li>
                                        <li class="text-right"><span class="counter">169</span></li>
                                    </ul>
                                </div>
                            </div></a>
                            <a href="#"><div class="col-lg-3 col-sm-6 col-xs-12">
                                <div class="white-box" style="box-shadow: 0 4px 8px 0 grey;">
                                    <h3 class="box-title">Open Projects</h3>
                                    <ul class="list-inline two-part">
                                        <li><i class="icon-folder-alt text-danger"></i></li>
                                        <li class="text-right"><span class="">311</span></li>
                                    </ul>
                                </div>
                            </div></a>
                            <a href="#"><div class="col-lg-3 col-sm-6 col-xs-12">
                                <div class="white-box" style="box-shadow: 0 4px 8px 0 grey;">
                                    <h3 class="box-title">NEW Invoices</h3>
                                    <ul class="list-inline two-part">
                                        <li><i class="ti-wallet text-success"></i></li>
                                        <li class="text-right"><span class="">117</span></li>
                                    </ul>
                                </div>
                            </div></a>
                        </div>
               '),*/
        );  
    }
}