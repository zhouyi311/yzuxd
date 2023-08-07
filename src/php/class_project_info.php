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

        // Check if 'id' is not set or empty, and then assign a unique ID based on the file path
        if (!isset($projectData['id']) || empty($projectData['id'])) {
            $projectData['id'] = basename($file) . "_" . substr(md5($file), 0, 8);
        }

        $projectData['file'] = $file;
        return $projectData;
    }

    public static function loadAll()
    {
        $directory = __DIR__ . '/../page_data/projects';
        $projects = [];
        $seenIds = [];

        // Load all projects and handle duplicates
        foreach (glob($directory . '/*.json') as $file) {
            $project = new ProjectInfo(self::getProjectDataFromFile($file));

            // If the ID has been seen before, assign a unique ID based on the filename
            if (in_array($project->id, $seenIds)) {
                $basenameId = basename($project->path) . "_" . substr(md5($project->path), 4, 4);
                if (in_array($basenameId, $seenIds)) {
                    $project->id = $basenameId . "_" . random_int(0, 9999) . "_" . md5($project->path); // Hash the full path if basename is also redundant
                } else {
                    $project->id = $basenameId;
                }
            }

            $seenIds[] = $project->id;
            $projects[] = $project;
        }

        // Sort and return projects
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

        // Get next project excluding negative indexOrder
        for ($i = $currentProjectIndex + 1; $i < count($allProjects); $i++) {
            if ($allProjects[$i]->indexOrder >= 0) {
                $project->next = $allProjects[$i];
                break;
            }
        }

        // Get last project excluding negative indexOrder
        for ($i = $currentProjectIndex - 1; $i >= 0; $i--) {
            if ($allProjects[$i]->indexOrder >= 0) {
                $project->last = $allProjects[$i];
                break;
            }
        }
        return $project;
    }

    // public static function doesProjectExist($id)
    // {
    //     $directory = __DIR__ . '/../page_data/projects'; // Absolute path for reading data
    //     foreach (glob($directory . '/*.json') as $file) {
    //         if (basename($file, '.json') == $id) {
    //             return true;
    //         }
    //     }
    //     return false;
    // }

}

?>