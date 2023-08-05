<?php

require_once __DIR__ . '/class_site_info.php';
$site_info = SiteInfo::loadInfo();

include_once __DIR__ . '/class_project_info.php';

$projectId = $_GET['project']; // Retrieve the 'project' query parameter.

$project = ProjectInfo::loadById($projectId); // Load the project with the given ID

echo $projectID;

if ($project === null) { // No project found for the given ID
    header("HTTP/1.0 404 Not Found");
    $_POST['error_title'] = 'Project Does Not Exist';
    $_POST['error_message'] = 'Please ensure that the project ID is valid.';
    if (!@include(__DIR__ . '/page_404.php')) {
        echo "<h1>Error 404</h1>";
        echo "<h2>" . $_POST['error_title'] . "</h2>";
        echo "<p>" . $_POST['error_message'] . "</p>";
        echo "<p><a href='/'>Go to homepage</a></p>";
    }
    exit;
}

session_start();

if ($project->password !== 'no' && isset($_POST['password']) && $_POST['password'] === $project->password) {
    $_SESSION['authenticated'] = true;
    // Redirect to avoid form re-submission on refresh
    header("Location: ?project=$projectId");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<?php include __DIR__ . '/include_head.php'; ?>

<body class="project_page">
    <!-- header -->
    <header class="project_header">
        <div class="container-fluid">
            <div class="row">
                <!-- nav bar -->
                <nav class="page_navbar project_navbar navbar fixed-top px-4" id="page_navbar">
                    <!-- nav logo -->
                    <div class="navbar-brand">
                        <a class="" href="<?php echo $baseUrl ?>">
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
                    <div class="offcanvas offcanvas-end px-4" data-bs-scroll="true" tabindex="-1" id="offcanvasNavbar"
                        aria-labelledby="offcanvasNavbarLabel">
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
                                    <a class="nav-link" href="#projects"><span class="h3 fw-bold">PROJECTS</span> </a>
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
        <h1>
            <?php echo htmlspecialchars($project->title); ?>
        </h1>
        <p>
            <?php echo htmlspecialchars($project->date); ?>
        </p>
        <p>Categories:
            <?php echo htmlspecialchars(implode(', ', $project->categories)); ?>
        </p>
        <p>
            <?php echo htmlspecialchars($project->summary['text']); ?>
        </p>
        <img src="<?php echo $project->path . htmlspecialchars($project->summary['summaryImage']); ?>"
            alt="<?php echo htmlspecialchars($project->title); ?>">

        <!-- Main content goes here -->
        <?php if (empty($project->password) || $project->password !== 'no' && (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true)): ?>
            <p>This project is password protected. Please enter the password to view the content.</p>
            <form method="POST">
                <input type="password" name="password">
                <input type="submit" value="Submit">
            </form>
        <?php else: ?>
            <div class="project-content">
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
        <?php endif; ?>
    </main>

    <div class="high_holder">
        <p>1<br>2<br>3<br>4<br>5</p>
    </div>
    <?php include __DIR__ . '/include_footer.php'; ?>
</body>

</html>
<?php session_destroy(); ?>