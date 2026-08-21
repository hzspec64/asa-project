<?php
require_once __DIR__ . '/../../asa_config.php';
require_once __DIR__ . '/../core/database.php';
?>
<!doctype html>
<html class="no-js" lang="id">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Tentang Kami - <?php echo APP_NAME;?></title>
        <meta name="description" content="Asa Palestina adalah inisiatif pengumpulan dana donasi kemanusiaan yang turunannya berakar dari Adara Relief.">
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
                                    <h2>Tentang Kami</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Hero End -->

            <!--? Visi Misi Start -->
            <div class="service-area section-padding30">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-xl-7 col-lg-8 col-md-10 col-sm-10">
                            <!-- Section Tittle -->
                            <div class="section-tittle text-center mb-80">
                                <span>Apa yang kami pegang</span>
                                <h2>Visi & Misi Kemanusiaan</h2>
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
                                    <h5><a href="#">Visi</a></h5>
                                    <p>Menjadi jembatan kepedulian yang menghadirkan asa dan kehidupan lebih layak bagi saudara di Palestina.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="single-cat active text-center mb-50">
                                <div class="cat-icon">
                                    <span class="flaticon-think"></span>
                                </div>
                                <div class="cat-cap">
                                    <h5><a href="#">Misi</a></h5>
                                    <p>Mengumpulkan dana donasi secara terbuka dan menyalurkannya melalui program kemanusiaan yang tepat sasaran.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="single-cat text-center mb-50">
                                <div class="cat-icon">
                                    <span class="flaticon-gear"></span>
                                </div>
                                <div class="cat-cap">
                                    <h5><a href="#">Nilai</a></h5>
                                    <p>Amanah, transparan, dan profesional menjadi pondasi setiap langkah penyaluran donasi Sahabat Asa.</p>
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
                                    <span>Kisah kami</span>
                                    <h2>Dari Adara Relief</h2>
                                </div>
                                <p>Asa Palestina lahir dari kepedulian yang panjang. Kami merupakan lembaga kemanusiaan yang telah berkiprah dalam misi membantu sesama di berbagai wilayah terdampak konflik dan bencana.</p>
                                <p>Melihat penderitaan yang berkepanjangan di Palestina, kami mengambil ikhtiar khusus untuk mengumpulkan dan menyalurkan donasi secara fokus. Setiap kontribusi Sahabat Asa kami kelola dengan amanah agar dapat memberikan dampak nyata.</p>
                                <a href="/program" class="btn">Lihat Program Donasi</a>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <!-- about-img -->
                            <div class="about-img ">
                                <div class="about-font-img d-none d-lg-block">
                                    <img src="assets/img/gallery/about2.png" alt="Asa Palestina">
                                </div>
                                <div class="about-back-img ">
                                    <img src="assets/img/gallery/about1.png" alt="Kemanusiaan">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- About End-->

            <!-- Want To work -->
            <section class="wantToWork-area ">
                <div class="container">
                    <div class="wants-wrapper w-padding2  section-bg" data-background="assets/img/gallery/section_bg01.png">
                        <div class="row align-items-center justify-content-between">
                            <div class="col-xl-7 col-lg-9 col-md-8">
                                <div class="wantToWork-caption wantToWork-caption2">
                                    <h2>Jadilah Bagian dari Asa Ini</h2>
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

            <!--? Testimonial Start -->
            <div class="testimonial-area testimonial-padding">
                <div class="container">
                    <!-- Testimonial contents -->
                    <div class="row d-flex justify-content-center">
                        <div class="col-xl-8 col-lg-8 col-md-10">
                            <div class="h1-testimonial-active dot-style">
                                <!-- Single Testimonial -->
                                <div class="single-testimonial text-center">
                                    <div class="testimonial-caption ">
                                        <div class="testimonial-top-cap">
                                            <p>“Donasi terkecil sekalipun, jika dilandasi keikhlasan, dapat menjadi asa bagi mereka yang hidup di tengah kesulitan.”</p>
                                            <span class="text-success">- Sahabat Asa -</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Single Testimonial -->
                                <div class="single-testimonial text-center">
                                    <div class="testimonial-caption ">
                                        <div class="testimonial-top-cap">
                                            <p>“Transparansi adalah amanah. Kami ingin setiap donatur tahu ke mana bantuan mereka disalurkan.”</p>
                                            <span class="text-success">- Tim Asa Palestina -</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Testimonial End -->

            <!--? Count Down Start -->
            <div class="count-down-area pt-25 section-bg" data-background="assets/img/gallery/section_bg02.png">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-12 col-md-12">
                            <div class="count-down-wrapper">
                                <div class="row justify-content-between">
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="single-counter text-center">
                                            <span class="counter color-green">1</span>
                                            <span class="plus">+</span>
                                            <p class="color-green">Lembaga Kemanusiaan</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="single-counter text-center">
                                            <span class="counter color-green">100</span>
                                            <span class="plus">%</span>
                                            <p class="color-green">Transparansi</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="single-counter text-center">
                                            <span class="counter color-green">3</span>
                                            <span class="plus">+</span>
                                            <p class="color-green">Program Donasi</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6">
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
        </main>

        <?php
        require_once __DIR__ . '/../components/footer.php';
        ?>

        <?php
        require_once __DIR__ . '/../components/body_script.php';
        ?>
    </body>
</html>
