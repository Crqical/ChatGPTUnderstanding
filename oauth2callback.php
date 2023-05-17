<?php
require_once 'vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig('client_secrets.json');  // Path to your client_secrets.json file
$client->addScope(Google_Service_Docs::DOCUMENTS_READONLY);
$client->addScope(Google_Service_Drive::DRIVE_READONLY);
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php');  // Path to this file

if (! isset($_GET['code'])) {
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/';  // Path to the file that retrieves the document and calculates the word count
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
?>
