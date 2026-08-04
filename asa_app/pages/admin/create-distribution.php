<?php
require_once __DIR__ . '/../../../asa_config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/guard.php';
require_once __DIR__ . '/../../core/database.php';

$errors = [];

$title = "";
$description = "";
$campaignId = "";
$amount = "";
$distributionDate = "";

// Load campaigns
$stmt = $pdo->query("
    SELECT
        id,
        title
    FROM campaigns
    WHERE status = 'active'
    ORDER BY title
");

$campaigns = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $campaignId = $_POST['campaign_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $amount = trim($_POST['amount']);
    $distributionDate = $_POST['distribution_date'];

    if ($campaignId === "") {
        $errors[] = "Campaign is required.";
    }

    if ($title === "") {
        $errors[] = "Title is required.";
    }

    if ($amount === "") {
        $errors[] = "Amount is required.";
    } elseif (!is_numeric($amount) || $amount < 0) {
        $errors[] = "Amount is invalid.";
    }

    if ($distributionDate === "") {
        $errors[] = "Distribution date is required.";
    }

    if (empty($errors)) {

        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM campaigns
            WHERE id = ?
        ");

        $stmt->execute([$campaignId]);

        if ($stmt->fetchColumn() == 0) {
            $errors[] = "Campaign is invalid.";
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
            __DIR__ . '/../../../public_html/uploads/distribution/' . $image
        );

    }

    if (empty($errors)) {

        $stmt = $pdo->prepare("
            INSERT INTO distributions
            (
                campaign_id,
                title,
                description,
                amount,
                distribution_date,
                image
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
            $campaignId,
            $title,
            $description,
            $amount,
            $distributionDate,
            $image
        ]);

        header("Location: /admin/distributions");
        exit;

    }

}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Tambah Distribusi - <?php echo APP_NAME;?></title>
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
                                <h1 class="fs-3 mb-1">Tambah Distribusi</h1>
                                <p class="mb-0">Tambah data penyaluran donasi</p>
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
                                            Campaign
                                        </label>
                                        <select
                                            name="campaign_id"
                                            class="form-select"
                                            required
                                        >
                                            <option value="">
                                                -- Select Campaign --
                                            </option>
                                            <?php foreach ($campaigns as $campaign): ?>
                                            <option
                                                value="<?= $campaign['id'] ?>"
                                                <?= $campaignId == $campaign['id'] ? 'selected' : '' ?>
                                            >
                                                <?= htmlspecialchars($campaign['title']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
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
                                            Description
                                        </label>
                                        <textarea
                                            name="description"
                                            rows="5"
                                            class="form-control"
                                        ><?= htmlspecialchars($description) ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">
                                            Amount
                                        </label>
                                        <input
                                            type="number"
                                            name="amount"
                                            class="form-control"
                                            min="0"
                                            step="0.01"
                                            value="<?= htmlspecialchars($amount) ?>"
                                            required
                                        >
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">
                                            Distribution Date
                                        </label>
                                        <input
                                            type="date"
                                            name="distribution_date"
                                            class="form-control"
                                            value="<?= htmlspecialchars($distributionDate) ?>"
                                            required
                                        >
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button
                                            type="submit"
                                            class="btn btn-success"
                                        >
                                            Save Distribution
                                        </button>
                                        <a
                                            href="/admin/distributions"
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