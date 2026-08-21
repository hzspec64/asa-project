<?php
require_once __DIR__ . '/../../asa_config.php';
require_once __DIR__ . '/../core/database.php';

$slug = $_GET['slug'] ?? '';

$stmt = $pdo->prepare("
    SELECT *
    FROM articles
    WHERE slug = ? AND status = 'published'
    LIMIT 1
");
$stmt->execute([$slug]);
$article = $stmt->fetch();

// Artikel terkait (selain artikel ini)
$related = $pdo->query("
    SELECT *
    FROM articles
    WHERE status = 'published'
    ORDER BY created_at DESC
    LIMIT 4
")->fetchAll();

if (!$article) {
    http_response_code(404);
}
?>
<!doctype html>
    <html class="no-js" lang="id">
        <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title><?php echo $article ? htmlspecialchars($article['title']) : 'Artikel'; ?> - <?php echo APP_NAME;?></title>
        <meta name="description" content="<?php echo $article ? htmlspecialchars(mb_strimwidth(strip_tags($article['content']), 0, 160, '...')) : ''; ?>">
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
            <?php if (!$article): ?>
            <!--? Hero Start -->
            <div class="slider-area2">
                <div class="slider-height2 d-flex align-items-center">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="hero-cap hero-cap2 pt-20 text-center">
                                    <h2>Artikel Tidak Ditemukan</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <section class="blog_area section-padding">
                <div class="container text-center">
                    <p class="text-muted">Maaf, artikel yang Anda cari tidak tersedia atau belum dipublikasikan.</p>
                    <a href="/blog" class="btn">Kembali ke Artikel</a>
                </div>
            </section>
            <?php else: ?>
            <!--? Hero Start -->
            <div class="slider-area2">
                <div class="slider-height2 d-flex align-items-center">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="hero-cap hero-cap2 pt-20 text-center">
                                    <h2><?php echo htmlspecialchars($article['title']); ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Hero End -->
            <!--? Blog Area Start -->
            <section class="blog_area single-post-area section-padding">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 posts-list">
                            <div class="single-post">
                                <?php if (!empty($article['image'])): ?>
                                <div class="feature-img mb-4">
                                    <img class="img-fluid" src="/uploads/article/<?php echo htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                                </div>
                                <?php endif; ?>
                                <div class="blog_details">
                                    <h2 style="color: #2d2d2d;"><?php echo htmlspecialchars($article['title']); ?></h2>
                                    <ul class="blog-info-link mt-3 mb-4">
                                        <li>
                                            <a href="#">
                                                <i class="fa fa-user"></i> Asa Palestina
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="fa fa-calendar"></i> <?php echo date('d M Y', strtotime($article['created_at'])); ?>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="mt-3">
                                        <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="navigation-top">
                                <div class="d-sm-flex justify-content-between text-center">
                                    <div class="col-sm-4 text-center my-2 my-sm-0"></div>
                                    <ul class="social-icons">
                                        <li>
                                            <a href="#">
                                                <i class="fab fa-facebook-f"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="fab fa-twitter"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="fab fa-instagram"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="blog-author">
                                <div class="media align-items-center">
                                    <img src="assets/img/blog/author.png" alt="">
                                    <div class="media-body">
                                        <a href="#">
                                            <h4>Tim Asa Palestina</h4>
                                        </a>
                                        <p>Kami berikhtiar menyampaikan kabar dan penyaluran donasi Sahabat Asa untuk kemanusiaan Palestina.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="blog_right_sidebar">
                                <aside class="single_sidebar_widget search_widget">
                                    <form action="#">
                                        <div class="form-group">
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control" placeholder='Cari artikel' onfocus="this.placeholder = ''" onblur="this.placeholder = 'Cari artikel'">
                                                <div class="input-group-append">
                                                    <button class="btns" type="button">
                                                        <i class="ti-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="button rounded-0 primary-bg text-white w-100 btn_1 boxed-btn" type="submit">Cari</button>
                                    </form>
                                </aside>
                                <aside class="single_sidebar_widget post_category_widget">
                                    <h4 class="widget_title" style="color: #2d2d2d;">Kategori</h4>
                                    <ul class="list cat-list">
                                        <li><a href="#" class="d-flex"><p>Kemanusiaan</p></a></li>
                                        <li><a href="#" class="d-flex"><p>Pendidikan</p></a></li>
                                        <li><a href="#" class="d-flex"><p>Kesehatan</p></a></li>
                                        <li><a href="#" class="d-flex"><p>Penyaluran</p></a></li>
                                    </ul>
                                </aside>
                                <?php if (!empty($related)): ?>
                                <aside class="single_sidebar_widget popular_post_widget">
                                    <h3 class="widget_title" style="color: #2d2d2d;">Artikel Lainnya</h3>
                                    <?php foreach ($related as $index => $recent): ?>
                                        <?php if ((int)$recent['id'] === (int)$article['id']) continue; ?>
                                        <?php
                                        $rImage = $recent['image']
                                            ? '/uploads/article/' . htmlspecialchars($recent['image'])
                                            : 'assets/img/post/post_' . (($index % 10) + 1) . '.png';
                                        ?>
                                        <div class="media post_item">
                                            <img src="<?php echo $rImage; ?>" alt="post" style="width:80px;height:70px;object-fit:cover;">
                                            <div class="media-body">
                                                <a href="/blog_details?slug=<?php echo urlencode($recent['slug']); ?>">
                                                    <h3 style="color: #2d2d2d;"><?php echo htmlspecialchars(mb_strimwidth($recent['title'], 0, 40, '...')); ?></h3>
                                                </a>
                                                <p><?php echo date('d M Y', strtotime($recent['created_at'])); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </aside>
                                <?php endif; ?>
                                <aside class="single_sidebar_widget newsletter_widget">
                                    <h4 class="widget_title" style="color: #2d2d2d;">Newsletter</h4>
                                    <form action="#">
                                        <div class="form-group">
                                            <input type="email" class="form-control" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Masukkan email'" placeholder='Masukkan email' required>
                                        </div>
                                        <button class="button rounded-0 primary-bg text-white w-100 btn_1 boxed-btn" type="submit">Langganan</button>
                                    </form>
                                </aside>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Blog Area End -->
            <?php endif; ?>
        </main>

        <?php
        require_once __DIR__ . '/../components/footer.php';
        ?>

        <?php
        require_once __DIR__ . '/../components/body_script.php';
        ?>
    </body>
</html>
