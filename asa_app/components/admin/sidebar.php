<?php
$dashboard_active = (isset($CURRENT_PAGE) && $CURRENT_PAGE === 'dashboard') ? 'active' : '';
$campaign_active = (isset($CURRENT_PAGE) && $CURRENT_PAGE === 'campaign') ? 'active' : '';
$donation_active = (isset($CURRENT_PAGE) && $CURRENT_PAGE === 'donation') ? 'active' : '';
$distribution_active = (isset($CURRENT_PAGE) && $CURRENT_PAGE === 'distribution') ? 'active' : '';
$article_active = (isset($CURRENT_PAGE) && $CURRENT_PAGE === 'article') ? 'active' : '';
$gallery_active = (isset($CURRENT_PAGE) && $CURRENT_PAGE === 'gallery') ? 'active' : '';
$user_active = (isset($CURRENT_PAGE) && $CURRENT_PAGE === 'user') ? 'active' : '';
$contact_active = (isset($CURRENT_PAGE) && $CURRENT_PAGE === 'contact') ? 'active' : '';
?>
<aside id="sidebar" class="sidebar">
    <div class="logo-area">
        <a href="/admin">
            <img src="/assets/admin/images/logo-a.png" alt="" width="36">
            <!-- <img src="/assets/admin/images/logo-asa.png" alt="" width="64"> -->
        </a>
    </div>
    <ul class="nav flex-column">
        <li class="px-4 py-2"><small class="nav-text opacity-50">Navigasi</small></li>
        <li><a class="nav-link <?=$dashboard_active;?>" href="/admin/"><i class="ti ti-home"></i><span class="nav-text">Dashboard</span></a></li>
        <li><a class="nav-link <?=$campaign_active;?>" href="/admin/campaigns"><i class="ti ti-speakerphone"></i><span class="nav-text">Program</span></a></li>
        <li><a class="nav-link <?=$donation_active;?>" href="/admin/donations"><i class="ti ti-moneybag-heart"></i><span class="nav-text">Donasi</span></a></li>
        <li><a class="nav-link <?=$distribution_active;?>" href="/admin/distributions"><i class="ti ti-arrow-left-from-arc"></i><span class="nav-text">Distribusi</span></a></li>
        <li><a class="nav-link <?=$article_active;?>" href="/admin/articles"><i class="ti ti-news"></i><span class="nav-text">Artikel</span></a></li>
        <li><a class="nav-link <?=$gallery_active;?>" href="/admin/gallery"><i class="ti ti-library-photo"></i><span class="nav-text">Galeri</span></a></li>
        <li><a class="nav-link <?=$user_active;?>" href="/admin/users"><i class="ti ti-users"></i><span class="nav-text">Pengelola</span></a></li>
        <li><a class="nav-link <?=$contact_active;?>" href="/admin/contacts"><i class="ti ti-users"></i><span class="nav-text">Pesan</span></a></li>
    </ul>
</aside>
