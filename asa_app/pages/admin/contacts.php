<?php
require_once __DIR__ . '/../../../asa_config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/guard.php';
require_once __DIR__ . '/../../core/database.php';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action']) &&
    $_POST['action'] === 'delete'
) {

    $deleteId = filter_input(
        INPUT_POST,
        'id',
        FILTER_VALIDATE_INT
    );

    if ($deleteId) {

        $deleteStmt = $pdo->prepare("
            DELETE FROM contacts
            WHERE id = :id
        ");

        $deleteStmt->execute([
            ':id' => $deleteId
        ]);
    }

    $currentPageNum = isset($_GET['page'])
        ? (int) $_GET['page']
        : 1;

    header(
        "Location: /admin/contacts?page=" .
        $currentPageNum
    );

    exit;
}


$CURRENT_PAGE = 'contact';

$perPage = 10;

$page = isset($_GET['page'])
    ? max(1, (int) $_GET['page'])
    : 1;

$offset = ($page - 1) * $perPage;


// Count total records
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM contacts
");

$totalRows = (int) $stmt->fetchColumn();

$totalPages = max(
    1,
    ceil($totalRows / $perPage)
);


// Load current page
$stmt = $pdo->prepare("
    SELECT
        id,
        name,
        email,
        subject,
        created_at
    FROM contacts
    ORDER BY created_at DESC
    LIMIT :limit
    OFFSET :offset
");

$stmt->bindValue(
    ':limit',
    $perPage,
    PDO::PARAM_INT
);

$stmt->bindValue(
    ':offset',
    $offset,
    PDO::PARAM_INT
);

$stmt->execute();

$contacts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>
            Kontak - <?php echo APP_NAME;?>
        </title>
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1"
        >
        <?php
        require_once __DIR__ .
            '/../../components/admin/head_link.php';
        ?>
    </head>
    <body>
        <div
            id="overlay"
            class="overlay"
        ></div>
        <!-- TOPBAR -->
        <?php
        require_once __DIR__ .
            '/../../components/admin/navbar.php';
        ?>
        <!-- SIDEBAR -->
        <?php
        require_once __DIR__ .
            '/../../components/admin/sidebar.php';
        ?>
        <!-- MAIN CONTENT -->
        <main
            id="content"
            class="content py-10"
        >
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div
                            class="d-flex justify-content-between align-items-center mb-4"
                        >
                            <div>
                                <h1 class="fs-3 mb-1">
                                    Kontak
                                </h1>
                                <p class="mb-0 opacity-50">
                                    Pesan dan pertanyaan dari pengunjung.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <!-- SEARCH / ACTION -->
                        <div>
                            <div
                                class="d-flex gap-2 mb-3 flex-wrap justify-content-between"
                            >
                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Cari pesan..."
                                    style="max-width: 250px;"
                                >
                                <div class="d-flex gap-2">
                                    <button
                                        class="btn btn-outline-secondary"
                                    >
                                        <i class="ti ti-filter"></i>
                                        Filter
                                    </button>
                                    <button
                                        class="btn btn-outline-secondary"
                                    >
                                        <i class="ti ti-file-excel"></i>
                                        Excel
                                    </button>
                                    <button
                                        class="btn btn-outline-secondary"
                                    >
                                        <i class="ti ti-file-pdf"></i>
                                        PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- TABLE -->
                        <div class="card table-responsive">
                            <table
                                class="table mb-0 text-nowrap table-hover"
                            >
                                <thead
                                    class="table-light border-light"
                                >
                                    <tr>
                                        <th>
                                            Nama
                                        </th>
                                        <th>
                                            Email
                                        </th>
                                        <th>
                                            Subject
                                        </th>
                                        <th>
                                            Dikirim
                                        </th>
                                        <th width="120">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (empty($contacts)): ?>
                                    <tr>
                                        <td
                                            colspan="5"
                                            class="text-center py-4"
                                        >
                                            Belum ada pesan.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($contacts as $contact): ?>
                                        <tr class="align-middle">
                                            <td>
                                                <?= htmlspecialchars(
                                                    $contact['name']
                                                ) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars(
                                                    $contact['email']
                                                ) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars(
                                                    $contact['subject']
                                                ) ?>
                                            </td>
                                            <td>
                                                <?= date(
                                                    'd M Y H:i',
                                                    strtotime(
                                                        $contact['created_at']
                                                    )
                                                ) ?>
                                            </td>
                                            <td>
                                                <!-- VIEW -->
                                                <a
                                                    href="/admin/view-contact?id=<?= $contact['id'] ?>"
                                                    title="View"
                                                >
                                                    <i
                                                        class="ti ti-eye"
                                                    ></i>
                                                </a>
                                                <!-- DELETE -->
                                                <form
                                                    action=""
                                                    method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Hapus pesan ini?');"
                                                >
                                                    <input
                                                        type="hidden"
                                                        name="action"
                                                        value="delete"
                                                    >
                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= htmlspecialchars(
                                                            $contact['id']
                                                        ) ?>"
                                                    >
                                                    <button
                                                        type="submit"
                                                        class="btn p-0 border-0 bg-transparent link-danger ms-2"
                                                        title="Hapus"
                                                    >
                                                        <i
                                                            class="ti ti-trash"
                                                        ></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                                <!-- PAGINATION -->
                                <tfoot>
                                    <tr>
                                        <td colspan="5">
                                            <div
                                                class="d-flex justify-content-between align-items-center"
                                            >
                                                <div>
                                                    Showing
                                                    <strong>
                                                        <?= $totalRows == 0
                                                            ? 0
                                                            : $offset + 1
                                                        ?>
                                                    </strong>
                                                    -
                                                    <strong>
                                                        <?= min(
                                                            $offset + $perPage,
                                                            $totalRows
                                                        ) ?>
                                                    </strong>
                                                    of
                                                    <strong>
                                                        <?= $totalRows ?>
                                                    </strong>
                                                    messages
                                                </div>
                                                <nav>
                                                    <ul
                                                        class="pagination mb-0"
                                                    >
                                                        <!-- PREVIOUS -->
                                                        <li
                                                            class="page-item <?= $page == 1
                                                                ? 'disabled'
                                                                : ''
                                                            ?>"
                                                        >
                                                            <a
                                                                class="page-link"
                                                                href="?page=<?= $page - 1 ?>"
                                                            >
                                                                Previous
                                                            </a>
                                                        </li>
                                                        <!-- PAGE NUMBERS -->
                                                        <?php
                                                        for (
                                                            $i = 1;
                                                            $i <= $totalPages;
                                                            $i++
                                                        ):
                                                        ?>
                                                            <li
                                                                class="page-item <?= $i == $page
                                                                    ? 'active'
                                                                    : ''
                                                                ?>"
                                                            >
                                                                <a
                                                                    class="page-link"
                                                                    href="?page=<?= $i ?>"
                                                                >
                                                                    <?= $i ?>
                                                                </a>
                                                            </li>
                                                        <?php endfor; ?>
                                                        <!-- NEXT -->
                                                        <li
                                                            class="page-item <?= $page == $totalPages
                                                                ? 'disabled'
                                                                : ''
                                                            ?>"
                                                        >
                                                            <a
                                                                class="page-link"
                                                                href="?page=<?= $page + 1 ?>"
                                                            >
                                                                Next
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </nav>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <?php
                require_once __DIR__ .
                    '/../../components/admin/footer.php';
                ?>
            </div>
        </main>
        <?php
        require_once __DIR__ .
            '/../../components/admin/body_script.php';
        ?>
    </body>
</html>