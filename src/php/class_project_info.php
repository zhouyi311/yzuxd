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
        // echo "Entered loadById<br>";

        $allProjects = self::loadAll(); // Get all projects sorted by order
        $currentProjectIndex = array_search($id, array_column($allProjects, 'id'));

        // If project with ID is not found
        if ($currentProjectIndex === false) {
            // echo "No match found for ID: $id<br>";
            return null;
        }

        $project = $allProjects[$currentProjectIndex];

        // Get last project if it exists
        if ($currentProjectIndex < count($allProjects) - 1) {
            $project->last = $allProjects[$currentProjectIndex + 1];
        }

        // Get next project if it exists
        if ($currentProjectIndex > 0) {
            $project->next = $allProjects[$currentProjectIndex - 1];
        }

        // echo "Successfully fetched ProjectInfo for ID: $id<br>";
        return $project;
    }


    // public static function loadById($id)
    // {
    //     echo "Entered loadById<br>";

    //     $directory = __DIR__ . '/../page_data/projects';

    //     foreach (glob($directory . '/*.json') as $file) {
    //         echo "Checking file: $file<br>";
    //         $projectData = self::getProjectDataFromFile($file);
    //         echo "Loaded data for file: $file<br>";
    //         var_dump($projectData);

    //         if ($projectData['id'] == $id) {
    //             echo "Match found for ID: $id<br>";
    //             try {
    //                 $project = new ProjectInfo($projectData);
    //                 echo "Successfully created ProjectInfo instance for ID: $id<br>";
    //                 return $project;
    //             } catch (Exception $e) {
    //                 echo "Error while creating ProjectInfo instance: " . $e->getMessage() . "<br>";
    //             }
    //         }
    //     }

    //     echo "No match found<br>";
    //     return null;
    // }

    // public static function loadById($id)
    // {
    //     echo "Entered loadById<br>"; // Debug statement

    //     $directory = __DIR__ . '/../page_data/projects';

    //     foreach (glob($directory . '/*.json') as $file) {
    //         echo "Checking file: $file<br>"; // Debug statement

    //         $projectData = self::getProjectDataFromFile($file);

    //         if ($projectData['id'] == $id) {
    //             echo "Match found for ID: $id<br>"; // Debug statement
    //             return new ProjectInfo($projectData);
    //         }
    //     }
    //     echo "No match found<br>"; // Debug statement
    //     return null;
    // }

    // public static function loadById($id)
    // {
    //     $directory = __DIR__ . '/../page_data/projects'; // Absolute path for reading data
    //     $allProjects = self::loadAll(); // Get all projects sorted by order

    //     $currentProjectIndex = array_search($id, array_column($allProjects, 'id'));

    //     // If project with ID is not found
    //     if ($currentProjectIndex === false) {
    //         return null;
    //     }

    //     $project = $allProjects[$currentProjectIndex];

    //     // Get last project if it exists
    //     if ($currentProjectIndex < count($allProjects) - 1) {
    //         $project->last = $allProjects[$currentProjectIndex + 1];
    //     }

    //     // Get next project if it exists
    //     if ($currentProjectIndex > 0) {
    //         $project->next = $allProjects[$currentProjectIndex - 1];
    //     }

    //     return $project;
    // }

    


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