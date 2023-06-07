<?php
// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Make sure docName is set in the session
if (isset($_SESSION['docName'])) {
    $docName = $_SESSION['docName'];
    // Now you can use $docName in this script
} else {
    echo "No document name found in session.";
    exit();
}

// Parse JSON file
$data = json_decode(file_get_contents($docName . ".json"), true);

// Set a threshold for the ratio of content length between two revisions
// If the content length ratio is greater than the threshold, it may indicate copy-pasting
$threshold = 2; // Adjust this value based on your specific needs

// Compare the content length of each revision with the previous one
$cheatingInstances = 0;
$totalInstances = 0;

for ($i = 1; $i < count($data); $i++) {
    $prevContentLength = strlen($data[$i - 1]['content']);
    $currentContentLength = strlen($data[$i]['content']);

    // Avoid division by zero error
    if ($prevContentLength != 0) {
        $ratio = $currentContentLength / $prevContentLength;
        if ($ratio > $threshold) {
            $prevTime = new DateTime($data[$i - 1]['revisionTime']);
            $currentTime = new DateTime($data[$i]['revisionTime']);
            $interval = $prevTime->diff($currentTime);
            $hours = $interval->h + ($interval->days * 24);

            // Check if the increase in content occurred in less than 2 hours
            if ($hours < 2) {
                $cheatingInstances++;
            }
        }
    }

    $totalInstances++;
}

// Calculate the percentage chance of cheating
$percentageChance = ($cheatingInstances / $totalInstances) * 100;

// if ($cheatingInstances > 0) {
//     echo "Possible cheating detected between revisions.\n";
//     echo "Percentage chance of cheating: " . $percentageChance . "%\n";
// } else {
//     echo "No cheating found.\n";
//     echo "Percentage chance of cheating: " . $percentageChance . "%\n";
// }






// Get the content of the current document
$currentDocumentContent = "";
foreach ($data as $doc) {
    if ($doc['revisionTime'] === 'CURRENT') {
        $currentDocumentContent = $doc['content'];
        break;
    }
}

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://chatgpt-detector.p.rapidapi.com/gpt/detect",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode([
        'text' => $currentDocumentContent
    ]),
    CURLOPT_HTTPHEADER => [
        "X-RapidAPI-Host: chatgpt-detector.p.rapidapi.com",
        "X-RapidAPI-Key: 197dd9710fmsh2372baca08699bep13250fjsn94a3c04694b8",
        "content-type: application/json"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    // Decode the response
    $responseData = json_decode($response, true);
    $gptInfo = $responseData['data']['output']['batches'][0]['is_gpt'];
    $probabilityFake = $responseData['data']['output']['probability_fake'];

    // Display the required information
    echo "Is_GPT: " . ($gptInfo ? "True" : "False") . "\n";
    echo "Probability Fake: " . $probabilityFake . "\n";
}


$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://plagiarism-checker-and-auto-citation-generator-multi-lingual.p.rapidapi.com/plagiarism",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode([
        'text' => $currentDocumentContent,
        'language' => 'en',
        'includeCitations' => false,  // change null to false
        'scrapeSources' => false  // change null to false
    ]),
    CURLOPT_HTTPHEADER => [
        "X-RapidAPI-Host: plagiarism-checker-and-auto-citation-generator-multi-lingual.p.rapidapi.com",
        "X-RapidAPI-Key: 197dd9710fmsh2372baca08699bep13250fjsn94a3c04694b8",
        "content-type: application/json"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

// if ($err) {
//     echo "cURL Error #:" . $err;
// } else {
//     echo $response;
// }
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/livebloggerofficial/Custom-List@main/style.css" />
  </head>
  <body>
    <div class="list-container">
      <ol>
        <li>
         Eibil's Script Detecting Cheating <br> <?php 

if ($cheatingInstances > 0) {
    echo "Possible cheating detected between revisions.\n";
    echo "Percentage chance of cheating: " . $percentageChance . "%\n";
} else {
    echo "No cheating found.\n";
  echo "<br>";
    echo "Percentage chance of cheating: " . $percentageChance . "%\n";
}
?>
        </li>
        <li>
         Chat GPT Checker API's Detecting Cheating<br> <?php 

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    // Decode the response
    $responseData = json_decode($response, true);

    // Check if the keys exist in the array
    if (isset($responseData['data']['output']['batches'][0]['is_gpt'])) {
        $gptInfo = $responseData['data']['output']['batches'][0]['is_gpt'];
    } else {
        $gptInfo = null; // Or assign a default value
    }

    if (isset($responseData['data']['output']['probability_fake'])) {
        $probabilityFake = $responseData['data']['output']['probability_fake'];
    } else {
        $probabilityFake = null; // Or assign a default value
    }

    // Display the required information
    echo "Is_GPT: " . ($gptInfo ? "True" : "False") . "\n";
    echo "<br>";
    echo "Probability Fake: " . $probabilityFake . "\n";
}?>
        </li>
        <li>
         Plagirism Checker API Detecting Cheating<br> <?php 

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    echo $response;
}?>
        </li>
        <li>
     This PHP Script went through 2 Different API's and 2 different scripts. The 2 APIs used were Chat GPT/AI Checker, and the second was a plagirism API Checker. The two scripts created was a Script that checks the time of the revisions, the history of the revisions, and checks if copy and paste if found. The last script determines if the student copy and pasted, finishing the assigment way to fast through a method.
        </li>
      
      </ol>
    </div>
  </body>
</html>