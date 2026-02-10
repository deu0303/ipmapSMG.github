<?php
include 'includes/config.php';
$message = '';

// Ensure PPDB related tables exist (helpful if setup script wasn't run)
function ensure_ppdb_tables($conn) {
    $queries = [];
    $queries[] = "CREATE TABLE IF NOT EXISTS students_ppdb (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        birth_place VARCHAR(255),
        birth_date DATE,
        address TEXT,
        previous_school VARCHAR(255),
        file_document VARCHAR(255) DEFAULT 'default.pdf',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $queries[] = "CREATE TABLE IF NOT EXISTS ppdb_fields (
      id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      field_label VARCHAR(255) NOT NULL,
      field_type VARCHAR(50) DEFAULT 'text',
      field_options TEXT,
      is_required TINYINT(1) DEFAULT 0,
      field_order INT DEFAULT 0,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $queries[] = "CREATE TABLE IF NOT EXISTS ppdb_field_data (
      id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      student_id INT UNSIGNED NOT NULL,
      field_id INT UNSIGNED NOT NULL,
      field_value TEXT,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    foreach ($queries as $q) {
        @mysqli_query($conn, $q);
    }

    // Insert some default fields if none exist
    $res = mysqli_query($conn, "SELECT COUNT(*) as c FROM ppdb_fields");
    if ($res) {
        $count = intval(mysqli_fetch_assoc($res)['c']);
        if ($count === 0) {
            $f1 = "INSERT INTO ppdb_fields (field_label, field_type, field_options, is_required, field_order) VALUES ('Nomor HP', 'text', '', 1, 1)";
            $f2 = "INSERT INTO ppdb_fields (field_label, field_type, field_options, is_required, field_order) VALUES ('Jenis Kelamin', 'select', 'Laki-laki,Perempuan', 1, 2)";
            $f3 = "INSERT INTO ppdb_fields (field_label, field_type, field_options, is_required, field_order) VALUES ('Catatan Tambahan', 'textarea', '', 0, 3)";
            @mysqli_query($conn, $f1);
            @mysqli_query($conn, $f2);
            @mysqli_query($conn, $f3);
        }
    }
}

// Run ensure once
ensure_ppdb_tables($conn);

// Make sure students_ppdb has the expected columns (handles older schema with 'name')
function ensure_students_ppdb_columns($conn) {
    // If table doesn't exist, create it via ensure_ppdb_tables first
    $expected = [
        'full_name' => "VARCHAR(255) NOT NULL",
        'birth_place' => "VARCHAR(255)",
        'birth_date' => "DATE",
        'address' => "TEXT",
        'previous_school' => "VARCHAR(255)",
        'file_document' => "VARCHAR(255) DEFAULT 'default.pdf'"
    ];

    foreach ($expected as $col => $type) {
        $q = "SELECT COUNT(*) as c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'students_ppdb' AND COLUMN_NAME = '" . mysqli_real_escape_string($conn, $col) . "'";
        $res = @mysqli_query($conn, $q);
        $has = 0;
        if ($res) {
            $has = intval(mysqli_fetch_assoc($res)['c']);
        }
        if ($has === 0) {
            // Add column
            $alter = "ALTER TABLE students_ppdb ADD COLUMN `" . $col . "` " . $type;
            @mysqli_query($conn, $alter);
        }
    }

    // If older schema used 'name' column, and full_name is empty, try to migrate
    $res = @mysqli_query($conn, "SHOW COLUMNS FROM students_ppdb LIKE 'name'");
    if ($res && mysqli_num_rows($res) > 0) {
        // copy name -> full_name for existing rows where full_name IS NULL or empty
        @mysqli_query($conn, "UPDATE students_ppdb SET full_name = name WHERE (full_name IS NULL OR full_name = '') AND name IS NOT NULL");
    }
}

