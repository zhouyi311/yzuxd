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

session_start();


// load projects
require_once __DIR__ . '/class_site_info.php';
$site_info = SiteInfo::loadInfo();

include_once __DIR__ . '/class_project_info.php';


$projectId = $_GET['project']; // Retrieve the 'project' query parameter

$project = ProjectInfo::loadById($projectId); // Load the project with the given ID


if (!$project) {
    header("HTTP/1.0 404 Not Found");
    $_POST['error_title'] = 'Project does not exist';
    $_POST['error_message'] = 'Please ensure that the project ID is valid.';
    if (!@include(__DIR__ . '/page_404.php')) {
        echo "<h1>Error 404</h1>";
        echo "<h2>" . $_POST['error_title'] . "</h2>";
        echo "<p>" . $_POST['error_message'] . "</p>";
        echo "<p><a href='/'>Go to homepage</a></p>";
    }
    exit;
}

// Check form submission
if (isset($_POST['password']) && !isset($_SESSION['form_processed'])) {
    if ($_POST['password'] === $project->password) {
        $_SESSION['authenticated'] = true;
        $_SESSION['form_processed'] = true; // Set a flag to indicate form processed
        header("Location: ?project=$projectId");
        exit;
    } else {
        $_SESSION['error_message'] = "Incorrect password!"; // Optionally, you can set an error message here if the password is incorrect.
    }
}

$project_pw = $project->password;

// Check if the password is required for this project
$isPasswordRequired = isset($project_pw) && $project_pw !== '' && $project_pw !== 'no';

// Check if the user is authenticated for this project
$isAuthenticated = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;

// If a password is posted and it matches the project password, set the authentication session
if (!$isAuthenticated && $isPasswordRequired && isset($_POST['password']) && $_POST['password'] === $project_pw) {
    $_SESSION['authenticated'] = true;
    $isAuthenticated = true;
    header("Location: ?project=$projectId");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/include_head.php'; ?>

<body id='body_root'>
    <div id="project_page_wrapper" class="page_wrapper">
        <!-- header -->
        <header class="project_header">
            <div class="container-fluid">
                <div class="row">
                    <!-- nav bar -->
                    <nav class="page_navbar project_navbar navbar fixed-top px-4" id="page_navbar">
                        <!-- nav logo -->
                        <div class="navbar-brand">
                            <a class="" href="<?php echo $site_root_url; ?>">
                                <img src="src/img/favicon/logo.svg" alt="logo" height="24">
                                <?php echo htmlspecialchars($site_info->sitename); ?>
                            </a>
                            <span class="project_name text-secondary fw-light fs-6">
                                <?php echo $project->title ? '| ' . htmlspecialchars($project->title) : ''; ?>
                            </span>
                        </div>
                        <!-- nav btn -->
                        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <!-- drawer -->
                        <div class="offcanvas offcanvas-end px-4" data-bs-scroll="true" tabindex="-1"
                            id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                            <div class="offcanvas-header">
                                <h3 class="offcanvas-title h6 text-secondary" id="offcanvasNavbarLabel">
                                    <?php echo htmlspecialchars($site_info->information['siteTitle']); ?>
                                </h3>
                                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                    aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
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
                        </div>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Main -->
        <main id="project_main" data-bs-spy="scroll" data-bs-target="#navbar_target" data-bs-root-margin="0px 0px -40%"
            data-bs-smooth-scroll="true">
            <article class="project_case_study">
                <header class="project_header">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 project_header_container">
                                <h1>
                                    <?php echo htmlspecialchars($project->title); ?>
                                </h1>
                                <p>
                                    <?php echo htmlspecialchars($project->date); ?>
                                </p>
                                <p>Categories:
                                    <?php echo htmlspecialchars(implode(', ', $project->categories)); ?>
                                </p>
                                <?php
                                $summaryText = $project->summary['text'];
                                foreach ($summaryText as $textItem) {
                                    echo "<p>" . htmlspecialchars($textItem) . "</p>";
                                }
                                ?>
                                <!-- <img src="<?php echo $project->path . htmlspecialchars($project->summary['summaryImage']); ?>"
                                    alt="<?php echo htmlspecialchars($project->title); ?>"> -->


                            </div>
                        </div>
                    </div>
                </header>
                <section class="project_section">
                </section>
            </article>
            <!-- Main content goes here -->
            <?php if ($isAuthenticated || !$isPasswordRequired): ?>
                <!-- // Display the project -->
                <div class="project-content">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">

                                <?php
                                foreach ($project->content as $section) {
                                    echo "<h2>" . htmlspecialchars($section['headline']) . "</h2>";

                                    foreach ($section['content'] as $contentItem) {
                                        $type = $contentItem['type'];
                                        $content = $contentItem['content'];

                                        if ($type === 'text') {
                                            echo "<p>" . htmlspecialchars($content) . "</p>";
                                        } elseif ($type === 'image') {
                                            echo "<img src='" . $project->path . $content . "'>";
                                        } elseif ($type === 'full-bleed-image') {
                                            echo "<img class='full-bleed' src='" . $project->path . $content . "'>";
                                        } elseif ($type === 'video') {
                                            // Handle video content
                                        } elseif ($type === 'auto-play-video') {
                                            // Handle auto-play video content
                                        } elseif ($type === 'code') {
                                            // Handle code content
                                        }
                                    }
                                }
                                ?>

                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="pw_form_container p-5 my-5 bg-light">
                                <div class="row">
                                    <div class="col-md-6 offset-md-3 col-xl-4 offset-xl-4 ">
                                        <form class="pw_form d-flex flex-column gap-3" method="POST">
                                            <h4>Enter Password</h4>
                                            <div class="form-floating">
                                                <input type="password" name="password" class="form-control"
                                                    id="floatingPassword" placeholder="Password" required>
                                                <label for="floatingPassword">Password</label>
                                            </div>
                                            <?php
                                            if (!empty($_POST['password']) && !$isAuthenticated) {
                                                $posted_pw = isset($_POST['password']) ? $_POST['password'] : 'No posted pw';
                                                echo 'Wrong password';
                                            }
                                            ?>
                                            <button type="submit" class="btn btn-dark">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>


            <?php

            ?>

            <div class="load_more_section">
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <div class='last_next_selector gap-1 d-flex'>
                                <?php
                                $hasLastProject = isset($project->last->title);
                                $hasNextProject = isset($project->next->title);
                                function generateCTAContent($hasProject, $targetProject, $type, $defaultMessage)
                                {
                                    $iconLast = $type === "Previous" ? '<i class="bi bi-chevron-left"></i>' : '';
                                    $iconNext = $type === "Next" ? '<i class="bi bi-chevron-right"></i>' : '';
                                    $lastNextClass = $type === "Previous" ? 'pre_case' : 'next_case';
                                    $caseTitle = $type . "";
                                    $projectPath = $targetProject ? '?project=' . $targetProject->id : "#";
                                    $projectTitle = $targetProject ? $targetProject->title : $defaultMessage;
                                    $linkClass = $hasProject ? "" : "disabled";

                                    return "<a href='{$projectPath}' class='case {$lastNextClass} {$linkClass} p-4 text-decoration-none'>
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
                                <?= generateCTAContent($hasLastProject, $project->last ?? null, "Previous", "You're at the top"); ?>
                                <?= generateCTAContent($hasNextProject, $project->next ?? null, "Next", "You've reached the end"); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
        <?php include __DIR__ . '/include_footer.php'; ?>
    </div>
</body>

</html>
<?php
session_destroy();
// for development only
?>