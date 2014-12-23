<?php

namespace Spredfast\CustomSolutions;

/**
 * Description of WebServicesClient
 *
 * @author Anton Kuku
 */
class WebServicesClient {

    /**
     *
     * @var WebServices 
     */
    public $worker;

    /**
     * 
     * @param string $client_id
     * @param string $client_secret
     * @param string $redirect_uri
     */
    public function __construct($client_id = null, $client_secret = null, $redirect_uri = null) {
        $this->worker = new WebServices($client_id, $client_secret, $redirect_uri);
    }

    /**
     * 
     */
    public function authorize() {
        return $this->worker->authorize();
    }

    /**
     * 
     * @param string $token
     */
    public function setAccessToken($token) {
        $this->worker->setAccessToken($token);
    }

    /**
     * 
     * @return string
     */
    public function getAccessToken() {
        return $this->worker->getAccessToken();
    }

    /**
     * 
     * @param string $environment
     * @param integer $company_id
     * @param integer $offset
     * @param integer $limit
     * @return array
     */
    public function getCompanyAccounts($environment, $company_id, $offset = 0, $limit = 100) {
        $url = "company/accounts/$environment/$company_id/$offset/$limit";
        return $this->worker->get($url);
    }

    /**
     * 
     * @param string $environment
     * @param integer $company_id
     * @param integer $offset
     * @param integer $limit
     * @return array
     */
    public function getCompanyUsers($environment, $company_id, $offset = 0, $limit = 100) {
        $url = "company/users/$environment/$company_id/$offset/$limit";
        return $this->worker->get($url);
    }

    /**
     * 
     * @param string $environment
     * @param integer $company_id
     * @return string|null
     */
    public function getCompanyName($environment, $company_id) {
        $url = "company/name/$environment/$company_id";
        $data = $this->worker->get($url);
        $result = null;
        if (isset($data->name)) {
            $result = $data->name;
        }
        return $result;
    }

    /**
     * Returns total number of rows for functions that should return set of data(company/users, company/accounts, etc)
     * @return integer|null
     */
    public function getTotalCount() {
        return $this->worker->getTotalCount();
    }

    /**
     * 
     * @param string $filepath FULL path to file. You can use realpath() function
     * @return array|null Returns null if fails
     */
    public function convertCSV2JSON($filepath) {
        return $this->worker->post('csv2json/convert', array('file' => '@' . $filepath));
    }

    /**
     * 
     * @param string $filepath FULL path to file. You can use realpath() function
     * @return array|null Returns null if fails
     */
    public function extractCSV2JSON($filepath) {
        $data = $this->worker->post('csv2json/extract', array('file' => '@' . $filepath));
        if ($data instanceof \stdClass) {
            return (array) $data;
        }
        return null;
    }

    /**
     * Retrieves set of data for social media content + metrics
     * @param string $url
     * @return array|null Returns null if fails or can't get data for this source
     */
    public function socialMediaLink($url) {
        $data = $this->worker->get('socialmedialink?url=' . $url);
        if ($data instanceof \stdClass) {
            return json_decode(json_encode($data), true);;
        }
        return null;
    }

}
