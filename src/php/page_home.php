<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// root url
function getSiteRootUrl()
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
    $domainName = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    $folderPath = isset($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : '';

    if ($folderPath === '/' || $folderPath === '\\') {
        return $protocol . $domainName . '/';
    }
    return $protocol . $domainName . $folderPath . '/';
}
$site_root_url = getSiteRootUrl();

// load projects
require_once __DIR__ . '/class_info_site.php';
$site_info = SiteInfo::loadInfo();

include_once __DIR__ . '/class_info_project.php';
$projects = ProjectInfo::loadAll();

?>

<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/include_head.php'; ?>

<body id="home_page_root">
    <div id="home_page_wrapper" class="page_wrapper">
        <!-- header -->
        <header class="home_page_header">
            <div class="container-fluid">
                <div class="row">
                    <!-- nav bar -->
                    <nav class="page_navbar home_navbar navbar fixed-top px-4" id="page_navbar">
                        <!-- nav logo -->
                        <div class="navbar-brand nav_listen_target">
                            <a class="logo" href="<?php echo $site_root_url; ?>">
                                <img src="src/img/favicon/logo.svg" alt="logo" height="24">
                            </a>
                            <a class="h5 mb-0" href="<?php echo $site_root_url; ?>">
                                <?php echo htmlspecialchars($site_info->sitename); ?>
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
                                    <?php echo htmlspecialchars($site_info->information['siteTitle']); ?>
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
                                    <p class=" text-body-tertiary">
                                        &copy;
                                        <?php echo htmlspecialchars($site_info->information['siteCopyright']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Main -->
        <main id="home_main" data-bs-spy="scroll" data-bs-target="#navbar_target" data-bs-root-margin="0px 0px -40%" data-bs-smooth-scroll="true">
            <!-- hero -->
            <section class="page_section" id="home">
                <div class="container">
                    <div class="row">
                        <div class='col-12'>
                            <div class="hero_card px-4 py-4 px-md-0 py-md-0 rounded-5">
                                <!-- hero card -->
                                <div class="col-12">
                                    <h2 class="text-white display-4 mt-3">
                                        <?php echo htmlspecialchars($site_info->frontPageContent["heroGreeting"]); ?>
                                    </h2>
                                    <h1 class="text-white text_hero">
                                        <?php echo $site_info->frontPageContent["heroHeadline"]; ?>
                                    </h1>
                                </div>
                                <div class='col-md-7 col-lg-5'>
                                    <div class="text-white">
                                        <p class="lead text-light">
                                            <?php echo htmlspecialchars($site_info->frontPageContent["heroIntroduction"]); ?>
                                        </p>
                                    </div>
                                    <div class="text-white-50">
                                        <?php
                                        foreach ($site_info->frontPageContent["heroParagraphsArray"] as $heroParagraph) {
                                            $heroParagraph = htmlspecialchars($heroParagraph);
                                            echo "<p>$heroParagraph</p> ";
                                        }
                                        ?>
                                    </div>
                                    <div class="call_to_action_group mb-4 mt-5">
                                        <a class="hero_btn btn btn-dark btn-lg border-0 rounded-pill px-5" href="#projects">
                                            <?php echo htmlspecialchars($site_info->frontPageContent["heroCallToAction"]); ?>
                                            <i class="bi bi-arrow-down-short align-middle"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg_container">
                    <!-- hero decoration -->
                    <div class="hero_decoration_container">
                        <!-- <div id="particles-js"></div> -->

                        <canvas id="canvas-bg" width="800" height="800">
                            <!-- Your Browser Don't Support Canvas, Please Download Chrome ^_^ -->
                        </canvas>
                        <div class="extra_wrapper">
                            <div class="container-xxl">
                                <div class="row">
                                    <div class="col-12 col-md-7 offset-md-5 col-lg-8 offset-lg-4 col-xl-8 offset-xl-4 bg_positioner none_select">
                                        <div class="dec_bg">
                                        </div>
                                        <div class="dec_items">
                                            <div id="hero_moveable">
                                                <div class="hero_self_tilting">
                                                    <div class="hero_tilting_group">
                                                        <img class="hero_decoration ztrans_0" src="src/img/ui-dec-00.png?v=h_2" alt="banner decoration image"></img>
                                                        <img class="hero_decoration extra_layer ztrans_1" src="src/img/ui-dec-01.png?v=h_2" alt="banner decoration image" aria-hidden="true"></img>
                                                        <img class="hero_decoration extra_layer ztrans_2" src="src/img/ui-dec-02.png?v=h_2" alt="banner decoration image" aria-hidden="true"></img>
                                                        <img class="hero_decoration extra_layer ztrans_3" src="src/img/ui-dec-03.png?v=h_2" alt="banner decoration image" aria-hidden="true"></img>
                                                        <img class="hero_decoration extra_layer ztrans_4" src="src/img/ui-dec-04.png?v=h_2" alt="banner decoration image" aria-hidden="true"></img>
                                                        <img class="hero_decoration extra_layer ztrans_5" src="src/img/ui-dec-05.png?v=h_2" alt="banner decoration image" aria-hidden="true"></img>
                                                        <img class="hero_decoration extra_layer ztrans_6" src="src/img/ui-dec-06.png?v=h_2" alt="banner decoration image" aria-hidden="true"></img>
                                                        <img class="hero_decoration extra_layer ztrans_7" src="src/img/ui-dec-07.png?v=h_2" alt="banner decoration image" aria-hidden="true"></img>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                            <h2 class=" text-uppercase text-black">Projects</h2>
                        </div>
                    </div>
                </div>
                <div class="container" id="projects_container">
                    <div class="row gy-4 gy-lg-5 gx-xl-5">
                        <?php
                        function createProjectCard($project, $isFeatured)
                        {
                            $projectPath = $project->path . "/";
                            $projectLink = '?project=' . htmlspecialchars($project->id);
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
                            echo "<div class='project_card " . ($isFeatured ? "featured_card" : "") . " card border-0 overflow-hidden rounded-5 bg-white h-100'>";
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
                                    echo "<span class='category-container badge rounded-pill text-secondary fw-normal'>$category</span> ";
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
                        foreach ($projects as $project) {
                            if ($project->indexOrder >= 0 && $project->indexOrder < 10) {
                                createProjectCard($project, true);
                            } elseif ($project->indexOrder >= 10) {
                                createProjectCard($project, false);
                            }
                        }
                        ?>
                    </div>
                </div>
            </section>
            <!-- contact -->
            <section class="page_section" id="contact">
                <div class="bg_container"></div>
                <div class="container">
                    <div class="row gy-5">
                        <div class="col-xl-6 offset-xl-1 col-lg-7 order-2 ">
                            <div class="contact_card rounded-5 text-dark p-5 shadow-lg-5">
                                <h2 class="text-black text_hero2 mb-3">What's up!</h2>
                                <h2 class="text-black">Let's Make Some Noise Together!</h2>
                                <form id="contactForm" class="contact_form my-5 text-dark fw-medium">
                                    <div class="form-floating mb-2 fw-light">
                                        <input id="msg_submit_email" class="form-control border-2 rounded-4 border-secondary fw-light" type="email" name="fromEmail" placeholder="Your email" required>
                                        <label class="floatingInput border-0 bg-transparent" for="msg_submit_email">Your
                                            Email</label>
                                    </div>
                                    <div class="form-floating fw-light">
                                        <textarea id="msg_submit_content" class="form-control border-2 rounded-4 border-secondary fw-light" name="message" placeholder="Your message" style="height: 6rem" required></textarea>
                                        <label class="floatingInput border-0 bg-transparent" for="msg_submit_content">Your
                                            Message</label>
                                    </div>

                                    <div id="formOutput" class="text-dark my-3"></div>

                                    <div class="d-grid gap-2">
                                        <button class="btn btn-dark rounded-pill fw-bold" type="submit">
                                            Send Message
                                        </button>
                                    </div>
                                </form>

                                <h3 class="section_headline text-uppercase visually-hidden">Contact</h3>
                                <div class="contact_sheet text-nowrap">

                                    <h4>EMAIL</h4>
                                    <div class="mb-4">
                                        <a class="text-dark link-offset-3 text-decoration-none" href="mailto:<?php echo htmlspecialchars($site_info->information['myEmail']); ?>">
                                            <?php echo htmlspecialchars($site_info->information['myEmail']); ?>
                                        </a>
                                    </div>

                                    <h4>LOCATION</h4>
                                    <div class="mb-4">
                                        <?php echo htmlspecialchars($site_info->information['myLocation']); ?>
                                    </div>

                                    <h4>SOCIAL NETWORK</h4>
                                    <div class="d-flex gap-4 mb-3">
                                        <div>
                                            <a class="text-dark link-offset-3" target="_blank" href="<?php echo htmlspecialchars($site_info->information['myLinkedin']); ?>">
                                                <i class=" bi bi-linkedin align-middle" style="font-size: 1.5rem;"></i>
                                            </a>
                                        </div>
                                        <div>
                                            <a class="text-dark link-offset-3" target="_blank" href="<?php echo htmlspecialchars($site_info->information['myGithub']); ?>">
                                                <i class=" bi bi-github align-middle" style="font-size: 1.5rem;"></i>
                                            </a>
                                        </div>
                                        <div>
                                            <a class="text-dark link-offset-3" target="_blank" href="<?php echo htmlspecialchars($site_info->information['myTwitter']); ?>">
                                                <i class=" bi bi-twitter align-middle" style="font-size: 1.5rem;"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="contact_dec_flex_wrap h-100 d-flex flex-column justify-content-center align-items-center">
                                <div class="contact_decoration_container none_select">
                                    <div id="contact_moveable">
                                        <div class="contact_self_tilting">
                                            <div class="contact_tilting_group">
                                                <img class="contact_decoration none_select base_layer" src="src/img/phone-base.png?v=z021" alt="image decoration" aria-hidden="true">
                                                <img class="contact_decoration none_select extra_layer extra_rise_btn" src="src/img/phone-btn.png?v=z021" alt="image decoration" aria-hidden="true">
                                                <img class="contact_decoration none_select extra_layer extra_rise_0" src="src/img/phone-face.svg?v=z021" alt="image decoration" aria-hidden="true">
                                                <img class="contact_decoration none_select extra_layer extra_rise_1 " src="src/img/phone-icon.png?v=z021" alt="image decoration" aria-hidden="true">
                                                <img class="contact_decoration none_select extra_layer extra_rise_2 " src="src/img/phone-icon-1.svg?v=z021" alt="image decoration" aria-hidden="true">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <?php include __DIR__ . '/include_footer.php'; ?>
        <script src="src/js/page_home.js?v=429" type="text/javascript"></script>
    </div>
</body>

</html>