<?php 
include 'includes/header.php'; 
$post = null;
$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($post_id > 0) {
    $query = "SELECT * FROM posts WHERE id = $post_id";
    $result = mysqli_query($conn, $query);
    $post = mysqli_fetch_assoc($result);
}
?>

<section class="page-header">
    <h1><?php echo $post ? htmlspecialchars($post['title']) : 'Artikel Tidak Ditemukan'; ?></h1>
    <?php if ($post): ?>
        <p class="article-meta">
            Kategori: <?php echo htmlspecialchars($post['category']); ?> | Diterbitkan pada: <?php echo date('d F Y', strtotime($post['created_at'])); ?>
        </p>
    <?php endif; ?>
</section>

<section class="page-section">
    <div class="container">
        <?php if ($post): 
            $image_path = 'assets/images/posts/' . htmlspecialchars($post['image']);
            if (!file_exists($image_path) || empty($post['image'])) {
                $image_path = 'https://via.placeholder.com/800x400.png?text=Gambar+Berita';
            }
        ?>
            <div class="article-container">
                <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="article-image">
                <div class="article-content konten-apa-adanya">
                    <?php echo htmlspecialchars($post['content']); ?>
                </div>
            </div>
        <?php else: ?>
            <p style="text-align:center;">Maaf, artikel yang Anda cari tidak dapat ditemukan.</p>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>