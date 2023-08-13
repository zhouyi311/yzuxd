<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . '/class_info_site.php';
include_once __DIR__ . '/class_info_project.php';
include_once __DIR__ . '/class_renderer_header.php';

$siteInfo = SiteInfo::loadInfo();
$projects = ProjectInfo::loadAll();

?>

<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/include_head.php'; ?>

<body id="sitemap_root">
    <div id="sitemap_page_wrapper" class="page_wrapper">
        <!-- header -->
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
        <!-- Main -->
        <main id="sitemap_main" data-bs-spy="scroll" data-bs-target="#navbar_target" data-bs-root-margin="0px 0px -40%" data-bs-smooth-scroll="true">
            <!-- hero -->
            <section class="sitemap_page_home bg-white pt-5" id="home">
                <div class="container">
                    <div class="row">
                        <div class='col-12'>
                            <h1 class="text-uppercase my-5">
                                Site Archive
                            </h1>
                            <img src="src/img/all-page-deco-01.png" alt="head banner decoration" class="w-100 rounded-5">
                            <!-- <div class='col-12 text-body-tertiary'><hr></div> -->
                        </div>
                    </div>
                </div>
            </section>
            <!-- projects -->
            <?php /////////////////////////////////////////////////////////////////////////////////////////////////////////////////// ?>
            <section class="page_section bg-white" id="projects">
                <div class="container">
                    <div class="row">
                        <div class="section_headline">
                            <h2 class="text-black h3 fw-bold">Showcase Pages</h2>
                        </div>
                    </div>
                </div>
                <div class="container" id="projects_container">
                    <div class="row gy-4 gy-lg-5 gx-xl-5">
                        <?php function createProjectCard($project, $isFeatured = true)
                        {
                            $projectPath = $project->path . "/";
                            $projectLink = '?page=' . htmlspecialchars($project->id);
                            $title = isset($project->title) ? htmlspecialchars($project->title) : "";
                            $subhead = isset($project->summary['subhead']) ? $project->summary['subhead'] : null;
                            $categories = isset($project->summary['categories']) ? $project->summary['categories'] : [];
                            $thumbnailSrc = isset($project->summary['thumbnail']) ? htmlspecialchars($projectPath . $project->summary['thumbnail']) : "";
                            $summaryText = isset($project->summary['text']) ? $project->summary['text'] : [];

                            echo "<div class='col-sm-6 col-md-4 col-xl-3'>";
                            echo "<div class='project_card card border-0 overflow-hidden rounded-5 bg-white h-100'>";


                            if ($isFeatured) {
                                echo "<a class='card_thumb_link' href='$projectLink'>";
                                echo "<div class='card_thumbnail ratio ratio-1x1 rounded-5'>";
                                echo "<img class='object-fit-cover' src='$thumbnailSrc' alt='$title thumbnail'>";
                                echo "</div></a>";
                            }

                            echo "<div class='card_info h-100'>";
                            echo "<div class='card_info_headline'>";
                            echo "<a href='$projectLink'><p class='h6 " . ($isFeatured ? null : "mt-3") . "'>$title</p>";
                            if (isset($subhead) && $isFeatured) {
                                echo "<p class='h6 text-body-tertiary'>$subhead</p>";
                            }
                            echo "</a></div>";
                            if (!empty($categories) && $isFeatured) {
                                echo "<div class='card_info_categories text-truncate'>";
                                foreach ($categories as $category) {
                                    $category = htmlspecialchars($category);
                                    echo "<span class='category-container badge rounded-pill text-body-secondary fw-normal'>$category</span> ";
                                }
                                echo "</div>";
                            }
                            echo "<a href='$projectLink' class='link link-secondary p_a_summary_link'>";
                            echo "<div class='card_info_summary" . "'>";
                            //  . ($isFeatured ? null : "_alt")
                            echo "<div class='summary_content no_mask'>";
                            foreach ($summaryText as $textItem) {
                                echo "<p class='mb-1 text-body-secondary'>" . htmlspecialchars($textItem) . "</p>";
                            }
                            echo "</div></div></a>";
                            echo "</div></div></div>";
                        }
                        function renderProjects($projects)
                        {
                            $count = 0;
                            foreach ($projects as $project) {
                                if ($project->indexOrder >= 0 && $project->indexOrder < 10) {
                                    createProjectCard($project);
                                    $count++;
                                } elseif ($project->indexOrder >= 10) {
                                    createProjectCard($project);
                                    $count++;
                                }
                            }
                            if ($count===0){
                                echo "<p>Empty :)</p>";
                            }
                            echo "<div class='col-12 text-body-tertiary'><hr></div>";
                            echo "<h3 class='fw-bold'>Archived Pages</h3>";

                            foreach ($projects as $project) {
                                if ($project->indexOrder < 0) {
                                    createProjectCard($project, false);
                                }
                            }
                        }
                        renderProjects($projects) ?>
                    </div>
                </div>
                <h6 class="text-center mt-5 pt-5 mx-5">
                    Thanks for visiting my site, I hope you have a lovely day and evening.
                </h6>

            </section>
            <!-- contact -->
        </main>
        <?php include __DIR__ . '/include_footer.php'; ?>
        <script src="src/js/page_home.js?v=429" type="text/javascript"></script>
    </div>
</body>

</html>