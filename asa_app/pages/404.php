<?php
require_once __DIR__ . '/../core/config.php';
?>
<!doctype html>
<html class="no-js" lang="zxx">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>404 - Page Not Found - <?php echo APP_NAME;?></title>
        <meta name="description" content="The page you are looking for does not exist.">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php
        require_once __DIR__ . '/../components/head_link.php';
        ?>
    </head>
    <body class="d-flex flex-column min-vh-100">
        <?php
        require_once __DIR__ . '/../components/preload.php';
        require_once __DIR__ . '/../components/header.php';
        ?>

        <main class="flex-grow-1 d-flex align-items-center justify-content-center py-5">
            <div class="container text-center">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6">
                        <!-- Big 404 display -->
                        <h1 class="display-1 fw-bold text-success mb-0" style="font-size: 6rem;">404</h1>

                        <!-- Main heading -->
                        <h2 class="fw-semibold mb-3">Page Not Found</h2>

                        <!-- Explanatory message -->
                        <p class="text-muted fs-5 mb-4">
                            Oops! The page you’re looking for doesn’t exist or has been moved.
                        </p>

                        <!-- Call-to-action button -->
                        <a href="/" class="btn btn-success btn-lg rounded-pill px-4 me-2">
                            Return Home
                        </a>
                    </div>
                </div>
            </div>
        </main>

        <?php
        require_once __DIR__ . '/../components/footer.php';
        ?>

        <?php
        require_once __DIR__ . '/../components/body_script.php';
        ?>
    </body>
</html>