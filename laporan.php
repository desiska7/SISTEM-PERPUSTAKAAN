<?php
// Aktifkan error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'koneksi.php';

// Cek koneksi database
if (!$conn) {
    die("<div style='background:#fae9e9; color:#a1554c; padding:20px; margin:20px; border-radius:60px; border:1px solid #e3c3c0; font-family:system-ui;'>❌ Koneksi database gagal: " . mysqli_connect_error() . "</div>");
}

// ============================================
// PROSES PENGEMBALIAN BUKU
// ============================================
if (isset($_GET['kembali'])) {
    $id_pinjam = $_GET['kembali'];
    
    // Ambil id_buku dari peminjaman
    $cari = mysqli_query($conn, "SELECT id_buku FROM peminjaman WHERE id_pinjam = $id_pinjam");
    $data = mysqli_fetch_assoc($cari);
    $id_buku = $data['id_buku'];
    
    // Mulai transaksi
    mysqli_begin_transaction($conn);
    
    // Hapus peminjaman
    $hapus = mysqli_query($conn, "DELETE FROM peminjaman WHERE id_pinjam = $id_pinjam");
    
    if ($hapus) {
        // Update stok buku
        $update = mysqli_query($conn, "UPDATE buku SET stok = stok + 1 WHERE id_buku = $id_buku");
        
        if ($update) {
            mysqli_commit($conn);
            $success = "Buku berhasil dikembalikan. Stok bertambah.";
        } else {
            mysqli_rollback($conn);
            $error = "Gagal update stok: " . mysqli_error($conn);
        }
    } else {
        mysqli_rollback($conn);
        $error = "Gagal hapus peminjaman: " . mysqli_error($conn);
    }
}

// ============================================
// QUERY LAPORAN
// ============================================
$query = "SELECT 
            p.id_pinjam,
            a.nama AS nama_anggota,
            b.judul AS judul_buku,
            p.tanggal_pinjam,
            p.tanggal_kembali,
            b.id_buku
          FROM peminjaman p
          LEFT JOIN anggota a ON p.id_anggota = a.id_anggota
          LEFT JOIN buku b ON p.id_buku = b.id_buku
          ORDER BY p.tanggal_pinjam DESC";

