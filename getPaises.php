<?php
require_once __DIR__ . '/vendor/autoload.php';

$analytics = initializeAnalytics();
$profile = getFirstProfileId($analytics);
$results = getResults($analytics, $profile);
printResults($results); 

function initializeAnalytics() {
  $KEY_FILE_LOCATION = __DIR__ . '/service-account-credentials.json';
  $client = new Google_Client();
  $client->setApplicationName("Hello Analytics Reporting");
  $client->setAuthConfig($KEY_FILE_LOCATION);
  $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
  $analytics = new Google_Service_Analytics($client);
  return $analytics;
}

function getFirstProfileId($analytics) {
  $accounts = $analytics->management_accounts->listManagementAccounts();

  if (count($accounts->getItems()) > 0) {
    $items = $accounts->getItems();
    $firstAccountId = $items[0]->getId();

    $properties = $analytics->management_webproperties
        ->listManagementWebproperties($firstAccountId);

    if (count($properties->getItems()) > 0) {
      $items = $properties->getItems();
      $firstPropertyId = $items[0]->getId();

      $profiles = $analytics->management_profiles
          ->listManagementProfiles($firstAccountId, $firstPropertyId);

      if (count($profiles->getItems()) > 0) {
        $items = $profiles->getItems();

        return $items[0]->getId();

      } else {
        throw new Exception('No views (profiles) found for this user.');
      }
    } else {
      throw new Exception('No properties found for this user.');
    }
  } else {
    throw new Exception('No accounts found for this user.');
  }
}

function getResults($analytics, $profileId) {
   $optParams = array(
   'dimensions' => 'ga:country,ga:city', // 'ga:country,ga:browser',
   'max-results' => '150',
   'sort' => 'ga:country',
   'filters' => 'ga:country=@a'
);
	
	$ids = 'ga:41516xxx';
$startDate = '2018-08-09';
$endDate = '2018-08-16';
$metrics = 'ga:users';
   
return $analytics->data_ga->get($ids, $startDate, $endDate, $metrics, $optParams);
	   
}

function printResults($results) {
  if (count($results->getRows()) > 0) {
    $profileName = $results->getProfileInfo()->getProfileName();
    $rows = $results->getRows();
	foreach ($rows as $sclave=>$valor) {
		echo $valor[0]." Ciudad: ".$valor[1]." Visitas: ".$valor[2];
		echo "<br>";
	}
  } else {
    print "No results found.\n";
  }
}
