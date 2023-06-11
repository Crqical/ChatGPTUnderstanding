<?php session_start();

require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setAuthConfig('client_secrets.json');  // Path to your client_secrets.json file
$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
$client->addScope(Google_Service_Docs::DOCUMENTS_READONLY);
$client->addScope(Google_Service_Sheets::SPREADSHEETS_READONLY);
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php');  // Path to your OAuth 2.0 callback file

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
    $driveService = new Google_Service_Drive($client);

    // Query to get Google Documents owned by the user
    $optParams = array(
    'q' => "'me' in owners and mimeType='application/vnd.google-apps.document'",
      'fields' => 'files(id, name)',
    );

    $results = $driveService->files->listFiles($optParams);




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
						<a href="index.html" class="logo"><strong>GradeFlow</strong> <span>Test</span></a>
						<nav>
							
						</nav>
					</header>

			

				<!-- Banner -->
					<section id="banner" class="major">
						<div class="inner">
							<header class="major">
								<h1>Your Name is <?php 
   $userInfo = $driveService->about->get(['fields' => 'user']);
        $googleUsername = $userInfo->getUser()->getDisplayName();

        echo " " . $googleUsername . "<br><br>";
  ?></h1>
							</header>
							<div class="content">
								<p>PHP Function that uses Google Drive/Docs to look through Google Document ID. Determine if cheating occurs using a multi step process. Created by Eibil </p>
								<ul class="actions">
						
								</ul>
							</div>
						</div>
					</section>

				<!-- Main -->
					<div id="main">

			

						<!-- Two -->
							<section id="two">
								<div class="inner">
									<header class="major">
										<h2>Documents</h2>
									</header>
									<p><?php









    if (count($results->getFiles()) == 0) {
        print "No Google Documents found.\n";
    } else {
       

     
        foreach ($results->getFiles() as $file) {
            printf("Document: %s (%s)<br>", $file->getName(), $file->getId());

echo "<a href='keepers.php?url=" . $file->getId() . "' class='button next'>Analyze</a>";

            // $revisions = $driveService->revisions->listRevisions($file->getId());
            // foreach ($revisions->getRevisions() as $revision) {
            //     $modTime = $revision->getModifiedTime();
            //     echo "Revision ID: " . $revision->getId() . ", Modified Time: " . $modTime . "<br>";
            // }

        

            echo "<hr>"; // Add a horizontal rule
        }
    }
} else {
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';  // Path to your OAuth 2.0 callback file
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}


?>

<h1>Google Sheets</h1>
<?php 
// Query to get Google Sheets owned by the user
    $optParams = array(
        'q' => "'me' in owners and mimeType='application/vnd.google-apps.spreadsheet'",
        'fields' => 'files(id, name)',
    );

    $results = $driveService->files->listFiles($optParams);

    if (count($results->getFiles()) == 0) {
        print "No Google Sheets found.\n";
    } else {
        foreach ($results->getFiles() as $file) {
            printf("Sheet: %s (%s)<br>", $file->getName(), $file->getId());
echo "<a href='sheetss.php?url=" . $file->getId() . "&name=" . urlencode($file->getName()) . "' class='button next'>Analyze</a>";
            echo "<hr>";
        }
    } // <- This closing brace was missing

?>



                    
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
											<label for="name">Document ID/URL:</label>
											<input type="text" name="name" id="name" />
										</div><br>
									
									<ul class="actions">
										<li><input type="submit" value="Inspect" class="primary" /></li>
									</ul>
								</form>
							

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