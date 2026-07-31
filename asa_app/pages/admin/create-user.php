<?php
require_once __DIR__ . '/../../../asa_config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/guard.php';
require_once __DIR__ . '/../../core/database.php';

$errors = [];

$name = "";
$email = "";
$role = "staff";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $role = $_POST['role'];

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

    if (!in_array($role, ['admin', 'staff'])) {
        $errors[] = "Role is invalid.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM users
            WHERE email = ?
        ");

        $stmt->execute([$email]);

        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Email already exists.";
        }
    }

    if (empty($errors)) {
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
                ?
            )
        ");

        $stmt->execute([
            $name,
            $email,
            password_hash($password, PASSWORD_DEFAULT),
            $role
        ]);

        header("Location: /admin/users");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Tambah Pengelola - <?php echo APP_NAME;?></title>
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
                                <a href="/admin/users" class="btn btn-primary">
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

                                <form method="post">
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input
                                            type="text"
                                            name="name"
                                            class="form-control"
                                            value="<?= htmlspecialchars($name) ?>"
                                            required
                                        >
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input
                                            type="email"
                                            name="email"
                                            class="form-control"
                                            value="<?= htmlspecialchars($email) ?>"
                                            required
                                        >
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Password</label>
                                            <input
                                                type="password"
                                                name="password"
                                                class="form-control"
                                                required
                                            >
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Confirm Password</label>
                                            <input
                                                type="password"
                                                name="confirm_password"
                                                class="form-control"
                                                required
                                            >
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">Role</label>
                                        <select
                                            name="role"
                                            class="form-select"
                                            required
                                        >
                                            <option value="">-- Select Role --</option>
                                            <option
                                                value="admin"
                                                <?= $role === 'admin' ? 'selected' : '' ?>
                                            >
                                                Admin
                                            </option>
                                            <option
                                                value="staff"
                                                <?= $role === 'staff' ? 'selected' : '' ?>
                                            >
                                                Staff
                                            </option>
                                        </select>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button
                                            type="submit"
                                            class="btn btn-primary"
                                        >
                                            Save User
                                        </button>
                                        <a
                                            href="/admin/users"
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