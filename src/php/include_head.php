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

    <meta name="description" content="<?php
    if (isset($project)) {
        $summaryText = $project->summary['text'];
        echo htmlspecialchars(is_array($summaryText) ? implode(' ', $summaryText) : $summaryText);
    } else {
        echo htmlspecialchars($site_info->information['siteDescription']);
    }
    ?>">


    <link rel="apple-touch-icon" sizes="180x180" href="src/img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="src/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="src/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="src/img/favicon/site.webmanifest">

    <meta property="og:image" content="src/img/favicon/logo-social-media.png">
    <meta property="twitter:image" content="src/img/favicon/logo-social-media.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&family=Source+Sans+3:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link rel="stylesheet" href="src/css/styles.css?v=3256">
</head>