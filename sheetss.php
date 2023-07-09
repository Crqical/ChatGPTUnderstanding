<?php
require_once 'vendor/autoload.php';

session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');







if(isset($_GET['UID'])) {
    // Get the UID
    $uid = $_GET['UID'];

}
  




 if ($uid == 1) {
$content = '';
   $text = '';




$client = new Google_Client();
$client->setAuthConfig('client_secrets.json');  // Path to your client_secrets.json file
$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
$client->addScope(Google_Service_Docs::DOCUMENTS_READONLY);
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php');  // Path to your OAuth 2.0 callback file

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
    $docsService = new Google_Service_Docs($client);

    // Specific Document ID, taken from the URL if present
    $documentId = isset($_GET['url']) ? $_GET['url'] : null;
    if(!$documentId) {
        die("No document ID specified.");
    }

    // Get the document content
    $document = $docsService->documents->get($documentId);
    $content = $document->getBody()->getContent();

    // Process the document content
    $text = '';
    foreach ($content as $element) {
        if ($element->getParagraph()) {
            foreach ($element->getParagraph()->getElements() as $element) {
                if ($element->getTextRun()) {
                    $text .= $element->getTextRun()->getContent();
                }
            }
        }
    }

    // // Display document content
    // echo "Content of document:<br>";
    // echo $text;

} else {
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';  // Path to your OAuth 2.0 callback file
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
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

// Set the default prompt value
$defaultPrompt = "  ";

// Check if the form is submitted to update the prompt
if (isset($_POST['update_prompt'])) {
    $prompt = $_POST['prompt'];
    $_SESSION['prompt'] = $prompt;
} else {
    $_SESSION['prompt'] = $defaultPrompt; // Set the default prompt value if not updated
}

// Check that both the URL and docName are set
if (isset($_GET['url']) && isset($_GET['name'])) {
    $docId = $_GET['url'];
    $docName = $_GET['name'];
}
?>
<!DOCTYPE HTML>
<!--
    Forty by HTML5 UP
    html5up.net | @ajlkn
    Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
<head>
    <title>GradeFlow Test</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
</head>
<body class="is-preload">

    <!-- Wrapper -->
        <div id="wrapper">

            <!-- Header -->
                <header id="header" class="alt">
                    <a href="index.php" class="logo"><strong>GradeFlow</strong> <span>Test</span></a>
                    <nav>
                        
                    </nav>
                </header>



            <!-- Main -->
                <div id="main">

        

                    <!-- Two -->
                        <section id="two">
                            <div class="inner">
                                <header class="major">
                                    
                                </header>
                         <!DOCTYPE HTML>
<html>
<head>
    <title>GradeFlow Test</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body class="is-preload">

    <div id="wrapper">
        <header id="header" class="alt">
            <a href="index.php" class="logo"><strong>GradeFlow</strong> <span>Test</span></a>
            <nav></nav>
        </header>

        <div id="main">
            <section id="two">
                <div class="inner">
                    <header class="major">
                        <h2>Documents</h2>
                    </header>
                    <h1>Google Documents</h1>

                    <p>Documents URL:</p>

                    <input type="text" id="docId" name="docId" value="<?= isset($_GET['url']) ? $_GET['url'] : '' ?>"><br>
                   
                  


                    <hr>

                    <form action="sheetgrade.php?url=<?php echo $docId; ?>" method="post">
                        <input type="hidden" name="docId" value="<?= isset($_GET['id']) ? $_GET['id'] : '' ?>">
                        <input type="hidden" name="docName" value="<?= isset($_SESSION['docName']) ? $_SESSION['docName'] : '' ?>">
                     <p>Documents Data:</p>
<textarea id="data2" name="data2" rows="6" cols="50"><?= $text ?></textarea>

   
                        <p>Prompt:</p>
                        <textarea id="prompt" name="prompt" rows="6" cols="50"><?= isset($_SESSION['prompt']) ? $_SESSION['prompt'] : '' ?></textarea><br>
                        <p>Questions:</p>
                        <div id="questions">
                            <input type="text" name="question1" placeholder="Enter question 1" required><br>
                            <select name="operator1">
                                <option value="<">Less Than</option>
                                <option value=">">Greator Than </option>
                                <option value="==">Equal to  </option>
                            </select>
                           <select name="compareValue1" placeholder="Comparison Value" width="100px">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                              <option value="4">4</option>
                              <option value="5">5</option>
                              <option value="6">6</option>
                              <option value="7">7</option>
                              <option value="8">8</option>
                              <option value="9">9</option>
                              <option value="10">10</option>
                            </select>
                           
                        </div>
                        <button type="button" id="add">Add More Questions</button>
                        <input type="submit" value="Grade Document">
                    </form>

                 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function() {
  var i = 1;
  $('#add').click(function() {
    i++;
    $('#questions').append('<br> <input type="text" name="question' + i + '" placeholder="Enter question ' + i + '" required><select name="compare' + i + '"><option value="<">Less than</option><option value=">">Greater than</option><option value="=">Equal to</option></select><select name="compareValue' + i + '" placeholder="Comparison Value" style="width: 100px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select><br>');
  });
});
</script>


