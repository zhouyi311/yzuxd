<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . '/class_info_site.php';
include_once __DIR__ . '/class_info_project.php';
include_once __DIR__ . '/class_renderer_header.php';
include_once __DIR__ . '/class_renderer_article.php';

$siteInfo = SiteInfo::loadInfo();

$projectKey = $siteInfo->pageKeys['projectKey'];
$projectId = $_GET[$projectKey];

$project = ProjectInfo::loadById($projectId);

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

//setup renderer
$articleRenderer = new ArticleContentRenderer($project);
$siteHeaderRenderer = new HeaderRenderer('project', $project, $isPasswordRequired, $isAuthenticated);

?>

<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/include_head.php'; ?>

<body id='proj_page_root'>
    <div id="project_page_wrapper" class="page_wrapper">
        <!-- header -->
        <?php
        echo $siteHeaderRenderer->render();
        ?>

        <!-- Main -->
        <main id="project_main" data-bs-spy="scroll" data-bs-target="#navbar_target" data-bs-root-margin="0px 0px -25%" data-bs-smooth-scroll="true">
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