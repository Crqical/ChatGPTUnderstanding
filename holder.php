<?php
require_once 'vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig('client_secrets.json');  // Path to your client_secrets.json file
$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
$client->addScope(Google_Service_Docs::DOCUMENTS_READONLY);
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php');  // Path to your OAuth 2.0 callback file

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
    $driveService = new Google_Service_Drive($client);
    $docsService = new Google_Service_Docs($client);

    // Query to get Google Documents owned by the user
    $optParams = array(
        'q' => "'me' in owners and mimeType='application/vnd.google-apps.document'",
        'fields' => 'files(id, name)',
    );

    $results = $driveService->files->listFiles($optParams);

    if (count($results->getFiles()) == 0) {
        print "No Google Documents found.\n";
    } else {
        $userInfo = $driveService->about->get(['fields' => 'user']);
        $googleUsername = $userInfo->getUser()->getDisplayName();

        echo "Hello How are y: " . $googleUsername . "<br><br>";

        print "Google Documents:<br>";
        foreach ($results->getFiles() as $file) {
            printf("Document: %s (%s)<br>", $file->getName(), $file->getId());
            
            // Get the document content
            $document = $docsService->documents->get($file->getId());
            $content = $document->getBody()->getContent();

            // Process the document content
            $text = '';
            $headings = false;
            $images = false;
            foreach ($content as $element) {
                if ($element->getParagraph()) {
                    foreach ($element->getParagraph()->getElements() as $element) {
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

            // Count the words
            $wordCount = str_word_count($text);

            // Display word count, headings, and image count
            echo "Total number of words: " . $wordCount . "<br>";
            if ($wordCount < 100) {
                echo "NOT ENOUGH WORDS<br>";
            } else {
                echo "ENOUGH WORDS<br>";
            }
            echo "Headings in the document: " . ($headings ? "YES" : "NO") . "<br>";
            echo "Images in the document: " . ($images ? "YES" : "NO") . "<br>";
            echo "<hr>"; // Add a horizontal rule
        }
    }
} else {
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';  // Path to your OAuth 2.0 callback file
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
?>