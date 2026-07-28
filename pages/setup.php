<?php
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/database.php';

$errors = [];

$name = "";
$email = "";

$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$userCount = (int) $stmt->fetchColumn();

if ($userCount > 0) {
    header('Location: /signin');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($name === "") {
        $errors[] = "Name is required.";
    }

    if ($email === "") {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email is invalid.";
    }

    if ($password === "") {
        $errors[] = "Password is required.";
    }

    if ($confirmPassword === "") {
        $errors[] = "Confirm password is required.";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Password confirmation does not match.";
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO users
            (
                name,
                email,
                password,
                role
            )
            VALUES
            (
                ?,
                ?,
                ?,
                'admin'
            )
        ");

        $stmt->execute([
            $name,
            $email,
            $hash
        ]);

        header('Location: /signin');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Setup - <?php echo APP_NAME;?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php
        require_once __DIR__ . '/../components/admin/head_link.php';
        ?>
    </head>

    <body>
        <div class="container d-flex align-items-center justify-content-center min-vh-100">
            <div class="card " style="max-width:420px; width:100%;">
                <div class="card-body p-5">
                    <div class="text-center mb-3">
                        <a href="/" class="mb-4 d-inline-block">
                            <img src="/assets/admin/images/logo-asa.png" width="100"/>
                        </a>
                        <h1 class="card-title mb-5 h5">Setup application<br/><span class="opacity-50">Create root account</span></h1>
                    </div>

                    <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <form method="post" class="needs-validation mt-3" novalidate>
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Full name</label>
                            <input name="name" id="fullName" type="text" class="form-control" placeholder="Jane Doe" required value="<?= htmlspecialchars($name) ?>">
                            <div class="invalid-feedback">Please enter your name.</div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input name="email" id="email" type="email" class="form-control" placeholder="name@example.com" required value="<?= htmlspecialchars($email) ?>">
                            <div class="invalid-feedback">Please enter a valid email.</div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input name="password" id="password" type="password" class="form-control" placeholder="Create a password" required minlength="6">
                            <div class="invalid-feedback">Please provide a password (min 6 characters).</div>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm password</label>
                            <input name="confirm_password" id="confirmPassword" type="password" class="form-control" placeholder="Repeat password" required oninput="this.setCustomValidity(document.getElementById('password').value !== this.value ? 'Passwords do not match.' : '')">
                            <div class="invalid-feedback">Passwords must match.</div>
                        </div>
                        <button class="btn btn-success w-100" type="submit">Sign up</button>
                    </form>
                </div>
            </div>
        </div>

        <?php
        require_once __DIR__ . '/../components/admin/body_script.php';
        ?>
    </body>
</html>