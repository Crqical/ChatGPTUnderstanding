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

<!DOCTYPE html>
<html>
<head>
    <title>Document Evaluator</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function(){
            var i = 1;
            $('#add').click(function(){
                i++;
                $('#questions').append('<input type="text" name="question'+i+'" placeholder="Enter question '+i+'" required><br>');
            });
        });
    </script>
</head>
<body>
  <h1>Google Documents</h1>
  <p>Document Name:</p>

    <input type="text" id="fname" name="fname" value="<?= isset($_SESSION['docName']) ? $_SESSION['docName'] : '' ?>"><br>
    <p>Document URL:</p>

    <input type="text" id="docId" name="docId" value="<?= isset($_GET['id']) ? $_GET['id'] : '' ?>"><hr>

    <form action="grade.php" method="post">
        <input type="hidden" name="docId" value="<?= isset($_GET['id']) ? $_GET['id'] : '' ?>">
        <input type="hidden" name="docName" value="<?= isset($_SESSION['docName']) ? $_SESSION['docName'] : '' ?>">
        <p>Prompt:</p>
        <textarea id="prompt" name="prompt" rows="6" cols="50"><?= isset($_SESSION['prompt']) ? $_SESSION['prompt'] : '' ?></textarea>
        <p>Questions:</p>
        <div id="questions">
            <input type="text" name="question1" placeholder="Enter question 1" required><br>
        </div>
        <button type="button" id="add">Add More Questions</button>
        <input type="submit" value="Grade Document">
    </form>
</body>
</html>
