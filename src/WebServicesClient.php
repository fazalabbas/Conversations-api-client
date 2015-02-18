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
     * @param int $offset
     * @param int $limit
     * @param array $fields
     * @return string
     */
    protected function makeQueryParamsString($offset, $limit, $fields = array()) {
        $params = ['skip' => $offset, 'limit' => $limit];
        if (!empty($fields)) {
            $params['fields'] = implode(',', $fields);
        }
        return http_build_query($params);
    }

    /**
     *
     * @param string $environment
     * @param integer $company_id
     * @param integer $offset
     * @param integer $limit
     * @param array $fields
     * @return array
     */
    public function getCompanyAccounts($environment, $company_id, $offset = 0, $limit = 100, $fields = array()) {
        $url = "company/accounts/$environment/$company_id?" . $this->makeQueryParamsString($offset, $limit, $fields);
        return $this->worker->get($url);
    }

    /**
     *
     * @param string $environment
     * @param integer $company_id
     * @param integer $offset
     * @param integer $limit
     * @param array $fields
     * @return array
     */
    public function getCompanyVoices($environment, $company_id, $offset = 0, $limit = 100, $fields = array()) {
        $url = "company/voices/$environment/$company_id?" . $this->makeQueryParamsString($offset, $limit, $fields);
        return $this->worker->get($url);
    }

    /**
     *
     * @param string $environment
     * @param integer $company_id
     * @param integer $offset
     * @param integer $limit
     * @param array $fields
     * @return array
     */
    public function getCompanyInitiatives($environment, $company_id, $offset = 0, $limit = 100, $fields = array()) {
        $url = "company/initiatives/$environment/$company_id?" . $this->makeQueryParamsString($offset, $limit, $fields);
        return $this->worker->get($url);
    }

    /**
     * Contains fields from accounts,initiatives,voices.
     * Some voices can be attached to multiple initiatives so it will return you duplicates.
     * Use this method to pull accounts for ARARMARK/Gannett
     * @param string $environment
     * @param integer $company_id
     * @param integer $offset
     * @param integer $limit
     * @return array
     */
    public function getCompanyAccountsUnified($environment, $company_id, $offset = 0, $limit = 100, $fields = array()) {
        $url = "company/accountsunified/$environment/$company_id?" . $this->makeQueryParamsString($offset, $limit, $fields);
        return $this->worker->get($url);
    }

    /**
     *
     * @param string $environment
     * @param integer $company_id
     * @param integer $offset
     * @param integer $limit
     * @param array $fields
     * @return array
     */
    public function getCompanyUsers($environment, $company_id, $offset = 0, $limit = 100, $fields = array()) {
        $url = "company/users/$environment/$company_id?" . $this->makeQueryParamsString($offset, $limit, $fields);
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
     * Returns number of users, initiatives, collections, streams, teams, etc.
     * Primarily used for 30 day User Reports
     * @param string $environment
     * @param integer $company_id
     * @param string $start_date Y-m-d
     * @param string $end_date Y-m-d
     * @return array
     */
    public function getCompanyStats($environment, $company_id, $start_date = null, $end_date = null) {
        $url = "company/stats/$environment/$company_id";
        if ($start_date) {
            $url .= "/$start_date";
            if ($end_date) {
                $url .= "/$end_date";
            }
        }
        $result = $this->worker->get($url);
        return $result;
    }

    /**
     * Get list of companies(id, name, created)
     * @param string $environment
     * @param int $offset
     * @param int $limit
     * @param array $fields
     * @return array
     */
    public function getCompanyList($environment, $offset = 0, $limit = 100, $fields = array()) {
        $url = "company/list/$environment?" . $this->makeQueryParamsString($offset, $limit, $fields);
        return $this->worker->get($url);
    }

    /**
     * Search company(id, name, created) using a part of name
     * @param string $environment
     * @param string $name
     * @param integer $offset
     * @param integer $limit
     * @param array $fields
     * @return array
     */
    public function findCompany($environment, $name, $offset = 0, $limit = 100, $fields = array()) {
        $name = str_replace(' ', '%20', $name);
        $url = "company/find/$environment/$name?" . $this->makeQueryParamsString($offset, $limit, $fields);
        return $this->worker->get($url);
    }

    /**
     * Returns list of streams for company
     * @param string $environment
     * @param int $company_id
     * @param int $offset
     * @param int $limit
     * @param array $fields
     * @return array
     */
    public function getCompanyStreams($environment, $company_id, $offset = 0, $limit = 100, $fields = array()) {
        $url = "company/streams/$environment/$company_id?" . $this->makeQueryParamsString($offset, $limit, $fields);
        return $this->worker->get($url);
    }

    /**
     * Returns list of streams for company using campaign_id (initiative_id)
     * @param string $environment
     * @param int $company_id
     * @param int $initiative_id campaign_id
     * @param int $offset
     * @param int $limit
     * @param array $fields
     * @return array
     */
    public function getCompanyInitiativeStreams($environment, $company_id, $initiative_id, $offset = 0, $limit = 100, $fields = array()) {
        $url = "company/initiativestreams/$environment/$company_id/$initiative_id?" . $this->makeQueryParamsString($offset, $limit, $fields);
        return $this->worker->get($url);
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
     * @param  string $filepath FULL path to file. You can use realpath() function
     * @return array|null Returns null if fails
     */
    public function convertCSV2JSON($filepath) {
        return $this->worker->post('csv2json/convert', array('file' => '@' . $filepath));
    }

    /**
     *
     * @param  string $filepath FULL path to file. You can use realpath() function
     * @return array|null Returns null if fails
     */
    public function extractCSV2JSON($filepath) {
        $data = $this->worker->post('csv2json/extract', array('file' => '@' . $filepath));
        if ($data instanceof \stdClass) {
            return (array)$data;
        }
        return null;
    }

    /**
     * Retrieves set of data for social media content + metrics
     * @param  string $url
     * @return array|null Returns null if fails or can't get data for this source
     */
    public function socialMediaLink($url) {
        $data = $this->worker->get('socialmedialink?url=' . $url);
        if ($data instanceof \stdClass) {
            return json_decode(json_encode($data), true);
        }
        return null;
    }

    /**
     * Retrieves all comments for Facebook post/photo
     * @param  string $url
     * @param  array $fields could be empty if you need all fields
     * @param  string $filter could be stream|toplevel only
     * @return array|null Returns null if fails or can't get data for this source
     */
    public function getFacebookComments($url, $fields = array(), $filter = 'stream') {
        $flds = null;
        if (!empty($fields)) {
            $flds = implode(',', $fields);
        }
        $params = ['fields' => $flds, 'filter' => $filter, 'url' => $url];
        $query_params = http_build_query($params);
        $data = $this->worker->get('socialmedia/facebook/comments?' . $query_params);
        return $data;
    }

    /**
     * Make query param for fields
     * @param array $fields
     * @return null|string
     */
    protected function makeFieldsParam($fields = array()) {
        $result = null;
        if (!empty($fields)) {
            $result = 'fields=' . implode(',', $fields);
        }
        return $result;
    }

    /**
     * Retrieve list of categories
     * @param array $fields
     * @return array
     */
    public function getAbsorbCategories($fields = array()) {
        $data = $this->worker->get('absorb/categories?' . $this->makeFieldsParam($fields));
        return $data;
    }

    /**
     * Retrieve list of departments(companies)
     * @param array $fields
     * @return array
     */
    public function getAbsorbDepartments($fields = array()) {
        $data = $this->worker->get('absorb/departments?' . $this->makeFieldsParam($fields));
        return $data;
    }

    /**
     * Retrieve list of students in department
     * @param $department_id
     * @param array $fields
     * @return array
     */
    public function getAbsorbStudents($department_id, $fields = array()) {
        $data = $this->worker->get('absorb/students/' . $department_id . '?' . $this->makeFieldsParam($fields));
        return $data;
    }

    /**
     * Retrieve data for students about categories, courses, progress, etc
     * @param $student_id
     * @return array
     */
    public function getAbsorbStudentEnrollment($student_id) {
        $data = $this->worker->get('absorb/student/enrollment/' . $student_id);
        return $data;
    }

    /**
     * Retrieve data for students about categories, courses, progress, etc
     * @param $department_id
     * @param $category_id
     * @return array
     */
    public function getAbsorbStudentsEnrollment($department_id, $category_id = null) {
        $url = 'absorb/students/enrollment/' . $department_id;
        if ($category_id) {
            $url .= '/' . $category_id;
        }
        $data = $this->worker->get($url);
        return $data;
    }

    /**
     * Returns list of accounts(companies in list 1013) in Totango. Returns ALL data from Totango API.
     * @param array $fields
     * @return array
     */
    public function getTotangoAccounts($fields = array()) {
        $data = $this->worker->get('totango/accounts' . '?' . $this->makeFieldsParam($fields));
        return $data;
    }

    /**
     * Returns account(company) name in Totango
     * @param string $environment
     * @param int $company_id
     * @return string|null
     */
    public function getTotangoName($environment, $company_id) {
        $url = "totango/name/$environment/$company_id";
        $data = $this->worker->get($url);
        $result = null;
        if (isset($data->name)) {
            $result = $data->name;
        }
        return $result;
    }

    /**
     * Returns activity for company. Used for Conversations 30 Day User Report
     * @param string $environment
     * @param int $company_id
     * @param int $days can be 30/60/90
     * @param array $fields
     * @return array
     */
    public function getTotangoActivity($environment, $company_id, $days = 30, $fields = array()) {
        $data = $this->worker->get("totango/activity/$environment/$company_id/$days" . '?' . $this->makeFieldsParam($fields));
        return $data;
    }

    /**
     * Returns user names using array of e-mails
     * @param string $environment
     * @param array $list
     * @return array
     */
    public function getCompanyUsernames($environment, $list) {
        $company_id = 1;
        $data = $this->worker->post("company/usernames/$environment/$company_id", http_build_query(array('users' => $list)));
        return $data;
    }

}
