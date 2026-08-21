<?php
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../../asa_config.php';
require_once __DIR__ . '/../core/database.php';

$errors = [];

$name = "";
$email = "";
$amount = "";
$note = "";
$campaignId = "";

// Kampanye aktif untuk pilihan donasi
$campaigns = $pdo->query("
    SELECT c.*,
        (
            SELECT COALESCE(SUM(amount), 0)
            FROM donations d
            WHERE d.campaign_id = c.id AND d.status = 'paid'
        ) AS raised
    FROM campaigns c
    WHERE c.status = 'active'
    ORDER BY c.created_at DESC
")->fetchAll();

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['donor_name'] ?? '');
    $email = trim($_POST['donor_email'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $note = trim($_POST['note'] ?? '');
    $campaignId = $_POST['campaign_id'] ?? '';
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (empty($submittedToken) || !hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        $errors[] = "Permintaan tidak valid. Silakan coba lagi.";
    }

    if ($name === "") {
        $errors[] = "Nama wajib diisi.";
    }

    if ($campaignId === "") {
        $errors[] = "Pilih program donasi terlebih dahulu.";
    } elseif (!is_numeric($campaignId)) {
        $errors[] = "Program donasi tidak valid.";
    }

    if ($amount === "" || !is_numeric($amount) || (float) $amount <= 0) {
        $errors[] = "Nominal donasi harus berupa angka lebih dari nol.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO donations
            (
                campaign_id,
                donor_name,
                donor_email,
                amount,
                note,
                status
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                'pending'
            )
        ");

        $stmt->execute([
            (int) $campaignId,
            $name,
            $email === "" ? null : $email,
            (float) $amount,
            $note === "" ? null : $note
        ]);

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        header("Location: /donate?success=1");
        exit;
    }
}
?>
<!doctype html>
<html class="no-js" lang="id">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Donasi - <?php echo APP_NAME;?></title>
        <meta name="description" content="Salurkan donasi Sahabat Asa untuk program kemanusiaan Palestina.">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php
        require_once __DIR__ . '/../components/head_link.php';
        ?>
    </head>
    <body>
        <?php
        require_once __DIR__ . '/../components/preload.php';
        require_once __DIR__ . '/../components/header.php';
        ?>

        <main>
            <!--? Hero Start -->
            <div class="slider-area2">
                <div class="slider-height2 d-flex align-items-center">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="hero-cap hero-cap2 pt-20 text-center">
                                    <h2>Donasi</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Hero End -->
            <!--? Contact Area start -->
            <section class="contact-section">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="section-tittle text-center mb-40">
                                <span>Wujudkan asa</span>
                                <h2>Formulir Donasi</h2>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-8">
                            <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success mb-4">
                                Terima kasih, <?= htmlspecialchars($name ?: 'Sahabat Asa'); ?>. Donasi Anda telah kami terima dan akan segera kami proses. Semoga menjadi amal jariah.
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>

                            <form class="form-contact contact_form" action="/donate" method="post" id="donateForm">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="campaign_id" class="form-label">Pilih Program Donasi</label>
                                            <select class="form-control" name="campaign_id" id="campaign_id" required>
                                                <option value="">-- Pilih Program --</option>
                                                <?php foreach ($campaigns as $campaign): ?>
                                                    <option value="<?= (int) $campaign['id']; ?>" <?= ($campaignId == $campaign['id']) ? 'selected' : ''; ?>>
                                                        <?= htmlspecialchars($campaign['title']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="donor_name" class="form-label">Nama</label>
                                            <input
                                                class="form-control"
                                                name="donor_name"
                                                id="donor_name"
                                                type="text"
                                                value="<?= htmlspecialchars($name) ?>"
                                                placeholder="Nama lengkap / Anonim"
                                                required
                                            >
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="donor_email" class="form-label">Email (opsional)</label>
                                            <input
                                                class="form-control"
                                                name="donor_email"
                                                id="donor_email"
                                                type="email"
                                                value="<?= htmlspecialchars($email) ?>"
                                                placeholder="Alamat email"
                                            >
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="amount" class="form-label">Nominal Donasi (Rp)</label>
                                            <input
                                                class="form-control"
                                                name="amount"
                                                id="amount"
                                                type="number"
                                                min="1"
                                                step="1000"
                                                value="<?= htmlspecialchars($amount) ?>"
                                                placeholder="Contoh: 100000"
                                                required
                                            >
                                            <div class="mt-2 d-flex flex-wrap gap-2">
                                                <button type="button" class="btn btn-outline-success btn-sm nominal-btn" data-value="50000">Rp50.000</button>
                                                <button type="button" class="btn btn-outline-success btn-sm nominal-btn" data-value="100000">Rp100.000</button>
                                                <button type="button" class="btn btn-outline-success btn-sm nominal-btn" data-value="250000">Rp250.000</button>
                                                <button type="button" class="btn btn-outline-success btn-sm nominal-btn" data-value="500000">Rp500.000</button>
                                                <button type="button" class="btn btn-outline-success btn-sm nominal-btn" data-value="1000000">Rp1.000.000</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="note" class="form-label">Pesan / Doa (opsional)</label>
                                            <textarea
                                                class="form-control w-100"
                                                name="note"
                                                id="note"
                                                cols="30"
                                                rows="5"
                                                placeholder="Tuliskan pesan atau doa untuk saudara di Palestina"
                                            ><?= htmlspecialchars($note) ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-3">
                                    <button type="submit" class="button button-contactForm boxed-btn">Kirim Donasi</button>
                                </div>
                                <p class="text-muted mt-3 small">
                                    Donasi akan dikelola secara amanah dan transparan oleh Asa Palestina, turunan dari Adara Relief.
                                </p>
                            </form>
                        </div>
                        <div class="col-lg-3 offset-lg-1">
                            <div class="media contact-info">
                                <span class="contact-info__icon">
                                    <i class="ti-info-alt"></i>
                                </span>
                                <div class="media-body">
                                    <h3>Transparan</h3>
                                    <p>Laporan penyaluran dana dipublikasikan secara berkala.</p>
                                </div>
                            </div>
                            <div class="media contact-info">
                                <span class="contact-info__icon">
                                    <i class="ti-heart"></i>
                                </span>
                                <div class="media-body">
                                    <h3>Amanah</h3>
                                    <p>Setiap rupiah disalurkan sesuai program yang dipilih.</p>
                                </div>
                            </div>
                            <div class="media contact-info">
                                <span class="contact-info__icon">
                                    <i class="ti-email"></i>
                                </span>
                                <div class="media-body">
                                    <h3>info@asapalestina.com</h3>
                                    <p>Sampaikan pertanyaan Anda kapan saja.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Contact Area End -->
        </main>

        <?php
        require_once __DIR__ . '/../components/footer.php';
        ?>

        <?php
        require_once __DIR__ . '/../components/body_script.php';
        ?>
        <script>
            document.querySelectorAll('.nominal-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.getElementById('amount').value = btn.getAttribute('data-value');
                });
            });
        </script>
    </body>
</html>
