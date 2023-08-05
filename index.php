<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function getBaseUrl()
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
    $domainName = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    $folderPath = isset($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : '';

    // If the script is in the site root, do not append '/'
    if ($folderPath === '/') {
        return $protocol . $domainName;
    }
    return $protocol . $domainName . $folderPath . '/';
}
$baseUrl = getBaseUrl();
// echo $baseUrl;

require_once __DIR__ . '/src/php/class_site_info.php';
$site_info = SiteInfo::loadInfo();

$project = isset($_GET['project']) ? $_GET['project'] : null; // Check if $_GET['project'] is set

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    // If the error is severe, throw an ErrorException
    if ($errno === E_ERROR) {
        throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
    }
    // For other types of errors, just return false to use PHP's default error handler
    return false;
});

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