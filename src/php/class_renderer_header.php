<?php
class HeaderRenderer
{

    private $type; // 'home', 'project', etc.
    private $siteInfo; // Instance of the SiteInfo class
    private $project; // The project information, if any
    private $isPasswordRequired;
    private $isAuthenticated;
    private $projectKey;

    public function __construct($type, $siteInfo, $project = null, $projectKey = null, $isPasswordRequired = false, $isAuthenticated = false)
    {
        $this->type = $type;
        $this->siteInfo = $siteInfo;
        $this->project = $project;
        $this->isPasswordRequired = $isPasswordRequired;
        $this->isAuthenticated = $isAuthenticated;
        $this->projectKey = $projectKey;
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
        $siteInfo = $this->siteInfo;
        ?>
        <header class="home_page_header">
            <div class="container-fluid">
                <div class="row">
                    <!-- nav bar -->
                    <nav class="page_navbar homepage_navbar navbar fixed-top px-4" id="page_navbar">
                        <!-- nav logo -->
                        <div class="navbar-brand nav_listen_target">
                            <a class="logo" href="<?php echo $siteInfo->rootUrl; ?>">
                                <img src="src/img/favicon/logo.svg" alt="logo" height="24">
                            </a>
                            <a class="h5 mb-0" href="<?php echo $siteInfo->rootUrl; ?>">
                                <?php echo htmlspecialchars($siteInfo->sitename); ?>
                            </a>
                        </div>
                        <!-- nav btn -->
                        <button class="navbar-toggler nav_listen_target" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <!-- drawer -->
                        <div class="offcanvas offcanvas-end px-4" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                            <div class="offcanvas-header mb-4">
                                <h3 class="offcanvas-title h6 text-body-tertiary" id="offcanvasNavbarLabel">
                                    <?php echo htmlspecialchars($siteInfo->information['siteTitle']); ?>
                                </h3>
                                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <div class="drawer_top_group">
                                    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3" id="navbar_target">
                                        <li class="nav-item">
                                            <a class="nav-link" href="#home"><span class="h3 fw-bold">HOME</span></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#projects"><span class="h3 fw-bold">PROJECTS</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#contact"><span class="h3 fw-bold">CONTACT</span>
                                            </a>
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
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </header>
        <?php
    }

    private function renderProjectHeader()
    {
        $siteInfo = $this->siteInfo;
        $project = $this->project;
        $isAuthenticated = $this->isAuthenticated;
        $isPasswordRequired = $this->isPasswordRequired;
        $projectKey = $this->projectKey;
        ?>
        <header class="project_page_header">
            <header class="project_page_header">
                <div class="container-fluid">
                    <div class="row">
                        <!-- nav bar -->
                        <nav class="page_navbar project_navbar navbar fixed-top px-4" id="page_navbar">
                            <!-- nav logo -->
                            <div class="navbar-brand">
                                <a class="logo" href="<?php echo $siteInfo->rootUrl; ?>">
                                    <img src="src/img/favicon/logo.svg" alt="logo" height="24">
                                </a>
                                <a class="h5 mb-0" href="<?php echo $siteInfo->rootUrl; ?>">
                                    <?php echo htmlspecialchars($siteInfo->sitename); ?>
                                </a>
                                <?php
                                if (isset($project->title)) {
                                    echo '<span class="project_name slideout">|</span><span class="project_name text-secondary fs-6 slideout">';
                                    echo htmlspecialchars($project->title) . '</span>';
                                }
                                ?>

                            </div>
                            <!-- nav btn -->
                            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
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
                                <div class="offcanvas-body">
                                    <div class="drawer_top_group">
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
                                        <a class="btn btn-dark rounded-pill px-4 fw-bold d-flex justify-content-between" href="<?php echo $siteInfo->rootUrl; ?>"><i class="bi bi-arrow-left pe-2 align-middle"></i><span class="w-100">
                                                HOMEPAGE</span></a>
                                    </div>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
            </header>
        </header>
        <?php
    }

    private function renderArchiveHeader()
    {
        $siteInfo = $this->siteInfo;
        $project = $this->project;
        $isAuthenticated = $this->isAuthenticated;
        $isPasswordRequired = $this->isPasswordRequired;
        ?>
        <header class="sitemap_page_header">
            <div class="container-fluid">
                <div class="row">
                    <!-- nav bar -->
                    <nav class="page_navbar sitemap_navbar navbar fixed-top px-4" id="page_navbar" data-bs-theme="light">
                        <!-- nav logo -->
                        <div class="navbar-brand">
                            <a class="logo" href="<?php echo $siteInfo->rootUrl; ?>">
                                <img src="src/img/favicon/logo.svg" alt="logo" height="24">
                            </a>
                            <a class="h5 mb-0" href="<?php echo $siteInfo->rootUrl; ?>">
                                <?php echo htmlspecialchars($siteInfo->sitename); ?>
                            </a>
                        </div>
                        <!-- nav btn -->
                        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <!-- drawer -->
                        <div class="offcanvas offcanvas-end px-4" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                            <div class="offcanvas-header mb-4">
                                <h3 class="offcanvas-title h6 text-body-tertiary" id="offcanvasNavbarLabel">
                                    <?php echo htmlspecialchars($siteInfo->information['siteTitle']); ?>
                                </h3>
                                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <div class="drawer_top_group">
                                    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3" id="navbar_target">
                                        <li class="nav-item">
                                            <a class="nav-link" href="#home"><span class="h6 fw-bold">HOME</span></a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="drawer_btm_group d-flex flex-column gap-4 pb-5">
                                    <div class="btn-group rounded-pill fw-bold overflow-hidden" role="group">
                                    </div>
                                    <a class="btn btn-dark rounded-pill px-4 fw-bold d-flex justify-content-between" href="<?php echo $siteInfo->rootUrl; ?>">
                                        <i class="bi bi-arrow-left pe-2 align-middle"></i>
                                        <span class="w-100">HOMEPAGE</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </header>
        <?php
    }

    private function renderNavBar()
    {
    }
    private function renderDrawer()
    {
    }


}

// Example usage:
// $siteInfo = SiteInfo::loadInfo();
// 
// 

// $project = ...; // Some way of loading your project data
// $siteHeaderRenderer = new HeaderRenderer('project', $siteInfo, $project);
// echo $siteHeaderRenderer->render();

// }

?>