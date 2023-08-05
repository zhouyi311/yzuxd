<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php
        if (isset($project)) {
            echo htmlspecialchars($project->title) . " | " . htmlspecialchars($site_info->sitename);
        } else {
            echo ($site_info->sitename) . " | " . ($site_info->information['siteTitle']);
        }
        ?>
    </title>

    <meta name="description"
        content="<?php echo isset($project) ? htmlspecialchars($project->summary['text']) : htmlspecialchars($site_info->information['siteDescription']); ?>">

    <link rel="apple-touch-icon" sizes="180x180" href="src/img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="src/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="src/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="src/img/favicon/site.webmanifest">

    <meta property="og:image" content="src/img/favicon/logo-social-media.png">
    <meta property="twitter:image" content="src/img/favicon/logo-social-media.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="src/css/styles.css?v=<?php echo time(); ?>">
</head>