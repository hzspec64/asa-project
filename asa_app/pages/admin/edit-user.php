<?php
require_once __DIR__ . '/../../../asa_config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/guard.php';
require_once __DIR__ . '/../../core/database.php';

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($id <= 0) {
    header("Location: /admin/users");
    exit;
}

$stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE id = ?
");

$stmt->execute([$id]);

$user = $stmt->fetch();

if (!$user) {
    header("Location: /admin/users");
    exit;
}

$errors = [];

$name = $user['name'];
$email = $user['email'];
$role = $user['role'];

if (
    $_SESSION['user']['id'] == $id &&
    $role !== 'admin'
) {
    $errors[] = "You cannot change your own role.";
}

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

    if (!in_array($role, ['admin', 'staff'])) {
        $errors[] = "Role is invalid.";
    }

    // Password validation (only if user entered one)
    if ($password !== "" || $confirmPassword !== "") {

        if ($password === "") {
            $errors[] = "Password is required.";
        }

        if ($confirmPassword === "") {
            $errors[] = "Confirm password is required.";
        }

        if ($password !== $confirmPassword) {
            $errors[] = "Password confirmation does not match.";
        }
    }

    // Duplicate email
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM users
            WHERE email = ?
            AND id <> ?
        ");

        $stmt->execute([
            $email,
            $id
        ]);

        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Email already exists.";
        }
    }

    if (empty($errors)) {
        if ($password === "") {
            $stmt = $pdo->prepare("
                UPDATE users
                SET
                    name = ?,
                    email = ?,
                    role = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $name,
                $email,
                $role,
                $id
            ]);
        } else {
            $stmt = $pdo->prepare("
                UPDATE users
                SET
                    name = ?,
                    email = ?,
                    password = ?,
                    role = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $name,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $role,
                $id
            ]);

        }

        header("Location: /admin/users");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Edit Data Pengelola - <?php echo APP_NAME;?></title>
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
                                <h1 class="fs-3 mb-1">Edit Pengelola</h1>
                                <p class="mb-0">Perbarui data pengelola.</p>
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
                                            <label class="form-label">
                                                Password
                                                <small class="text-muted">(Leave blank to keep current password)</small>
                                            </label>
                                            <input
                                                type="password"
                                                name="password"
                                                class="form-control"
                                            >
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                Confirm Password
                                            </label>
                                            <input
                                                type="password"
                                                name="confirm_password"
                                                class="form-control"
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
                                            class="btn btn-success"
                                        >
                                            Update User
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