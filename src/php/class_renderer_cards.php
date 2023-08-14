<?php
include_once __DIR__ . '/class_info_site.php';
include_once __DIR__ . '/class_info_project.php';
class ProjectCardsRenderer
{
    private $projects;
    private $isArchive;
    private $siteInfo;

    public function __construct($isArchive = false, $projects = null, $siteInfo = null)
    {
        $this->isArchive = is_bool($isArchive) ? $isArchive : false;
        $this->siteInfo = ($siteInfo instanceof SiteInfo) ? $siteInfo : SiteInfo::loadInfo();
        $this->projects = ($projects instanceof ProjectInfo) ? $projects : ProjectInfo::loadAll();
    }

    public function render()
    {
        $inArchive = $this->isArchive;

        if (!$inArchive) {
            $countProjs = 0;
            echo "<div class='container projects_container' id='projects_container'>";
            echo "<div class='row g-4 gy-lg-5 gx-xl-5'>";
            foreach ($this->projects as $project) {
                if ($project->indexOrder >= 0 && $project->indexOrder < 10) {
                    $this->createProjectCard($project, true);
                    $countProjs++;
                } elseif ($project->indexOrder >= 10) {
                    $this->createProjectCard($project, false);
                    $countProjs++;
                }
            }
            if ($countProjs === 0) {
                echo "<div class='col'><p>Not enough projects, please check data</p></div>";
            }
            echo "</div></div>";
        }


        if ($inArchive) {
            $countProjs = 0;
            echo "<div class='container projects_container archive_container' id='archive_container'>";
            echo "<div class='row g-4 pt-5'>";
            echo "<h3 >Showcase Projects</h3>";
            foreach ($this->projects as $project) {
                if ($project->indexOrder >= 0 && $project->indexOrder < 10) {
                    $this->createProjectList($project, true);
                    $countProjs++;
                } elseif ($project->indexOrder >= 10) {
                    $this->createProjectList($project, false);
                    $countProjs++;
                }
            }
            if ($countProjs === 0) {
                echo "<div class='col'><p>Not enough projects, please check data</p></div>";
            }
            echo "<h3 class='pt-4'>Archived Pages</h3>";
            foreach ($this->projects as $project) {
                if ($project->indexOrder < 0) {
                    $this->createProjectList($project, false); // Render hidden projects, but not as featured
                    $countProjs++;
                }
            }
            if ($countProjs === 0) {
                echo "<div class='col'><p>Not enough projects, please check data</p></div>";
            }
            echo "</div></div>";
        }
    }


    private function createProjectList($project, $isFeatured)
    {
        // $projectPath = $project->path . "/";
        $projKey = $this->siteInfo->pageKeys['projectKey'];
        $projectLink = "?{$projKey}=" . htmlspecialchars($project->id);
        $title = isset($project->title) ? htmlspecialchars($project->title) : "";
        $subhead = isset($project->summary['subhead']) ? $project->summary['subhead'] : null;
        $categories = isset($project->summary['categories']) ? $project->summary['categories'] : [];
        // $thumbnailSrc = isset($project->summary['thumbnail']) ? htmlspecialchars($projectPath . $project->summary['thumbnail']) : "";
        $summaryText = isset($project->summary['text']) ? $project->summary['text'] : [];

        echo "<div class='col-md-6'><div class='card archive_card p-4 rounded-3 border-0 bg-light shadow-sm h-100' id='{$project->anchorId}'>";
        echo "<h6 class='fw-medium mb-3'><a href='$projectLink' class='no_deco link stretched-link'><span class='me-3'>$title</span>";
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $category = htmlspecialchars($category);
                echo "<span class='category-container badge bg_subtle rounded-pill text-secondary fw-normal ms-1 align-middle'>$category</span> ";
            }
        }
        echo "</a></h6>";
        if (!empty($subhead) && $isFeatured) {
            echo "<p class='text-body-tertiary'>$subhead</p>";
        }
        echo "<div class='card_info'>";

        echo "<div class='card_info_summary'>";
        foreach ($summaryText as $textItem) {
            echo "<p class='mb-1 text-body-secondary'>" . htmlspecialchars($textItem) . "</p>";
        }
        echo "</div></div>";
        echo '</div></div>';
    }

    private function createProjectCard($project, $isFeatured)
    {
        $projKey = $this->siteInfo->pageKeys['projectKey'];
        $projectPath = $project->path . "/";
        $projectLink = "?{$projKey}=" . htmlspecialchars($project->id);
        $title = isset($project->title) ? htmlspecialchars($project->title) : "";
        $subhead = isset($project->summary['subhead']) ? $project->summary['subhead'] : null;
        $categories = isset($project->summary['categories']) ? $project->summary['categories'] : [];
        $thumbnailSrc = isset($project->summary['thumbnail']) ? htmlspecialchars($projectPath . $project->summary['thumbnail']) : "";
        $summaryText = isset($project->summary['text']) ? $project->summary['text'] : [];

        if ($isFeatured) {
            echo "<div class='col-12'>";
        } else {
            echo "<div class='col-sm-6 col-lg-4'>";
        }
        echo "<div class='project_card " . ($isFeatured ? "featured_card " : null);
        echo " card border-0 overflow-hidden rounded-5 bg-white h-100' id='$project->anchorId'>";
        echo "<a class='card_thumb_link' href='$projectLink'>";
        echo "<div class='card_thumbnail ratio ratio-1x1 rounded-5'>";
        echo "<img class='object-fit-cover' src='$thumbnailSrc' alt='$title thumbnail'>";
        echo "</div></a>";
        echo "<div class='card_info h-100'>";
        echo "<div class='card_info_headline text-nowrap text-truncate'>";
        echo "<a href='$projectLink'><h4>$title</h4>";
        if (isset($subhead) && $isFeatured) {
            echo "<p class='h6 text-body-tertiary'>$subhead</p>";
        }
        echo "</a></div><div class='card_info_categories text-truncate'>";
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $category = htmlspecialchars($category);
                echo "<span class='category-container badge rounded-pill text-body-secondary fw-normal'>$category</span> ";
            }
        }
        echo "</div><div class='card_info_summary'>";
        echo "<div class='summary_content'>";
        foreach ($summaryText as $textItem) {
            echo "<p class='mb-1 text-body-secondary'>" . htmlspecialchars($textItem) . "</p>";
        }
        echo "</div></div><div class='card_info_cta mt-1 mb-2'>";
        echo "<a class='btn btn-dark rounded-pill px-4 fw-medium' href='$projectLink'>";
        echo "Learn More <i class='bi bi-arrow-right-short align-middle'></i>";
        echo "</a></div></div></div></div>";

    }

}

?>