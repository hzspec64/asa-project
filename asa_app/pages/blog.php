<?php
require_once __DIR__ . '/../../asa_config.php';
require_once __DIR__ . '/../core/database.php';

$articles = $pdo->query("
    SELECT *
    FROM articles
    WHERE status = 'published'
    ORDER BY created_at DESC
")->fetchAll();
?>
<!doctype html>
<html class="no-js" lang="id">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Artikel - <?php echo APP_NAME;?></title>
        <meta name="description" content="Artikel, kabar, dan cerita kemanusiaan seputar program donasi Asa Palestina.">
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
                                <div class="hero-cap hero-cap2 pt-70 text-center">
                                    <h2>Artikel</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Hero End -->
            <!--? Blog Area Start-->
            <section class="blog_area section-padding">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 mb-5 mb-lg-0">
                            <div class="blog_left_sidebar">
                                <?php if (empty($articles)): ?>
                                    <div class="col-12 text-center">
                                        <p class="text-muted">Belum ada artikel yang dipublikasikan.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($articles as $index => $article): ?>
                                        <?php
                                        $image = $article['image']
                                            ? '/uploads/article/' . htmlspecialchars($article['image'])
                                            : 'assets/img/blog/single_blog_' . (($index % 5) + 1) . '.png';
                                        $day = date('d', strtotime($article['created_at']));
                                        $month = date('M', strtotime($article['created_at']));
                                        $excerpt = mb_strimwidth(strip_tags($article['content']), 0, 180, '...');
                                        ?>
                                        <article class="blog_item">
                                            <div class="blog_item_img">
                                                <img class="card-img rounded-0" src="<?php echo $image; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                                                <a href="/blog_details?slug=<?php echo urlencode($article['slug']); ?>" class="blog_item_date">
                                                    <h3><?php echo $day; ?></h3>
                                                    <p><?php echo $month; ?></p>
                                                </a>
                                            </div>
                                            <div class="blog_details">
                                                <a class="d-inline-block" href="/blog_details?slug=<?php echo urlencode($article['slug']); ?>">
                                                    <h2 class="blog-head" style="color: #2d2d2d;"><?php echo htmlspecialchars($article['title']); ?></h2>
                                                </a>
                                                <p><?php echo htmlspecialchars($excerpt); ?></p>
                                                <ul class="blog-info-link">
                                                    <li>
                                                        <a href="#">
                                                            <i class="fa fa-user"></i> Asa Palestina
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="/blog_details?slug=<?php echo urlencode($article['slug']); ?>">
                                                            <i class="fa fa-arrow-right"></i> Baca
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </article>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
                                        <li>
                                            <a href="#" class="d-flex">
                                                <p>Kemanusiaan</p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="d-flex">
                                                <p>Pendidikan</p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="d-flex">
                                                <p>Kesehatan</p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="d-flex">
                                                <p>Penyaluran</p>
                                            </a>
                                        </li>
                                    </ul>
                                </aside>
                                <?php if (!empty($articles)): ?>
                                <aside class="single_sidebar_widget popular_post_widget">
                                    <h3 class="widget_title" style="color: #2d2d2d;">Artikel Terbaru</h3>
                                    <?php foreach (array_slice($articles, 0, 4) as $index => $recent): ?>
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
        </main>

        <?php
        require_once __DIR__ . '/../components/footer.php';
        ?>

        <?php
        require_once __DIR__ . '/../components/body_script.php';
        ?>
    </body>
</html>
