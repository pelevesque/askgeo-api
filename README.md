# AskGeo API

## About

A PHP class to consume AskGeo's API. askgeo.com

## Initialize

    include('AskGeoAPI.php');

    // Use your own account id and api key.
    $account_id = '1265';
    $api_key = 'g1d11a5117a4143be0f5bed5a9e33df4e9103deb5a32658d22505f2a04ba3nk6';
    $format = 'obj';
    $secure = true;
    $curl_options = array();

    $api = new Pel\Helper\AskGeoAPI($account_id, $api_key, $format, $secure, $curl_options);

## Calls

### General Call

    $databases = 'TimeZone';
    $points = array(45.485169, -73.699036);

    $api->get($databases, $points);

    // Optional callback and datetime.
    $api->get($databases, $points, $callback, $datetime);

### TimeZone Call

    $points = array([45.485169, -73.699036], [10.5435, -99.32]);

    $api->getTimeZone($points);

    // Optional callback and datetime.
    $api->getTimeZone($points, $callback, $datetime);
