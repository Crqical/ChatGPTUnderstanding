<?php
require_once 'vendor/autoload.php';
// WORKING ABD FIXED
session_start();

$client = new Google_Client();
$client->setAuthConfig('client_secrets.json');  // Path to your client_secrets.json file
$client->addScope(Google_Service_Docs::DOCUMENTS_READONLY);
$client->addScope(Google_Service_Drive::DRIVE_READONLY);
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/index2.php');  // Path to your OAuth 2.0 callback file

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
    
    // Use the Drive API to list all the Google Documents
    $driveService = new Google_Service_Drive($client);
    $files = $driveService->files->listFiles(array('q' => "mimeType='application/vnd.google-apps.document'"))->getFiles();

    // Use the Docs API to analyze each document
    $docsService = new Google_Service_Docs($client);
    foreach ($files as $file) {
        $documentId = $file->getId();
        $document = $docsService->documents->get($documentId);

        $text = '';
        $headings = false;
        $images = false;
        foreach ($document->getBody()->getContent() as $content) {
            if ($content->getParagraph()) {
                foreach ($content->getParagraph()->getElements() as $element) {
                    if ($element->getTextRun()) {
                        $text .= $element->getTextRun()->getContent();
                    }
                    $headings = true;
                }
                if ($element->getInlineObjectElement()) {
                    $images = true;
                }
            }
        }

        $wordCount = str_word_count($text);
        echo "Total number of words: " . $wordCount . "<br>";
        if ($wordCount < 100) {
            echo "NOT ENOUGH WORDS<br>";
        } else {
            echo "ENOUGH WORDS<br>";
        }
        echo "Headings in the document: " . ($headings ? "YES" : "NO") . "<br>";
        echo "Images in the document: " . ($images ? "YES" : "NO") . "<br>";
    }
} else {
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/index2.php';  // Path to your OAuth 2.0 callback file
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
?>
