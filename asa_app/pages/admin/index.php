<?php
require_once __DIR__ . '/../../../asa_config.php';
require_once __DIR__ . '/../../core/session.php';
require_once __DIR__ . '/../../core/guard.php';
require_once __DIR__ . '/../../core/database.php';

$CURRENT_PAGE = 'dashboard';

/*
|--------------------------------------------------------------------------
| Dashboard Statistics
|--------------------------------------------------------------------------
*/

// Total campaigns
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM campaigns
");

$totalCampaigns = (int) $stmt->fetchColumn();


// Active campaigns
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM campaigns
    WHERE status = 'active'
");

$activeCampaigns = (int) $stmt->fetchColumn();


// Total paid donations
$stmt = $pdo->query("
    SELECT COALESCE(SUM(amount), 0)
    FROM donations
    WHERE status = 'paid'
");

$totalDonations = (float) $stmt->fetchColumn();


// Total distributions
$stmt = $pdo->query("
    SELECT COALESCE(SUM(amount), 0)
    FROM distributions
");

$totalDistributions = (float) $stmt->fetchColumn();


// Pending donations
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM donations
    WHERE status = 'pending'
");

$pendingDonations = (int) $stmt->fetchColumn();


// Total users
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM users
");

$totalUsers = (int) $stmt->fetchColumn();


// Total donation transactions
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM donations
");

$totalDonationTransactions = (int) $stmt->fetchColumn();


// Current active campaigns with donation progress
$stmt = $pdo->query("
    SELECT
        c.id,
        c.title,
        c.target_amount,
        COALESCE(
            SUM(
                CASE
                    WHEN d.status = 'paid'
                    THEN d.amount
                    ELSE 0
                END
            ),
            0
        ) AS collected_amount
    FROM campaigns c
    LEFT JOIN donations d
        ON d.campaign_id = c.id
    WHERE c.status = 'active'
    GROUP BY
        c.id,
        c.title,
        c.target_amount
    ORDER BY c.created_at DESC
    LIMIT 5
");

$activeCampaignList = $stmt->fetchAll();


// Recent donations
$stmt = $pdo->query("
    SELECT
        d.id,
        d.donor_name,
        d.amount,
        d.status,
        d.donation_date,
        c.title AS campaign_title
    FROM donations d
    INNER JOIN campaigns c
        ON c.id = d.campaign_id
    ORDER BY d.donation_date DESC
    LIMIT 5
");

$recentDonations = $stmt->fetchAll();


// Recent distributions
$stmt = $pdo->query("
    SELECT
        d.id,
        d.title,
        d.amount,
        d.distribution_date,
        c.title AS campaign_title
    FROM distributions d
    INNER JOIN campaigns c
        ON c.id = d.campaign_id
    ORDER BY d.distribution_date DESC, d.id DESC
    LIMIT 5
");

$recentDistributions = $stmt->fetchAll();


// Monthly donation statistics for current year
$currentYear = date('Y');

$stmt = $pdo->prepare("
    SELECT
        MONTH(donation_date) AS month,
        COALESCE(SUM(amount), 0) AS total
    FROM donations
    WHERE
        status = 'paid'
        AND YEAR(donation_date) = ?
    GROUP BY MONTH(donation_date)
    ORDER BY month
");

$stmt->execute([$currentYear]);

$monthlyDonationRows = $stmt->fetchAll();

$monthlyDonations = array_fill(1, 12, 0);

foreach ($monthlyDonationRows as $row) {
    $monthlyDonations[(int) $row['month']] = (float) $row['total'];
}


// Monthly distribution statistics for current year
$stmt = $pdo->prepare("
    SELECT
        MONTH(distribution_date) AS month,
        COALESCE(SUM(amount), 0) AS total
    FROM distributions
    WHERE YEAR(distribution_date) = ?
    GROUP BY MONTH(distribution_date)
    ORDER BY month
");

$stmt->execute([$currentYear]);

$monthlyDistributionRows = $stmt->fetchAll();

$monthlyDistributions = array_fill(1, 12, 0);

foreach ($monthlyDistributionRows as $row) {
    $monthlyDistributions[(int) $row['month']] = (float) $row['total'];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php
    require_once __DIR__ . '/../../components/admin/head_link.php';
    ?>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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

            <!-- HEADER -->
            <div class="row">
                <div class="col-12">

                    <div class="mb-6">

                        <h1 class="fs-3 mb-1">
                            Dashboard
                        </h1>

                        <p class="opacity-50">
                            Overview of your philanthropy activities.
                        </p>

                    </div>

                </div>
            </div>


            <!-- KPI CARDS -->
            <div class="row g-3 mb-3">

                <!-- Campaigns -->
                <div class="col-lg-3 col-12">

                    <div class="card p-4 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-2">

                        <div class="d-flex gap-3">

                            <div class="icon-shape icon-md bg-primary text-white rounded-2">

                                <i class="ti ti-heart-handshake fs-4"></i>

                            </div>

                            <div>

                                <h2 class="mb-3 fs-6">
                                    Total Campaigns
                                </h2>

                                <h3 class="fw-bold mb-0">
                                    <?= number_format($totalCampaigns) ?>
                                </h3>

                                <p class="text-primary mb-0 small">
                                    <?= number_format($activeCampaigns) ?> active
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Donations -->
                <div class="col-lg-3 col-12">

                    <div class="card p-4 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-2">

                        <div class="d-flex gap-3">

                            <div class="icon-shape icon-md bg-success text-white rounded-2">

                                <i class="ti ti-cash fs-4"></i>

                            </div>

                            <div>

                                <h2 class="mb-3 fs-6">
                                    Total Donations
                                </h2>

                                <h3 class="fw-bold mb-0">
                                    Rp <?= number_format($totalDonations, 0, ',', '.') ?>
                                </h3>

                                <p class="text-success mb-0 small">
                                    <?= number_format($totalDonationTransactions) ?> transactions
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Distribution -->
                <div class="col-lg-3 col-12">

                    <div class="card p-4 bg-info bg-opacity-10 border border-info border-opacity-25 rounded-2">

                        <div class="d-flex gap-3">

                            <div class="icon-shape icon-md bg-info text-white rounded-2">

                                <i class="ti ti-send fs-4"></i>

                            </div>

                            <div>

                                <h2 class="mb-3 fs-6">
                                    Total Distributed
                                </h2>

                                <h3 class="fw-bold mb-0">
                                    Rp <?= number_format($totalDistributions, 0, ',', '.') ?>
                                </h3>

                                <p class="text-info mb-0 small">
                                    Fund utilization
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Pending -->
                <div class="col-lg-3 col-12">

                    <div class="card p-4 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-2">

                        <div class="d-flex gap-3">

                            <div class="icon-shape icon-md bg-warning text-white rounded-2">

                                <i class="ti ti-clock fs-4"></i>

                            </div>

                            <div>

                                <h2 class="mb-3 fs-6">
                                    Pending Donations
                                </h2>

                                <h3 class="fw-bold mb-0">
                                    <?= number_format($pendingDonations) ?>
                                </h3>

                                <p class="text-warning mb-0 small">
                                    Need verification
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- SUMMARY CARDS -->
            <div class="row g-3 mb-3">

                <!-- Balance -->
                <div class="col-lg-4 col-12">

                    <div class="card">

                        <div class="card-body p-4">

                            <div class="d-flex justify-content-between border-bottom pb-5 mb-3">

                                <div>

                                    <h3 class="fw-bold h4">
                                        Rp <?= number_format($totalDonations - $totalDistributions, 0, ',', '.') ?>
                                    </h3>

                                    <span>
                                        Remaining Funds
                                    </span>

                                </div>

                                <div>

                                    <i class="ti ti-wallet fs-1 text-primary"></i>

                                </div>

                            </div>

                            <div class="d-flex justify-content-between align-items-center small">

                                <div class="text-muted">
                                    Donations minus distributions
                                </div>

                                <div>
                                    <a
                                        href="/admin/donations"
                                        class="link-primary text-decoration-underline"
                                    >
                                        View
                                    </a>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Active Campaigns -->
                <div class="col-lg-4 col-12">

                    <div class="card">

                        <div class="card-body p-4">

                            <div class="d-flex justify-content-between border-bottom pb-5 mb-3">

                                <div>

                                    <h3 class="fw-bold h4">
                                        <?= number_format($activeCampaigns) ?>
                                    </h3>

                                    <span>
                                        Active Campaigns
                                    </span>

                                </div>

                                <div>

                                    <i class="ti ti-heart fs-1 text-danger"></i>

                                </div>

                            </div>

                            <div class="d-flex justify-content-between align-items-center small">

                                <div class="text-muted">
                                    Currently accepting donations
                                </div>

                                <div>
                                    <a
                                        href="/admin/campaigns"
                                        class="link-primary text-decoration-underline"
                                    >
                                        View
                                    </a>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Users -->
                <div class="col-lg-4 col-12">

                    <div class="card">

                        <div class="card-body p-4">

                            <div class="d-flex justify-content-between border-bottom pb-5 mb-3">

                                <div>

                                    <h3 class="fw-bold h4">
                                        <?= number_format($totalUsers) ?>
                                    </h3>

                                    <span>
                                        System Users
                                    </span>

                                </div>

                                <div>

                                    <i class="ti ti-users fs-1 text-warning"></i>

                                </div>

                            </div>

                            <div class="d-flex justify-content-between align-items-center small">

                                <div class="text-muted">
                                    Administrators and staff
                                </div>

                                <div>
                                    <a
                                        href="/admin/users"
                                        class="link-primary text-decoration-underline"
                                    >
                                        View
                                    </a>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- CHARTS -->
            <div class="row g-3 mb-3">

                <!-- Donation vs Distribution -->
                <div class="col-12 col-lg-8">

                    <div class="card">

                        <div class="card-header d-flex justify-content-between align-items-center bg-transparent px-4 py-3">

                            <h3 class="h5 mb-0">
                                Donations vs Distributions
                            </h3>

                            <span class="small text-muted">
                                <?= $currentYear ?>
                            </span>

                        </div>

                        <div class="card-body p-4">

                            <div id="donationDistributionChart"></div>

                        </div>

                    </div>

                </div>


                <!-- Campaign Overview -->
                <div class="col-12 col-lg-4">

                    <div class="card">

                        <div class="card-header bg-transparent px-4 py-3">

                            <h3 class="h5 mb-0">
                                Campaign Overview
                            </h3>

                        </div>

                        <div class="card-body p-4">

                            <div id="campaignChart"></div>

                            <div class="row text-center border-top mt-4 pt-4">

                                <div class="col-6 border-end">

                                    <h3 class="fw-bold mb-2">
                                        <?= number_format($activeCampaigns) ?>
                                    </h3>

                                    <small class="text-secondary">
                                        Active
                                    </small>

                                </div>

                                <div class="col-6">

                                    <h3 class="fw-bold mb-2">
                                        <?= number_format($totalCampaigns - $activeCampaigns) ?>
                                    </h3>

                                    <small class="text-secondary">
                                        Other
                                    </small>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- LISTS -->
            <div class="row g-3">

                <!-- Active Campaigns -->
                <div class="col-lg-4">

                    <div class="card h-100">

                        <div class="card-header d-flex justify-content-between align-items-center px-4 py-3">

                            <h4 class="mb-0 h5">
                                Active Campaigns
                            </h4>

                            <a
                                href="/admin/campaigns"
                                class="small text-primary text-decoration-underline"
                            >
                                View All
                            </a>

                        </div>

                        <ul class="list-group list-group-flush">

                            <?php if (empty($activeCampaignList)): ?>

                                <li class="list-group-item text-center py-4 text-muted">
                                    No active campaigns.
                                </li>

                            <?php else: ?>

                                <?php foreach ($activeCampaignList as $campaign): ?>

                                    <?php

                                    $target = (float) $campaign['target_amount'];
                                    $collected = (float) $campaign['collected_amount'];

                                    $percentage = $target > 0
                                        ? ($collected / $target) * 100
                                        : 0;

                                    $percentage = min(100, $percentage);

                                    ?>

                                    <li class="list-group-item">

                                        <div class="d-flex justify-content-between align-items-start mb-2">

                                            <div class="flex-grow-1">

                                                <p class="mb-1">
                                                    <?= htmlspecialchars($campaign['title']) ?>
                                                </p>

                                                <small class="text-muted">
                                                    Rp <?= number_format($collected, 0, ',', '.') ?>
                                                    /
                                                    Rp <?= number_format($target, 0, ',', '.') ?>
                                                </small>

                                            </div>

                                            <span class="badge bg-primary-subtle text-primary">
                                                <?= number_format($percentage, 0) ?>%
                                            </span>

                                        </div>

                                        <div class="progress" style="height: 5px;">

                                            <div
                                                class="progress-bar"
                                                style="width: <?= $percentage ?>%"
                                            ></div>

                                        </div>

                                    </li>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </ul>

                    </div>

                </div>


                <!-- Recent Donations -->
                <div class="col-lg-4">

                    <div class="card h-100">

                        <div class="card-header d-flex justify-content-between align-items-center px-4 py-3">

                            <h4 class="mb-0 h5">
                                Recent Donations
                            </h4>

                            <a
                                href="/admin/donations"
                                class="small text-primary text-decoration-underline"
                            >
                                View All
                            </a>

                        </div>

                        <ul class="list-group list-group-flush">

                            <?php if (empty($recentDonations)): ?>

                                <li class="list-group-item text-center py-4 text-muted">
                                    No donations yet.
                                </li>

                            <?php else: ?>

                                <?php foreach ($recentDonations as $donation): ?>

                                    <li class="list-group-item d-flex align-items-center gap-3">

                                        <div class="icon-shape icon-sm bg-success-subtle text-success rounded">

                                            <i class="ti ti-cash"></i>

                                        </div>

                                        <div class="flex-grow-1">

                                            <p class="mb-1">
                                                <?= htmlspecialchars($donation['donor_name']) ?>
                                            </p>

                                            <div class="d-flex align-items-center gap-2 text-muted">

                                                <small>
                                                    Rp <?= number_format($donation['amount'], 0, ',', '.') ?>
                                                </small>

                                                <small>
                                                    •
                                                </small>

                                                <small>
                                                    <?= htmlspecialchars($donation['campaign_title']) ?>
                                                </small>

                                            </div>

                                        </div>

                                        <?php if ($donation['status'] === 'paid'): ?>

                                            <span class="badge bg-success-subtle text-success">
                                                Paid
                                            </span>

                                        <?php elseif ($donation['status'] === 'pending'): ?>

                                            <span class="badge bg-warning-subtle text-warning">
                                                Pending
                                            </span>

                                        <?php else: ?>

                                            <span class="badge bg-danger-subtle text-danger">
                                                Cancelled
                                            </span>

                                        <?php endif; ?>

                                    </li>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </ul>

                    </div>

                </div>


                <!-- Recent Distributions -->
                <div class="col-lg-4">

                    <div class="card h-100">

                        <div class="card-header d-flex justify-content-between align-items-center px-4 py-3">

                            <h4 class="mb-0 h5">
                                Recent Distributions
                            </h4>

                            <a
                                href="/admin/distributions"
                                class="small text-primary text-decoration-underline"
                            >
                                View All
                            </a>

                        </div>

                        <ul class="list-group list-group-flush">

                            <?php if (empty($recentDistributions)): ?>

                                <li class="list-group-item text-center py-4 text-muted">
                                    No distributions yet.
                                </li>

                            <?php else: ?>

                                <?php foreach ($recentDistributions as $distribution): ?>

                                    <li class="list-group-item d-flex align-items-center gap-3">

                                        <div class="icon-shape icon-sm bg-info-subtle text-info rounded">

                                            <i class="ti ti-send"></i>

                                        </div>

                                        <div class="flex-grow-1">

                                            <p class="mb-1">
                                                <?= htmlspecialchars($distribution['title']) ?>
                                            </p>

                                            <div class="d-flex align-items-center gap-2 text-muted">

                                                <small>
                                                    Rp <?= number_format($distribution['amount'], 0, ',', '.') ?>
                                                </small>

                                                <small>
                                                    •
                                                </small>

                                                <small>
                                                    <?= htmlspecialchars($distribution['campaign_title']) ?>
                                                </small>

                                            </div>

                                        </div>

                                        <span class="badge bg-info-subtle text-info">
                                            Distributed
                                        </span>

                                    </li>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </ul>

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


    <!-- CHARTS -->
    <script>

        /*
        |--------------------------------------------------------------------------
        | Donation vs Distribution Chart
        |--------------------------------------------------------------------------
        */

        const donationDistributionChart = new ApexCharts(
            document.querySelector("#donationDistributionChart"),
            {
                chart: {
                    type: 'bar',
                    height: 320,
                    toolbar: {
                        show: false
                    }
                },

                series: [
                    {
                        name: 'Donations',
                        data: <?= json_encode(array_values($monthlyDonations)) ?>
                    },
                    {
                        name: 'Distributions',
                        data: <?= json_encode(array_values($monthlyDistributions)) ?>
                    }
                ],

                xaxis: {
                    categories: [
                        'Jan',
                        'Feb',
                        'Mar',
                        'Apr',
                        'May',
                        'Jun',
                        'Jul',
                        'Aug',
                        'Sep',
                        'Oct',
                        'Nov',
                        'Dec'
                    ]
                },

                dataLabels: {
                    enabled: false
                },

                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return 'Rp ' + Number(value).toLocaleString('id-ID');
                        }
                    }
                },

                tooltip: {
                    y: {
                        formatter: function (value) {
                            return 'Rp ' + Number(value).toLocaleString('id-ID');
                        }
                    }
                },

                legend: {
                    position: 'top'
                }
            }
        );

        donationDistributionChart.render();


        /*
        |--------------------------------------------------------------------------
        | Campaign Chart
        |--------------------------------------------------------------------------
        */

        const campaignChart = new ApexCharts(
            document.querySelector("#campaignChart"),
            {
                chart: {
                    type: 'donut',
                    height: 250
                },

                series: [
                    <?= $activeCampaigns ?>,
                    <?= max(0, $totalCampaigns - $activeCampaigns) ?>
                ],

                labels: [
                    'Active',
                    'Other'
                ],

                legend: {
                    position: 'bottom'
                },

                dataLabels: {
                    enabled: true
                }
            }
        );

        campaignChart.render();

    </script>

</body>

</html>