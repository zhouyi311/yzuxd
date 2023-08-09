<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Error 404</title>

    <style>

        html {
            height: 100%;
        }

        body {
            font-family: 'Roboto', 'Lato', sans-serif;
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
            color: #888;
            font-size: 50px;
            display: inline-block;
            padding-right: 12px;
            animation: type .5s alternate infinite;
            transition: all 0.6s;
        }

        p{
            color: #888;
        }

        .fw-bold {
            font-weight: bold;
        }

        .return_home{
            margin-top: 16px;
            font-weight: bold;
            font-size: 18px;
            padding: 16px 32px;
            display: inline-block;
            background-color: #d00058;
            text-decoration: none;
            color: white;
            border-radius: 12px;
            transition: 0.3s;
        }
        .return_home:hover{
            background-color: #0069c2;
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
                Error
                <?php echo isset($_POST['error_type']) ? $_POST['error_type'] : '404'; ?>
            </h1>
            <h2>
                <?php echo isset($_POST['error_title']) ? $_POST['error_title'] : 'Oops! Something went wrong.'; ?>
            </h2>
            <p>
                <?php echo isset($_POST['error_message']) ? $_POST['error_message'] : 'We were unable to find the page.'; ?>
            </p>
            <a class="return_home" href="/">Return To Homepage</a>
        </div>
    </div>
    <script>
    </script>
</body>

</html>