<?php

class ProjectInfo
{
    public $id;
    public $title;
    public $date;
    public $categories;
    public $summary;
    public $password;
    public $indexOrder;
    public $content;
    public $path;

    public function __construct($projectData)
    {
        $this->id = $projectData['id'];
        $this->title = $projectData['title'];
        $this->date = $projectData['date'];
        $this->categories = $projectData['categories'];
        $this->summary = $projectData['summary'];
        $this->password = $projectData['password'];
        $this->indexOrder = $projectData['indexOrder'];
        $this->content = $projectData['content'];
        $this->path = '/src/page_data/projects/' . basename($projectData['file'], '.json') . '/';
    }

    private static function getProjectDataFromFile($file)
    {
        $json = file_get_contents($file);
        $projectData = json_decode($json, true)[0];
        $projectData['file'] = $file;
        return $projectData;
    }

    public static function loadAll()
    {
        $directory = __DIR__ . '/../page_data/projects'; // Absolute path for reading data

        $projects = [];
        foreach (glob($directory . '/*.json') as $file) {
            $projects[] = new ProjectInfo(self::getProjectDataFromFile($file));
        }

        return $projects;
    }

    public static function loadById($id)
    {
        $directory = __DIR__ . '/../page_data/projects'; // Absolute path for reading data
        foreach (glob($directory . '/*.json') as $file) {
            $projectData = self::getProjectDataFromFile($file);
            if ($projectData['id'] == $id) {
                return new ProjectInfo($projectData);
            }
        }
        return null;
    }

    public static function doesProjectExist($id)
    {
        $directory = __DIR__ . '/../page_data/projects'; // Absolute path for reading data
        foreach (glob($directory . '/*.json') as $file) {
            if (basename($file, '.json') == $id) {
                return true;
            }
        }
        return false;
    }
}

?>