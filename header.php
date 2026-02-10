<?php
// Session check ada di sini
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

// Mengambil nama halaman saat ini untuk digunakan di sidebar
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Web IPMAP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin_style.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; // <-- INI YANG MEMANGGIL SIDEBAR ?>
        
        <main class="admin-main-content">
            <div class="content-header">
                <h1>
                    <?php 
                        // Judul halaman dinamis berdasarkan nama file
                        switch($currentPage) {
                            case 'dashboard.php': echo 'Dashboard'; break;
                            case 'kelola_pesan.php': echo 'Kotak Masuk'; break;
                            case 'baca_pesan.php': echo 'Baca Pesan'; break;
                            case 'kelola_slider.php': echo 'Kelola Slider'; break;
                            case 'kelola_berita.php': echo 'Kelola Berita'; break;
                            case 'tambah_berita.php': echo 'Tambah Berita'; break;
                            case 'edit_berita.php': echo 'Edit Berita'; break;
                            case 'kelola_profil.php': echo 'Kelola Profil'; break;
                            case 'edit_konten_profil.php': echo 'Edit Konten Profil'; break;
                            case 'kelola_guru.php': echo 'Kelola Guru & Staff'; break;
                            case 'kelola_pendaftaran.php': echo 'Pendaftar PPDB'; break;
                            case 'kelola_kolom_ppdb.php': echo 'Kelola Kolom PPDB'; break;
                            case 'pengaturan_akun.php': echo 'Pengaturan Akun'; break;
                            default: echo 'Admin Panel';
                        }
                    ?>
                </h1>
                <div class="user-info">
                    <span>Selamat Datang, <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong></span>
                    <a href="pengaturan_akun.php">⚙️ Pengaturan</a>
                    <a href="logout.php">🚪 Logout</a>
                </div>
            </div>
            <div class="content-box">