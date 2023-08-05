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
        $file = 'src/page_data/site_info.json';
        $json = file_get_contents($file);
        $data = json_decode($json, true);
        return new SiteInfo(
            $data['sitename'],
            $data['information'], // Pass the entire 'information' object
            $data['frontPageContent']
        );
    }
}
?>