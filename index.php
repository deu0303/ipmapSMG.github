<?php include 'includes/header.php'; ?>

<section class="hero">
    <?php
    // Ambil gambar dari database untuk slider
    $slider_images = mysqli_query($conn, "SELECT image_filename FROM slider_images LIMIT 5");
    if ($slider_images && mysqli_num_rows($slider_images) > 0) {
        while ($img = mysqli_fetch_assoc($slider_images)) {
            // Tampilkan setiap gambar sebagai slide
            echo '<div class="hero-slide" style="background-image: url(\'assets/images/slider/' . htmlspecialchars($img['image_filename']) . '\');"></div>';
        }
    } else {
        // Fallback jika tidak ada gambar di database
        echo '<div class="hero-slide" style="background-image: url(\'https://via.placeholder.com/1920x1080.png?text=Selamat+Datang\'); animation: none; opacity: 1;"></div>';
    }
    ?>

    <div class="hero-overlay">
        <h1>Selamat Datang di Web IPMAP</h1>
        <p>Mencetak Generasi Cerdas, Kreatif, dan Berakhlak Mulia.</p>
        <a href="ppdb.php" class="btn">Pendaftaran PPDB</a>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <h2 class="section-title">Sekilas Tentang Kami</h2>
        <div class="stats-grid">
            <?php
                // Hitung jumlah Mahasiswa (gunakan tabel students_ppdb)
                $result_mahasiswa = mysqli_query($conn, "SELECT id FROM students_ppdb");
                $jml_mahasiswa = $result_mahasiswa ? mysqli_num_rows($result_mahasiswa) : 0;

                // Hitung jumlah Alumni jika tabel 'alumni' ada, otherwise 0
                $jml_alumni = 0;
                $tbl_check = mysqli_query($conn, "SHOW TABLES LIKE 'alumni'");
                if ($tbl_check && mysqli_num_rows($tbl_check) > 0) {
                    $res_al = mysqli_query($conn, "SELECT id FROM alumni");
                    $jml_alumni = $res_al ? mysqli_num_rows($res_al) : 0;
                }

                // Hitung jumlah Pengurus: coba hitung dari kolom position di teachers, fallback ke jumlah teachers
                $jml_pengurus = 0;
                $res_peng = mysqli_query($conn, "SELECT COUNT(*) AS c FROM teachers WHERE position LIKE '%pengurus%' OR position LIKE '%pengur%'");
                if ($res_peng) {
                    $rowp = mysqli_fetch_assoc($res_peng);
                    $jml_pengurus = intval($rowp['c']);
                }
                if ($jml_pengurus === 0) {
                    $res_all = mysqli_query($conn, "SELECT id FROM teachers");
                    $jml_pengurus = $res_all ? mysqli_num_rows($res_all) : 0;
                }
            ?>
            <div class="stat-card">
                <h3><?php echo $jml_mahasiswa; ?></h3>
                <p>Mahasiswa</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $jml_alumni; ?></h3>
                <p>Alumni</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $jml_pengurus; ?></h3>
                <p>Pengurus</p>
            </div>
        </div>
    </div>
</section>

<section class="page-section" style="background-color: #ffffff;">
    <div class="container">
        <h2 class="section-title">Berita & Kegiatan Terbaru</h2>
        <div class="post-grid">
            <?php
            $latest_posts = mysqli_query($conn, "SELECT id, title, content, image, created_at FROM posts ORDER BY created_at DESC LIMIT 3");
            if ($latest_posts && mysqli_num_rows($latest_posts) > 0) {
                while ($post = mysqli_fetch_assoc($latest_posts)) {
                    $image_path = 'assets/images/posts/' . htmlspecialchars($post['image']);
                    if (!file_exists($image_path) || empty($post['image'])) {
                        $image_path = 'https://via.placeholder.com/400x250.png?text=Web+IPMAP';
                    }
            ?>
                <div class="post-card">
                    <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="post-card-img">
                    <div class="post-card-content">
                        <h4><a href="detail_postingan.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h4>
                        <p><?php echo substr(strip_tags($post['content']), 0, 100) . '...'; ?></p>
                        <small><?php echo date('d F Y', strtotime($post['created_at'])); ?></small>
                    </div>
                </div>
            <?php
                }
            } else {
                echo "<p style='text-align:center;'>Belum ada berita untuk ditampilkan.</p>";
            }
            ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>