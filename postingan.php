<?php include 'includes/header.php'; ?>

<section class="page-header">
    <h1>Berita & Kegiatan</h1>
</section>

<section class="page-section">
    <div class="container">
        <div class="post-grid">
            <?php
            $result = mysqli_query($conn, "SELECT * FROM posts ORDER BY created_at DESC");
            if ($result && mysqli_num_rows($result) > 0) {
                while ($post = mysqli_fetch_assoc($result)) { 
                    $image_path = 'assets/images/posts/' . htmlspecialchars($post['image']);
                    if (!file_exists($image_path) || empty($post['image'])) {
                        $image_path = 'https://via.placeholder.com/400x250.png?text=Sekolah+XYZ';
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
                echo "<p style='text-align:center; grid-column: 1 / -1;'>Belum ada berita yang dipublikasikan.</p>";
            }
            ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>