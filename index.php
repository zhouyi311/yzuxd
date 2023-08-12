<?php
// print error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// error_reporting(E_ALL & ~E_NOTICE);

// include site info
require_once __DIR__ . '/src/php/class_info_site.php';
$site_info = SiteInfo::loadInfo();

// Check if $_GET['project'] is set
$project = isset($_GET['page']) ? $_GET['page'] : null;

// set error handeler
// set_error_handler(function ($errno, $errstr, $errfile, $errline) {
//     if ($errno === E_ERROR) { // If the error is severe, throw an ErrorException
//         throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
//     } // For other types of errors, just return false to use PHP's default error handler
//     return false;
// });

// load home or project page based on id
try {
    if ($project) {
        if (file_exists(__DIR__ . '/src/php/page_project.php')) {
            include __DIR__ . '/src/php/page_project.php';
        } else {
            throw new Exception('The project page error.');
        }
    } else {
        if (file_exists(__DIR__ . '/src/php/page_home.php')) {
            include __DIR__ . '/src/php/page_home.php';
        } else {
            throw new Exception('The home page error.');
        }
    }
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    $_POST['error_type'] = '500';
    $_POST['error_title'] = 'Oops!';
    $_POST['error_message'] = $e->getMessage();
    try {
        include __DIR__ . '/src/php/page_404.php';
    } catch (ErrorException $e) {
        echo "<h1>Error</h1>";
        echo "<h2>" . $_POST['error_title'] . "</h2>";
        echo "<p>" . $_POST['error_message'] . "</p>";
        echo "<a href='/'>Go to homepage</a>";
        exit;
    }
}

?>