<div id="questions"></div>


                </div>
            </section>
        </div><div

        <section id="contact">
    <div class="inner">
        <div class="field half">
            <label for="name">Entire URL:</label>
            <input type="text" name="url" id="name" value="<?= isset($_GET['url']) ? $_GET['url'] : '' ?>" />
        </div></div>
        
        <ul class="actions">
            <li>
                <form action="https://docs.google.com/spreadsheets/d/<?= isset($_GET['url']) ? $_GET['url'] : '' ?>/edit" method="GET" target="_blank">
                    <input type="submit" value="Go to SHEETS" class="primary" />
                </form>
            </li>
        </ul>
    </div>
</section>


                <footer id="footer">
                    <div class="inner">
                        <ul class="icons">
                            <li><a href="#" class="icon brands alt fa-twitter"><span class="label">Twitter</span></a></li>
                            <li><a href="#" class="icon brands alt fa-facebook-f"><span class="label">Facebook</span></a></li>
                            <li><a href="#" class="icon brands alt fa-instagram"><span class="label">Instagram</span></a></li>
                            <li><a href="#" class="icon brands alt fa-github"><span class="label">GitHub</span></a></li>
                            <li><a href="#" class="icon brands alt fa-linkedin-in"><span class="label">LinkedIn</span></a></li>
                        </ul>
                        <ul class="copyright">
                            <li>&copy; Untitled</li><li>Design: <a href="https://html5up.net">HTML5 UP</a></li>
                        </ul>
                    </div>
                </footer>

            </div>
        </section>

    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery.scrolly.min.js"></script>
    <script src="assets/js/jquery.scrollex.min.js"></script>
    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>

