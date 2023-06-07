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

    // Create an array to hold the file names, content and revision times
    $fileData = array();

    // Add the current document to the array
    $currentFile = $docName . ' CURRENT.docx';
    if (file_exists($currentFile)) {
        $phpWord = IOFactory::load($currentFile);
        $content = '';
        foreach ($phpWord->getSections() as $section) {
            $elements = $section->getElements();
            foreach ($elements as $element) {
                if (get_class($element) === 'PhpOffice\PhpWord\Element\TextRun') {
                    foreach ($element->getElements() as $text) {
                        $content .= $text->getText();
                    }
                }
            }
        }

        $fileData[] = array(
            'name' => $currentFile,
            'content' => $content,
            'revisionTime' => 'CURRENT'
        );
    }

    // Add the matching file names, their content and revision times to the array
    if (count($matchingFiles) > 0) {
        foreach ($matchingFiles as $file) {
            $phpWord = IOFactory::load($file);
            $content = '';
            foreach ($phpWord->getSections() as $section) {
                $elements = $section->getElements();
                foreach ($elements as $element) {
                    if (get_class($element) === 'PhpOffice\PhpWord\Element\TextRun') {
                        foreach ($element->getElements() as $text) {
                            $content .= $text->getText();
                        }
                    }
                }
            }

            // Extract the revision number from the file name
            $revisionNumber = (int) substr($file, strlen($docName) + 1, -5);

            $fileData[] = array(
                'name' => $file,
                'content' => $content,
                'revisionTime' => $_SESSION['revisionTimes'][$revisionNumber] ?? ''
            );
        }
    }

    // Convert the array to JSON
    $json = json_encode($fileData, JSON_PRETTY_PRINT);

    // Write the JSON to a file
    $file = $_SESSION['docName'] . '.json';
    file_put_contents($file, $json);

    // Redirect to checker.php
    header('Location: checker.php?id='.$docId);
    exit();  // Add this line
}
?>
