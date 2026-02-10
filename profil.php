<?php include 'includes/header.php'; ?>

<section class="page-header">
    <h1>Profil organisasi ipmap semarang</h1>
</section>

<section class="page-section">
    <div class="container">
        <?php $sub_page = isset($_GET['sub']) ? $_GET['sub'] : 'sejarah'; ?>
        
        <div class="profile-layout">
            <aside class="profile-sidebar">
                <ul>
                    <li><a href="profil.php?sub=sejarah" class="<?= ($sub_page == 'sejarah') ? 'active' : '' ?>">Sejarah Sekolah</a></li>
                    <li><a href="profil.php?sub=visi-misi" class="<?= ($sub_page == 'visi-misi') ? 'active' : '' ?>">Visi & Misi</a></li>
                    <li><a href="profil.php?sub=struktur" class="<?= ($sub_page == 'struktur') ? 'active' : '' ?>">Struktur Organisasi</a></li>
                    <li><a href="profil.php?sub=sarana" class="<?= ($sub_page == 'sarana') ? 'active' : '' ?>">Sarana & Prasarana</a></li>
                    <li><a href="profil.php?sub=guru" class="<?= ($sub_page == 'guru') ? 'active' : '' ?>">Data Seluruh Anggota</a></li>
                </ul>
            </aside>

            <main class="profile-content">
                <?php
                if ($sub_page == 'guru') {
                    echo "<h2>Data Seluruh Anggota</h2>";
                    $result = mysqli_query($conn, "SELECT * FROM teachers ORDER BY name ASC");
                    // Gunakan class .post-grid dan .post-card agar tampilan konsisten
                    echo "<div class='post-grid'>"; 
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $image_path_guru = 'assets/images/teachers/' . htmlspecialchars($row['photo']);
                            if (!file_exists($image_path_guru) || empty($row['photo'])) {
                                $image_path_guru = 'https://via.placeholder.com/400x400.png?text=Foto';
                            }
                            // Kartu untuk setiap guru
                            echo "<div class='post-card' style='text-align:center;'>";
                            echo "<img src='". $image_path_guru ."' alt='".htmlspecialchars($row['name'])."' class='post-card-img' style='height:300px; border-radius:10px;'>";
                            echo "<div class='post-card-content'>";
                            echo "<h4>" . htmlspecialchars($row['name']) . "</h4>";
                            echo "<p style='color:var(--blue-dark); font-weight:500;'>" . htmlspecialchars($row['position']) . "</p>";
                            echo "</div>";
                            echo "</div>";
                        }
                    } else { 
                        echo "<p>Data guru belum tersedia.</p>"; 
                    }
                    echo "</div>";

                } else {
                    $key_halaman_aman = mysqli_real_escape_string($conn, $sub_page);
                    $query = "SELECT konten FROM profil_konten WHERE key_halaman = '$key_halaman_aman'";
                    $result = mysqli_query($conn, $query);

                    if ($result && mysqli_num_rows($result) > 0) {
                        $konten_json = mysqli_fetch_assoc($result)['konten'];
                        $data = json_decode($konten_json, true);

                        if (is_array($data)) {
                            // Tampilkan konten sesuai jenis halaman
                            if ($sub_page == 'sejarah') {
                                echo "<h2>" . htmlspecialchars($data['judul'] ?? 'Judul Belum Diatur') . "</h2>";
                                echo "<div class='konten-apa-adanya'>" . htmlspecialchars($data['deskripsi'] ?? '') . "</div>";
                            } 
                            elseif ($sub_page == 'visi-misi') {
                                echo "<h3>" . htmlspecialchars($data['judul_visi'] ?? 'Visi Belum Diatur') . "</h3>";
                                echo "<div class='konten-apa-adanya'>" . htmlspecialchars($data['isi_visi'] ?? '') . "</div>";
                                echo "<h3 style='margin-top:20px;'>" . htmlspecialchars($data['judul_misi'] ?? 'Misi Belum Diatur') . "</h3>";
                                echo "<div class='konten-apa-adanya'>" . htmlspecialchars($data['isi_misi'] ?? '') . "</div>";
                            } 
                            elseif ($sub_page == 'struktur') {
                                echo "<h2>" . htmlspecialchars($data['judul'] ?? 'Judul Belum Diatur') . "</h2>";
                                echo "<div class='konten-apa-adanya'>" . htmlspecialchars($data['deskripsi'] ?? '') . "</div><br>";
                                $gambar = $data['gambar'] ?? 'default_chart.png';
                                $image_path = 'assets/images/profil/' . htmlspecialchars($gambar);
                                if (file_exists($image_path)) {
                                    echo "<img src='$image_path' style='max-width:100%; border:1px solid #ddd; border-radius:10px;'>";
                                }
                            } 
                            elseif ($sub_page == 'sarana') {
                                echo "<h2>" . htmlspecialchars($data['judul'] ?? 'Judul Belum Diatur') . "</h2>";
                                echo "<ul>";
                                $daftar_sarana = $data['daftar_sarana'] ?? '';
                                $items = explode("\n", trim($daftar_sarana));
                                foreach ($items as $item) {
                                    if (!empty(trim($item))) {
                                        echo "<li>" . htmlspecialchars(trim($item)) . "</li>";
                                    }
                                }
                                echo "</ul>";
                            }
                        } else {
                            echo "<h2>Format Konten Salah</h2><p>Data untuk halaman ini tidak dalam format yang benar. Silakan periksa di panel admin.</p>";
                        }
                    } else {
                        echo "<h2>Konten Tidak Ditemukan</h2>";
                    }
                }
                ?>
            </main>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>