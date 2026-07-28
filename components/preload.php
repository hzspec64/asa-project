<?php
require_once __DIR__ . '/../core/session.php';

$isDev = true;

if (defined('IS_DEV')) {
    $isDev = IS_DEV;
}

if ($isDev) echo '<label class="github-fork-ribbon left-top" data-ribbon="Development" title="Development">Development</label>';
?>

<!-- ? Preloader Start -->
<div id="preloader-active">
    <div class="preloader d-flex align-items-center justify-content-center">
        <div class="preloader-inner position-relative">
            <div class="preloader-circle"></div>
            <div class="preloader-img pere-text">
                <img src="assets/img/logo/loader.png" alt="">
            </div>
        </div>
    </div>
</div>
<!-- Preloader Start -->