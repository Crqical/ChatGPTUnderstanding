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

        $range = 'Sheet1';  // Replace with your specific sheet name if necessary

        // Fetch both formula and unformatted values
        $responseWithFormulas = $sheetsService->spreadsheets_values->get($sheetId, $range, ['valueRenderOption' => 'FORMULA']);
        $responseWithValues = $sheetsService->spreadsheets_values->get($sheetId, $range, ['valueRenderOption' => 'UNFORMATTED_VALUE']);
        
        $valuesWithFormulas = $responseWithFormulas->getValues();
        $valuesWithValues = $responseWithValues->getValues();

        if (empty($valuesWithFormulas)) {
            die("Error: No data found.\n");
        } 

        // Collect the prompt and questions from POST
        if (!empty($_POST)) { // Check if $_POST is not empty
            $prompt = isset($_POST['prompt']) ? $_POST['prompt'] : '';
            echo "<h2>Prompt:</h2>";
            echo "<p>" . htmlspecialchars($prompt) . "</p>";

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
            echo "<h2>Questions:</h2>";
            echo "<p>" . htmlspecialchars($questions) . "</p>";
        }

        // Merge Sheets data into a string for the GPT-3 prompt
        $sheetData = '';
        foreach ($valuesWithFormulas as $rowIndex => $row) {
            foreach ($row as $cellIndex => $cell) {
                $sheetData .= "Row ".($rowIndex+1)." Cell ".($cellIndex+1)."\n";
                $sheetData .= "Formula: " . $cell . "\n";
                $sheetData .= "Result: " . $valuesWithValues[$rowIndex][$cellIndex] . "\n\n";
            }
        }
        $prompt .= ' ' . $questions . ' ' . $sheetData;

        echo "<h2>Google Sheets Data:</h2>";
        echo "<pre>" . htmlspecialchars($sheetData) . "</pre>"; // Added this line to print Google Sheets data

        // Set up GPT-3 parameters
        $apiKey = "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";
        $model = "text-davinci-003";
        $temperature = 0.7;
        $maxTokens = 256;
        $topP = 1;
        $frequencyPenalty = 0;
        $presencePenalty = 0;

        $data = array(
            'model' => $model,
            'prompt' => $prompt,
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
            'top_p' => $topP,
            'frequency_penalty' => $frequencyPenalty,
            'presence_penalty' => $presencePenalty
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/completions"); // Changed the URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $apiKey));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            die('Error: ' . curl_error($ch));
        }

        $jsonResponse = json_decode($response, true);
        if (isset($jsonResponse['error'])) {
            die('API Error: ' . $jsonResponse['error']['message']);
        }

        $generatedText = '';

        if (isset($jsonResponse['choices']) && count($jsonResponse['choices']) > 0 && isset($jsonResponse['choices'][0]['text'])) {
            $generatedText = $jsonResponse['choices'][0]['text'];

            // Display the Chat GPT response
            echo "<p>Chat GPT Response:</p>";
            echo "<p>$generatedText</p>";
        } else {
            die('Error: No response generated.');
        }

        curl_close($ch);
    } else {
        echo "No Google Sheets ID found in the URL.\n";
    }
} else {
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
echo $questions;
echo "<hr>";
echo $prompt;
?>
