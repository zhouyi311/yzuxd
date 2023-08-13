<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . '/class_info_site.php';
include_once __DIR__ . '/class_info_project.php';
include_once __DIR__ . '/class_renderer_header.php';
include_once __DIR__ . '/class_renderer_cards.php';

$siteInfo = SiteInfo::loadInfo();
$projects = ProjectInfo::loadAll();

$siteHeaderRenderer = new HeaderRenderer('archive', $siteInfo);
$projectCardsRendererWithHidden = new ProjectCardsRenderer($projects, true);

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
        <main id="archive_main" data-bs-spy="scroll" data-bs-target="#navbar_target" data-bs-root-margin="0px 0px -40%" data-bs-smooth-scroll="true">
            <!-- hero -->
            <section class="archive_page_home bg-white pt-5" id="home">
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
            <section class="page_section bg-white" id="projects">
                <div class="container">
                    <div class="row">
                        <div class="section_headline">
                            <h2 class="text-black h3 fw-bold">Showcase Pages</h2>
                        </div>
                    </div>
                </div>


                <?php
                $projectCardsRendererWithHidden->render();
                ?>
                <h6 class="text-center pt-5 mt-5 mx-5">
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