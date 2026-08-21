<?php
require_once __DIR__ . '/../../asa_config.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/database.php';

$errors = [];

$name = "";
$email = "";
$subject = "";
$message = "";

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $submittedToken = $_POST['csrf_token'] ?? '';

    // CSRF validation
    if (
        empty($submittedToken) ||
        !hash_equals($_SESSION['csrf_token'], $submittedToken)
    ) {
        $errors[] = "Invalid request. Please try again.";
    }

    // Validate name
    if ($name === "") {
        $errors[] = "Name is required.";
    }

    // Validate email
    if ($email === "") {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email is invalid.";
    }

    // Validate subject
    if ($subject === "") {
        $errors[] = "Subject is required.";
    }

    // Validate message
    if ($message === "") {
        $errors[] = "Message is required.";
    }

    if (empty($errors)) {

        $stmt = $pdo->prepare("
            INSERT INTO contacts
            (
                name,
                email,
                subject,
                message
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?
            )
        ");

        $stmt->execute([
            $name,
            $email,
            $subject,
            $message
        ]);

        // Regenerate token after successful submission
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        header("Location: /contact?success=1");
        exit;
    }
}
?>
<!doctype html>
<html class="no-js" lang="id">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Contact - <?php echo APP_NAME;?></title>
        <meta name="description" content="">
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
                                    <h2>Hubungi Kami</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Hero End -->
            <!--?  Contact Area start  -->
            <section class="contact-section">
                <div class="container">
                    <div class="d-none d-sm-block mb-5 pb-4">
                        <div
                            id="map"
                            style="height: 480px; position: relative; overflow: hidden;"
                        ></div>

                        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                        <script>
                            const latitude = -6.3641269;
                            const longitude = 106.8419329;

                            const map = L.map('map', {
                                scrollWheelZoom: false
                            }).setView(
                                [latitude, longitude],
                                17
                            );

                            L.tileLayer(
                                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                                {
                                    attribution: '&copy; OpenStreetMap contributors'
                                }
                            ).addTo(map);

                            L.marker([latitude, longitude])
                                .addTo(map);
                        </script>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <h2 class="contact-title">Hubungi Kami</h2>
                        </div>
                        <div class="col-lg-8">
                            <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>

                            <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success mb-4">
                                Thank you. Your message has been sent successfully.
                            </div>
                            <?php endif; ?>

                            <form
                                class="form-contact contact_form"
                                action="/contact"
                                method="post"
                                id="contactForm"
                            >
                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= htmlspecialchars($csrfToken) ?>"
                                >

                                <div class="row">

                                    <div class="col-12">
                                        <div class="form-group">
                                            <textarea
                                                class="form-control w-100"
                                                name="message"
                                                id="message"
                                                cols="30"
                                                rows="9"
                                                placeholder="Enter Message"
                                                required
                                            ><?= htmlspecialchars($message) ?></textarea>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input
                                                class="form-control"
                                                name="name"
                                                id="name"
                                                type="text"
                                                value="<?= htmlspecialchars($name) ?>"
                                                placeholder="Enter your name"
                                                required
                                            >
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input
                                                class="form-control"
                                                name="email"
                                                id="email"
                                                type="email"
                                                value="<?= htmlspecialchars($email) ?>"
                                                placeholder="Email"
                                                required
                                            >
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <input
                                                class="form-control"
                                                name="subject"
                                                id="subject"
                                                type="text"
                                                value="<?= htmlspecialchars($subject) ?>"
                                                placeholder="Enter Subject"
                                                required
                                            >
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group mt-3">
                                    <button
                                        type="submit"
                                        class="button button-contactForm boxed-btn"
                                    >
                                        Send
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-3 offset-lg-1">
                            <div class="media contact-info">
                                <span class="contact-info__icon">
                                    <i class="ti-home"></i>
                                </span>
                                <div class="media-body">
                                    <h3>Depok, Jawa Barat</h3>
                                    <p>Indonesia</p>
                                </div>
                            </div>
                            <div class="media contact-info">
                                <span class="contact-info__icon">
                                    <i class="ti-tablet"></i>
                                </span>
                                <div class="media-body">
                                    <h3>+62 21 1234 5678</h3>
                                    <p>Senin s.d. Jumat 09.00–17.00</p>
                                </div>
                            </div>
                            <div class="media contact-info">
                                <span class="contact-info__icon">
                                    <i class="ti-email"></i>
                                </span>
                                <div class="media-body">
                                    <h3>info@asapalestina.com</h3>
                                    <p>Sampaikan pertanyaan kapan saja!</p>
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
    </body>
</html>