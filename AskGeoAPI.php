<?php

/**
 * A PHP class to consume AskGeo's API.
 *
 * askgeo.com
 * askgeo.com/#web-api
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
     * @const  string  API's URL prefix
     */
    const URL_PREFIX = 'api.askgeo.com/v1';

    /**
     * @var  string  a call's URL string
     */
    protected $URL;

    /**
     * @var  array  Curl options
     */
    protected $curl_options;

    /**
     * @var  string  return format
     */
    protected $format;

    /**
     * Constructor
     *
     * @param   string  account id
     * @param   string  API key
     * @param   string  format ('json', 'xml', 'obj')
     * @param   bool    secure connection (https)
     * @param   array   Curl options
     * @return  void
     */
    public function __construct(
        $account_id,
        $api_key,
        $format = 'json',
        $secure = TRUE,
        $curl_options = array()
    ){
        $this->format = $format;

        if ($format == 'obj') {
            $format = 'json';
        }

        $this->buildURL($account_id, $api_key, $format, $secure);
        $this->curl_options = $curl_options;
    }

    /**
     * Calls the API with a general request
     *
     * points ex:
     * - many points = array(44.454, -45.45)
     * - many points = array([44.454, -45.45], [10.454, -90.45])
     *
     * @param   mixed   database(s) (string or array)
     * @param   mixed   points (array, or array of arrays)
     * @param   string  callback
     * @param   string  datetime
     * @return  object  response
     */
    public function get($databases, $points, $callback = null, $datetime = null)
    {
        $query = $this->buildQuery($databases, $points, $callback, $datetime);

        $response = $this->callAPI($query);

        if ($this->format == 'obj') {
            $response = json_decode($response);
        }

        return $response;
    }

    /**
     * Calls the API with a TimeZone request
     *
     * Shortcut method for TimeZone requests.
     *
     * points ex:
     * - many points = array(44.454, -45.45)
     * - many points = array([44.454, -45.45], [10.454, -90.45])
     *
     * @param   mixed   points (array, or array of arrays)
     * @param   string  callback
     * @param   string  datetime
     * @return  mixed   response
     */
    public function getTimeZone($points, $callback = null, $datetime = null)
    {
        return $this->get('TimeZone', $points, $callback, $datetime);
    }

    /**
     * Sets the URL
     *
     * @param   string  account id
     * @param   string  API key
     * @param   string  format ('json' or 'xml')
     * @param   bool    secure connection (https)
     * @return  void
     */
    protected function buildURL(
        $account_id,
        $api_key,
        $format = 'json',
        $secure = true
    ) {
        $this->URL = $secure ? 'https://' : 'http://';

        $this->URL .= self::URL_PREFIX . '/' . $account_id . '/';
        $this->URL .= $api_key . '/query.' . $format . '?';
    }

    /**
     * Builds the query
     *
     * points ex:
     * - many points = array(44.454, -45.45)
     * - many points = array([44.454, -45.45], [10.454, -90.45])
     *
     * @param   mixed   database(s) (string or array)
     * @param   mixed   points (array, or array of arrays)
     * @param   string  callback
     * @param   string  datetime
     * @return  string  query
     */
    protected function buildQuery(
        $databases,
        $points,
        $callback = null,
        $datetime = null
    ) {
        // Make sure databases and points are arrays.
        if (! is_array($databases)) {
            $databases = (array) $databases;
        }

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

        // Build the query.
        $query = 'databases=' . urlencode($databases);
        $query .= '&points=' . urlencode($points);

        if (is_string($callback)) {
            $query .= '&callback=' . urlencode($callback);
        }

        if (is_string($datetime)) {
            $query .= '&dateTime=' . urlencode($datetime);
        }

        return $query;
    }

    /**
     * Calls the API with a query
     *
     * @param   string  query
     * @return  mixed   response
     */
    protected function callAPI($query)
    {
        $url = $this->URL . $query;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (! empty($this->curl_options)) {
            curl_setopt_array($ch, $this->curl_options);
        }

        return curl_exec($ch);
    }
}
