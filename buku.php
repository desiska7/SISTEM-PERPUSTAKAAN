<?php include 'koneksi.php';

if(isset($_POST['simpan'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis = mysqli_real_escape_string($conn, $_POST['penulis']);
    $tahun = $_POST['tahun'];
    $stok = $_POST['stok'];
    
    $query = "INSERT INTO buku (judul, penulis, tahun_terbit, stok) VALUES ('$judul','$penulis','$tahun','$stok')";
    if(mysqli_query($conn, $query)) $success = "Data buku tersimpan.";
    else $error = "Error: ".mysqli_error($conn);
}

if(isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM buku WHERE id_buku = $id");
    header("Location: buku.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku · literasihub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:#faf7f2; padding:24px; color:#2c3e4f; }
        .container { max-width:1280px; margin:0 auto; background:#ffffffdd; backdrop-filter:blur(2px); border-radius:40px; box-shadow:0 30px 60px -15px rgba(60,45,30,0.15); overflow:hidden; border:1px solid #f0e7db; }
        .header { padding:32px 40px; background:linear-gradient(145deg,#ffffff 0%,#fcf8f3 100%); border-bottom:1px solid #efe5d9; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; }
        .header h1 { font-size:2.2rem; font-weight:600; letter-spacing:-0.01em; color:#2c3e4f; }
        .header h1 span { color:#b76e3c; font-weight:400; font-size:1.8rem; margin-right:8px; }
        .btn-outline { background:transparent; border:1.5px solid #c9ad93; color:#5e6f7d; padding:10px 24px; border-radius:40px; text-decoration:none; font-weight:500; transition:0.2s; }
        .btn-outline:hover { background:#efe5d9; border-color:#b58d6b; color:#2c3e4f; }
        .db-info { background:#f2ebe2; padding:12px 40px; font-size:0.9rem; color:#4a5a6a; border-bottom:1px solid #e2d5c6; font-weight:500; }
        .content { padding:40px; }
        .card { background:#fefcf9; border-radius:28px; padding:32px; border:1px solid #ede3d7; margin-bottom:40px; }
        .card h2 { font-size:1.8rem; font-weight:500; margin-bottom:28px; color:#2c3e4f; }
        .form-grid { display:grid; grid-template-columns:1fr 1fr 1fr 1fr auto; gap:16px; align-items:end; }
        .form-group label { display:block; font-size:0.9rem; font-weight:600; color:#5d6d7c; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.3px; }
        .form-group input { width:100%; padding:14px 18px; border:1.5px solid #e2d5c6; border-radius:60px; font-size:1rem; background:white; transition:0.2s; }
        .form-group input:focus { outline:none; border-color:#b58d6b; box-shadow:0 0 0 3px rgba(181,141,107,0.1); }
        .btn-primary { background:#b76e3c; color:white; border:none; padding:14px 32px; border-radius:60px; font-weight:600; font-size:1rem; cursor:pointer; transition:0.2s; white-space:nowrap; }
        .btn-primary:hover { background:#9e5a2e; }
        .alert { padding:14px 20px; border-radius:60px; margin-bottom:24px; font-weight:500; background:#eef5e9; color:#3f6b4b; border:1px solid #c8dbc0; }
        .alert-error { background:#fae9e9; color:#a1554c; border:1px solid #e3c3c0; }
        .table-wrapper { background:white; border-radius:24px; overflow:hidden; border:1px solid #ede3d7; }
        table { width:100%; border-collapse:collapse; }
        th { background:#f2ebe2; color:#4a5a6a; font-weight:600; font-size:0.9rem; text-transform:uppercase; letter-spacing:0.3px; padding:16px 20px; text-align:left; }
        td { padding:16px 20px; border-bottom:1px solid #f0e7db; color:#2c3e4f; }
        tr:hover td { background:#fdf9f5; }
        .stok-badge { background:#e2eeda; color:#3f6b4b; padding:4px 14px; border-radius:40px; font-size:0.85rem; font-weight:500; }
        .btn-hapus { border:1.5px solid #d4b8a5; color:#7e5a48; padding:6px 18px; border-radius:40px; text-decoration:none; font-size:0.85rem; font-weight:500; transition:0.2s; background:none; }
        .btn-hapus:hover { background:#d4b8a5; color:white; }
        @media (max-width:900px) { .form-grid { grid-template-columns:1fr; } }
    </style>
</head>
<body>
<div class="container">
    <div class="header"><h1><span>📖</span>Data Buku</h1><a href="index.php" class="btn-outline">← kembali</a></div>
    <div class="db-info">if0_41327178_perpustakaan_ku · sql210.byetcluster.com</div>
    <div class="content">
        <div class="card">
            <h2>+ tambah buku</h2>
            <?php if(isset($success)) echo "<div class='alert'>$success</div>"; ?>
            <?php if(isset($error)) echo "<div class='alert alert-error'>$error</div>"; ?>
            <form method="POST" class="form-grid">
                <div class="form-group"><label>judul</label><input type="text" name="judul" placeholder="Pemrograman PHP" required></div>
                <div class="form-group"><label>penulis</label><input type="text" name="penulis" placeholder="Nabila Maesaroh" required></div>
                <div class="form-group"><label>tahun</label><input type="number" name="tahun" placeholder="2022" required></div>
                <div class="form-group"><label>stok</label><input type="number" name="stok" value="1" min="1" required></div>
                <button type="submit" name="simpan" class="btn-primary">simpan</button>
            </form>
        </div>
        <h2 style="margin-bottom:20px; font-weight:500;">📋 koleksi buku</h2>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>id</th><th>judul</th><th>penulis</th><th>tahun</th><th>stok</th><th></th></tr></thead>
                <tbody>
                <?php
                $result = mysqli_query($conn, "SELECT * FROM buku ORDER BY id_buku DESC");
                if(mysqli_num_rows($result)>0) {
                    while($row=mysqli_fetch_assoc($result)) {
                        echo "<tr><td>".$row['id_buku']."</td><td>".$row['judul']."</td><td>".$row['penulis']."</td><td>".$row['tahun_terbit']."</td>";
                        echo "<td><span class='stok-badge'>".$row['stok']."</span></td>";
                        echo "<td><a href='?hapus=".$row['id_buku']."' class='btn-hapus' onclick='return confirm(\"Hapus?\")'>hapus</a></td></tr>";
                    }
                } else echo "<tr><td colspan='6' style='text-align:center;padding:40px;color:#9aa9b5;'>— belum ada buku —</td></tr>";
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>