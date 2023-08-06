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

    public $last = null;
    public $next = null;

    public function __construct($projectData)
    {
        $this->id = $projectData['id'];
        $this->title = $projectData['title'];
        $this->date = $projectData['date'];
        $this->categories = $projectData['categories'];
        $this->summary = $projectData['summary'];
        if (is_string($this->summary['text'])) {
            $this->summary['text'] = array($this->summary['text']);
        }
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
        $directory = __DIR__ . '/../page_data/projects';
        $projects = [];

        foreach (glob($directory . '/*.json') as $file) {
            $projects[] = new ProjectInfo(self::getProjectDataFromFile($file));
        }

        // Sort by indexOrder, then by ID if index orders are the same
        usort($projects, function ($a, $b) {
            if ($a->indexOrder == $b->indexOrder) {
                return $a->id <=> $b->id; // Sort by ID
            }
            return $a->indexOrder <=> $b->indexOrder; // Sort by indexOrder
        });

        return $projects;
    }

    public static function loadById($id)
    {

        $allProjects = self::loadAll(); // Get all projects sorted by order
        $currentProjectIndex = array_search($id, array_column($allProjects, 'id'));

        if ($currentProjectIndex === false) {
            return null;
        }

        $project = $allProjects[$currentProjectIndex];

        if ($currentProjectIndex < count($allProjects) - 1) {
            $project->next = $allProjects[$currentProjectIndex + 1];
        }

        if ($currentProjectIndex > 0) {
            $project->last = $allProjects[$currentProjectIndex - 1];
        }
        return $project;
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