<?php
require_once __DIR__ . '/../../../asa_config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/guard.php';
require_once __DIR__ . '/../../core/database.php';

$errors = [];

$campaignId = "";
$donorName = "";
$donorEmail = "";
$donorPhone = "";
$amount = "";
$donationDate = date('Y-m-d\TH:i');
$status = "pending";
$note = "";

// Load campaigns
$stmt = $pdo->query("
    SELECT
        id,
        title
    FROM campaigns
    ORDER BY title
");

$campaigns = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $campaignId = $_POST['campaign_id'];
    $donorName = trim($_POST['donor_name']);
    $donorEmail = trim($_POST['donor_email']);
    $donorPhone = trim($_POST['donor_phone']);
    $amount = trim($_POST['amount']);
    $donationDate = $_POST['donation_date'];
    $status = $_POST['status'];
    $note = trim($_POST['note']);

    if ($campaignId === "") {
        $errors[] = "Campaign is required.";
    }

    if ($donorName === "") {
        $errors[] = "Donor name is required.";
    }

    if (
        $donorEmail !== "" &&
        !filter_var($donorEmail, FILTER_VALIDATE_EMAIL)
    ) {
        $errors[] = "Email is invalid.";
    }

    if ($amount === "") {
        $errors[] = "Amount is required.";
    } elseif (!is_numeric($amount) || $amount <= 0) {
        $errors[] = "Amount is invalid.";
    }

    if ($donationDate === "") {
        $errors[] = "Donation date is required.";
    }

    if (!in_array($status, ['pending', 'paid', 'cancelled'])) {
        $errors[] = "Status is invalid.";
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

    $proofImage = null;

    if (
        isset($_FILES['proof_image']) &&
        $_FILES['proof_image']['error'] == UPLOAD_ERR_OK
    ) {

        $extension = strtolower(
            pathinfo(
                $_FILES['proof_image']['name'],
                PATHINFO_EXTENSION
            )
        );

        $proofImage = date('YmdHis') . '-' . uniqid() . '.' . $extension;

        move_uploaded_file(
            $_FILES['proof_image']['tmp_name'],
            __DIR__ . '/../../../public_html/uploads/donation/' . $proofImage
        );

    }

    if (empty($errors)) {

        $stmt = $pdo->prepare("
            INSERT INTO donations
            (
                campaign_id,
                donor_name,
                donor_email,
                donor_phone,
                amount,
                donation_date,
                status,
                proof_image,
                note
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
            $campaignId,
            $donorName,
            $donorEmail ?: null,
            $donorPhone ?: null,
            $amount,
            $donationDate,
            $status,
            $proofImage,
            $note ?: null
        ]);

        header("Location: /admin/donations");
        exit;

    }

}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Tambah Donasi - <?php echo APP_NAME;?></title>
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
                                <h1 class="fs-3 mb-1">Tambah Pengelola</h1>
                                <p class="mb-0">Pengaturan pengelola</p>
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
                                        <label class="form-label">Campaign</label>
                                        <select name="campaign_id" class="form-select" required>
                                            <option value="">-- Select Campaign --</option>
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
                                        <label class="form-label">Donor Name</label>
                                        <input
                                            type="text"
                                            name="donor_name"
                                            class="form-control"
                                            value="<?= htmlspecialchars($donorName) ?>"
                                            required
                                        >
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email</label>
                                            <input
                                                type="email"
                                                name="donor_email"
                                                class="form-control"
                                                value="<?= htmlspecialchars($donorEmail) ?>"
                                            >
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Phone</label>
                                            <input
                                                type="text"
                                                name="donor_phone"
                                                class="form-control"
                                                value="<?= htmlspecialchars($donorPhone) ?>"
                                            >
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Amount</label>
                                            <input
                                                type="number"
                                                name="amount"
                                                class="form-control"
                                                min="1"
                                                step="0.01"
                                                value="<?= htmlspecialchars($amount) ?>"
                                                required
                                            >
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Donation Date</label>
                                            <input
                                                type="datetime-local"
                                                name="donation_date"
                                                class="form-control"
                                                value="<?= htmlspecialchars($donationDate) ?>"
                                                required
                                            >
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Proof Image</label>
                                        <input
                                            type="file"
                                            name="proof_image"
                                            class="form-control"
                                            accept=".jpg,.jpeg,.png,.webp"
                                        >
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select
                                            name="status"
                                            class="form-select"
                                            required
                                        >
                                            <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>
                                                Pending
                                            </option>
                                            <option value="paid" <?= $status == 'paid' ? 'selected' : '' ?>>
                                                Paid
                                            </option>
                                            <option value="cancelled" <?= $status == 'cancelled' ? 'selected' : '' ?>>
                                                Cancelled
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Note</label>
                                        <textarea
                                            name="note"
                                            rows="4"
                                            class="form-control"
                                        ><?= htmlspecialchars($note) ?></textarea>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button
                                            type="submit"
                                            class="btn btn-success"
                                        >
                                            Save Donation
                                        </button>
                                        <a
                                            href="/admin/donations"
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