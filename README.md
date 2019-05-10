[![Build Status](https://travis-ci.org/pelevesque/askgeo-api.svg?branch=master)](https://travis-ci.org/pelevesque/askgeo-api)

# askgeo-api

## About

A PSR-4 PHP class to consume askgeo's API.. (https://askgeo.com)

## Initialize

```php
include('AskGeoAPI.php');

// ! Use your own account id and api key. !
$account_id = '1165';
$api_key = 'g1d11a5117a4143be0f5fge5a9e33df4e9103deb5a12658d22512f2a04ba3nk6';
$format = 'obj';
$secure = true;
$curl_options = array();

$api = new Pel\Helper\AskGeoAPI($account_id, $api_key, $format, $secure, $curl_options);
```

## Calls

### General Call

```php
$databases = 'TimeZone';
$points = array(45.485169, -73.699036);

$api->get($databases, $points);

// Optional callback and datetime.
$api->get($databases, $points, $callback, $datetime);
```

### TimeZone Call

```php
$points = array([45.485169, -73.699036], [10.5435, -99.32]);

$api->getTimeZone($points);

// Optional callback and datetime.
$api->getTimeZone($points, $callback, $datetime);
```

### Other Specialized Calls

Like the TimeZone call, askgeo-api can also call all the available databases directly.
For more information, take a look at the source code.
