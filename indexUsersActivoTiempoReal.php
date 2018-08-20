<?php
/**
 * 1.Create and Execute a Real Time Report
 * An application can request real-time data by calling the get method on the Analytics service object.
 * The method requires an ids parameter which specifies from which view (profile) to retrieve data.
 * For example, the following code requests real-time data for view (profile) ID 56789.
 */
 require_once __DIR__ . '/vendor/autoload.php';
 $analytics = initializeAnalytics();
 
 function initializeAnalytics() {
  $KEY_FILE_LOCATION = __DIR__ . '/service-account-credentials.json';
  $client = new Google_Client();
  $client->setApplicationName("Hello Analytics Reporting");
  $client->setAuthConfig($KEY_FILE_LOCATION);
  $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
  $analytics = new Google_Service_Analytics($client);
  return $analytics;
}
 
$optParams = array(
    'dimensions' => 'rt:medium');

try {
  $results = $analytics->data_realtime->get(
      'ga:41516807',
      'rt:activeUsers',
      $optParams);
  // Success.
} catch (apiServiceException $e) {
  // Handle API service exceptions.
  $error = $e->getMessage();
}

print_r($results);
echo "<hr>";
printRealtimeReport($results);

/**
 * 2. Print out the Real-Time Data
 * The components of the report can be printed out as follows:
 */

function printRealtimeReport($results) {
  printReportInfo($results);
  printQueryInfo($results);
  printProfileInfo($results);
  printColumnHeaders($results);
  printDataTable($results);
  printTotalsForAllResults($results);
}

function printDataTable(&$results) {
	echo "<h1>printDataTable</h1>";
	$table = "";
  if (count($results->getRows()) > 0) {
    $table .= '<table>';

    // Print headers.
    $table .= '<tr>';

    foreach ($results->getColumnHeaders() as $header) {
      $table .= '<th>' . $header->name . '</th>';
    }
    $table .= '</tr>';

    // Print table rows.
    foreach ($results->getRows() as $row) {
      $table .= '<tr>';
        foreach ($row as $cell) {
          $table .= '<td>'
                 . htmlspecialchars($cell, ENT_NOQUOTES)
                 . '</td>';
        }
      $table .= '</tr>';
    }
    $table .= '</table>';

  } else {
    $table .= '<p>No Results Found.</p>';
  }
  print $table;
}

function printColumnHeaders(&$results) {
  $html = '';
  $headers = $results->getColumnHeaders();

  foreach ($headers as $header) {
    $html .= <<<HTML
<pre>
Column Name       = {$header->getName()}
Column Type       = {$header->getColumnType()}
Column Data Type  = {$header->getDataType()}
</pre>
HTML;
  }
  print $html;
}

function printQueryInfo(&$results) {
  $query = $results->getQuery();
  print_r($query);
  $html = <<<HTML
<pre>
Ids         = ga:41516xxx
Metrics     = rt:activeUsers
Dimensions  = rt:medium
Sort        = 
Filters     = 
Max Results = max-results
</pre>
HTML;

  print $html;
}

function printProfileInfo(&$results) {
  $profileInfo = $results->getProfileInfo();

  $html = <<<HTML
<pre>
Account ID               = {$profileInfo->getAccountId()}
Web Property ID          = {$profileInfo->getWebPropertyId()}
Internal Web Property ID = {$profileInfo->getInternalWebPropertyId()}
Profile ID               = {$profileInfo->getProfileId()}
Profile Name             = {$profileInfo->getProfileName()}
Table ID                 = {$profileInfo->getTableId()}
</pre>
HTML;

  print $html;
}

function printReportInfo(&$results) {
  $html = <<<HTML
  <pre>
Kind                  = {$results->getKind()}
ID                    = {$results->getId()}
Self Link             = {$results->getSelfLink()}
Total Results         = {$results->getTotalResults()}
</pre>
HTML;

  print $html;
}

function printTotalsForAllResults(&$results) {
  $totals = $results->getTotalsForAllResults();
	$html = "";
  foreach ($totals as $metricName => $metricTotal) {
    $html .= "Metric Name  = $metricName\n";
    $html .= "Metric Total = $metricTotal";
  }

  print $html;
}