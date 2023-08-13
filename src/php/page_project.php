<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . '/class_info_site.php';
include_once __DIR__ . '/class_info_project.php';
include_once __DIR__ . '/class_renderer_header.php';
include_once __DIR__ . '/class_renderer_article.php';

$siteInfo = SiteInfo::loadInfo();

$projectKey = $siteInfo->siteStructureInfo['projectPageQueryKey'];
$projectId = $_GET[$projectKey];

$project = ProjectInfo::loadById($projectId);
$articleRenderer = new ArticleContentRenderer($project);

if (!$project) {
    header("HTTP/1.0 404 Not Found");
    $_POST['error_title'] = 'Page does not exist';
    $_POST['error_message'] = 'Please ensure that the page ID is valid.';
    if (!@include(__DIR__ . '/page_404.php')) {
        echo "<h1>Error 404</h1>";
        echo "<h2>" . $_POST['error_title'] . "</h2>";
        echo "<p>" . $_POST['error_message'] . "</p>";
        echo "<p><a href='/'>Go to homepage</a></p>";
    }
    exit;
}

// pw check process
session_start();
$project_pw = $project->password;
// Check form submission
if (isset($_POST['password']) && !isset($_SESSION['form_processed'])) {
    if ($_POST['password'] === $project->password) {
        $_SESSION['authenticated'] = true;
        $_SESSION['form_processed'] = true; // Set a flag to indicate form processed
        header("Location: ?{$projectKey}={$projectId}");
        exit;
    } else {
        $_SESSION['error_message'] = "Incorrect password!"; // Optionally, you can set an error message here if the password is incorrect.
    }
}
// Check pw requirement and if the user is authenticated for this project
$isPasswordRequired = !empty($project_pw) && $project_pw !== 'no';
$isAuthenticated = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;

