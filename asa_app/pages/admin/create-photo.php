<?php
require_once __DIR__ . '/../../../asa_config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/guard.php';
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/image.php';

$errors = [];

$title = "";
$description = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if ($title === "") {
        $errors[] = "Title is required.";
    }

    if (
        !isset($_FILES['image']) ||
        $_FILES['image']['error'] != UPLOAD_ERR_OK
    ) {
        $errors[] = "Photo is required.";
    }

    $image = null;

    if (empty($errors)) {
        $extension = strtolower(
            pathinfo(
                $_FILES['image']['name'],
                PATHINFO_EXTENSION
            )
        );

        $allowed = [
            'jpg',
            'jpeg',
        ];

        if (!in_array($extension, $allowed)) {
            $errors[] = "Invalid image format.";
        } else {
            $filename = uniqid();

            $result = createImageVersions(
                $_FILES['image']['tmp_name'],
                __DIR__ . '/../../../public_html/uploads/gallery',
                $filename
            );

            if (!$result) {
                $errors[] = "Unable to process image.";
            } else {
                $image = $result;
            }
        }
    }

    if (empty($errors)) {

        $stmt = $pdo->prepare("
            INSERT INTO galleries
            (
                title,
                description,
                image
            )
            VALUES
            (
                ?,
                ?,
                ?
            )
        ");

        $stmt->execute([
            $title,
            $description ?: null,
            $image
        ]);

        header("Location: /admin/gallery");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Tambah Pengelola - <?php echo APP_NAME;?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        require_once __DIR__ . '/../../components/admin/head_link.php';
        ?>
    </head>

    <body>
        <div id="overlay" class="overlay"></div>
        <!-- TOPBAR -->
        <?php
        require_once __DIR__ . '/../../components/admin/navbar.php';
        ?>

        <!-- SIDEBAR -->
        <?php
        require_once __DIR__ . '/../../components/admin/sidebar.php';
        ?>

        <!-- MAIN CONTENT -->
        <main id="content" class="content py-10">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                            <div class="">
                                <h1 class="fs-3 mb-1">Tambah Foto</h1>
                                <p class="mb-0">Tambah foto galeri.</p>
                            </div>
                            <div>
                                <a href="/admin/users" class="btn btn-success">
                                    User List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body p-4">

                                <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php endif; ?>

                                <form method="post" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Photo Title
                                        </label>
                                        <input
                                            type="text"
                                            name="title"
                                            class="form-control"
                                            value="<?= htmlspecialchars($title) ?>"
                                            required
                                        >
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Photo
                                        </label>
                                        <input
                                            type="file"
                                            name="image"
                                            class="form-control"
                                            accept=".jpg,.jpeg,.png,.webp"
                                            required
                                        >
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">
                                            Description
                                        </label>
                                        <textarea
                                            name="description"
                                            rows="5"
                                            class="form-control"
                                        ><?= htmlspecialchars($description) ?></textarea>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button
                                            type="submit"
                                            class="btn btn-success"
                                        >
                                            Save Photo
                                        </button>
                                        <a
                                            href="/admin/gallery"
                                            class="btn btn-secondary"
                                        >
                                            Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                require_once __DIR__ . '/../../components/admin/footer.php';
                ?>
            </div>
        </main>

        <?php
        require_once __DIR__ . '/../../components/admin/body_script.php';
        ?>
    </body>
</html>