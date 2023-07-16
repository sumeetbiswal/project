<?php

namespace Drupal\library\Lib;

use Drupal\Core\Controller\ControllerBase;


class DataModel extends ControllerBase {

	const CODEVAL		= 	'srch_codevalues';
	const STATE 		= 	'srch_states';
	const CITY 			= 	'srch_cities';
	const COUNTRY 		= 	'srch_countries';
	const COMPANYINFO 	= 	'srch_companyinfo';

	const EMPPERSONAL 	= 'srch_personalinfo';
	const EMPCONTACT	= 'srch_contactinfo';
	const EMPACADEMIC	= 'srch_academicinfo';
	const EMPEXPRNC	 	= 'srch_employeementinfo';
	const EMPOFFICIAL	= 'srch_officialinfo';


  const EMPTAGGING = 'srch_tagging';

	const USERDATA = 'users_field_data';
}
