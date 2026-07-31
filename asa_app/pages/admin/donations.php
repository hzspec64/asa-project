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
    FROM donations
");

$totalRows = (int) $stmt->fetchColumn();

$totalPages = max(1, ceil($totalRows / $perPage));

// Load current page
$stmt = $pdo->prepare("
    SELECT
        donations.id,
        donations.donor_name,
        donations.donor_email,
        donations.amount,
        donations.donation_date,
        donations.status,
        campaigns.title AS campaign
    FROM donations
    INNER JOIN campaigns
        ON campaigns.id = donations.campaign_id
    ORDER BY donations.donation_date DESC
    LIMIT :limit
    OFFSET :offset
");

$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

$donations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Donasi - <?php echo APP_NAME;?></title>
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
                                <h1 class="fs-3 mb-1">Donasi</h1>
                                <p class="mb-0">Kelola donasi</p>
                            </div>
                            <div>
                                <a href="/admin/create-donation" class="btn btn-primary">
                                    Tambah Donasi
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
                                    placeholder="Search donations..."
                                    style="max-width:250px;"
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
                                        <th>Donor</th>
                                        <th>Campaign</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if (empty($donations)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            Donasi belum tersedia atau tidak ditemukan.
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($donations as $donation): ?>
                                    <tr class="align-middle">
                                        <td>
                                            <strong>
                                                <?= htmlspecialchars($donation['donor_name']) ?>
                                            </strong>
                                            <?php if (!empty($donation['donor_email'])): ?>
                                                <br>
                                                <small class="text-muted">
                                                    <?= htmlspecialchars($donation['donor_email']) ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($donation['campaign']) ?>
                                        </td>
                                        <td>
                                            Rp <?= number_format($donation['amount'], 0, ',', '.') ?>
                                        </td>
                                        <td>
                                            <?= date('d M Y', strtotime($donation['donation_date'])) ?>
                                            <br>
                                            <small class="text-muted">
                                                <?= date('H:i', strtotime($donation['donation_date'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php
                                            switch ($donation['status']) {
                                                case 'paid':
                                                    $class = 'success';
                                                    break;
                                                case 'cancelled':
                                                    $class = 'danger';
                                                    break;
                                                default:
                                                    $class = 'warning';
                                            }
                                            ?>
                                            <span class="badge bg-<?= $class ?>">
                                                <?= ucfirst($donation['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a
                                                href="/admin/edit-donation?id=<?= $donation['id'] ?>"
                                                title="Edit"
                                            >
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <a
                                                href="#"
                                                class="link-danger ms-2"
                                                onclick="return confirm('Delete this donation?')"
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
                                                    donasi

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