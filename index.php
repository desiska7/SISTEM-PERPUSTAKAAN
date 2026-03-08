<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LiterasiHub · Perpustakaan Digital</title>
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: #2c3e4f;
        }
        
        .container {
            max-width: 1280px;
            width: 100%;
            background: #ffffffdd;
            backdrop-filter: blur(2px);
            border-radius: 40px;
            box-shadow: 0 30px 60px -15px rgba(60, 45, 30, 0.2);
            overflow: hidden;
            border: 1px solid #f0e7db;
        }
        
        .hero {
            padding: 60px 48px 40px 48px;
            background: linear-gradient(145deg, #ffffff 0%, #fcf8f3 100%);
            border-bottom: 1px solid #efe5d9;
        }
        
        .hero h1 {
            font-size: 3.2rem;
            font-weight: 600;
            letter-spacing: -0.02em;
            color: #2c3e4f;
            margin-bottom: 16px;
            line-height: 1.1;
        }
        
        .hero h1 span {
            color: #b76e3c;
            font-weight: 500;
            font-style: italic;
        }
        
        .hero p {
            font-size: 1.2rem;
            color: #5e6f7d;
            max-width: 600px;
            line-height: 1.5;
            font-weight: 400;
        }
        
        .db-badge {
            background: #f2ebe2;
            padding: 12px 48px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
            color: #4a5a6a;
            border-bottom: 1px solid #e7dcd0;
            font-weight: 500;
        }
        
        .db-badge::before {
            content: "●";
            color: #7c9a6e;
            font-size: 1.2rem;
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            padding: 48px;
            gap: 24px;
            background: #ffffff;
        }
        
        .card {
            background: #fefcf9;
            border-radius: 28px;
            padding: 32px 24px;
            text-decoration: none;
            color: inherit;
            border: 1px solid #ede3d7;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }
        
        .card:hover {
            background: #ffffff;
            border-color: #c9ad93;
            transform: translateY(-6px);
            box-shadow: 0 25px 35px -15px rgba(150, 110, 70, 0.15);
        }
        
        .card-icon {
            font-size: 2.8rem;
            margin-bottom: 24px;
            filter: drop-shadow(0 4px 4px rgba(0,0,0,0.02));
        }
        
        .card h3 {
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 12px;
            color: #2c3e4f;
            letter-spacing: -0.01em;
        }
        
        .card p {
            color: #657c92;
            line-height: 1.5;
            font-size: 0.98rem;
            font-weight: 400;
        }
        
        .footer {
            background: #f2ebe2;
            padding: 24px 48px;
            text-align: center;
            color: #5d6d7c;
            font-size: 0.95rem;
            border-top: 1px solid #e2d5c6;
            font-weight: 500;
        }
        
        @media (max-width: 900px) {
            .grid { grid-template-columns: repeat(2, 1fr); padding: 32px; }
            .hero { padding: 40px 32px; }
            .hero h1 { font-size: 2.5rem; }
        }
        
        @media (max-width: 500px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero">
            <h1>Perustakaan<span>Ku</span></h1>
            <p>Kelola anggota, koleksi buku, dan sirkulasi peminjaman dengan tenang & elegan.</p>
        </div>
        
        <div class="db-badge">
            terhubung ke · if0_41327178_perpustakaan_ku · sql210.byetcluster.com
        </div>
        
        <div class="grid">
            <a href="anggota.php" class="card">
                <div class="card-icon">👤</div>
                <h3>Anggota</h3>
                <p>Data anggota perpustakaan, tambah atau lihat daftar.</p>
            </a>
            
            <a href="buku.php" class="card">
                <div class="card-icon">📖</div>
                <h3>Buku</h3>
                <p>Koleksi buku, stok, dan informasi penulis.</p>
            </a>
            
            <a href="peminjaman.php" class="card">
                <div class="card-icon">🔄</div>
                <h3>Peminjaman</h3>
                <p>Catat transaksi pinjam, pilih anggota & buku.</p>
            </a>
            
            <a href="laporan.php" class="card">
                <div class="card-icon">📋</div>
                <h3>Laporan</h3>
                <p>Riwayat peminjaman dan status pengembalian.</p>
            </a>
        </div>
        
        <div class="footer">
            © 2026 · perpustakaan_ku · 
        </div>
    </div>
</body>
</html>