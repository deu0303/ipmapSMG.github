<aside class="admin-sidebar">
    <div class="sidebar-header">
        <h2><a href="dashboard.php">ADMIN PANEL</a></h2>
    </div>
    <ul class="sidebar-nav">
        <li>
            <a href="dashboard.php" class="<?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
                🏠 Dashboard
            </a>
        </li>
        <li>
            <a href="kelola_pesan.php" class="<?php echo ($currentPage == 'kelola_pesan.php' || $currentPage == 'baca_pesan.php') ? 'active' : ''; ?>">
                ✉️ Kotak Masuk
            </a>
        </li>
        <li>
            <a href="kelola_slider.php" class="<?php echo ($currentPage == 'kelola_slider.php') ? 'active' : ''; ?>">
                🖼️ Kelola Slider
            </a>
        </li>
        <li>
            <a href="kelola_berita.php" class="<?php echo ($currentPage == 'kelola_berita.php' || strpos($currentPage, '_berita.php')) ? 'active' : ''; ?>">
                📰 Kelola Berita
            </a>
        </li>
        <li>
            <a href="kelola_profil.php" class="<?php echo ($currentPage == 'kelola_profil.php' || strpos($currentPage, '_profil.php') || strpos($currentPage, '_guru.php')) ? 'active' : ''; ?>">
                👤 Kelola Profil
            </a>
        </li>
        <li>
            <a href="kelola_pendaftaran.php" class="<?php echo ($currentPage == 'kelola_pendaftaran.php' || strpos($currentPage, '_pendaftar.php') || strpos($currentPage, '_kolom_ppdb.php')) ? 'active' : ''; ?>">
                🎓 Pendaftaran PPDB
            </a>
        </li>
    </ul>
    <div class="sidebar-footer">
        <a href="../index.php" target="_blank">Lihat Website Publik</a>
    </div>
</aside>