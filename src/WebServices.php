<?php

namespace Spredfast\CustomSolutions;

class WebServices {

    /**
     * 
     * @var string
     */
    protected $client_id;

    /**
     *
     * @var string
     */
    protected $client_secret;

    /**
     *
     * @var string 
     */
    protected $redirect_uri;

    /**
     *
     * @var string 
     */
    protected $base_url = "http://ws.spredfast.us/";

    /**
     *
     * @var string 
     */
    protected $access_token;

    /**
     * Contains the last HTTP status code returned
     * @var integer 
     */
    public $http_code;

    /**
     * Contains the last HTTP headers returned
     * @var array 
     */
    public $http_info = array();

    /**
     *
     * @var array 
     */
    public $http_headers;

    /**
     *
     * @var string 
     */
    protected $user_agent = "WebServicesCLI";

    /**
     * 
     * @param string $client_id
     * @param string $client_secret
     * @param string $redirect_uri
     */
    public function __construct($client_id = null, $client_secret = null, $redirect_uri = null) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_uri = $redirect_uri;
    }

    /**
     * 
     * @param string $token
     */
    public function setAccessToken($token) {
        $this->access_token = $token;
    }

    /**
     * 
     * @return string
     */
    public function getAccessToken() {
        return $this->access_token;
    }

    /**
     * 
     * @return string access_token
     */
    public function authorize() {
        $code = $this->requestCode();
        return $this->requestAccessToken($code);
    }

    /**
     * Retrieves code for access_token
     * @return string
     */
    public function requestCode() {
        $ch = curl_init();
        $params = $this->getDefaultAuthParams();
        $params['response_type'] = 'code';
        $url = $this->base_url . 'oauth/authorize' . '?' . http_build_query($params);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); //don't follow redirects
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        curl_close($ch);
        if ($curl_info['http_code'] == 302) {
            return $this->getCodeFromURL($curl_info['redirect_url']);
        } else {
            throw new \Exception("Can't get authentication code");
        }
    }

    /**
     * Retrieves access token using requested code
     * @param string $code
     * @return string
     */
    public function requestAccessToken($code) {
        $ch = curl_init();
        $params = $this->getDefaultAuthParams();
        $params['grant_type'] = 'authorization_code';
        $params['code'] = $code;
        curl_setopt($ch, CURLOPT_URL, $this->base_url . 'oauth/access_token');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        $server_output = curl_exec($ch);
        $json_result = json_decode($server_output);
        curl_close($ch);
        $this->access_token = $json_result->access_token;
        return $json_result->access_token;
    }

    /**
     * Parse url for redirect and get code
     * @param string $url
     * @return string
     */
    private function getCodeFromURL($url) {
        $parts = parse_url($url);
        $queryParts = explode('=', $parts['query']);
        return $queryParts[1];
    }

    /**
     * 
     * @return array
     */
    private function getDefaultAuthParams() {
        return array(
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri' => $this->redirect_uri
        );
    }

    /**
     * 
     * @param string $url
     * @return array
     */
    public function get($url) {
        return $this->http($url, 'GET');
    }

    /**
     * 
     * @param string $url
     * @param array $params
     * @return array
     */
    public function post($url, $params = array()) {
        return $this->http($url, 'POST', $params);
    }

    /**
     * 
     * @param string $url
     * @return string
     */
    public function delete($url) {
        return $this->http($url, 'DELETE');
    }

    /**
     * Make a HTTP request
     * @param string $url
     * @param string $method
     * @param array $postfields
     * @return stdClass API results
     */
    function http($url, $method, $postfields = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $url = $this->base_url . $url;
        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, TRUE);
                if (!empty($postfields)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
                }
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            /*
              if (!empty($postfields)) {
              $url = "{$url}?" . http_build_query($postfields);
              }
             * 
             */
        }
        curl_setopt($ch, CURLOPT_URL, $this->attachAccessTokenToURL($url));
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        $headers = $this->getHeadersFromString($header);
        $this->http_headers = $headers;
        $this->http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->http_info = array_merge($this->http_info, curl_getinfo($ch));
        curl_close($ch);
        return json_decode($body);
    }

    /**
     * Add access token to url
     * @param string $url
     * @return string
     */
    private function attachAccessTokenToURL($url) {
        if (!$this->access_token) {
            return $url;
        }
        if (strpos($url, '?') === false) {
            $url .= '?' . http_build_query(array('access_token' => $this->access_token));
        } else {
            $url .= '&' . http_build_query(array('access_token' => $this->access_token));
        }
        return $url;
    }

    /**
     * Returns associative array with all headers
     * @param string $headerContent
     * @return array
     */
    private function getHeadersFromString($headerContent) {
        $headers = array();
        $arrRequests = explode("\r\n\r\n", $headerContent);
        for ($index = 0; $index < count($arrRequests) - 1; $index++) {
            foreach (explode("\r\n", $arrRequests[$index]) as $i => $line) {
                if ($i !== 0) {
                    @list ($key, $value) = explode(': ', $line);
                    $headers[$key] = $value;
                }
            }
        }
        return $headers;
    }

    /**
     * Returns total count of rows for previous query.
     * @return integer|null
     */
    public function getTotalCount() {
        if (isset($this->http_headers['X-Total-Count'])) {
            return $this->http_headers['X-Total-Count'];
        }
        return null;
    }

}
