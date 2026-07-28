<?php
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/database.php';

$errors = [];

$email = "";
$password = "";

$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$userCount = (int) $stmt->fetchColumn();

if ($userCount === 0) {
    header('Location: /setup');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email === "") {
        $errors[] = "Email is required.";
    }

    if ($password === "") {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            SELECT *
            FROM users
            WHERE email = ?
        ");

        $stmt->execute([$email]);

        $user = $stmt->fetch();

        if (
            $user &&
            password_verify($password, $user['password'])
        ) {
            $_SESSION['user'] = $user;

            header('Location: /admin');
            exit;
        } else {
            $isError = true;
            $errorMessage = "Email and/or password is incorrect";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Signin - <?php echo APP_NAME;?></title>
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
                        <h1 class="card-title mb-5 h5">Sign in to your account</h1>
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
                            <label for="email" class="form-label">Email address</label>
                            <input name="email" id="email" type="email" class="form-control" placeholder="name@example.com" required autofocus value="<?= htmlspecialchars($email) ?>">
                            <div class="invalid-feedback">Please enter a valid email.</div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label d-flex justify-content-between">
                                <span>Password</span>
                                <a href="#" class="small link-success">Forgot Password?</a>
                            </label>
                            <input name="password" id="password" type="password" class="form-control" placeholder="Password" required minlength="6">
                            <div class="invalid-feedback">Please provide a password (min 6 characters).</div>
                        </div>

                        <button class="btn btn-success w-100" type="submit">Sign in</button>
                    </form>
                </div>
            </div>
        </div>

        <?php
        require_once __DIR__ . '/../components/admin/body_script.php';
        ?>
    </body>
</html>