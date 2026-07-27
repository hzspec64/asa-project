<?php
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/database.php';

$isError = false;
$errorMessage = "";

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
        $isError = true;
        $errorMessage = "Name can not be blank";
    } else if ($email === "") {
        $isError = true;
        $errorMessage = "Email can not be blank";
    } else if ($password === "") {
        $isError = true;
        $errorMessage = "Password can not be blank";
    } else if ($password !== $confirmPassword) {
        $isError = true;
        $errorMessage = 'Password confirmation does not match';
    } else {
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

                    <?php
                    if ($isError) {
                    ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $errorMessage;?>
                    </div>
                    <?php
                    }
                    ?>

                    <form method="post" class="needs-validation mt-3" novalidate>
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Full name</label>
                            <input name="name" id="fullName" type="text" class="form-control" placeholder="Jane Doe" required value="<?php echo $name;?>">
                            <div class="invalid-feedback">Please enter your name.</div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input name="email" id="email" type="email" class="form-control" placeholder="name@example.com" required value="<?php echo $email;?>">
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