<?php
require_once __DIR__ . '/../../../asa_config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/guard.php';
require_once __DIR__ . '/../../core/database.php';
require_once __DIR__ . '/../../core/image.php';

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($id <= 0) {
    header("Location: /admin/articles");
    exit;
}

$stmt = $pdo->prepare("
    SELECT *
    FROM articles
    WHERE id = ?
");

$stmt->execute([$id]);

$article = $stmt->fetch();

if (!$article) {
    header("Location: /admin/articles");
    exit;
}

$errors = [];

$title = $article['title'];
$content = $article['content'];
$status = $article['status'];
$image = $article['image'];

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
            AND id <> ?
        ");

        $stmt->execute([
            $slug,
            $id
        ]);

        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Another article already has the same title.";
        }
    }

    // Upload new image (optional)
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
                __DIR__ . '/../../../public_html/uploads/article',
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
            UPDATE articles
            SET
                title = ?,
                slug = ?,
                content = ?,
                image = ?,
                status = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $title,
            $slug,
            $content,
            $image,
            $status,
            $id
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
                                <h1 class="fs-3 mb-1">Edit Artikel</h1>
                                <p class="mb-0">Perbarui artikel.</p>
                            </div>
                            <div>
                                <a href="/admin/articles" class="btn btn-success">
                                    Article List
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
                                    <?php if ($image): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Current Image</label>
                                        <div>
                                            <img
                                                src="/uploads/article/<?= htmlspecialchars($image) ?>"
                                                class="img-thumbnail"
                                                style="max-width:250px"
                                            >
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Replace Image
                                        </label>
                                        <small class="text-muted d-block mb-2">
                                            Leave empty to keep the current image.
                                        </small>
                                        <input
                                            type="file"
                                            name="image"
                                            class="form-control"
                                            accept=".jpg,.jpeg"
                                        >
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Content
                                        </label>
                                        <textarea
                                            name="content"
                                            id="summernote"
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
                                            Update Article
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