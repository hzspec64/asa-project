<?php
require_once __DIR__ . '/../../../asa_config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/guard.php';
require_once __DIR__ . '/../../core/database.php';

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($id <= 0) {
    header("Location: /admin/contacts");
    exit;
}

$stmt = $pdo->prepare("
    SELECT *
    FROM contacts
    WHERE id = ?
");

$stmt->execute([$id]);

$contact = $stmt->fetch();

if (!$contact) {
    header("Location: /admin/contacts");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {

        $stmt = $pdo->prepare("
            DELETE FROM contacts
            WHERE id = ?
        ");

        $stmt->execute([$id]);

        header("Location: /admin/contacts");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>View Contact - <?php echo APP_NAME;?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        require_once __DIR__ . '/../../components/admin/head_link.php';
        ?>
    </head>
    <body>
        <div id="overlay" class="overlay"></div>

        <?php
        require_once __DIR__ . '/../../components/admin/navbar.php';
        require_once __DIR__ . '/../../components/admin/sidebar.php';
        ?>

        <main id="content" class="content py-10">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                            <div>
                                <h1 class="fs-3 mb-1">View Contact</h1>
                                <p class="mb-0">Detail pesan dari pengunjung.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body p-4">

                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <div class="form-control bg-light">
                                        <?= htmlspecialchars($contact['name']) ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <div class="form-control bg-light">
                                        <?= htmlspecialchars($contact['email']) ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Subject</label>
                                    <div class="form-control bg-light">
                                        <?= htmlspecialchars($contact['subject']) ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Message</label>
                                    <div class="form-control bg-light" style="min-height: 180px; white-space: pre-wrap;">
                                        <?= htmlspecialchars($contact['message']) ?>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Submitted</label>
                                    <div class="form-control bg-light">
                                        <?= date('d M Y H:i', strtotime($contact['created_at'])) ?>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <a
                                        href="/admin/contacts"
                                        class="btn btn-secondary"
                                        onclick="history.back(); return false;"
                                    >
                                        Back
                                    </a>

                                    <form
                                        method="post"
                                        class="d-inline"
                                        onsubmit="return confirm('Delete this contact message? This action cannot be undone.');"
                                    >
                                        <input
                                            type="hidden"
                                            name="action"
                                            value="delete"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-danger"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                </div>

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