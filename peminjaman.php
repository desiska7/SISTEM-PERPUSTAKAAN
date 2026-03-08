<?php
// Aktifkan error reporting untuk debugging (hapus setelah live)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'koneksi.php';

// Cek koneksi database
if (!$conn) {
    die("<div style='background:#fae9e9; color:#a1554c; padding:20px; margin:20px; border-radius:60px; border:1px solid #e3c3c0; font-family:system-ui;'>❌ Koneksi database gagal: " . mysqli_connect_error() . "</div>");
}

// Proses simpan peminjaman
if(isset($_POST['simpan'])) {
    // Validasi input tidak kosong
    if(empty($_POST['id_anggota']) || empty($_POST['id_buku']) || empty($_POST['tgl_pinjam']) || empty($_POST['tgl_kembali'])) {
        $error = "Semua field harus diisi!";
    } else {
        // Escape input untuk keamanan
        $id_anggota = mysqli_real_escape_string($conn, $_POST['id_anggota']);
        $id_buku = mysqli_real_escape_string($conn, $_POST['id_buku']);
        $tgl_pinjam = mysqli_real_escape_string($conn, $_POST['tgl_pinjam']);
        $tgl_kembali = mysqli_real_escape_string($conn, $_POST['tgl_kembali']);
        
        // Validasi tanggal
        if(strtotime($tgl_kembali) < strtotime($tgl_pinjam)) {
            $error = "Tanggal kembali harus setelah tanggal pinjam!";
        } else {
            // Cek apakah id_anggota valid
            $cek_anggota = mysqli_query($conn, "SELECT * FROM anggota WHERE id_anggota = '$id_anggota'");
            if(mysqli_num_rows($cek_anggota) == 0) {
                $error = "Anggota tidak ditemukan!";
            } else {
                // Cek stok buku
                $cek_stok = mysqli_query($conn, "SELECT stok, judul FROM buku WHERE id_buku = '$id_buku'");
                if(mysqli_num_rows($cek_stok) > 0) {
                    $buku = mysqli_fetch_assoc($cek_stok);
                    
                    if($buku['stok'] > 0) {
                        // Mulai transaksi
                        mysqli_begin_transaction($conn);
                        
                        try {
                            // Simpan peminjaman
                            $query = "INSERT INTO peminjaman (id_anggota, id_buku, tanggal_pinjam, tanggal_kembali) 
                                      VALUES ('$id_anggota', '$id_buku', '$tgl_pinjam', '$tgl_kembali')";
                            
                            if(mysqli_query($conn, $query)) {
                                // Update stok buku
                                $update = mysqli_query($conn, "UPDATE buku SET stok = stok - 1 WHERE id_buku = '$id_buku'");
                                
                                if($update) {
                                    mysqli_commit($conn);
                                    $success = "Transaksi peminjaman berhasil dicatat.";
                                    
                                    // Reset POST agar form kosong
                                    $_POST = array();
                                } else {
                                    throw new Exception("Gagal update stok: " . mysqli_error($conn));
                                }
                            } else {
                                throw new Exception("Gagal simpan peminjaman: " . mysqli_error($conn));
                            }
                        } catch (Exception $e) {
                            mysqli_rollback($conn);
                            $error = $e->getMessage();
                        }
                    } else {
                        $error = "Maaf, stok buku '{$buku['judul']}' sedang habis.";
                    }
                } else {
                    $error = "Buku tidak ditemukan!";
                }
            }
        }
    }
}

// Ambil data untuk dropdown
$anggota = mysqli_query($conn, "SELECT * FROM anggota ORDER BY nama");
$buku = mysqli_query($conn, "SELECT * FROM buku WHERE stok > 0 ORDER BY judul");

// ============================================
// QUERY JOIN YANG DIMINTA SOAL
// ============================================
$query_join = "SELECT 
                anggota.nama, 
                buku.judul, 
                peminjaman.tanggal_pinjam
              FROM peminjaman
              JOIN anggota ON peminjaman.id_anggota = anggota.id_anggota
              JOIN buku ON peminjaman.id_buku = buku.id_buku
              ORDER BY peminjaman.tanggal_pinjam DESC";

$result_join = mysqli_query($conn, $query_join);

