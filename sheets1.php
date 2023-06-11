<?php
require_once 'vendor/autoload.php';

session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

$client = new Google_Client();
$client->setAuthConfig('client_secrets.json');
$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
$client->addScope(Google_Service_Sheets::SPREADSHEETS_READONLY);
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php');

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
    $driveService = new Google_Service_Drive($client);
    $sheetsService = new Google_Service_Sheets($client);

    if (isset($_GET['url'])) {
        $sheetId = $_GET['url'];

        $sheet = $driveService->files->get($sheetId);
        $userInfo = $driveService->about->get(['fields' => 'user']);
        $googleUsername = $userInfo->getUser()->getDisplayName();

        $sheet->getName();
        $sheet->getId();

        // Read all values, cell references, formulas, and equations from the Google Sheets file
        $range = 'Sheet1';  // Replace with your specific sheet name if necessary
        $responseWithFormulas = $sheetsService->spreadsheets_values->get($sheetId, $range, ['valueRenderOption' => 'FORMULA']);
        $responseWithValues = $sheetsService->spreadsheets_values->get($sheetId, $range, ['valueRenderOption' => 'UNFORMATTED_VALUE']);
        
        $valuesWithFormulas = $responseWithFormulas->getValues();
        $valuesWithValues = $responseWithValues->getValues();

        if (empty($valuesWithFormulas)) {
            die("Error: No data found.\n");
        } 

        $content = '';
        foreach ($valuesWithFormulas as $rowIndex => $row) {
    foreach ($row as $cellIndex => $cell) {
        echo "Row ".($rowIndex+1)." Cell ".($cellIndex+1)."<br>";
        echo "Formula: " . $cell . "<br>";
        echo "Result: " . $valuesWithValues[$rowIndex][$cellIndex] . "<br><br>";
    }
}


        // Build the questions for the prompt and question map
        $questions = '';
        $questionMap = [];
        $questionNumber = 1;
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'question') !== false) {
                $questions .= '(' . $value . '), ';
                $questionMap[$questionNumber] = $value;
                $questionNumber++;
            }
        }
        $questions = rtrim($questions, ', ');

        // Collect the prompt from POST
        $prompt = isset($_POST['prompt']) ? $_POST['prompt'] . ' ' . $questions : '';

        // Remaining code is unchanged from Script2...

    } else {
        echo "No Google Sheets ID found in the URL.\n";
    }
} else {
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

echo $questions;
echo $prompt;

?>
