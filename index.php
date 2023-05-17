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

        echo "Hello : " . $googleUsername . "<br><br>";

        print "Google Documents:<br>";
        foreach ($results->getFiles() as $file) {
            $counting = 0; // Reset counting for each document
            printf("Document: %s (%s)<br>", $file->getName(), $file->getId());

            // Get the document content
            $document = $docsService->documents->get($file->getId());
            $content = $document->getBody()->getContent();

            // Process the document content
            $text = '';
            $headings = false;

          
          $images = false;
$imageCount = 0;
foreach ($content as $element) {
    if ($element->getParagraph()) {
        foreach ($element->getParagraph()->getElements() as $paragraphElement) {
            if ($paragraphElement->getInlineObjectElement() || ($paragraphElement->getTextRun() && strpos($paragraphElement->getTextRun()->getContent(), '<img') !== false)) {
                $images = true;
                $imageCount++;
                break;
            }
        }
    }
}

          
            foreach ($content as $element) {
                if ($element->getParagraph()) {
                    foreach ($element->getParagraph()->getElements() as $element) {
                        if ($element->getTextRun()) {
                            $text .= $element->getTextRun()->getContent();
                        }
                        $headings = true;
                    }
                  
                }
            }



          
$titleExists = false;
foreach ($content as $element) {
    if ($element->getParagraph() && $element->getParagraph()->getParagraphStyle()) {
        $paragraphStyle = $element->getParagraph()->getParagraphStyle();
        if ($paragraphStyle->getNamedStyleType() === 'TITLE') {
            $titleExists = true;
            break;
        }
    }
}        
            // Count the words
            $wordCount = str_word_count($text);

            // Display word count, headings, and image count
            echo "Total number of words: " . $wordCount . "<br>";
          echo "Number of images: " . $imageCount . "<br>";

            if ($wordCount < 100) {
                echo "There needs to be at least 100 words, you have " . $wordCount . "<br>";
            } else {
                $counting++;
            }

            // Check for headings
            if ($headings) {
                $counting++;
            } else {
                echo "THERE IS NO HEADING<br>";
            }

            // Check for images
            if ($images) {
                $counting++;
            } else {
                echo "You do not have an image<br>";
            }


          
            // Check for a specific word
            if (strpos($text, 'benrud') !== false) {
                $counting++;
            } else {
                echo "The specific word 'benrud' is not found in the document<br>";
            }

          if ($titleExists) {
    $counting++;
} else {
    echo "There is no title in the document<br>";
}


          
            echo '<label for="file"></label>
<progress id="file" value="'.$counting.'" max="5">32%</progress>';

          If($counting == 5) {
            echo "A+ FOR YOU. You are SMART BOI";
          } else {
            echo "YOU DO NOT GET a A+ GET BETTER LOOSER";
          }

          
            echo "<hr>"; // Add a horizontal rule

          
       

        }
    }
} else {
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';  // Path to your OAuth 2.0 callback file
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
?>