</body>
</html>


   
 <?php
 }elseif($uid == 2) {

 }

 

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

     if($uid == 2) {
    
    $responseWithFormulas = $sheetsService->spreadsheets_values->get($sheetId, $range, ['valueRenderOption' => 'FORMULA']);
    $responseWithValues = $sheetsService->spreadsheets_values->get($sheetId, $range, ['valueRenderOption' => 'UNFORMATTED_VALUE']);

    // ... rest of the code ...
}
}
}

 if($uid == 2) {
        $valuesWithFormulas = $responseWithFormulas->getValues();
        $valuesWithValues = $responseWithValues->getValues();
 }
        if (empty($valuesWithFormulas)) {
            die("\n");
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
// Set the default prompt value
$defaultPrompt = "
  ";

// Check if the form is submitted to update the prompt
if (isset($_POST['update_prompt'])) {
    $prompt = $_POST['prompt'];
    $_SESSION['prompt'] = $prompt;
} else {
    $_SESSION['prompt'] = $defaultPrompt; // Set the default prompt value if not updated
}

// Check that both the URL and docName are set
if (isset($_GET['url']) && isset($_GET['name'])) {
    $docId = $_GET['url'];
    $docName = $_GET['name'];
}
?>

<!DOCTYPE HTML>
<!--
    Forty by HTML5 UP
    html5up.net | @ajlkn
    Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
<head>
    <title>GradeFlow Test</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
</head>
<body class="is-preload">

    <!-- Wrapper -->
        <div id="wrapper">

            <!-- Header -->
                <header id="header" class="alt">
                    <a href="index.php" class="logo"><strong>GradeFlow</strong> <span>Test</span></a>
                    <nav>
                        
                    </nav>
                </header>



            <!-- Main -->
                <div id="main">

        

                    <!-- Two -->
                        <section id="two">
                            <div class="inner">
                                <header class="major">
                                   
                                </header>
                         <!DOCTYPE HTML>
<html>
<head>
    <title>GradeFlow Test</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body class="is-preload">

    <div id="wrapper">
        <header id="header" class="alt">
            <a href="index.php" class="logo"><strong>GradeFlow</strong> <span>Test</span></a>
            <nav></nav>
        </header>

        <div id="main">
            <section id="two">
                <div class="inner">
                    <header class="major">
                        <h2>Sheets</h2>
                    </header>
                    <h1>Google Sheets</h1>

                    <p>Sheets URL:</p>

                    <input type="text" id="docId" name="docId" value="<?= isset($_GET['url']) ? $_GET['url'] : '' ?>"><br>
                 

                  


                   

                    <form action="sheetgrade.php?url=<?php echo $docId; ?>" method="post">
                        <input type="hidden" name="docId" value="<?= isset($_GET['id']) ? $_GET['id'] : '' ?>">
                        <input type="hidden" name="docName" value="<?= isset($_SESSION['docName']) ? $_SESSION['docName'] : '' ?>">

 <hr>

  <p>Sheets Data:</p>
<textarea id="data" name="data" rows="6" cols="50"><?= $sheetData; ?></textarea>

                      
                        <p>Prompt:</p>
                        <textarea id="prompt" name="prompt" rows="6" cols="50"><?= isset($_SESSION['prompt']) ? $_SESSION['prompt'] : '' ?></textarea><br>
                        <p>Questions:</p>
                        <div id="questions">
                            <input type="text" name="question1" placeholder="Enter question 1" required><br>
                            <select name="operator1">
                                <option value="<">Less Than</option>
                                <option value=">">Greator Than </option>
                                <option value="==">Equal to  </option>
                            </select>
                           <select name="compareValue1" placeholder="Comparison Value" width="100px">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                              <option value="4">4</option>
                              <option value="5">5</option>
                              <option value="6">6</option>
                              <option value="7">7</option>
                              <option value="8">8</option>
                              <option value="9">9</option>
                              <option value="10">10</option>
                            </select>
                           
                        </div>
                        <button type="button" id="add">Add More Questions</button>
                        <input type="submit" value="Grade Document">
                    </form>

                 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function() {
  var i = 1;
  $('#add').click(function() {
    i++;
    $('#questions').append('<br> <input type="text" name="question' + i + '" placeholder="Enter question ' + i + '" required><select name="compare' + i + '"><option value="<">Less than</option><option value=">">Greater than</option><option value="=">Equal to</option></select><select name="compareValue' + i + '" placeholder="Comparison Value" style="width: 100px;"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select><br>');
  });
});
</script>


<div id="questions"></div>


                </div>
            </section>
        </div><div

        <section id="contact">
    <div class="inner">
        <div class="field half">
            <label for="name">Entire URL:</label>
            <input type="text" name="url" id="name" value="<?= isset($_GET['url']) ? $_GET['url'] : '' ?>" />
        </div></div>
        
        <ul class="actions">
            <li>
                <form action="https://docs.google.com/spreadsheets/d/<?= isset($_GET['url']) ? $_GET['url'] : '' ?>/edit" method="GET" target="_blank">
                    <input type="submit" value="Go to SHEETS" class="primary" />
                </form>
            </li>
        </ul>
    </div>
</section>


                <footer id="footer">
                    <div class="inner">
                        <ul class="icons">
                            <li><a href="#" class="icon brands alt fa-twitter"><span class="label">Twitter</span></a></li>
                            <li><a href="#" class="icon brands alt fa-facebook-f"><span class="label">Facebook</span></a></li>
                            <li><a href="#" class="icon brands alt fa-instagram"><span class="label">Instagram</span></a></li>
                            <li><a href="#" class="icon brands alt fa-github"><span class="label">GitHub</span></a></li>
                            <li><a href="#" class="icon brands alt fa-linkedin-in"><span class="label">LinkedIn</span></a></li>
                        </ul>
                        <ul class="copyright">
                            <li>&copy; Untitled</li><li>Design: <a href="https://html5up.net">HTML5 UP</a></li>
                        </ul>
                    </div>
                </footer>

            </div>
        </section>

    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery.scrolly.min.js"></script>
    <script src="assets/js/jquery.scrollex.min.js"></script>
    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>

</body>
</html>

   

}