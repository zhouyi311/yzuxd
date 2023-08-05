<?php
class ProjectInfo {
    public $id;
    public $title;
    public $date;
    public $categories;
    public $summary;
    public $password;
    public $indexOrder;
    public $content;
    public $path;

    public function __construct($id, $title, $date, $categories, $summary, $password, $indexOrder, $content, $path) {
        $this->id = $id;
        $this->title = $title;
        $this->date = $date;
        $this->categories = $categories;
        $this->summary = $summary;
        $this->password = $password;
        $this->indexOrder = $indexOrder;
        $this->content = []; // Initialize as an empty array
        $this->path = $path;
    
        // Loop through each content item in the provided array
        foreach ($content as $contentItem) {
            // Each content item is an associative array with `type` and `content` keys
            $this->content[] = $contentItem;
        }
    }

    public static function loadAll() {
        $directory = 'src/page_data/projects';
        $projects = [];
    
        foreach (glob($directory . '/*.json') as $file) {
            $json = file_get_contents($file);
            $projectData = json_decode($json, true);
            $projectData = $projectData[0];  // Access the first (and only) project in the array
            $path = str_replace('.json', '/', $file); // Get rid of .json
            $projects[] = new ProjectInfo(
                $projectData['id'],
                $projectData['title'],
                $projectData['date'],
                $projectData['categories'],
                $projectData['summary'],
                $projectData['password'],
                $projectData['indexOrder'],
                $projectData['content'],
                $path // Add path here
            );
        }
    
        return $projects;
    }
    

    public static function loadById($id) {
        $directory = 'src/page_data/projects';
        foreach (glob($directory . '/*.json') as $file) {
            $json = file_get_contents($file);
            $projectData = json_decode($json, true)[0];
            if ($projectData['id'] == $id) {
                $path = str_replace('.json', '/', $file); // Get rid of .json
                return new ProjectInfo(
                    $projectData['id'],
                    $projectData['title'],
                    $projectData['date'],
                    $projectData['categories'],
                    $projectData['summary'],
                    $projectData['password'],
                    $projectData['indexOrder'],
                    $projectData['content'],
                    $path // Add path here
                );
            }
        }
        return null; // Return null if no project with the given ID is found
    }

    public static function doesProjectExist($id)
    {
        $directory = 'src/page_data/projects';
        foreach (glob($directory . '/*.json') as $file) {
            $json = file_get_contents($file);
            $projectData = json_decode($json, true)[0];
            if ($projectData['id'] == $id) {
                return true;
            }
        }
        return false;
    }
    
}
?>