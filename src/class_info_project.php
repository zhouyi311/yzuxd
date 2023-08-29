<?php

class ProjectInfo
{
    public $id;
    public $anchorId;
    public $path;
    public $lastModified;
    public $indexOrder;
    public $password;
    public $title;
    public $summary;
    public $article;
    public $last = null;
    public $next = null;
    public function __construct($projectData, $path)
    {
        $this->id = $projectData['id'];
        $this->lastModified = $projectData['lastModified'];
        $this->path = 'src/site_data/pages/' . $path;

        $this->indexOrder = $projectData['indexOrder'];
        if (!is_numeric($this->indexOrder)) {
            $this->indexOrder = -1;
        }

        $this->password = $projectData['password'];
        $this->title = $projectData['title'];
        $this->summary = $projectData['summary'];
        if (isset($this->summary['text']) && !is_array($this->summary['text'])) {
            $this->summary['text'] = array($this->summary['text']);
        }
        if (isset($this->summary['categories']) && !is_array($this->summary['categories'])) {
            $this->summary['categories'] = array($this->summary['categories']);
        }

        $this->article = $projectData['article'];
        foreach ($this->article as $index => &$section) {
            empty($section['headline']) ? $section['headline'] = 'Section ' . ($index + 1) : null;
            $headlineId = strtolower(preg_replace('/[^a-z0-9]/i', '_', $section['headline'])); // Replace all special characters with underscores and lowercase
            $headlineId = 'sec_' . ($index + 1) . '_' . $headlineId;
            $section['headlineId'] = $headlineId;
        }
    }

    private static function getProjectDataFromFile($file, $dirName)
    {
        $json = file_get_contents($file);
        $projectData = json_decode($json, true)[0];

        if (empty($projectData['id'])) {
            $projectData['id'] = $dirName . "_" . crc32($file);
        }

        $projectData['id'] = rawurlencode($projectData['id']);
        $projectData['file'] = $file;
        $projectData['lastModified'] = filemtime($file);
        return $projectData;
    }

    public static function loadAll()
    {
        $directory = __DIR__ . '/site_data/pages/';
        $projects = [];
        $seenIds = [];

        // Load all projects and handle duplicates
        foreach (glob($directory . '/*', GLOB_ONLYDIR) as $dir) {
            $jsonFile = $dir . '/page_data.json';

            if (file_exists($jsonFile)) {
                $project = new ProjectInfo(self::getProjectDataFromFile($jsonFile, basename($dir)), basename($dir));

                if (in_array($project->id, $seenIds)) { // If the ID has been seen before
                    $project->id = basename($dir) . "_" . crc32($dir); // Guarantee uniqueness with crc32
                }

                $project->anchorId = 'p_' . substr(md5($project->id), 0, 8);
                $seenIds[] = $project->id;
                $projects[] = $project;
            }
        }

        // Sort and return projects
        usort($projects, function ($a, $b) {
            if ($a->indexOrder == $b->indexOrder) {
                return strcmp($a->id, $b->id); // Sort by ID if indexOrder is the same
            }
            return $a->indexOrder <=> $b->indexOrder; // Sort primarily by indexOrder
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

}

?>