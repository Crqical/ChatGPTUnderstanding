<?php
require_once 'vendor/autoload.php';

session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

if (isset($_SESSION['docName'])) {
    $docName = $_SESSION['docName'];
} else {
    die("Error: docName not set in session data.");
}

$jsonFile = $docName . '.json';
$content = '';
if (file_exists($jsonFile)) {
    $jsonContent = file_get_contents($jsonFile);
    $jsonArray = json_decode($jsonContent, true);

    foreach ($jsonArray as $item) {
        if (strpos($item['name'], 'CURRENT') !== false) {
            $content = $item['content'];
            break;
        }
    }
} else {
    die("Error: JSON file not found.");
}

if (empty($content)) {
    die("Error: No content found in JSON.");
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


$apiKey = "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";
$model = "text-davinci-003";
$temperature = 0.7;
$maxTokens = 256;
$topP = 1;
$frequencyPenalty = 0;
$presencePenalty = 0;

$data = array(
    'model' => $model,
    'prompt' => $prompt . ' ' . $content,
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

    // Extract the scores from the generated text
$pattern = '/Question (\d+) \/ Score: (\d{1,2})/';
    preg_match_all($pattern, $generatedText, $matches, PREG_SET_ORDER);

    // Display the Chat GPT response
    echo "<p>Chat GPT Response:</p>";
    echo "<p>$generatedText</p>";

    // Display the questions and scores
    echo "<p></p>";
    foreach ($matches as $match) {
        echo "<hr>";
        $questionNumber = $match[1];
        $score = $match[2];
        $questionText = $questionMap[$questionNumber] ?? 'Unknown question';
        echo "Question $questionNumber ($questionText) / Score: $score <br>";
    }
} else {
    die('Error: No response generated.');
}

curl_close($ch);
?>
<hr>

<p>Prompt: <?php echo $prompt; ?></p>
<?php var_dump($_POST); ?>
