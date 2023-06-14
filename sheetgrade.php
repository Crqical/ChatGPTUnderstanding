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

        $range = 'Sheet1';

        $responseWithFormulas = $sheetsService->spreadsheets_values->get($sheetId, $range, ['valueRenderOption' => 'FORMULA']);
        $responseWithValues = $sheetsService->spreadsheets_values->get($sheetId, $range, ['valueRenderOption' => 'UNFORMATTED_VALUE']);

        $valuesWithFormulas = $responseWithFormulas->getValues();
        $valuesWithValues = $responseWithValues->getValues();

        if (empty($valuesWithFormulas)) {
            die("Error: No data found.\n");
        }

        $prompt = $_POST['prompt'] ?? '';  // Use null coalescing operator for cleaner syntax
        if (!empty($prompt)) {
            echo "<h2>Prompt:</h2>";
            echo "<p>" . htmlspecialchars($prompt) . "</p>";
        }

        $questions = '';
        $questionMap = [];
        $questionNumber = 1;
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'question') !== false) {
                $questions .= '(' . $value . '), ';
                $compare = $_POST["compare{$questionNumber}"] ?? null;  // Use null coalescing operator for cleaner syntax
                $compareValue = $_POST["compareValue{$questionNumber}"] ?? null;  // Use null coalescing operator for cleaner syntax
                $questionMap[$questionNumber] = [
                    'question' => $value,
                    'compare' => $compare,
                    'compareValue' => $compareValue,
                ];
                $questionNumber++;
            }
        }
        $questions = rtrim($questions, ', ');
        if (!empty($questions)) {
            echo "<h2>Questions:</h2>";
            echo "<p>" . htmlspecialchars($questions) . "</p>";
        }

        $sheetData = '';
        foreach ($valuesWithFormulas as $rowIndex => $row) {
            foreach ($row as $cellIndex => $cell) {
                $sheetData .= "Row " . ($rowIndex + 1) . " Cell " . ($cellIndex + 1) . "\n";
                $sheetData .= "Formula: " . $cell . "\n";
                $sheetData .= "Result: " . $valuesWithValues[$rowIndex][$cellIndex] . "\n\n";
            }
        }
        $prompt .= ' ' . $questions . ' ' . $sheetData;

        if (!empty($sheetData)) {
            echo "<h2>Google Sheets Data:</h2>";
            echo "<pre>" . htmlspecialchars($sheetData) . "</pre>";
        }

       $apiKey = "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";
$model = "gpt-3.5-turbo-0613";
$temperature = 0.7;
$maxTokens = 256;
$topP = 1;
$frequencyPenalty = 0;
$presencePenalty = 0;

$messages = array(
    array("role" => "user", "content" => $prompt . ' ' . $questions . ' ' . $sheetData)
);


$data = array(
    'model' => $model,
    'messages' => $messages,
    'temperature' => $temperature,
    'max_tokens' => $maxTokens,
    'top_p' => $topP,
    'frequency_penalty' => $frequencyPenalty,
    'presence_penalty' => $presencePenalty
);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");
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

if (isset($jsonResponse['choices']) && count($jsonResponse['choices']) > 0 && isset($jsonResponse['choices'][0]['message']['content'])) {
    $generatedText = $jsonResponse['choices'][0]['message']['content'];

    echo "<p>Chat GPT Response:</p>";
    echo "<p>$generatedText</p>";
}

            $scoreData = array();
            preg_match_all('/"questionID(\d+)": \{"score": (\d+),/', $jsonResponse['choices'][0]['text'], $matches);

            foreach ($matches[1] as $index => $questionNumber) {
                $score = (int) $matches[2][$index];
                $scoreData[$questionNumber] = $score;
            }

            $output = array();
            foreach ($questionMap as $questionNumber => $questionData) {
                if (isset($scoreData[$questionNumber]) && isset($questionData['compare']) && isset($questionData['compareValue'])) {
                    $compare = $questionData['compare'];
                    $compareValue = (int) $questionData['compareValue'];
                    $score = $scoreData[$questionNumber];

                    $output["questionID{$questionNumber}"] = array(
                        'promptScore' => $score,
                        'jsonScore' => $jsonResponse['choices'][0]['text']["q{$questionNumber}"]["score"],
                    );
                }
            }

            echo "<h2>Score Data:</h2>";
            echo "<pre>" . htmlspecialchars(json_encode($output, JSON_PRETTY_PRINT)) . "</pre>";

            // Save Chat GPT response as a JSON file
            $jsonFilePath = __DIR__ . '/chat_gpt_response.json';
            $jsonResponseTrimmed = preg_replace('/\s+/', ' ', $generatedText);
            $jsonContent = substr($jsonResponseTrimmed, strpos($jsonResponseTrimmed, '{'));
            file_put_contents($jsonFilePath, $jsonContent);
            echo "<p>Chat GPT response has been saved as a JSON file: <code>$jsonFilePath</code></p>";
        } else {
            die('Error: No response generated.');
        }

        curl_close($ch);


   $score = $valuesWithValues[0][0]; // assuming the score is at this position in the array

        $operator1 = $_POST['operator1'] ?? null;  // Use null coalescing operator for cleaner syntax
        $comparevalue1 = $_POST['comparevalue1'] ?? null;  // Use null coalescing operator for cleaner syntax

        $output = array(
            'score' => $score,
            'operator1' => $operator1
        );

        echo "<hr>";
        echo json_encode($output);
        echo "<hr>";

        // Load the JSON data from a file
        $jsonFilePath = __DIR__ . '/chat_gpt_response.json';
        $jsonString = file_get_contents($jsonFilePath);
        $jsonData = json_decode($jsonString, true);

        // Iterate over the parsed data and print the scores
        foreach ($jsonData as $question) {
            if (isset($question['score'])) {
                echo "Score: " . $question['score'] . "<br>";
            }
        }

echo "<hr>";




$questionId = 1;
while (true) {
    $operatorKey = "operator{$questionId}";
    $compareValueKey = "compareValue{$questionId}";

    if (isset($_POST[$operatorKey]) && isset($_POST[$compareValueKey])) {
        $operator = $_POST[$operatorKey];
        $compareValue = intval($_POST[$compareValueKey]);

        foreach ($jsonData as $question) {
            if (isset($question['score'])) {
                $equation = "{$question['score']} $operator $compareValue";

                if (eval("return $equation;")) {
                    echo "PASS because {$question['score']} is {$operator} than {$compareValue}<br>";
                } else {
                    echo "FAIL because {$question['score']} is not {$operator} than {$compareValue}<br>";
                }
            }
        }

    } else {
        // if we can't find both operatorX and compareValueX, then we break the loop
        break;
    }

    $questionId++;
}
}

var_dump($_POST);
?>
