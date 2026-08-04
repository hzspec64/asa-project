<?php
require_once __DIR__ . '/../../../asa_config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/guard.php';
require_once __DIR__ . '/../../core/database.php';

$CURRENT_PAGE = 'article';

$perPage = 10;

$page = isset($_GET['page'])
    ? max(1, (int) $_GET['page'])
    : 1;

$offset = ($page - 1) * $perPage;

// Count total records
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM articles
");

$totalRows = (int) $stmt->fetchColumn();

$totalPages = max(1, ceil($totalRows / $perPage));

// Load current page
$stmt = $pdo->prepare("
    SELECT
        articles.id,
        articles.title,
        articles.status,
        articles.created_at,
        users.name AS author
    FROM articles
    INNER JOIN users
        ON users.id = articles.user_id
    ORDER BY articles.created_at DESC
    LIMIT :limit
    OFFSET :offset
");

$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

$articles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Artikel - <?php echo APP_NAME;?></title>
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
                                <h1 class="fs-3 mb-1">Artikel</h1>
                                <p class="mb-0 opacity-50">Tabel pengelolaan artikel.</p>
                            </div>
                            <div>
                                <a href="/admin/create-article" class="btn btn-success">
                                    Tambah Artikel
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
                                    placeholder="Cari artikel..."
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
                                        <th>Judul</th>
                                        <th>Status</th>
                                        <th>Oleh</th>
                                        <th>Dibuat</th>
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if (empty($articles)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            Artikel belum tersedia atau tidak ditemukan.
                                        </td>
                                    </tr>
                                    <?php else: ?>

                                    <?php foreach ($articles as $article): ?>
                                    <tr class="align-middle">
                                        <td>
                                            <?= htmlspecialchars($article['title']) ?>
                                        </td>
                                        <td>
                                            <?php if ($article['status'] === 'published'): ?>
                                                <span class="badge bg-success">
                                                    Published
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    Draft
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($article['author']) ?>
                                        </td>
                                        <td>
                                            <?= date('d M Y', strtotime($article['created_at'])) ?>
                                        </td>
                                        <td>
                                            <a
                                                href="/admin/edit-article?id=<?= $article['id'] ?>"
                                                title="Edit"
                                            >
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <a
                                                href="#"
                                                class="link-danger ms-2"
                                                onclick="return confirm('Delete this article?')"
                                                title="Delete"
                                            >
                                                <i class="ti ti-trash"></i>
                                            </a>
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
                                                    artikel
                                                </div>
                                                <nav>
                                                    <ul class="pagination mb-0">
                                                        <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                                                            <a
                                                                class="page-link"
                                                                href="?page=<?= $page - 1 ?>"
                                                            >
                                                                Previous
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