// If a password is posted and it matches the project password, set the authentication session
if (!$isAuthenticated && $isPasswordRequired && isset($_POST['password']) && $_POST['password'] === $project_pw) {
    $_SESSION['authenticated'] = true;
    $isAuthenticated = true;
    header("Location: ?{$projectKey}={$projectId}");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/include_head.php'; ?>

<body id='proj_page_root'>
    <div id="project_page_wrapper" class="page_wrapper">
        <!-- header -->
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
                                            echo "<a class='btn btn-light text-truncate px-4' href='?{$projectKey}={$project->last->id}'><span class='fw-bold me-1'>Prev:</span><span class='inner_text fw-normal w-100'>{$project->last->title}</span></a>";
                                        }
                                        if ($existNextProject) {
                                            echo "<a class='btn btn-light text-truncate px-4' href='?{$projectKey}={$project->next->id}'><span class='fw-bold me-1'>Next:</span><span class='inner_text fw-normal w-100'>{$project->next->title}</span></a>";
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
        <!-- Main -->
        <main id="project_main" data-bs-spy="scroll" data-bs-target="#navbar_target" data-bs-root-margin="0px 0px -40%" data-bs-smooth-scroll="true">
            <article class="project_article" id="project_<?php echo $projectId ?>">
                <header class="project_article_header" id="page_home">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 project_header_container">
                                <h1 class="article_title text-dark mt-4">
                                    <?php echo htmlspecialchars($project->title); ?>
                                </h1>
                                <?php
                                if (!empty($project->summary['subhead'])) {
                                    echo '<p class="h5 article_subhead text-secondary">';
                                    echo htmlspecialchars($project->summary['subhead']) . "</p>";
                                }
                                ?>
                                <div class="row">
                                    <div class="col-lg-6 order-lg-2 col-xl-4 offset-xl-1 pt-3">
                                        <div class="project_intro_image none_select">
                                            <img class="intro_image none_select" src="<?php echo $project->path . "/" . htmlspecialchars($project->summary['summaryImage']); ?>"
                                                alt="<?php echo htmlspecialchars($project->title); ?> headline image">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 project_intro_summary py-5 pt-lg-3 mb-4 mb-lg-5">
                                        <!-- <div class="project_intro_summary_wrapper "> -->
                                        <?php
                                        echo '<div class="text-body-tertiary d-flex flex-wrap align-items-center gap-3 mb-4 mb-lg-5">';
                                        echo !empty($project->summary['caption']) ? '<div class="fst-italic">' . htmlspecialchars($project->summary['caption']) . '</div>' : "";
                                        if (!empty($project->summary['categories'])) {
                                            echo "<div class='article_category d-flex gap-2'>";
                                            foreach ($project->summary['categories'] as $category) {
                                                $category = htmlspecialchars($category);
                                                echo "<span class='category-container badge bg_subtle text-secondary rounded-pill '>$category</span> ";
                                            }
                                            echo "</div>";
                                        }
                                        echo '</div>';
                                        $summaryText = isset($project->summary['text']) ? $project->summary['text'] : [];
                                        foreach ($summaryText as $textItem) {
                                            echo "<p class='text-body'>" . htmlspecialchars($textItem) . "</p>";
                                        }
                                        if (!empty($project->summary['demoLink'])) {
                                            echo "<div class='d-flex gap-3 mt-4'>";
                                            $demo_link = ($project->summary['demoLink']);
                                            foreach ($demo_link as $name => $link) {
                                                echo "<a class='btn btn-light bg_subtle rounded-pill px-4' href='{$link}'>{$name}</a>";
                                            }
                                            echo '</div>';
                                        }
                                        ?>
                                        <!-- </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
                <!-- article sections content -->
                <?php if ($isAuthenticated || !$isPasswordRequired): ?>
                    <?php $articleRenderer->render(); ?>
                <?php else: ?>
                    <div class="container" id="enter_password">
                        <div class="row">
                            <div class="col-12">
                                <div class="pw_form_container p-5 my-5 bg-light">
                                    <div class="row">
                                        <div class="col-md-6 offset-md-3 col-xl-4 offset-xl-4 ">
                                            <form class="pw_form d-flex flex-column gap-3" method="POST">
                                                <h4>Enter Password</h4>
                                                <div class="form-floating">
                                                    <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                                                    <label for="floatingPassword">Password</label>
                                                </div>
                                                <?php
                                                if (!empty($_POST['password']) && !$isAuthenticated) {
                                                    $posted_pw = isset($_POST['password']) ? $_POST['password'] : 'No posted pw';
                                                    echo 'Wrong password';
                                                }
                                                ?>
                                                <button type="submit" class="btn btn-dark rounded-pill">Submit</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </article>
            <div class="load_more_section my-5">
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <div class='last_next_selector overflow-hidden text-dark'>
                                <?php
                                $hasLastProject = isset($project->last->title);
                                $hasNextProject = isset($project->next->title);
                                $urlQueryKeyStatement = "?{$projectKey}=";
                                function generateCTAContent($hasProject, $urlQueryKeyStatement, $targetProject, $type, $defaultMessage)
                                {
                                    $iconLast = $type === "Previous" ? '<i class="bi bi-chevron-bar-left align-middle"></i>' : null;
                                    $iconNext = $type === "Next" ? '<i class="bi bi-chevron-bar-right align-middle"></i>' : null;
                                    $lastNextClass = $type === "Previous" ? 'pre_case' : 'next_case';
                                    $caseTitle = $type . "";
                                    $projectPath = $targetProject ? $urlQueryKeyStatement . $targetProject->id : "#";
                                    $projectTitle = $targetProject ? $targetProject->title : $defaultMessage;
                                    $linkClass = $hasProject ? "" : "disabled";

                                    return "<a href='{$projectPath}' class='case {$lastNextClass} {$linkClass} p-4 py-3 text-decoration-none'>
                                            <div class='cta cta_title fw-bold'>
                                                {$iconLast}
                                                {$caseTitle}
                                                {$iconNext}
                                            </div>
                                            <div class='cta cta_name fw-medium'>
                                                {$projectTitle}
                                            </div>
                                        </a>";
                                }
                                ?>
                                <?= generateCTAContent($hasLastProject, $urlQueryKeyStatement, $project->last ?? null, "Previous", "You're at the top"); ?>
                                <?= generateCTAContent($hasNextProject, $urlQueryKeyStatement, $project->next ?? null, "Next", "You've reached the end"); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lightbox_overlay" id="lightboxOverlay">
                <img src="" id="lightboxImage">
                <button class="lightbox_close" id="lightboxClose"><span class="bi bi-x-lg align-middle"></span></button>
                <div class="btn-group shadow lightbox_controls rounded-pill overflow-hidden">
                    <button class="btn fw-medium px-4 border-0" id="lightboxZoomOut"><span class="bi bi-zoom-out align-middle"><span class="ms-2">Zoom Out</span></button>
                    <button class="btn fw-medium px-4 border-0" id="lightboxReset"><span class="bi bi-arrow-counterclockwise align-middle"><span class="ms-2">Reset</span></button>
                    <button class="btn fw-medium px-4 border-0" id="lightboxZoomIn"><span class="bi bi-zoom-in align-middle"><span class="ms-2">Zoom In</span></button>
                </div>

            </div>
        </main>
        <?php include __DIR__ . '/include_footer.php'; ?>
        <script src="src/js/page_project.js?v=586" type="text/javascript"></script>
    </div>
</body>

</html>
<?php
// session_destroy();
// for development only
?>