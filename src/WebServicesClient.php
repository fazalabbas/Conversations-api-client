<?php

namespace Spredfast\CustomSolutions;

/**
 * Description of WebServicesClient
 *
 * @author Anton Kuku
 */
class WebServicesClient {

    const CONVERSATIONS_ENV_APP  = 'app';
    const CONVERSATIONS_ENV_APP3 = 'app3';
    const CONVERSATIONS_ENV_VPC1 = 'vpc1';

    const PLATFORM_CONVERSATIONS = 'conversations';
    const PLATFORM_EXPERIENCES   = 'experiences';

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
     * @param bool $use
     */
    public function useHTTPS($use = true) {
        $this->worker->useHTTPS($use);
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
     * MAY CONTAIN DUPLICATES IF VOICE ASSOCIATED WITH MULTIPLE INITIATIVES
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
    public function getCompanyInitiativeStreams($environment, $initiative_id, $offset = 0, $limit = 100, $fields = array()) {
        $url = "company/initiativestreams/$environment/$initiative_id?" . $this->makeQueryParamsString($offset, $limit, $fields);
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
     * @param bool $use_emails Use user name or email as a key
     * @return array
     */
    public function getAbsorbStudentsEnrollment($department_id, $category_id = null, $use_emails = false) {
        $url = 'absorb/students/enrollment/' . $department_id;
        if ($category_id) {
            $url .= '/' . $category_id;
            if ($use_emails) {
                $url .= '/' . 1;
            }
        } elseif ($use_emails) {
            $url .= '/0/' . 1;
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

    /**
     * Returns data from companies table
     * @param string $environment
     * @param int $company_id
     * @param array $fields
     * @return array
     */
    public function getCompanyInfo($environment, $company_id, $fields = array()) {
        $data = $this->worker->get("company/info/$environment/$company_id" . '?' . $this->makeFieldsParam($fields));
        return $data;
    }

    /**
     * @param string $environment
     * @param int $company_id
     * @param int $offset
     * @param int $limit
     * @param array $fields
     * @return array
     */
    public function getCompanyAuthenticatedAccounts($environment, $company_id, $offset = 0, $limit = 100, $fields = array()) {
        $url = "company/authenticatedaccounts/$environment/$company_id?" . $this->makeQueryParamsString($offset, $limit, $fields);
        return $this->worker->get($url);
    }

    /**
     * Returns details for Stream Item Logs
     * @param string $environment
     * @param int $company_id
     * @param int $start_date Epoch timestamp
     * @param int $end_date Epoch timestamp
     * @param int $offset
     * @param int $limit
     * @param array $fields
     * @return array
     */
    public function getDetailedStreamItemLogs($environment, $company_id, $start_date, $end_date = 0, $offset = 0, $limit = 100, $fields = array()) {
        $url = "company/detailedstreamitemlog/$environment/$company_id/$start_date/$end_date?" . $this->makeQueryParamsString($offset, $limit, $fields);
        return $this->worker->get($url);
    }

    /**
     * Search for a company across all environments
     * @param string $name company name
     * @param array $fields
     * @return array
     */
    public function searchCompany($name, $fields = array()) {
        $url = "company/search/$name?" . $this->makeFieldsParam($fields);
        return $this->worker->get($url);
    }

    /**
     * Get stats for initiatives list. Returns number of users by role(admin, user),
     * number of messages(conversation, auto_import),
     * number of active accounts by social network
     * @param string $environment
     * @param int $company_id
     * @param array $initiatives
     * @param int $start_timestamp
     * @param int $end_timestamp
     * @return array
     */
    public function getCompanyInitiativeStats($environment, $company_id, array $initiatives, $start_timestamp = null, $end_timestamp = null) {
        $url = "company/initiativestats/$environment/$company_id/" . implode(',', $initiatives);
        if ($start_timestamp) {
            $url .= "/$start_timestamp";
            if ($end_timestamp) {
                $url .= "/$end_timestamp";
            }
        }
        return $this->worker->get($url);
    }

    /**
     * Get users associated with initiatives
     * @param string $environment
     * @param int $company_id
     * @param array $initiatives
     * @param int $offset
     * @param int $limit
     * @param array $fields
     * @return array
     */
    public function getCompanyInitiativeUsers($environment, $company_id, array $initiatives, $offset = 0, $limit = 100, $fields = array()) {
        $url = "company/initiativeusers/$environment/$company_id/" . implode(',', $initiatives) . "?" . $this->makeQueryParamsString($offset, $limit, $fields);
        return $this->worker->get($url);
    }

    /**
     * Get content labels
     * @param string $environment
     * @param int $company_id
     * @param int $offset
     * @param int $limit
     * @param array $fields
     * @return array
     */
    public function getCompanyContentLabels($environment, $company_id, $offset = 0, $limit = 100, $fields = array()) {
        $url = "company/contentlabels/$environment/$company_id" . '?' . $this->makeQueryParamsString($offset, $limit, $fields);
        return $this->worker->get($url);
    }

    /**
     * Get messages
     * @param string $environment
     * @param int $company_id
     * @param int $start_timestamp
     * @param int $end_timestamp
     * @param int $offset
     * @param int $limit
     * @param array $fields
     * @return array
     */
    public function getCompanyMessages($environment, $company_id, $start_timestamp, $end_timestamp, $offset = 0, $limit = 100, $fields = array()) {
        $url = "company/messages/$environment/$company_id/$start_timestamp/$end_timestamp" . '?' . $this->makeQueryParamsString($offset, $limit, $fields);
        return $this->worker->get($url);
    }

    /**
     * Get some info from ods.stream_item_log which contains actions about a message with content label
     * @param string $environment
     * @param int $company_id
     * @param int $start_timestamp
     * @param int $end_timestamp
     * @param int $offset
     * @param int $limit
     * @param array $fields
     * @return array
     */
    public function getCompanyContentLabelsUsage($environment, $company_id, $start_timestamp, $end_timestamp, $offset = 0, $limit = 100, $fields = array()) {
        $url = "company/contentlabelsusage/$environment/$company_id/$start_timestamp/$end_timestamp" . '?' . $this->makeQueryParamsString($offset, $limit, $fields);
        return $this->worker->get($url);
    }

    /**
     * @param string $environment
     * @param int $company_id
     * @param int $offset
     * @param int $limit
     * @param array $fields
     * @return array
     */
    public function getCompanyAccountSets($environment, $company_id, $offset = 0, $limit = 100, $fields = array()) {
        $url = "company/accountsets/$environment/$company_id" . '?' . $this->makeQueryParamsString($offset, $limit, $fields);
        return $this->worker->get($url);
    }

    /**
     * @param string $environment
     * @param array $list
     * @param array $fields
     * @return array
     */
    public function getCompanySelectedList($environment, array $list, array $fields = []) {
        $url = "company/selectedlist/$environment" . '?' . $this->makeFieldsParam($fields);
        return $this->worker->post($url, http_build_query(array('companies' => $list)));
    }

    /**
     * @param string $platform
     * @param int $company_id
     * @param null|string $environment
     * @return array
     */
    public function getGainsightCompanyStats($platform, $company_id, $environment = null) {
        $url = "gainsight/companystats/$platform/$company_id";
        if ($environment) {
            $url .= "/$environment";
        }
        return $this->worker->get($url);
    }

}
