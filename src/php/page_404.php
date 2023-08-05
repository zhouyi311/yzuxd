<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Error 404</title>

    <style>
        * {
            transition: all 0.6s;
        }

        html {
            height: 100%;
        }

        body {
            font-family: 'Roboto', 'Lato', sans-serif;
            color: #888;
            margin: 0;
        }

        #main {
            display: table;
            width: 100%;
            height: 100vh;
            text-align: center;
        }

        .fof {
            display: table-cell;
            vertical-align: middle;
        }

        .fof h1 {
            font-size: 50px;
            display: inline-block;
            padding-right: 12px;
            animation: type .5s alternate infinite;
        }

        .fw-bold {
            font-weight: bold;
        }

        @keyframes type {
            from {
                box-shadow: inset -3px 0px 0px #888;
            }

            to {
                box-shadow: inset -3px 0px 0px transparent;
            }
        }
    </style>

</head>

<body>
    <div id="main">
        <div class="fof">
            <h1>
                Error <?php echo isset($_POST['error_type']) ? $_POST['error_type'] : '404'; ?>
            </h1>
            <h2>
                <?php echo isset($_POST['error_title']) ? $_POST['error_title'] : 'Oops! Something went wrong.'; ?>
            </h2>
            <p>
                <?php echo isset($_POST['error_message']) ? $_POST['error_message'] : 'We were unable to find the page.'; ?>
            </p>
            <p>
                <a class="fw-bold" href="/">Return To Homepage</a>
            </p>
        </div>
    </div>

    <script>
    </script>
</body>

</html>