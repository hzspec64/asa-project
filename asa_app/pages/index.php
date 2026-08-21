<?php
require_once __DIR__ . '/../../asa_config.php';
require_once __DIR__ . '/../core/database.php';

// Kampanye aktif
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
    LIMIT 3
")->fetchAll();

// Artikel terbaru
$articles = $pdo->query("
    SELECT *
    FROM articles
    WHERE status = 'published'
    ORDER BY created_at DESC
    LIMIT 3
")->fetchAll();
?>
<!doctype html>
<html class="no-js" lang="id">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title><?php echo APP_NAME;?></title>
        <meta name="description" content="Asa Palestina - mengumpulkan dan menyalurkan donasi kemanusiaan untuk saudara kita di Palestina.">
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
            <!-- slider Area Start-->
            <div class="slider-area">
                <div class="slider-active">
                    <!-- Single Slider -->
                    <div class="single-slider slider-height d-flex align-items-center">
                        <div class="container">
                            <div class="row">
                                <div class="col-xl-7 col-lg-7 col-md-9 col-sm-10">
                                    <div class="hero__caption">
                                        <span data-animation="fadeInUp" data-delay=".4s">Kemanusiaan untuk Palestina</span>
                                        <h1 data-animation="fadeInUp" data-delay=".6s">Bangun Asa,<br> Tebar Kepedulian.</h1>
                                        <p data-animation="fadeInUp" data-delay=".8s">Asa Palestina lahir dari Adara Relief untuk mengumpulkan dan menyalurkan donasi bagi saudara kita yang tertimpa cobaan di Palestina.</p>
                                        <!-- Hero-btn -->
                                        <div class="hero__btn" data-animation="fadeInUp" data-delay="1s">
                                            <a href="/donate" class="btn hero-btn mb-10">Donasi Sekarang</a>
                                            <a href="/about" class="cal-btn ml-15">
                                                <i class="flaticon-null"></i>
                                                <p>Pelajari Tentang Kami</p>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- slider Area End-->

            <!--? Visi Misi Start -->
            <div class="service-area section-padding30">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-xl-7 col-lg-8 col-md-10 col-sm-10">
                            <!-- Section Tittle -->
                            <div class="section-tittle text-center mb-80">
                                <span>Apa yang kami lakukan</span>
                                <h2>Berikhtiar Meringankan Penderitaan</h2>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="single-cat text-center mb-50">
                                <div class="cat-icon">
                                    <span class="flaticon-null-1"></span>
                                </div>
                                <div class="cat-cap">
                                    <h5><a href="/program">Bantuan Kemanusiaan</a></h5>
                                    <p>Penyaluran pangan, air bersih, obat-obatan, dan kebutuhan darurat bagi warga Palestina yang terdampak konflik.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="single-cat active text-center mb-50">
                                <div class="cat-icon">
                                    <span class="flaticon-think"></span>
                                </div>
                                <div class="cat-cap">
                                    <h5><a href="/program">Pendidikan & Kesehatan</a></h5>
                                    <p>Mendukung layanan pendidikan dan kesehatan dasar agar anak-anak Palestina tetap memiliki masa depan.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="single-cat text-center mb-50">
                                <div class="cat-icon">
                                    <span class="flaticon-gear"></span>
                                </div>
                                <div class="cat-cap">
                                    <h5><a href="/program">Transparansi Dana</a></h5>
                                    <p>Setiap rupiah dilaporkan secara berkala melalui laporan penyaluran agar donatur tenang dan terpercaya.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Visi Misi End -->

            <!--? About Start-->
            <section class="about-low-area section-padding2">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6 col-md-10">
                            <div class="about-caption mb-50">
                                <!-- Section Tittle -->
                                <div class="section-tittle mb-35">
                                    <span>Tentang Asa Palestina</span>
                                    <h2>Kami Ada Karena Kemanusiaan Tak mengenal Batas</h2>
                                </div>
                                <p>Asa Palestina merupakan inisiatif pengumpulan dana donasi yang turunannya berakar dari Adara Relief, lembaga kemanusiaan yang telah lama berkiprah dalam misi kemanusiaan. Kami hadir untuk menjembatani kepedulian Sahabat Asa di Indonesia dengan saudara-saudara di Palestina yang membutuhkan.</p>
                                <p>Melalui program donasi yang transparan, kami berikhtiar meringankan beban penderitaan dan menumbuhkan asa baru bagi mereka yang tertimpa cobaan.</p>
                                <a href="/about" class="btn">Pelajari Selengkapnya</a>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <!-- about-img -->
                            <div class="about-img ">
                                <div class="about-font-img d-none d-lg-block">
                                    <img src="assets/img/gallery/about2.png" alt="Asa Palestina">
                                </div>
                                <div class="about-back-img ">
                                    <img src="assets/img/gallery/about1.png" alt="Kemanusiaan Palestina">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- About End-->

            <!-- Program Donasi Start -->
            <div class="our-cases-area section-padding30">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-xl-7 col-lg-8 col-md-10 col-sm-10">
                            <!-- Section Tittle -->
                            <div class="section-tittle text-center mb-80">
                                <span>Program donasi kami</span>
                                <h2>Program yang Sedang Berjalan</h2>
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
                                $barId = 'homeBar' . ($index + 1);
                                ?>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="single-cases mb-40">
                                        <div class="cases-img">
                                            <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($campaign['title']); ?>">
                                        </div>
                                        <div class="cases-caption">
                                            <h3><a href="/program"><?php echo htmlspecialchars($campaign['title']); ?></a></h3>
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
                    <div class="row">
                        <div class="col-12 text-center mt-20">
                            <a href="/program" class="btn">Lihat Semua Program</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Program Donasi End -->

            <!--? Count Down Start -->
            <div class="count-down-area pt-25 section-bg" data-background="assets/img/gallery/section_bg02.png">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-12 col-md-12">
                            <div class="count-down-wrapper">
                                <div class="row justify-content-between">
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <!-- Counter Up -->
                                        <div class="single-counter text-center">
                                            <span class="counter color-green">1</span>
                                            <span class="plus">+</span>
                                            <p class="color-green">Lembaga Kemanusiaan</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <!-- Counter Up -->
                                        <div class="single-counter text-center">
                                            <span class="counter color-green">100</span>
                                            <span class="plus">%</span>
                                            <p class="color-green">Transparansi Dana</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <!-- Counter Up -->
                                        <div class="single-counter text-center">
                                            <span class="counter color-green">3</span>
                                            <span class="plus">+</span>
                                            <p class="color-green">Program Donasi</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <!-- Counter Up -->
                                        <div class="single-counter text-center">
                                            <span class="counter color-green">1</span>
                                            <span class="plus">+</span>
                                            <p class="color-green">Sahabat Asa</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Count Down End -->

            <!--? Artikel Start -->
            <section class="home-blog-area section-padding30">
                <div class="container">
                    <!-- Section Tittle -->
                    <div class="row justify-content-center">
                        <div class="col-xl-6 col-lg-7 col-md-9 col-sm-10">
                            <div class="section-tittle text-center mb-90">
                                <span>Kabar terbaru</span>
                                <h2>Artikel & Cerita Kemanusiaan</h2>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php if (empty($articles)): ?>
                            <div class="col-12 text-center">
                                <p class="text-muted">Belum ada artikel yang dipublikasikan.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($articles as $index => $article): ?>
                                <?php
                                $image = $article['image']
                                    ? '/uploads/article/' . htmlspecialchars($article['image'])
                                    : 'assets/img/gallery/home-blog' . (($index % 2) + 1) . '.png';
                                $date = date('d M Y', strtotime($article['created_at']));
                                ?>
                                <div class="col-xl-4 col-lg-4 col-md-6">
                                    <div class="home-blog-single mb-30">
                                        <div class="blog-img-cap">
                                            <div class="blog-img">
                                                <img src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                                                <!-- Blog date -->
                                                <div class="blog-date text-center">
                                                    <span><?php echo date('d', strtotime($article['created_at'])); ?></span>
                                                    <p><?php echo date('M', strtotime($article['created_at'])); ?></p>
                                                </div>
                                            </div>
                                            <div class="blog-cap">
                                                <p>Asa Palestina</p>
                                                <h3><a href="/blog_details?slug=<?php echo urlencode($article['slug']); ?>"><?php echo htmlspecialchars($article['title']); ?></a></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <div class="col-12 text-center mt-20">
                            <a href="/blog" class="btn">Lihat Semua Artikel</a>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Artikel End -->

            <!-- Want To work -->
            <section class="wantToWork-area ">
                <div class="container">
                    <div class="wants-wrapper w-padding2  section-bg" data-background="assets/img/gallery/section_bg01.png">
                        <div class="row align-items-center justify-content-between">
                            <div class="col-xl-7 col-lg-9 col-md-8">
                                <div class="wantToWork-caption wantToWork-caption2">
                                    <h2>Donasimu Adalah Asa Bagi Mereka</h2>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-4">
                                <a href="/donate" class="btn white-btn f-right sm-left">Donasi Sekarang</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Want To work End -->
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
