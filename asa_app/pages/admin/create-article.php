<?php
require_once __DIR__ . '/../../../asa_config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/guard.php';
require_once __DIR__ . '/../../core/database.php';

$errors = [];

$title = "";
$content = "";
$status = "draft";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $status = $_POST['status'];

    if ($title === "") {
        $errors[] = "Title is required.";
    }

    if ($content === "") {
        $errors[] = "Content is required.";
    }

    if (!in_array($status, ['draft', 'published'])) {
        $errors[] = "Status is invalid.";
    }

    // Generate slug
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');

    // Check duplicate slug
    if (empty($errors)) {

        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM articles
            WHERE slug = ?
        ");

        $stmt->execute([$slug]);

        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Another article already has the same title.";
        }
    }

    // Upload image
    $image = null;

    if (
        isset($_FILES['image']) &&
        $_FILES['image']['error'] == UPLOAD_ERR_OK
    ) {

        $extension = strtolower(
            pathinfo(
                $_FILES['image']['name'],
                PATHINFO_EXTENSION
            )
        );

        $image = uniqid() . "." . $extension;

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            __DIR__ . '/../../../public_html/uploads/article/' . $image
        );
    }

    if (empty($errors)) {

        $stmt = $pdo->prepare("
            INSERT INTO articles
            (
                title,
                slug,
                content,
                image,
                status,
                user_id
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
            )
        ");

        $stmt->execute([
            $title,
            $slug,
            $content,
            $image,
            $status,
            $_SESSION['user']['id']
        ]);

        header("Location: /admin/articles");
        exit;
    }

}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Tambah Artikel - <?php echo APP_NAME;?></title>
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
                                <h1 class="fs-3 mb-1">Tambah Artikel</h1>
                                <p class="mb-0">Buat artikel baru</p>
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
                                            Title
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
                                            Image
                                        </label>

                                        <input
                                            type="file"
                                            name="image"
                                            class="form-control"
                                            accept=".jpg,.jpeg,.png,.webp"
                                        >

                                    </div>

                                    <div class="mb-3">

                                        <label class="form-label">
                                            Content
                                        </label>

                                        <textarea
                                            name="content"
                                            rows="10"
                                            class="form-control"
                                            required
                                        ><?= htmlspecialchars($content) ?></textarea>

                                    </div>

                                    <div class="mb-4">

                                        <label class="form-label">
                                            Status
                                        </label>

                                        <select
                                            name="status"
                                            class="form-select"
                                            required
                                        >

                                            <option value="draft"
                                                <?= $status == "draft" ? "selected" : "" ?>
                                            >
                                                Draft
                                            </option>

                                            <option value="published"
                                                <?= $status == "published" ? "selected" : "" ?>
                                            >
                                                Published
                                            </option>

                                        </select>

                                    </div>

                                    <div class="d-flex gap-2">

                                        <button
                                            class="btn btn-success"
                                            type="submit"
                                        >
                                            Save Article
                                        </button>

                                        <a
                                            href="/admin/articles"
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