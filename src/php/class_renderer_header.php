<?php
include_once __DIR__ . '/class_info_site.php';
include_once __DIR__ . '/class_info_project.php';
class HeaderRenderer
{
    private $type; // 'home', 'project', etc.
    private $siteInfo; // Instance of the SiteInfo class
    private $isPasswordRequired;
    private $isAuthenticated;
    private $projects;

    public function __construct($type, $projects = null, $isPasswordRequired = false, $isAuthenticated = false, $siteInfo = null)
    {
        $this->type = $type;

        switch ($this->type) {
            case 1:
                $this->type = 'home';
                break;
            case 2:
                $this->type = 'project';
                break;
            case 3:
                $this->type = 'archive';
                break;
            default:
                break;
        }

        $this->siteInfo = ($siteInfo instanceof SiteInfo) ? $siteInfo : SiteInfo::loadInfo();
        $this->projects = ($projects instanceof ProjectInfo) ? $projects : ProjectInfo::loadAll();
        $this->isPasswordRequired = $isPasswordRequired;
        $this->isAuthenticated = $isAuthenticated;
    }

    public function render()
    {
        ob_start();

        switch ($this->type) {
            case 'home':
                $this->renderHomeHeader();
                break;
            case 'project':
                $this->renderProjectHeader();
                break;
            case 'archive':
                $this->renderArchiveHeader();
                break;
            // Other cases can be added as needed
            default:
                throw new Exception("Unknown header type: {$this->type}");
        }

        return ob_get_clean();
    }

    private function renderHomeHeader()
    {
        $drawerContent = $this->homeDrawer();
        $this->navFrame($drawerContent);
    }
    private function renderProjectHeader()
    {
        $drawerContent = $this->projectDrawer();
        $this->navFrame($drawerContent);
    }
    private function renderArchiveHeader()
    {
        $drawerContent = $this->archiveDrawer();
        $this->navFrame($drawerContent);
    }

    private function navFrame($drawer_content)
    {
        $type = $this->type;
        $siteInfo = $this->siteInfo;
        $project = $this->projects;
        ?>
        <header class="<?php echo $type; ?>_page_header">
            <div class="container-fluid">
                <div class="row">
                    <!-- nav bar -->
                    <nav class="page_navbar <?php echo $type; ?>_page_navbar navbar fixed-top px-4" id="page_navbar">
                        <!-- nav logo -->
                        <div class="navbar-brand d-flex align-items-center gap-2">
                            <a class="h5 mb-0 no_deco site_title d-flex align-items-center gap-2" href="<?php echo $siteInfo->rootUrl; ?>">
                                <img src="src/img/favicon/logo.svg" alt="logo" height="24" class='site_logo'>
                                <span class="site_name">
                                    <?php echo htmlspecialchars($siteInfo->sitename); ?>
                                </span>
                            </a>
                            <?php
                            if (isset($project->title)) {
                                echo "<span class='project_name d-inline-block text-truncate text-secondary slideout'><span class='me-2'>|</span>";
                                echo "<span class='fs-6'>";
                                echo htmlspecialchars($project->title) . '</span>';
                                echo "</span>";
                            }
                            ?>
                        </div>
                        <!-- nav btn -->
                        <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <!-- drawer -->
                        <div class="offcanvas offcanvas-end px-4" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                            <div class="offcanvas-header mb-4">
                                <h3 class="offcanvas-title text-body-tertiary h6" id="offcanvasNavbarLabel">
                                    <?php echo htmlspecialchars($siteInfo->information['siteTitle']); ?>
                                </h3>
                                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body d-flex flex-column justify-content-between">
                                <?php echo $drawer_content; ?>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </header>
        <?php
    }

    private function homeDrawer()
    {
        $siteInfo = $this->siteInfo;
        ob_start();
        ?>
        <div class="drawer_top_group flex-grow-1">
            <ul class="navbar-nav justify-content-end flex-grow-1 pe-3" id="navbar_target">
                <li class="nav-item">
                    <a class="nav-link" href="#home"><span class="h3 fw-bold">HOME</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#projects"><span class="h3 fw-bold">PROJECTS</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact"><span class="h3 fw-bold">CONTACT</span></a>
                </li>
            </ul>
        </div>
        <div class="drawer_btm_group">
            <p class="fw-medium d-flex justify-content-between w-100">
                <span class="text-body-tertiary"> &copy;
                    <?php echo htmlspecialchars($siteInfo->information['siteCopyright']); ?>
                </span>
                <!-- <span class="text_subtle mx-3">|</span> -->
                <span class=""><a class="link text_subtle" href="<?php echo $siteInfo->rootUrl ?>?archive">Archive</a></span>
            </p>
        </div>
        <?php
        return ob_get_clean();
    }


