<?php
require_once __DIR__ . '/../../../asa_config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/guard.php';
require_once __DIR__ . '/../../core/database.php';

$errors = [];

$title = "";
$description = "";
$targetAmount = "";
$startDate = "";
$endDate = "";
$status = "draft";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $targetAmount = trim($_POST['target_amount']);
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $status = $_POST['status'];

    if ($title === "") {
        $errors[] = "Title is required.";
    }

    if ($description === "") {
        $errors[] = "Description is required.";
    }

    if ($targetAmount === "") {
        $errors[] = "Target amount is required.";
    } elseif (!is_numeric($targetAmount) || $targetAmount < 0) {
        $errors[] = "Target amount is invalid.";
    }

    if ($startDate === "") {
        $errors[] = "Start date is required.";
    }

    if (
        $startDate !== "" &&
        $endDate !== "" &&
        strtotime($endDate) < strtotime($startDate)
    ) {
        $errors[] = "End date cannot be earlier than start date.";
    }

    if (!in_array($status, [
        'draft',
        'active',
        'completed',
        'cancelled'
    ])) {
        $errors[] = "Status is invalid.";
    }

    // Generate slug
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');

    if (empty($errors)) {

        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM campaigns
            WHERE slug = ?
        ");

        $stmt->execute([$slug]);

        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Another campaign already has the same title.";
        }

    }

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

        $image = date('YmdHis') . '-' . uniqid() . '.' . $extension;

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            __DIR__ . '/../../../public_html/uploads/campaign/' . $image
        );

    }

    if (empty($errors)) {

        $stmt = $pdo->prepare("
            INSERT INTO campaigns
            (
                title,
                slug,
                description,
                target_amount,
                start_date,
                end_date,
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
                ?,
                ?,
                ?,
                ?
            )
        ");

        $stmt->execute([
            $title,
            $slug,
            $description,
            $targetAmount,
            $startDate,
            $endDate ?: null,
            $image,
            $status,
            $_SESSION['user']['id']
        ]);

        header("Location: /admin/campaigns");
        exit;

    }

}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Tambah Campaign - <?php echo APP_NAME;?></title>
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
                                <h1 class="fs-3 mb-1">Tambah Campaign</h1>
                                <p class="mb-0">Tambah data campaign.</p>
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
                                        <label class="form-label">Title</label>
                                        <input
                                            type="text"
                                            name="title"
                                            class="form-control"
                                            value="<?= htmlspecialchars($title) ?>"
                                            required
                                        >
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Image</label>
                                        <input
                                            type="file"
                                            name="image"
                                            class="form-control"
                                            accept=".jpg,.jpeg,.png,.webp"
                                        >
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea
                                            name="description"
                                            rows="6"
                                            class="form-control"
                                            required
                                        ><?= htmlspecialchars($description) ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Target Amount</label>
                                        <input
                                            type="number"
                                            name="target_amount"
                                            class="form-control"
                                            value="<?= htmlspecialchars($targetAmount) ?>"
                                            min="0"
                                            step="0.01"
                                            required
                                        >
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Start Date</label>
                                            <input
                                                type="date"
                                                name="start_date"
                                                class="form-control"
                                                value="<?= htmlspecialchars($startDate) ?>"
                                                required
                                            >
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">End Date</label>
                                            <input
                                                type="date"
                                                name="end_date"
                                                class="form-control"
                                                value="<?= htmlspecialchars($endDate) ?>"
                                            >
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Status</label>
                                        <select
                                            name="status"
                                            class="form-select"
                                            required
                                        >
                                            <option value="draft"
                                                <?= $status == "draft" ? "selected" : "" ?>>
                                                Draft
                                            </option>

                                            <option value="active"
                                                <?= $status == "active" ? "selected" : "" ?>>
                                                Active
                                            </option>

                                            <option value="completed"
                                                <?= $status == "completed" ? "selected" : "" ?>>
                                                Completed
                                            </option>

                                            <option value="cancelled"
                                                <?= $status == "cancelled" ? "selected" : "" ?>>
                                                Cancelled
                                            </option>
                                        </select>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button
                                            type="submit"
                                            class="btn btn-success"
                                        >
                                            Save Campaign
                                        </button>
                                        <a
                                            href="/admin/campaigns"
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