<?php

class SiteInfo
{
    public $sitename;
    public $information;
    public $fontPageContent;

    public function __construct($sitename, $information, $fontPageContent)
    {
        $this->sitename = $sitename;
        $this->information = $information; // This will be an associative array
        $this->fontPageContent = $fontPageContent; // This will be an associative array
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
            $data['frontPageContent']
        );
    }
}

?>

