<?php
require_once 'vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;

session_start();

// Check that both the URL and docName are set
if (isset($_GET['id']) && isset($_SESSION['docName'])) {
    $docId = $_GET['id'];
    $docName = $_SESSION['docName'];

    // Retrieve all .docx files in the directory
    $docxFiles = glob('*.docx');

    // Find the files that match the $docName pattern
    $matchingFiles = preg_grep('/^' . preg_quote($docName, '/') . '\s*\d*\.docx$/', $docxFiles);

    // Add the current document content to the array
    $currentFile = $docName . ' CURRENT.docx';
    if (file_exists($currentFile)) {
        $phpWord = IOFactory::load($currentFile);
        $content = '';
        foreach ($phpWord->getSections() as $section) {
            $elements = $section->getElements();
            foreach ($elements as $element) {
                if (get_class($element) === 'PhpOffice\PhpWord\Element\TextRun') {
                    foreach ($element->getElements() as $text) {
                        if (method_exists($text, 'getText')) {
                            $content .= $text->getText();
                        }
                    }
                }
            }
        }

        // Pass the $content to the Chat GPT API
        $apiKey = "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";
        $model = "text-davinci-003";
        $prompt = 'I want you to ACT like a High School English Teacher. Your purpose is to look at the questions I have provided you and score it through 1-10. 1 meaning the questions were not answered, and 10 meaning the student correctly answered the question. Please make sure it is accurate, and to remove points if any information is incorrect, this can be from dates/times/locations/etc.I would like you to be very strict, and base your grading on the explanation and detail provided. As well as removing points if there is anything that doesnt belong there. Please be as strict as possible, make sure it answers the question and has ALL OF THE INFORMATION NEEDED TO GET A GOOD SCORE, ANY GIBERISH, as well AS REMOVING POINTS IF THERE IS ANY ERRORS IN THE text. I want you to read the paragraph/essay and come back with a score of each question. In your response, I want it to be in this format. Question 1 / Score: . The questions are (Where was Martin Luther King JR Born), (How did Martin Luther King Jr Die), (How did Martin Luther King JR impact the US).';
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

        curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/completions");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $apiKey));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        } else {
            $jsonResponse = json_decode($response, true);
            $generatedText = '';

            if (isset($jsonResponse['choices']) && count($jsonResponse['choices']) > 0 && isset($jsonResponse['choices'][0]['text'])) {
                $generatedText = $jsonResponse['choices'][0]['text'];

                // Extract the scores from the generated text
                $pattern = '/Question (\d+) \/ Score: (\d+)/';
                preg_match_all($pattern, $generatedText, $matches, PREG_SET_ORDER);

                // Display the Chat GPT response
                echo "<p>Chat GPT Response:</p>";
                echo "<p>$generatedText</p>";

                // Display the questions and scores
                echo "<p>Question Scores:</p>";
                foreach ($matches as $match) {
                    $question = $match[1];
                    $score = $match[2];
                    echo "Question $question / Score: $score <br>";
                }
            } else {
                $generatedText = 'No response generated.';
            }
        }

        curl_close($ch);
    }
}
?>