$result = mysqli_query($conn, $query);
$total_data = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan · literasihub</title>
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
            max-width: 1400px;
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
        
        .btn-group {
            display: flex;
            gap: 10px;
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
        
        .btn-primary {
            background: #b76e3c;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: background 0.2s;
        }
        
        .btn-primary:hover {
            background: #9e5a2e;
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
            flex-wrap: wrap;
        }
        
        .db-info::before {
            content: "●";
            color: #7c9a6e;
            font-size: 1rem;
        }
        
        .content {
            padding: 40px;
        }
        
        .alert {
            padding: 14px 24px;
            border-radius: 60px;
            margin-bottom: 30px;
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
        
        .stats-card {
            background: #fefcf9;
            border-radius: 28px;
            padding: 28px 32px;
            margin-bottom: 40px;
            border: 1px solid #ede3d7;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .stats-info h3 {
            font-size: 1.1rem;
            font-weight: 500;
            color: #7e8d9c;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .stats-info .total {
            font-size: 3rem;
            font-weight: 600;
            color: #b76e3c;
            line-height: 1;
        }
        
        .stats-icon {
            font-size: 3.5rem;
            opacity: 0.3;
        }
        
        .table-wrapper {
            background: #fefcf9;
            border-radius: 28px;
            overflow: hidden;
            border: 1px solid #ede3d7;
        }
        
        .table-header {
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
        
        .table-responsive {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }
        
        th {
            background: #f8f4ef;
            color: #5d6d7c;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 18px 20px;
            text-align: left;
            border-bottom: 1px solid #e2d5c6;
        }
        
        td {
            padding: 16px 20px;
            border-bottom: 1px solid #f0e7db;
            color: #2c3e4f;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        tr:hover td {
            background: #fdf9f5;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 40px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-dipinjam {
            background: #fff0d9;
            color: #b76e3c;
        }
        
        .status-terlambat {
            background: #fae9e9;
            color: #a1554c;
        }
        
        .btn-kembali {
            background: #e2eeda;
            color: #3f6b4b;
            padding: 8px 20px;
            border-radius: 40px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-block;
            transition: all 0.2s;
            border: 1px solid #c8dbc0;
        }
        
        .btn-kembali:hover {
            background: #c8dbc0;
            color: #1e4a2e;
        }
        
        .empty-message {
            text-align: center;
            padding: 60px !important;
            color: #9aa9b5;
        }
        
        .empty-message span {
            font-size: 3rem;
            display: block;
            margin-bottom: 16px;
            opacity: 0.4;
        }
        
        .info-note {
            margin-top: 24px;
            padding: 16px 24px;
            background: #f2ebe2;
            border-radius: 60px;
            color: #5d6d7c;
            font-size: 0.95rem;
            border-left: 4px solid #b76e3c;
        }
        
        .footer {
            background: #f2ebe2;
            padding: 20px 40px;
            text-align: center;
            border-top: 1px solid #e2d5c6;
            color: #5d6d7c;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        @media (max-width: 700px) {
            .header {
                flex-direction: column;
                align-items: start;
            }
            .btn-group {
                width: 100%;
                justify-content: start;
            }
            .content {
                padding: 24px;
            }
            .stats-card {
                flex-direction: column;
                align-items: start;
                gap: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><span>📊</span>Laporan Peminjaman</h1>
            <div class="btn-group">
                <a href="index.php" class="btn-outline">← beranda</a>
                <a href="peminjaman.php" class="btn-primary">+ pinjam baru</a>
            </div>
        </div>
        
        <div class="db-info">
            if0_41327178_perpustakaan_ku · sql210.byetcluster.com · total data: <?php echo $total_data; ?>
        </div>
        
        <div class="content">
            <!-- Notifikasi -->
            <?php if(isset($success)): ?>
                <div class="alert alert-success">✓ <?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-error">✗ <?php echo $error; ?></div>
            <?php endif; ?>
            
            <!-- Statistik -->
            <div class="stats-card">
                <div class="stats-info">
                    <h3>total peminjaman aktif</h3>
                    <div class="total"><?php echo $total_data; ?></div>
                </div>
                <div class="stats-icon">📚</div>
            </div>
            
            <!-- Tabel Laporan -->
            <div class="table-wrapper">
                <div class="table-header">
                    <span>📋</span> daftar buku yang dipinjam
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>anggota</th>
                                <th>buku</th>
                                <th>tgl pinjam</th>
                                <th>tgl kembali</th>
                                <th>status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($total_data > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($result)): 
                                    $today = date('Y-m-d');
                                    $status = ($row['tanggal_kembali'] < $today) ? 'Terlambat' : 'Dipinjam';
                                    $status_class = ($row['tanggal_kembali'] < $today) ? 'status-terlambat' : 'status-dipinjam';
                                ?>
                                    <tr>
                                        <td>#<?php echo $row['id_pinjam']; ?></td>
                                        <td><?php echo $row['nama_anggota'] ?: '—'; ?></td>
                                        <td><?php echo $row['judul_buku'] ?: '—'; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_kembali'])); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $status_class; ?>">
                                                <?php echo $status; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="?kembali=<?php echo $row['id_pinjam']; ?>" 
                                               class="btn-kembali"
                                               onclick="return confirm('Kembalikan buku ini?')">
                                               kembali
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="empty-message">
                                        <span>📭</span>
                                        tidak ada buku yang sedang dipinjam
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Info -->
            <div class="info-note">
                ⓘ klik tombol "kembali" untuk mencatat pengembalian. stok buku akan bertambah otomatis.
            </div>
        </div>
        
        <div class="footer">
            © 2026 · literasihub
        </div>
    </div>
</body>
</html>