// Ensure columns before processing POST
ensure_students_ppdb_columns($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ... (Logika PHP Anda untuk memproses form tidak perlu diubah) ...
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $birth_place = mysqli_real_escape_string($conn, $_POST['birth_place']);
    $birth_date = mysqli_real_escape_string($conn, $_POST['birth_date']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $previous_school = mysqli_real_escape_string($conn, $_POST['previous_school']);

    $file_name = 'default.pdf';
    if (isset($_FILES['file_document']) && $_FILES['file_document']['error'] == 0) {
        $file_document = $_FILES['file_document'];
        $clean_name = preg_replace("/[^a-zA-Z0-9._-]/", "", basename($file_document['name']));
        $safe_name = str_replace(' ', '_', $clean_name);
        $file_name = time() . '_' . $safe_name;
        $target_file = "uploads/" . $file_name;
        if (!move_uploaded_file($file_document['tmp_name'], $target_file)) {
            $message = '<div class="alert error">Gagal mengupload file.</div>';
        }
    }

    if (empty($message)) {
        $query_student = "INSERT INTO students_ppdb (full_name, birth_place, birth_date, address, previous_school, file_document) VALUES ('$full_name', '$birth_place', '$birth_date', '$address', '$previous_school', '$file_name')";
        if (mysqli_query($conn, $query_student)) {
            $student_id = mysqli_insert_id($conn);
            if (isset($_POST['custom_fields']) && is_array($_POST['custom_fields'])) {
                foreach ($_POST['custom_fields'] as $field_id => $field_value) {
                    $field_id_safe = intval($field_id);
                    $field_value_safe = mysqli_real_escape_string($conn, $field_value);
                    $query_custom = "INSERT INTO ppdb_field_data (student_id, field_id, field_value) VALUES ($student_id, $field_id_safe, '$field_value_safe')";
                    mysqli_query($conn, $query_custom);
                }
            }
            $message = '<div class="alert success">Pendaftaran Anda berhasil dikirim!</div>';
        } else {
            $message = '<div class="alert error">Gagal menyimpan data ke database.</div>';
        }
    }
}
include 'includes/header.php';
?>

<section class="page-header">
    <h1>Pendaftaran Siswa Baru</h1>
</section>

<section class="page-section">
    <div class="container">
        <div class="form-wrapper">
            <p style="text-align:center; margin-bottom:20px;">Silakan isi formulir di bawah ini dengan data yang benar untuk mendaftar sebagai calon siswa baru.</p>
            <?php echo $message; ?>
            <form action="ppdb.php" method="post" enctype="multipart/form-data">
                <h4>Data Wajib</h4>
                <div class="form-group"><label for="full_name">Nama Lengkap</label><input type="text" id="full_name" name="full_name" required></div>
                <div class="form-group"><label for="birth_place">Tempat Lahir</label><input type="text" id="birth_place" name="birth_place" required></div>
                <div class="form-group"><label for="birth_date">Tanggal Lahir</label><input type="date" id="birth_date" name="birth_date" required></div>
                <div class="form-group"><label for="address">Alamat Lengkap</label><textarea id="address" name="address" rows="3" required></textarea></div>
                <div class="form-group"><label for="previous_school">Asal Sekolah</label><input type="text" id="previous_school" name="previous_school" required></div>
                <div class="form-group"><label for="file_document">Upload Berkas (PDF)</label><input type="file" id="file_document" name="file_document" accept=".pdf" required></div>
                
                <h4>Data Tambahan</h4>
                <?php
                // Logika untuk menampilkan custom fields tidak berubah
                $custom_fields = mysqli_query($conn, "SELECT * FROM ppdb_fields ORDER BY field_order ASC");
                while ($field = mysqli_fetch_assoc($custom_fields)) {
                    $field_id = $field['id'];
                    $label = htmlspecialchars($field['field_label']);
                    $is_required = $field['is_required'] ? 'required' : '';
                    echo "<div class='form-group'><label for='custom_field_$field_id'>$label</label>";
                    switch ($field['field_type']) {
                        case 'textarea': echo "<textarea name='custom_fields[$field_id]' id='custom_field_$field_id' $is_required></textarea>"; break;
                        case 'select':
                            echo "<select name='custom_fields[$field_id]' id='custom_field_$field_id' $is_required><option value=''>-- Pilih --</option>";
                            $options = explode(',', $field['field_options']);
                            foreach ($options as $option) {
                                $opt_val = trim($option); echo "<option value='$opt_val'>$opt_val</option>";
                            }
                            echo "</select>"; break;
                        default: echo "<input type='text' name='custom_fields[$field_id]' id='custom_field_$field_id' $is_required>"; break;
                    } echo "</div>";
                }
                ?>
                <div class="form-group" style="text-align:center; margin-top:30px;">
                    <button type="submit" class="btn">Daftar Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>