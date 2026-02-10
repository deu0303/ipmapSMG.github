<?php
include 'includes/config.php';
$message_status = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $query = "INSERT INTO contact_messages (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";
    if (mysqli_query($conn, $query)) {
        $message_status = '<div class="alert success">Pesan Anda berhasil terkirim. Terima kasih!</div>';
    } else {
        $message_status = '<div class="alert error">Maaf, terjadi kesalahan. Silakan coba lagi.</div>';
    }
}
include 'includes/header.php';
?>

<section class="page-header">
    <h1>Hubungi Kami</h1>
</section>

<section class="page-section">
    <div class="container">
        <?php echo $message_status; ?>
        <div class="contact-grid">
            <div class="contact-info">
                <h4>Informasi Kontak</h4>
                <p>Jangan ragu untuk menghubungi kami melalui detail di bawah ini atau melalui formulir di samping.</p>
                <p><strong>Alamat:</strong> Jl. PETOMPON SELATAN 07 SEMARANG JAWA TENGAH.</p>
                <p><strong>Telepon:</strong> 081228711454</p>
                <p><strong>Email:</strong> ipmapsemarang@gmail.com</p>
                <iframe src="http://googleusercontent.com/maps.google.com/6"
                    width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <div class="form-wrapper" style="padding:30px;">
                <h4>Kirim Pesan</h4>
                <form action="kontak.php" method="post">
                    <div class="form-group"><label for="name">Nama Anda</label><input type="text" id="name" name="name" required></div>
                    <div class="form-group"><label for="email">Email Anda</label><input type="email" id="email" name="email" required></div>
                    <div class="form-group"><label for="subject">Subjek</label><input type="text" id="subject" name="subject" required></div>
                    <div class="form-group"><label for="message">Pesan</label><textarea id="message" name="message" rows="5" required></textarea></div>
                    <div class="form-group"><button type="submit" class="btn">Kirim Pesan</button></div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>