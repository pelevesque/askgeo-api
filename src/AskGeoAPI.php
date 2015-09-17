<?php

/**
 * A PSR-4 PHP class to consume askgeo's API.
 *
 * https://askgeo.com
 *
 * @version     1.0
 * @author      Pierre-Emmanuel Lévesque
 * @email       pierre.e.levesque@gmail.com
 * @copyright   Copyright 2014, Pierre-Emmanuel Lévesque
 * @license     MIT License - @see README.md
 */

namespace Pel\Helper;

class AskGeoAPI
{
    /**
     * @const  string  API's URI
     */
    const API_URI = 'api.askgeo.com/v1';

    /**
     * @var  string  request url (without query string)
     */
    protected $request_url = 'joe';

    /**
     * @var  array  curl options
     */
    protected $curl_options;

    /**
     * @var  string  result format ('json', 'xml', 'obj')
     */
    protected $result_format;

    /**
     * Constructor
     *
     * @param   string  account id
     * @param   string  API key
     * @param   string  result format ('json', 'xml', 'obj')
     * @param   bool    is secure connection (https)
     * @param   array   curl options
     * @return  void
     */
    public function __construct(
        $account_id,
        $api_key,
        $result_format = 'obj',
        $is_secure_connection = true,
        $curl_options = array()
    ) {
        $this->changeUser(
            $account_id,
            $api_key,
            $result_format,
            $is_secure_connection,
            $curl_options
        );
    }

    /**
     * Changes the user
     *
     * @param   string  account id
     * @param   string  API key
     * @param   string  result format ('json', 'xml', 'obj')
     * @param   bool    is secure connection (https)
     * @param   array   curl options
     * @return  void
     */
    public function changeUser(
        $account_id,
        $api_key,
        $result_format = 'obj',
        $is_secure_connection = true,
        $curl_options = array()
    ) {
        $this->result_format = $result_format;

        if ($result_format == 'obj') {
            $result_format = 'json';
        }

        $this->buildRequestUrl(
            $account_id,
            $api_key,
            $result_format,
            $is_secure_connection
        );
        $this->curl_options = $curl_options;
    }

    /**
     * Makes a general API request
     *
     * databases ex:
     * - one database = 'TimeZone'
     * - many databases = array('TimeZone', 'Point', 'Astronomy')
     *
     * points ex:
     * - one point = array(44.454, -45.45)
     * - many points = array([44.454, -45.45], [10.454, -90.45])
     *
     * @param   mixed   database(s) (string or array)
     * @param   mixed   points (array, or array of arrays)
     * @param   string  callback
     * @param   string  datetime
     * @return  mixed   response
     */
    public function get($databases, $points, $callback = null, $datetime = null)
    {
        $query_string = $this->buildQueryString(
            $databases,
            $points,
            $callback,
            $datetime
        );

        $response = $this->request($query_string);

        if ($this->result_format == 'obj') {
            $response = json_decode($response);
        }

        return $response;
    }

    /**
     * Shortcut methods for all databases
     * 
     * @see  get() for parameter descriptions
     */

    /**
     *  Global Coverage
     */
    public function getPoint($point, $callback = null)
    {
        return $this->get('Point', $point, $callback);
    }

    public function getTimeZone($points, $callback = null, $datetime = null)
    {
        return $this->get('TimeZone', $points, $callback, $datetime);
    }

    public function getNaturalEarthCountry($points, $callback = null)
    {
        return $this->get('NaturalEarthCountry', $points, $callback);
    }

    public function getAstronomy($points, $callback = null, $datetime = null)
    {
        return $this->get('Astronomy', $points, $callback, $datetime);
    }

    /**
     *  US Coverage
     */
    public function getUsState2010($points, $callback = null)
    {
        return $this->get('UsState2010', $points, $callback);
    }

    public function getUsCounty2010($points, $callback = null)
    {
        return $this->get('UsCounty2010', $points, $callback);
    }

    public function getUsCountySubdivision2010($points, $callback = null)
    {
        return $this->get('UsCountySubdivision2010', $points, $callback);
    }

    public function getUsTract2010($points, $callback = null)
    {
        return $this->get('UsTract2010', $points, $callback);
    }

    public function getUsBlockGroup2010($points, $callback = null)
    {
        return $this->get('UsBlockGroup2010', $points, $callback);
    }

    public function getUsPlace2010($points, $callback = null)
    {
        return $this->get('UsPlace2010', $points, $callback);
    }

    public function getUsZcta2010($points, $callback = null)
    {
        return $this->get('UsZcta2010', $points, $callback);
    }

    /**
     * Builds the request url
     *
     * @param   string  account id
     * @param   string  API key
     * @param   string  result format ('json' or 'xml')
     * @param   bool    is secure connection (https)
     * @return  void
     */
    protected function buildRequestUrl(
        $account_id,
        $api_key,
        $result_format = 'json',
        $is_secure_connection = true
    ) {
        $this->request_url = $is_secure_connection ? 'https://' : 'http://';
        $this->request_url .= self::API_URI . '/' . $account_id . '/';
        $this->request_url .= $api_key . '/query.' . $result_format . '?';
    }

    /**
     * Builds the query string
     *
     * databases ex:
     * - one database = 'TimeZone'
     * - many databases = array('TimeZone', 'Point', 'Astronomy')
     *
     * points ex:
     * - one point = array(44.454, -45.45)
     * - many points = array([44.454, -45.45], [10.454, -90.45])
     *
     * @param   mixed   database(s) (string or array)
     * @param   mixed   points (array, or array of arrays)
     * @param   string  callback
     * @param   string  datetime
     * @return  string  query string
     */
    protected function buildQueryString(
        $databases,
        $points,
        $callback = null,
        $datetime = null
    ) {
        // Make sure databases is an array.
        if (! is_array($databases)) {
            $databases = (array) $databases;
        }

        // Make sure points is an array of arrays.
        if (! is_array($points[0])) {
            $points = array($points);
        }

        // Parse databases and points.
        $databases = implode(',', $databases);

        $points_temp = '';
        foreach ($points as $point) {
            $points_temp .= $point[0] . ',' . $point[1] . ';';
        }
        $points = substr($points_temp, 0, -1);

        // Build the query string.
        $query_string = 'databases=' . urlencode($databases);
        $query_string .= '&points=' . urlencode($points);

        if (is_string($callback)) {
            $query_string .= '&callback=' . urlencode($callback);
        }

        if (is_string($datetime)) {
            $query_string .= '&dateTime=' . urlencode($datetime);
        }

        return $query_string;
    }

    /**
     * Makes a request to the API
     *
     * @param   string  query string
     * @return  mixed   response
     */
    protected function request($query_string)
    {
        $url = $this->request_url . $query_string;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (! empty($this->curl_options)) {
            curl_setopt_array($ch, $this->curl_options);
        }

        return curl_exec($ch);
    }
}