    private function projectDrawer()
    {
        $siteInfo = $this->siteInfo;
        $project = $this->projects;
        $isAuthenticated = $this->isAuthenticated;
        $isPasswordRequired = $this->isPasswordRequired;
        $projectKey = $this->siteInfo->pageKeys['projectKey'];
        ob_start();
        ?>
        <div class="drawer_top_group flex-grow-1">
            <h4 class="list_title fw-medium text-body-secondary h6 mb-4" id="offcanvasNavbarLabel">
                <i class='bi bi-list-ul pe-1 align-middle'></i>
                <?php echo $project->title ? htmlspecialchars($project->title) : htmlspecialchars($siteInfo->information['siteTitle']); ?>
            </h4>
            <ul class="navbar-nav justify-content-end flex-grow-1 pe-3 mb-5" id="navbar_target">
                <li class="nav-item">
                    <a class="nav-link" href="#page_home"><span class="h5 fw-bold">Introduction</span></a>
                </li>
                <?php
                if ($isAuthenticated || !$isPasswordRequired) {
                    foreach ($project->article as $section) {
                        $headline = htmlspecialchars($section['headline']);
                        $headlineId = htmlspecialchars($section['headlineId']);
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link" href="#' . $headlineId . '"><span class="h5 fw-bold">' . $headline . '</span></a>';
                    }
                } else {
                    echo '<li class="nav-item"><a class="nav-link" href="#enter_password"><span class="h5 fw-bold">Enter Password</span></a></li>';
                }
                ?>
            </ul>
        </div>
        <div class="drawer_btm_group d-flex flex-column gap-4 pb-5">
            <div class="btn-group rounded-pill fw-bold overflow-hidden" role="group">
                <?php
                $existLastProject = !empty($project->last->id);
                $existNextProject = !empty($project->next->id);
                if ($existLastProject) {
                    echo "<a class='btn btn-light text-truncate px-4' href='?$projectKey={$project->last->id}'><span class='fw-bold me-1'>Prev:</span><span class='inner_text fw-normal w-100'>{$project->last->title}</span></a>";
                }
                if ($existNextProject) {
                    echo "<a class='btn btn-light text-truncate px-4' href='?$projectKey={$project->next->id}'><span class='fw-bold me-1'>Next:</span><span class='inner_text fw-normal w-100'>{$project->next->title}</span></a>";
                }
                ?>
            </div>
            <a class="btn btn-dark rounded-pill px-4 fw-bold d-flex justify-content-between" href="<?php echo $siteInfo->rootUrl; ?>">
                <i class="bi bi-arrow-left pe-2 align-middle"></i>
                <span class="w-100">RETURN TO HOMEPAGE</span>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }

    private function archiveDrawer()
    {
        $siteInfo = $this->siteInfo;
        $projects = $this->projects;
        ob_start();
        ?>
        <div class="drawer_top_group flex-grow-1">
            <h4 class="list_title fw-medium text-body-secondary h6 mb-4" id="offcanvasNavbarLabel">
                <i class='bi bi-list-ul pe-1 align-middle'></i>
                Pages
            </h4>
            <ul class="navbar-nav justify-content-end flex-grow-1 pe-3" id="navbar_target">
                <li class="nav-item">
                    <a class="nav-link" href="#home"><span class="h6">HOME</span></a>
                </li>
                <?php
                foreach ($projects as $navitems) {
                    $id = htmlspecialchars($navitems->id);
                    $headline = htmlspecialchars($navitems->title);
                    echo '<li class="nav-item">';
                    echo '<a class="nav-link" href="#' . $navitems->anchorId . '"><span class="h6">' . $headline . '</span></a>';
                }
                ?>
            </ul>
        </div>
        <div class="drawer_btm_group d-flex flex-column gap-4 pb-5">
            <div class="btn-group rounded-pill fw-bold overflow-hidden" role="group">
            </div>
            <a class="btn btn-dark rounded-pill px-4 fw-bold d-flex justify-content-between" href="<?php echo $siteInfo->rootUrl; ?>">
                <i class="bi bi-arrow-left pe-2 align-middle"></i>
                <span class="w-100">RETURN TO HOMEPAGE</span>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }




}

?>