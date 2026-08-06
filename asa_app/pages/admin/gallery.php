<?php
require_once __DIR__ . '/../../../asa_config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/guard.php';
require_once __DIR__ . '/../../core/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $deleteId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($deleteId) {
        $deleteStmt = $pdo->prepare("DELETE FROM galleries WHERE id = :id");
        $deleteStmt->execute([':id' => $deleteId]);
    }

    // Redirect back to the same page (preserving page number if present)
    $currentPageNum = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    header("Location: /admin/gallery?page=" . $currentPageNum);
    exit;
}

$CURRENT_PAGE = 'gallery';

$perPage = 10;

$page = isset($_GET['page'])
    ? max(1, (int) $_GET['page'])
    : 1;

$offset = ($page - 1) * $perPage;

// Count total records
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM galleries
");

$totalRows = (int) $stmt->fetchColumn();

$totalPages = max(1, ceil($totalRows / $perPage));

// Load current page
$stmt = $pdo->prepare("
    SELECT
        id,
        title,
        description,
        image,
        created_at
    FROM galleries
    ORDER BY created_at DESC
    LIMIT :limit
    OFFSET :offset
");

$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

$galleries = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Galeri - <?php echo APP_NAME;?></title>
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
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="">
                                <h1 class="fs-3 mb-1">Galeri</h1>
                                <p class="mb-0 opacity-50">Tabel pengelolaan foto galeri.</p>
                            </div>
                            <div>
                                <a href="/admin/create-photo" class="btn btn-success">
                                    Tambah Foto
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div>
                            <div class="d-flex gap-2 mb-3 flex-wrap justify-content-between">
                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Cari foto..."
                                    style="max-width: 250px;"
                                >
                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-secondary">
                                        <i class="ti ti-filter"></i> Filter </button>
                                    <button class="btn btn-outline-secondary">
                                        <i class="ti ti-file-excel"></i> Excel </button>
                                    <button class="btn btn-outline-secondary">
                                        <i class="ti ti-file-pdf"></i> PDF </button>
                                </div>
                            </div>
                        </div>
                        <div class="card table-responsive ">
                            <table class="table mb-0 text-nowrap table-hover">
                                <thead class="table-light border-light">
                                    <tr>
                                        <th width="80">Image</th>
                                        <th>Judul</th>
                                        <th>Deskripsi</th>
                                        <th>Dibuat</th>
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if (empty($galleries)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            Foto belum tersedia atau tidak ditemukan.
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($galleries as $gallery): ?>
                                    <tr class="align-middle">
                                        <td>
                                            <?php if (!empty($gallery['image'])): ?>
                                                <img
                                                    src="/uploads/gallery/<?= htmlspecialchars($gallery['image']) ?>"
                                                    alt="<?= htmlspecialchars($gallery['title']) ?>"
                                                    class="rounded"
                                                    style="width:60px;height:60px;object-fit:cover;"
                                                >
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($gallery['title']) ?>
                                        </td>
                                        <td>
                                            <?php
                                            $description = strip_tags($gallery['description']);
                                            if (strlen($description) > 60) {
                                                $description = substr($description, 0, 60) . '...';
                                            }
                                            ?>
                                            <?= htmlspecialchars($description) ?>
                                        </td>
                                        <td>
                                            <?= date('d M Y', strtotime($gallery['created_at'])) ?>
                                        </td>
                                        <td>
                                            <a
                                                href="/admin/edit-photo?id=<?= $gallery['id'] ?>"
                                                title="Edit"
                                            >
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form action="" method="POST" class="d-inline" onsubmit="return confirm('Hapus foto ini?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= htmlspecialchars($campaign['id']) ?>">
                                                <button type="submit"
                                                        class="btn p-0 border-0 bg-transparent link-danger ms-2"
                                                        title="Hapus">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="5">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    Menampilkan
                                                    <strong>
                                                        <?= $totalRows == 0 ? 0 : $offset + 1 ?>
                                                    </strong>
                                                    -
                                                    <strong>
                                                        <?= min($offset + $perPage, $totalRows) ?>
                                                    </strong>
                                                    dari
                                                    <strong><?= $totalRows ?></strong>
                                                    foto

                                                </div>
                                                <nav>
                                                    <ul class="pagination mb-0">
                                                        <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                                                            <a
                                                                class="page-link"
                                                                href="?page=<?= $page - 1 ?>"
                                                            >
                                                                Prev
                                                            </a>
                                                        </li>
                                                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                            <a
                                                                class="page-link"
                                                                href="?page=<?= $i ?>"
                                                            >
                                                                <?= $i ?>
                                                            </a>
                                                        </li>
                                                        <?php endfor; ?>
                                                        <li class="page-item <?= $page == $totalPages ? 'disabled' : '' ?>">

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
                require_once __DIR__ . '/../../components/admin/footer.php';
                ?>
            </div>
        </main>

        <?php
        require_once __DIR__ . '/../../components/admin/body_script.php';
        ?>
    </body>
</html>