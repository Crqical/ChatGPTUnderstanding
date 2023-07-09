<?php
require_once 'vendor/autoload.php';



// ### ###    ####   ### ##     ####   ####     ##  ##   
//  ##  ##     ##     ##  ##     ##     ##      ##  ##   
//  ##         ##     ##  ##     ##     ##      ##  ##   
//  ## ##      ##     ## ##      ##     ##       ## ##   
//  ##         ##     ##  ##     ##     ##        ##     
//  ##  ##     ##     ##  ##     ##     ##  ##    ##     
// ### ###    ####   ### ##     ####   ### ###    ##     
                                                      







        $prompt = $_POST['prompt'] ?? '';  // Use null coalescing operator for cleaner syntax
$data = $_POST['data'] ?? '';  // Use null coalescing operator for cleaner syntax
     

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
    

     
        $prompt .= ' ' . $questions . ' ' . $data;



      

$apiKey = "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";
$model = "gpt-3.5-turbo-16k";
$temperature = 0.7;
$maxTokens = 3600; // Modify this as needed
$topP = 1;
$frequencyPenalty = 0;
$presencePenalty = 0;

$prompt = $prompt . ' ' . $questions . ' ' . $data; // Your full prompt goes here
$promptTokens = explode(" ", $prompt); // Tokenize prompt into words

$finalGeneratedText = '';

while(!empty($promptTokens)) {
  $chunkTokens = array_splice($promptTokens, 0, $maxTokens); // take the first maxTokens tokens
  $chunkPrompt = implode(" ", $chunkTokens); // make the tokens into a string

  $messages = array(
      array("role" => "user", "content" => $chunkPrompt)
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

  // Call API with chunked prompt
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
  curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $apiKey));
  $response = curl_exec($ch);

  // Error handling, parsing response, and appending response content to finalGeneratedText
  if (curl_errno($ch)) {
      die('Error: ' . curl_error($ch));
  }

  $jsonResponse = json_decode($response, true);
  if (isset($jsonResponse['error'])) {
      die('API Error: ' . $jsonResponse['error']['message']);
  }

  if (isset($jsonResponse['choices']) && count($jsonResponse['choices']) > 0 && isset($jsonResponse['choices'][0]['message']['content'])) {
      $generatedText = $jsonResponse['choices'][0]['message']['content'];
      $finalGeneratedText .= $generatedText . " ";
  }

  curl_close($ch);
}

if (isset($jsonResponse['choices']) && count($jsonResponse['choices']) > 0 && isset($jsonResponse['choices'][0]['message']['content'])) {
    $generatedText = $jsonResponse['choices'][0]['message']['content'];

    echo "<p>Chat GPT Response:</p>";
    echo "<p>$generatedText</p>";
}


       echo "<hr>";
     

            // Save Chat GPT response as a JSON file
            $jsonFilePath = __DIR__ . '/chat_gpt_response.json';
            $jsonResponseTrimmed = preg_replace('/\s+/', ' ', $generatedText);
            $jsonContent = substr($jsonResponseTrimmed, strpos($jsonResponseTrimmed, '{'));
            file_put_contents($jsonFilePath, $jsonContent);
            echo "<p>Chat GPT response has been saved as a JSON file: <code>$jsonFilePath</code></p>";
     

        curl_close($ch);



    

        // Load the JSON data from a file
        $jsonFilePath = __DIR__ . '/chat_gpt_response.json';
        $jsonString = file_get_contents($jsonFilePath);
        $jsonData = json_decode($jsonString, true);

        // Iterate over the parsed data and print the scores
     

echo "<hr>";









?>

 <title>Hidden var_dump</title>
    <style>
        #dump-output {
            display: none; /* Hide the output initially */
        }
    </style>
</head>
<body>
  
    <form method="POST" action="#">
       
       
    </form>
    <button onclick="showDump()">Show var_dump output</button>
    <pre id="dump-output"><?php var_dump($_POST); ?></pre>

    <script>
        function showDump() {
            document.getElementById("dump-output").style.display = "block";
        }
    </script>
</body>




