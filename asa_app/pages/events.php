<?php
require_once __DIR__ . '/../../asa_config.php';
require_once __DIR__ . '/../core/database.php';

$galleries = $pdo->query("
    SELECT *
    FROM galleries
    ORDER BY created_at DESC
")->fetchAll();
?>
<!doctype html>
<html class="no-js" lang="id">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Kegiatan - <?php echo APP_NAME;?></title>
        <meta name="description" content="Dokumentasi kegiatan dan penyaluran donasi Asa Palestina melalui galeri kemanusiaan.">
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
                                    <h2>Kegiatan</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Hero End -->

            <!--? Gallery Start -->
            <div class="our-cases-area section-padding30">
                <div class="container">
                    <div class="row justify-content-center mb-60">
                        <div class="col-xl-7 col-lg-8 col-md-10 col-sm-10">
                            <div class="section-tittle text-center">
                                <span>Dokumentasi lapangan</span>
                                <h2>Galeri Kemanusiaan</h2>
                                <p class="mt-20">Setiap foto adalah jejak asa yang telah Sahabat Asa salurkan bagi saudara di Palestina.</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php if (empty($galleries)): ?>
                            <div class="col-12 text-center">
                                <p class="text-muted">Belum ada dokumentasi kegiatan yang ditampilkan.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($galleries as $gallery): ?>
                                <div class="col-lg-4 col-md-6 col-sm-6 mb-30">
                                    <div class="single-cases">
                                        <div class="cases-img">
                                            <img src="/uploads/gallery/<?php echo htmlspecialchars($gallery['image']); ?>" alt="<?php echo htmlspecialchars($gallery['title']); ?>">
                                        </div>
                                        <div class="cases-caption">
                                            <h3><?php echo htmlspecialchars($gallery['title']); ?></h3>
                                            <?php if (!empty($gallery['description'])): ?>
                                                <p class="text-muted"><?php echo htmlspecialchars($gallery['description']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Gallery End -->
        </main>

        <?php
        require_once __DIR__ . '/../components/footer.php';
        ?>

        <?php
        require_once __DIR__ . '/../components/body_script.php';
        ?>
    </body>
</html>
