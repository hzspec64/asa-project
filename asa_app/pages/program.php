<?php
require_once __DIR__ . '/../../asa_config.php';
require_once __DIR__ . '/../core/database.php';

$campaigns = $pdo->query("
    SELECT c.*,
        (
            SELECT COALESCE(SUM(amount), 0)
            FROM donations d
            WHERE d.campaign_id = c.id AND d.status = 'paid'
        ) AS raised
    FROM campaigns c
    WHERE c.status = 'active'
    ORDER BY c.created_at DESC
")->fetchAll();
?>
<!doctype html>
<html class="no-js" lang="id">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Program Donasi - <?php echo APP_NAME;?></title>
        <meta name="description" content="Daftar program donasi Asa Palestina yang sedang berjalan beserta capaian pengumpulan dananya.">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php
        require_once __DIR__ . '/../components/head_link.php';
        ?>
    </head>
    <body>
        <?php
        require_once __DIR__ . '/../components/preload.php';
        require_once __DIR__ . '/../components/header.php';
        ?>

        <main>
            <!--? Hero Start -->
            <div class="slider-area2">
                <div class="slider-height2 d-flex align-items-center">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="hero-cap hero-cap2 pt-20 text-center">
                                    <h2>Program Donasi</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Hero End -->

            <!-- Program Start -->
            <div class="our-cases-area section-padding30">
                <div class="container">
                    <div class="row justify-content-center mb-60">
                        <div class="col-xl-7 col-lg-8 col-md-10 col-sm-10">
                            <div class="section-tittle text-center">
                                <span>Ikhtiar kemanusiaan</span>
                                <h2>Mari Wujudkan Asa Bersama</h2>
                                <p class="mt-20">Pilih program di bawah ini dan salurkan donasi terbaik Sahabat Asa. Setiap program kami kelola dengan transparan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php if (empty($campaigns)): ?>
                            <div class="col-12 text-center">
                                <p class="text-muted">Belum ada program donasi yang aktif saat ini. Silakan kembali lagi nanti.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($campaigns as $index => $campaign): ?>
                                <?php
                                $target = (float) $campaign['target_amount'];
                                $raised = (float) $campaign['raised'];
                                $percent = $target > 0 ? min(100, round($raised / $target * 100)) : 0;
                                $image = $campaign['image']
                                    ? '/uploads/campaign/' . htmlspecialchars($campaign['image'])
                                    : 'assets/img/gallery/case' . (($index % 3) + 1) . '.png';
                                $barId = 'programBar' . ($index + 1);
                                ?>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="single-cases mb-40">
                                        <div class="cases-img">
                                            <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($campaign['title']); ?>">
                                        </div>
                                        <div class="cases-caption">
                                            <h3><a href="/donate"><?php echo htmlspecialchars($campaign['title']); ?></a></h3>
                                            <p class="text-muted mb-15"><?php echo mb_strimwidth(strip_tags($campaign['description']), 0, 110, '...'); ?></p>
                                            <!-- Progress Bar -->
                                            <div class="single-skill mb-15">
                                                <div class="bar-progress">
                                                    <div id="<?php echo $barId; ?>" class="barfiller">
                                                        <div class="tipWrap">
                                                            <span class="tip"></span>
                                                        </div>
                                                        <span class="fill" data-percentage="<?php echo $percent; ?>"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- / progress -->
                                            <div class="prices d-flex justify-content-between">
                                                <p>Terkumpul:<span> Rp<?php echo number_format($raised, 0, ',', '.'); ?></span></p>
                                                <p>Target:<span> Rp<?php echo number_format($target, 0, ',', '.'); ?></span></p>
                                            </div>
                                            <a href="/donate" class="btn mt-10 w-100">Donasi Program Ini</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Program End -->
        </main>

        <?php
        require_once __DIR__ . '/../components/footer.php';
        ?>

        <?php
        require_once __DIR__ . '/../components/body_script.php';
        ?>
        <script>
            $(function () {
                $('.barfiller').barfiller();
            });
        </script>
    </body>
</html>
