<?php

use Spredfast\CustomSolutions\WebServicesClient;

require_once '../vendor/autoload.php';

$client_id = null;
$client_secret = null;
$redirect_uri = null;
// Examples for client with simple functions
// Also you can use public API methods without credentials/authorization/access_token
$client = new WebServicesClient($client_id, $client_secret, $redirect_uri);
// Access token expires in 10 years. Please save it and do not get a new token everytime.
//$access_token = $client->authorize();
//$client->setAccessToken($access_token);
$environment = WebServicesClient::CONVERSATIONS_ENV_APP; // on of WebServicesClient::CONVERSATIONS_ENV_*
$company_id = 6;
$filepath = realpath('example.csv');
$social_media_url = "https://twitter.com/Spredfast/status/543505824967323648";
$social_media_facebook_comments_url = "https://www.facebook.com/Spredfast/posts/917110184975303";
//$data = $client->getCompanyAccounts($environment, $company_id);
//$data = $client->getCompanyUsers($environment, $company_id);
//$data = $client->getCompanyName($environment, $company_id);
//$data = $client->convertCSV2JSON($filepath); // public method
//$data = $client->extractCSV2JSON($filepath); // public method
//$data = $client->socialMediaLink($social_media_url); // public method
$data = $client->getFacebookComments($social_media_facebook_comments_url, array('id', 'message')); // public method
var_dump($data);