// Ambil data preview peminjaman terakhir
$preview = mysqli_query($conn, "SELECT 
    anggota.nama as nama_anggota,
    buku.judul as judul_buku,
    peminjaman.tanggal_pinjam,
    peminjaman.tanggal_kembali
FROM peminjaman
JOIN anggota ON peminjaman.id_anggota = anggota.id_anggota
JOIN buku ON peminjaman.id_buku = buku.id_buku
ORDER BY peminjaman.id_pinjam DESC
LIMIT 5");

// Hitung total untuk statistik
$total_anggota = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM anggota"))['total'];
$total_buku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM buku"))['total'];
$total_pinjam = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman · literasihub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #faf7f2;
            padding: 24px;
            color: #2c3e4f;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #ffffffdd;
            backdrop-filter: blur(2px);
            border-radius: 40px;
            box-shadow: 0 30px 60px -15px rgba(60, 45, 30, 0.15);
            overflow: hidden;
            border: 1px solid #f0e7db;
        }
        
        .header {
            padding: 32px 40px;
            background: linear-gradient(145deg, #ffffff 0%, #fcf8f3 100%);
            border-bottom: 1px solid #efe5d9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }
        
        .header h1 {
            font-size: 2.2rem;
            font-weight: 600;
            letter-spacing: -0.01em;
            color: #2c3e4f;
        }
        
        .header h1 span {
            color: #b76e3c;
            font-weight: 400;
            font-size: 1.8rem;
            margin-right: 8px;
        }
        
        .btn-outline {
            background: transparent;
            border: 1.5px solid #c9ad93;
            color: #5e6f7d;
            padding: 10px 24px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        
        .btn-outline:hover {
            background: #efe5d9;
            border-color: #b58d6b;
            color: #2c3e4f;
        }
        
        .db-info {
            background: #f2ebe2;
            padding: 12px 40px;
            font-size: 0.9rem;
            color: #4a5a6a;
            border-bottom: 1px solid #e2d5c6;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .db-info::before {
            content: "●";
            color: #7c9a6e;
            font-size: 1rem;
        }
        
        .content {
            padding: 40px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: #fefcf9;
            border-radius: 28px;
            padding: 20px;
            border: 1px solid #ede3d7;
            transition: all 0.2s;
        }
        
        .stat-card:hover {
            background: #ffffff;
            border-color: #c9ad93;
        }
        
        .stat-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #7e8d9c;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .stat-value {
            font-size: 2.2rem;
            font-weight: 600;
            color: #b76e3c;
            line-height: 1;
        }
        
        .form-card {
            background: #fefcf9;
            border-radius: 28px;
            padding: 32px;
            border: 1px solid #ede3d7;
            margin-bottom: 40px;
        }
        
        .form-card h2 {
            font-size: 1.8rem;
            font-weight: 500;
            margin-bottom: 28px;
            color: #2c3e4f;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .form-group {
            margin-bottom: 4px;
        }
        
        .form-group.full-width {
            grid-column: span 2;
        }
        
        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: #5d6d7c;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .form-group select, .form-group input {
            width: 100%;
            padding: 14px 18px;
            border: 1.5px solid #e2d5c6;
            border-radius: 60px;
            font-size: 1rem;
            background: white;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
            color: #2c3e4f;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23b58d6b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 18px center;
            background-size: 18px;
        }
        
        .form-group select:focus, .form-group input:focus {
            outline: none;
            border-color: #b58d6b;
            box-shadow: 0 0 0 3px rgba(181, 141, 107, 0.1);
        }
        
        .info-buku {
            background: #f2ebe2;
            padding: 12px 18px;
            border-radius: 60px;
            margin-top: 12px;
            font-size: 0.9rem;
            color: #5e6f7d;
            border-left: 4px solid #b76e3c;
            grid-column: span 2;
        }
        
        .btn-primary {
            background: #b76e3c;
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 60px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
            width: 100%;
            margin-top: 12px;
            font-family: 'Inter', sans-serif;
        }
        
        .btn-primary:hover {
            background: #9e5a2e;
        }
        
        .alert {
            padding: 14px 24px;
            border-radius: 60px;
            margin-bottom: 24px;
            font-weight: 500;
            font-size: 0.95rem;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert-success {
            background: #eef5e9;
            color: #3f6b4b;
            border: 1px solid #c8dbc0;
        }
        
        .alert-error {
            background: #fae9e9;
            color: #a1554c;
            border: 1px solid #e3c3c0;
        }
        
        .preview-card {
            background: #fefcf9;
            border-radius: 28px;
            overflow: hidden;
            border: 1px solid #ede3d7;
            margin-bottom: 40px;
        }
        
        .preview-header {
            background: #f2ebe2;
            padding: 20px 28px;
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e4f;
            border-bottom: 1px solid #e2d5c6;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .preview-list {
            padding: 8px;
        }
        
        .preview-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid #f0e7db;
            transition: background 0.2s;
        }
        
        .preview-item:last-child {
            border-bottom: none;
        }
        
        .preview-item:hover {
            background: #fdf9f5;
        }
        
        .preview-info {
            flex: 1;
        }
        
        .preview-name {
            font-weight: 600;
            color: #2c3e4f;
            margin-bottom: 4px;
        }
        
        .preview-book {
            color: #b76e3c;
            font-size: 0.9rem;
        }
        
        .preview-date {
            text-align: right;
            color: #7e8d9c;
            font-size: 0.9rem;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-left: 8px;
        }
        
        .badge-success {
            background: #e2eeda;
            color: #3f6b4b;
        }
        
        .badge-warning {
            background: #fff0d9;
            color: #a1554c;
        }
        
        .join-card {
            background: #fefcf9;
            border-radius: 28px;
            overflow: hidden;
            border: 1px solid #ede3d7;
        }
        
        .join-header {
            background: #b76e3c;
            color: white;
            padding: 20px 28px;
            font-size: 1.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .table-responsive {
            overflow-x: auto;
            padding: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #f2ebe2;
            color: #5d6d7c;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 16px 20px;
            text-align: left;
        }
        
        td {
            padding: 14px 20px;
            border-bottom: 1px solid #f0e7db;
            color: #2c3e4f;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        tr:hover td {
            background: #fdf9f5;
        }
        
        .empty-message {
            text-align: center;
            padding: 48px;
            color: #9aa9b5;
            font-style: italic;
        }
        
        .empty-message span {
            font-size: 3rem;
            display: block;
            margin-bottom: 12px;
            opacity: 0.5;
        }
        
        @media (max-width: 700px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
            .form-group.full-width {
                grid-column: span 1;
            }
            .info-buku {
                grid-column: span 1;
            }
            .header {
                flex-direction: column;
                align-items: start;
            }
            .content {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><span>📚</span>Peminjaman</h1>
            <a href="index.php" class="btn-outline">← kembali</a>
        </div>
        
        <div class="db-info">
            if0_41327178_perpustakaan_ku · sql210.byetcluster.com
        </div>
        
        <div class="content">
            <!-- Statistik Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">total anggota</div>
                    <div class="stat-value"><?php echo $total_anggota; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">total buku</div>
                    <div class="stat-value"><?php echo $total_buku; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">sedang dipinjam</div>
                    <div class="stat-value"><?php echo $total_pinjam; ?></div>
                </div>
            </div>
            
            <!-- Form Peminjaman -->
            <div class="form-card">
                <h2>📝 form peminjaman</h2>
                
                <?php if(isset($success)) { ?>
                    <div class="alert alert-success">✓ <?php echo $success; ?></div>
                <?php } ?>
                
                <?php if(isset($error)) { ?>
                    <div class="alert alert-error">✗ <?php echo $error; ?></div>
                <?php } ?>
                
                <form method="POST" id="formPeminjaman">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>pilih anggota</label>
                            <select name="id_anggota" required>
                                <option value="">— pilih —</option>
                                <?php
                                if($anggota && mysqli_num_rows($anggota) > 0) {
                                    while($a = mysqli_fetch_assoc($anggota)) {
                                        $selected = (isset($_POST['id_anggota']) && $_POST['id_anggota'] == $a['id_anggota']) ? 'selected' : '';
                                        echo "<option value='".$a['id_anggota']."' $selected>".$a['nama']."</option>";
                                    }
                                } else {
                                    echo "<option value='' disabled>belum ada anggota</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>pilih buku</label>
                            <select name="id_buku" required>
                                <option value="">— pilih —</option>
                                <?php
                                if($buku && mysqli_num_rows($buku) > 0) {
                                    while($b = mysqli_fetch_assoc($buku)) {
                                        $selected = (isset($_POST['id_buku']) && $_POST['id_buku'] == $b['id_buku']) ? 'selected' : '';
                                        echo "<option value='".$b['id_buku']."' $selected>".$b['judul']." (stok: ".$b['stok'].")</option>";
                                    }
                                } else {
                                    echo "<option value='' disabled>tidak ada buku tersedia</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>tanggal pinjam</label>
                            <input type="date" name="tgl_pinjam" value="<?php echo isset($_POST['tgl_pinjam']) ? $_POST['tgl_pinjam'] : date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>tanggal kembali</label>
                            <input type="date" name="tgl_kembali" value="<?php echo isset($_POST['tgl_kembali']) ? $_POST['tgl_kembali'] : date('Y-m-d', strtotime('+7 days')); ?>" required>
                        </div>
                        
                        <div class="info-buku">
                            ⓘ hanya buku dengan stok tersedia yang ditampilkan.
                        </div>
                    </div>
                    
                    <button type="submit" name="simpan" class="btn-primary">
                        simpan peminjaman
                    </button>
                </form>
            </div>
            
            <!-- Riwayat Peminjaman Terakhir -->
            <div class="preview-card">
                <div class="preview-header">
                    <span>📋</span> riwayat terakhir
                </div>
                <div class="preview-list">
                    <?php
                    if($preview && mysqli_num_rows($preview) > 0) {
                        while($p = mysqli_fetch_assoc($preview)) {
                            $tgl_pinjam = date('d/m/Y', strtotime($p['tanggal_pinjam']));
                            $tgl_kembali = date('d/m/Y', strtotime($p['tanggal_kembali']));
                            $today = date('Y-m-d');
                            $status = ($p['tanggal_kembali'] < $today) ? 'terlambat' : 'aktif';
                            $badge_class = ($p['tanggal_kembali'] < $today) ? 'badge-warning' : 'badge-success';
                            
                            echo "<div class='preview-item'>";
                            echo "<div class='preview-info'>";
                            echo "<div class='preview-name'>".$p['nama_anggota']."</div>";
                            echo "<div class='preview-book'>".$p['judul_buku']." <span class='badge $badge_class'>$status</span></div>";
                            echo "</div>";
                            echo "<div class='preview-date'>";
                            echo "$tgl_pinjam → $tgl_kembali";
                            echo "</div>";
                            echo "</div>";
                        }
                    } else {
                        echo "<div class='empty-message'>";
                        echo "<span>📭</span>";
                        echo "belum ada data peminjaman";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
            
            <!-- =========================================== -->
            <!-- TAMPILAN QUERY JOIN (SESUAI SOAL) -->
            <!-- =========================================== -->
            <div class="join-card">
                <div class="join-header">
                    <span>🔗</span> Laporan Peminjaman (JOIN 3 Tabel)
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Anggota</th>
                                <th>Judul Buku</th>
                                <th>Tanggal Pinjam</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($result_join && mysqli_num_rows($result_join) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result_join)): ?>
                                    <tr>
                                        <td><?php echo $row['nama']; ?></td>
                                        <td><?php echo $row['judul']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="empty-message">
                                        <span>📊</span>
                                        belum ada data peminjaman
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Catatan Query -->
            <div style="margin-top: 20px; padding: 16px 24px; background: #f2ebe2; border-radius: 60px; color: #5d6d7c; font-size: 0.9rem; border-left: 4px solid #b76e3c;">
                <strong>🔍 Query JOIN yang digunakan:</strong><br>
                <code style="background: #ffffff; padding: 8px 16px; border-radius: 40px; display: inline-block; margin-top: 8px;">
                    SELECT anggota.nama, buku.judul, peminjaman.tanggal_pinjam<br>
                    FROM peminjaman<br>
                    JOIN anggota ON peminjaman.id_anggota = anggota.id_anggota<br>
                    JOIN buku ON peminjaman.id_buku = buku.id_buku;
                </code>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('formPeminjaman').addEventListener('submit', function(e) {
            const tglPinjam = new Date(this.tgl_pinjam.value);
            const tglKembali = new Date(this.tgl_kembali.value);
            
            if(tglKembali < tglPinjam) {
                e.preventDefault();
                alert('tanggal kembali harus setelah tanggal pinjam.');
            }
        });
    </script>
</body>
</html>