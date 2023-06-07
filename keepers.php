<?php
require_once 'vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig('client_secrets.json');
$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
$client->addScope(Google_Service_Docs::DOCUMENTS_READONLY);
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php');

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
    $driveService = new Google_Service_Drive($client);

    if (isset($_GET['url'])) {
        $docId = $_GET['url'];

        $file = $driveService->files->get($docId);
        $userInfo = $driveService->about->get(['fields' => 'user']);
        $googleUsername = $userInfo->getUser()->getDisplayName();

        $file->getName();
        $file->getId();

        // Download the current version
        $exportMIMEType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        $currentContent = $driveService->files->export($file->getId(), $exportMIMEType, ['alt' => 'media']);
        file_put_contents($file->getName() . ' CURRENT.docx', $currentContent->getBody());

        $revisions = $driveService->revisions->listRevisions($file->getId());

        $revisionCounter = 1;

        foreach ($revisions->getRevisions() as $revision) {
            $revisionContent = $driveService->files->export($file->getId(), $exportMIMEType, ['alt' => 'media']);

            file_put_contents($file->getName() . ' ' . $revisionCounter . '.docx', $revisionContent->getBody());

            // Store revision times in the session
            $_SESSION['revisionTimes'][$revisionCounter] = $revision->getModifiedTime();

            $revisionCounter++;
        }

        // Store the document name in a session variable
        $_SESSION['docName'] = $file->getName();

        // Redirect to process.php after processing the revisions
        header('Location: process.php?id='.$docId.' ');
        exit();

    } else {
        echo "No Google Document ID found in the URL.\n";
    }
} else {
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
?>
