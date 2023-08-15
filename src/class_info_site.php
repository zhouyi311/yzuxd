<?php

class SiteInfo
{
    public $sitename;
    public $information;
    public $pageKeys;
    public $frontPageContent;
    public $rootUrl; // Declare the rootUrl property

    public function __construct($sitename, $information, $pageKeys, $frontPageContent)
    {
        $this->sitename = $sitename;
        $this->information = $information; // This will be an associative array
        $this->pageKeys = $pageKeys;
        $this->frontPageContent = $frontPageContent; // This will be an associative array
        $this->rootUrl = $this->getSiteRootUrl(); // Set the rootUrl property
        // Ensure heroParagraphsArray is always an array
        if (isset($this->frontPageContent['heroParagraphsArray']) && !is_array($this->frontPageContent['heroParagraphsArray'])) {
            $this->frontPageContent['heroParagraphsArray'] = [$this->frontPageContent['heroParagraphsArray']];
        }
    }
    public static function loadInfo()
    {
        $file = __DIR__ . '/page_data/site_info.json'; // Adjusted to an absolute path

        if (!file_exists($file)) {
            throw new Exception("The site_info.json file does not exist.");
        }

        $json = file_get_contents($file);

        if (!$json) {
            throw new Exception("Error reading the site_info.json file.");
        }

        $data = json_decode($json, true);

        if (!$data) {
            throw new Exception("Error decoding the site_info.json content.");
        }

        return new SiteInfo(
            $data['sitename'],
            $data['information'], // Pass the entire 'information' object
            $data['pageKeys'],
            $data['frontPageContent']
        );
    }

public function getSiteRootUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
        $domainName = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $basePath = $this->findBasePath(dirname($_SERVER['SCRIPT_FILENAME']));
        
        return $protocol . $domainName . $basePath;
    }
    private function findBasePath($currentPath) {
        // Stop if we're at the root directory
        if ($currentPath == dirname($currentPath)) {
            return '';
        }
        // Check if index.php exists in the current directory
        if (file_exists($currentPath . '/index.php')) {
            // Remove the document root to get the relative path
            return str_replace($_SERVER['DOCUMENT_ROOT'], '', $currentPath);
        } else {
            // If not, go one directory up and try again
            return $this->findBasePath(dirname($currentPath));
        }
    }
}


?>