<?php
session_start();

// Set the default prompt value
$defaultPrompt = "I want you to ACT like a High School English Teacher. Your purpose is to look at the questions I have provided you and score it through 1-10. 1 meaning the questions were not answered, and 10 meaning the student correctly answered the question. Please make sure it is accurate, and to remove points if any information is incorrect, this can be from dates/times/locations/etc. I would like you to be very strict, and base your grading on the explanation and detail provided. As well as removing points if there is anything that doesnt belong there. Please be as strict as possible, make sure it answers the question and has ALL OF THE INFORMATION NEEDED TO GET A GOOD SCORE, ANY GIBERISH, as well AS REMOVING POINTS IF THERE IS ANY ERRORS IN THE text. I want you to read the paragraph/essay and come back with a score of each question. In your response, I want it to be in this format. Question 1 / Score: .";

// Check if the form is submitted to update the prompt
if (isset($_POST['update_prompt'])) {
    $prompt = $_POST['prompt'];
    $_SESSION['prompt'] = $prompt;
} else {
    $_SESSION['prompt'] = $defaultPrompt; // Set the default prompt value if not updated
}

// Check that both the URL and docName are set
if (isset($_GET['id']) && isset($_SESSION['docName'])) {
    $docId = $_GET['id'];
    $docName = $_SESSION['docName'];
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
                                    <h2>Documents</h2>
                                </header>
                                <p><!DOCTYPE html>
<html>
<head>
    <title>Document Evaluator</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
       $(document).ready(function(){
    var i = 1;
    $('#add').click(function(){
        i++;
        $('#questions').append('<input type="text" name="questions[]" placeholder="Enter question '+i+'" required><br>');
    });
});

    </script>
</head>
<body>
  <h1>Google Documents</h1>
  <p>Document Name:</p>

    <input type="text" id="COOLNAME" name="COOLNAME" value="<?= isset($_SESSION['docName']) ? $_SESSION['docName'] : '' ?>"><br>
    <p>Document URL:</p>

    <input type="text" id="COOLNAME" name="COOLNAME" value="<?= isset($_GET['id']) ? $_GET['id'] : '' ?>"><br>


 <p>Each Question Points:</p>

    <input type="text" id="COOLNAME" name="COOLNAME" value="10">
  
  <hr>

  

    <form action="grade.php" method="post">
        <input type="hidden" name="docId" value="<?= isset($_GET['id']) ? $_GET['id'] : '' ?>">
        <input type="hidden" name="docName" value="<?= isset($_SESSION['docName']) ? $_SESSION['docName'] : '' ?>">
        <p>Prompt:</p>
        <textarea id="prompt" name="prompt" rows="6" cols="50"><?= isset($_SESSION['prompt']) ? $_SESSION['prompt'] : '' ?></textarea><br>
        <p>Questions:</p>
        <div id="questions">
            <input type="text" name="question1" placeholder="Enter question 1" required><br>
        </div>
        <button type="button" id="add">Add More Questions</button>
        <input type="submit" value="Grade Document">
    </form>
</body>
</html>

</p>
                                <ul class="actions">
                                </ul>
                            </div>
                        </section>

                </div>

            <!-- Contact -->
                <section id="contact">
                    <div class="inner">
                        <section>
                            
                                <div class="field half">
    <label for="name">Entire URL:</label>
    <input type="text" name="name" id="name" value="https://docs.google.com/document/d/<?= isset($_GET['id']) ? $_GET['id'] : '' ?>/edit" />
</div><br>

<ul class="actions">
    <li>
        <form action="https://docs.google.com/document/d/<?= isset($_GET['id']) ? $_GET['id'] : '' ?>/edit" method="GET" target="_blank">
            <input type="submit" value="Go to DOC" class="primary" />
        </form>
    </li>
</ul>

                        

            <!-- Footer -->
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

    <!-- Scripts -->
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/jquery.scrolly.min.js"></script>
        <script src="assets/js/jquery.scrollex.min.js"></script>
        <script src="assets/js/browser.min.js"></script>
        <script src="assets/js/breakpoints.min.js"></script>
        <script src="assets/js/util.js"></script>
        <script src="assets/js/main.js"></script>

</body>
</html>
