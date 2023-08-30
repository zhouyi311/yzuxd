<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . '/class_info_site.php';
include_once __DIR__ . '/class_info_project.php';
include_once __DIR__ . '/class_renderer_header.php';
include_once __DIR__ . '/class_renderer_cards.php';

$siteInfo = SiteInfo::loadInfo();
$projects = ProjectInfo::loadAll();

$siteHeaderRenderer = new HeaderRenderer('archive');
$projectCardsRendererWithHidden = new ProjectCardsRenderer(true);

?>

<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/include_head.php'; ?>

<body id="archive_root">
    <div id="archive_page_wrapper" class="page_wrapper">
        <!-- header -->
        <?php
        echo $siteHeaderRenderer->render();
        ?>
        </header>
        <!-- Main -->
        <main id="archive_main" data-bs-spy="" data-bs-target="#navbar_target" data-bs-root-margin="0px 0px -40%" data-bs-smooth-scroll="true">
            <!-- hero -->
            <section class="archive_page_home bg-white" id="home">
                <div class="bg" style="height:300px; overflow:hidden; background-image: url('src/img/all-page-deco-01.webp'); background-size: cover; background-position: center;">
                    <!-- <img src="src/img/all-page-deco-01.png" alt="head banner decoration" class="w-100"> -->
                </div>
                <div class="container">
                    <div class="row">
                        <div class='col-12'>
                            <h1 class="text-uppercase my-5 py-4">
                                Archive Library
                            </h1>
                            <!-- <div class='col-12 text-body-tertiary'><hr></div> -->
                        </div>
                    </div>
                </div>
            </section>
            <!-- projects -->
            <section class="archive_projects bg-white" id="projects">
                <?php
                $projectCardsRendererWithHidden->render();
                ?>
            </section>
            <div class="container">
                <div class="row">
                    <div class="col">
                        <h6 class="text-center my-5 p-5">
                            Thanks for visiting my site, I hope you have a lovely day and evening.
                        </h6>
                    </div>
                </div>
            </div>

            <!-- contact -->
        </main>
        <?php include __DIR__ . '/include_footer.php'; ?>
        <script src="src/js/page_archive.js?v=4222" type="text/javascript"></script>
    </div>
</body>

</html>