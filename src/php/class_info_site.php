<?php

class SiteInfo
{
    public $sitename;
    public $information;
    public $siteStructureInfo;
    public $frontPageContent;
    public $rootUrl; // Declare the rootUrl property

    public function __construct($sitename, $information, $siteStructureInfo, $frontPageContent)
    {
        $this->sitename = $sitename;
        $this->information = $information; // This will be an associative array
        $this->siteStructureInfo = $siteStructureInfo;
        $this->frontPageContent = $frontPageContent; // This will be an associative array
        $this->rootUrl = $this->getSiteRootUrl(); // Set the rootUrl property

        // Ensure heroParagraphsArray is always an array
        if (isset($this->frontPageContent['heroParagraphsArray']) && !is_array($this->frontPageContent['heroParagraphsArray'])) {
            $this->frontPageContent['heroParagraphsArray'] = [$this->frontPageContent['heroParagraphsArray']];
        }
    }
    public static function loadInfo()
    {
        $file = __DIR__ . '/../page_data/site_info.json'; // Adjusted to an absolute path

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
            $data['siteStructureInfo'],
            $data['frontPageContent']
        );
    }

    public function getSiteRootUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
        $domainName = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $folderPath = isset($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : '';

        if ($folderPath === '/' || $folderPath === '\\') {
            return $protocol . $domainName . '/';
        }
        return $protocol . $domainName . $folderPath . '/';
    }

}


?>