<?php

class ProjectInfo
{
    public $id;
    public $anchorId;
    public $path;
    public $indexOrder;
    public $password;
    public $title;
    public $summary;
    public $article;
    public $last = null;
    public $next = null;
    public function __construct($projectData)
    {
        // $projectData = $this->sanitizeData($projectData);

        $this->id = $projectData['id'];
        $this->path = '/src/page_data/projects/' . basename($projectData['file'], '.json');
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
            empty($section['headline']) ? $section['headline'] = 'Section ' . $index + 1 : null;
            $headlineId = strtolower(preg_replace('/[^a-z0-9]/i', '_', $section['headline'])); // Replace all special characters with underscores and lowercase
            $headlineId = 'sec_' . ($index + 1) . '_' . $headlineId; 
            $section['headlineId'] = $headlineId; 
        }
    }

    private static function getProjectDataFromFile($file)
    {
        $json = file_get_contents($file);
        $projectData = json_decode($json, true)[0];

        if (empty($projectData['id'])) { // Check if 'id' is not set or empty, and then assign a unique ID based on the file path
            $projectData['id'] = basename($file) . "_" . crc32($file);
        }

        $projectData['id'] = rawurlencode($projectData['id']);

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
                $basenameId = basename($project->path) . "_" . crc32($project->path);
                if (in_array($basenameId, $seenIds)) {
                    $project->id = $basenameId . "_" . random_int(0, 9999) . "_" . md5($project->path); // Hash the full path if basename is also redundant
                } else {
                    $project->id = $basenameId;
                }
            }
            $project->anchorId = 'p_' . substr(md5($project->id), 0, 8);

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

}

?>