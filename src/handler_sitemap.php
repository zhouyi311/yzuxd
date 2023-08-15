<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/class_info_site.php';
include_once __DIR__ . '/class_info_project.php';

$password = "000";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['password']) && $_POST['password'] === $password) {


        function calculatePriority($indexOrder)
        {
            if ($indexOrder < 0) {
                return 0.1; // Fixed low priority for negative numbers
            } else if ($indexOrder >= 0 && $indexOrder <= 9) {
                $priority = 0.9 - $indexOrder * 0.01;
                return $priority;
            } else {
                $decrement = 0.03; // The amount of decrement for each step
                $priority = 0.81 - (($indexOrder - 10) * $decrement);
                return max($priority, 0.5); // Ensures the returned priority doesn't go below 0.5
            }
        }

        // Load site information
        $siteInfo = SiteInfo::loadInfo();
        $baseURL = $siteInfo->rootUrl;

        $baseURL = $siteInfo->rootUrl;
        if (
            strpos($baseURL, 'localhost') !== false
            || preg_match('/^http:\/\/10\./', $baseURL)
            || preg_match('/^http:\/\/172\.(1[6-9]|2[0-9]|3[0-1])\./', $baseURL)
            || preg_match('/^http:\/\/192\.168\./', $baseURL)
        ) {
            $baseURL = $siteInfo->information['siteAddress'];
        }

        $archiveUrl = $baseURL . "?" . $siteInfo->pageKeys['archiveKey'];
        $projectUrl = $baseURL . "?" . $siteInfo->pageKeys['projectKey'] . "=";

        // Get URLs for static pages like index
        $urls = [];
        $lastModi = [];
        $images = [];
        $pagePriority = [];
        $staticUrls = [
            "$baseURL" => 1,
            "$archiveUrl" => 0.5,
        ];
        $latestModifiedTime = 0;

        // Load all projects and generate URLs for them
        $projects = ProjectInfo::loadAll();

        foreach ($projects as $project) {
            $urls[] = $projectUrl . $project->id;
            $pagePriority[] = calculatePriority($project->indexOrder);
            $lastModi[] = $project->lastModified;
            $images[] = $baseURL . '/' . $project->path . '/' . $project->summary['thumbnail'];
            if ($latestModifiedTime == 0 || $project->lastModified > $latestModifiedTime) {
                $latestModifiedTime = $project->lastModified;
            }
        }

        // Generate the sitemap
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . PHP_EOL;
        $sitemap .= '    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;

        foreach ($staticUrls as $url => $prio) {
            $latest = date('Y-m-d H:i:s', $latestModifiedTime);
            $sitemap .= '<url>' . PHP_EOL;
            $sitemap .= '    <loc>' . htmlentities($url) . '</loc>' . PHP_EOL;
            $sitemap .= '    <changefreq>monthly</changefreq>' . PHP_EOL;
            $sitemap .= '    <priority>' . $prio . '</priority>' . PHP_EOL;
            $sitemap .= '    <lastmod>' . $latest . '</lastmod>' . PHP_EOL;
            $sitemap .= '</url>' . PHP_EOL;
        }

        foreach ($urls as $index => $url) {
            $lastModiYMD = date('Y-m-d H:i:s', $lastModi[$index]);
            $priority = $pagePriority[$index];
            $image = $images[$index];
            $sitemap .= '<url>' . PHP_EOL;
            $sitemap .= '    <loc>' . htmlentities($url) . '</loc>' . PHP_EOL;
            $sitemap .= '    <changefreq>yearly</changefreq>' . PHP_EOL;
            $sitemap .= '    <priority>' . $priority . '</priority>' . PHP_EOL;
            $sitemap .= '    <lastmod>' . $lastModiYMD . '</lastmod>' . PHP_EOL;
            $sitemap .= '    <image:image>' . PHP_EOL;
            $sitemap .= '        <image:loc>' . $image . '</image:loc>' . PHP_EOL;
            $sitemap .= '    </image:image>' . PHP_EOL;
            $sitemap .= '</url>' . PHP_EOL;
        }

        $sitemap .= '</urlset>';

        // Save the sitemap to sitemap.xml
        file_put_contents(__DIR__ . '/../sitemap.xml', $sitemap);

        $message = "Success.<br>Sitemap generated!";
    } else {
        $message = "Incorrect password!";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sitemap Generator</title>
</head>

<body>

    <?php
    if (isset($message)) {
        echo "<p>" . $message . "</p>";
    }
    ?>

    <form action="" method="post">
        <label for="password">Enter Password:</label>
        <input type="password" id="password" name="password">
        <input type="submit" value="Generate Sitemap">
    </form>

</body>

</html>