<?php

use Pel\Helper\AskGeoAPI;

class AskGeoAPITest extends PHPUnit_Framework_TestCase
{
    protected static function newAskGeoAPI(
        $account_id = 'account_id',
        $api_key = 'api_key',
        $result_format = 'obj',
        $is_secure_connection = true,
        $curl_options = array()
    ) {
        return new AskGeoAPI(
            $account_id,
            $api_key,
            $result_format,
            $is_secure_connection,
            $curl_options
        );
    }

    protected static function callMethod($name, array $args, $obj = null)
    {
        if ($obj === null) {
            $obj = static::newAskGeoAPI();
        }

        $class = new ReflectionClass('Pel\Helper\AskGeoAPI');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }

    /*************************************************************
     * buidQueryString tests
     *************************************************************/

    public function testBuildQueryStringDatabasesOnePointsOne()
    {
        $databases = 'TimeZone';
        $points = array(0.5, 1);
        $qs = static::callMethod('buildQueryString', array($databases, $points));
        $qs_expected = "databases=TimeZone&points=0.5%2C1";
        $this->assertTrue($qs == $qs_expected);
    }

    public function testBuildQueryStringDatabasesManyPointsMany()
    {
        $databases = array('Point', 'TimeZone');
        $points = array(array(0.5, 1), array(2, 1.5));
        $qs = static::callMethod('buildQueryString', array($databases, $points));
        $qs_expected = "databases=Point%2CTimeZone&points=0.5%2C1%3B2%2C1.5";
        $this->assertTrue($qs == $qs_expected);
    }

    public function testBuildQueryStringCallbackAndDatetime()
    {
        $databases = 'TimeZone';
        $points = array(0.5, 1);
        $callback = 'myCallback';
        $datetime = 'myDatetime';
        $qs = static::callMethod('buildQueryString', array($databases, $points, $callback, $datetime));
        $qs_expected = "databases=TimeZone&points=0.5%2C1&callback=myCallback&dateTime=myDatetime";
        $this->assertTrue($qs == $qs_expected);
    }
}
