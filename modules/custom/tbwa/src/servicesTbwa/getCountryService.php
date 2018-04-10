<?php

namespace Drupal\tbwa\servicesTbwa;

use Symfony\Component\HttpFoundation\Response;
use Drupal\tigoapi\Exception\RequestException;
use Drupal\rest\ResourceResponse;

/**
 * Class Client.
 *
 * @package Drupal\tol
 */
class getCountryService implements getCountryServiceInterface{


  /**
   * Constructor.
   */
  public function __construct() {

  }


  function getCountryList() {
    $country_list = file_get_contents("http://battuta.medunes.net/api/country/all/?key=f386499c40b5e04f0f4d47d7f0855404");
    foreach (json_decode($country_list) as $country) {
      $countries[$country->code] = $country->name;
    }
    return $countries;
  }
  function getDepartmentList($country_code = "co") {
    $department_list = file_get_contents("http://battuta.medunes.net/api/region/".$country_code."/all/?key=f386499c40b5e04f0f4d47d7f0855404");
    foreach (json_decode($department_list) as $department) {
      $departments[$department->region] = $department->region;
    }
    return $departments;
  }
}
