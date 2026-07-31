<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/guard.php';
require_once __DIR__ . '/../../core/database.php';

$perPage = 10;

$page = isset($_GET['page'])
    ? max(1, (int) $_GET['page'])
    : 1;

$offset = ($page - 1) * $perPage;

// Count total records
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM campaigns
");

$totalRows = (int) $stmt->fetchColumn();

$totalPages = max(1, ceil($totalRows / $perPage));

// Load current page
$stmt = $pdo->prepare("
    SELECT
        campaigns.id,
        campaigns.title,
        campaigns.image,
        campaigns.target_amount,
        campaigns.start_date,
        campaigns.end_date,
        campaigns.status,
        users.name AS author
    FROM campaigns
    INNER JOIN users
        ON users.id = campaigns.user_id
    ORDER BY campaigns.created_at DESC
    LIMIT :limit
    OFFSET :offset
");

$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

$campaigns = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Inventory - <?php echo APP_NAME;?></title>
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
                                <h1 class="fs-3 mb-1">Campaigns</h1>
                                <p class="mb-0">Manage fundraising campaigns</p>
                            </div>
                            <div>
                                <a href="/admin/create-campaign" class="btn btn-primary">
                                    Add Campaign
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
                                    placeholder="Search campaigns..."
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
                                        <th>Title</th>
                                        <th>Target</th>
                                        <th>Period</th>
                                        <th>Status</th>
                                        <th>Author</th>
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if (empty($campaigns)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            No campaigns found.
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($campaigns as $campaign): ?>
                                    <tr class="align-middle">
                                        <td>
                                            <?php if (!empty($campaign['image'])): ?>
                                                <img
                                                    src="/uploads/campaign/<?= htmlspecialchars($campaign['image']) ?>"
                                                    alt="<?= htmlspecialchars($campaign['title']) ?>"
                                                    class="rounded"
                                                    style="width:60px;height:60px;object-fit:cover;"
                                                >
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($campaign['title']) ?>
                                        </td>
                                        <td>
                                            Rp <?= number_format($campaign['target_amount'], 0, ',', '.') ?>
                                        </td>
                                        <td>
                                            <?= date('d M Y', strtotime($campaign['start_date'])) ?>
                                            <br>
                                            <small class="text-muted">
                                                <?php if ($campaign['end_date']): ?>
                                                    <?= date('d M Y', strtotime($campaign['end_date'])) ?>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php
                                            switch ($campaign['status']) {
                                                case 'active':
                                                    $class = 'success';
                                                    break;
                                                case 'completed':
                                                    $class = 'primary';
                                                    break;
                                                case 'cancelled':
                                                    $class = 'danger';
                                                    break;
                                                default:
                                                    $class = 'secondary';
                                            }
                                            ?>
                                            <span class="badge bg-<?= $class ?>">
                                                <?= ucfirst($campaign['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($campaign['author']) ?>
                                        </td>
                                        <td>
                                            <a
                                                href="/admin/edit-campaign?id=<?= $campaign['id'] ?>"
                                                title="Edit"
                                            >
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <a
                                                href="#"
                                                class="link-danger ms-2"
                                                onclick="return confirm('Delete this campaign?')"
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
                                                    Showing
                                                    <strong>
                                                        <?= $totalRows == 0 ? 0 : $offset + 1 ?>
                                                    </strong>
                                                    -
                                                    <strong>
                                                        <?= min($offset + $perPage, $totalRows) ?>
                                                    </strong>
                                                    of
                                                    <strong><?= $totalRows ?></strong>
                                                    campaigns

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