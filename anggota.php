<?php
include 'koneksi.php';

if(isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    
    $query = "INSERT INTO anggota (nama, alamat, no_hp) VALUES ('$nama', '$alamat', '$no_hp')";
    
    if(mysqli_query($conn, $query)) {
        $success = "Data anggota tersimpan.";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

if(isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query = "DELETE FROM anggota WHERE id_anggota = $id";
    mysqli_query($conn, $query);
    header("Location: anggota.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anggota · literasihub</title>
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
            font-family: 'Inter', sans-serif;
            background: #faf7f2;
            padding: 24px;
            color: #2c3e4f;
        }
        
        .container {
            max-width: 1280px;
            margin: 0 auto;
            background: #ffffffdd;
            backdrop-filter: blur(2px);
            border-radius: 40px;
            box-shadow: 0 30px 60px -15px rgba(60,45,30,0.15);
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
        }
        
        .content {
            padding: 40px;
        }
        
        .card {
            background: #fefcf9;
            border-radius: 28px;
            padding: 32px;
            border: 1px solid #ede3d7;
            margin-bottom: 40px;
        }
        
        .card h2 {
            font-size: 1.8rem;
            font-weight: 500;
            margin-bottom: 28px;
            color: #2c3e4f;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 16px;
            align-items: end;
        }
        
        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: #5d6d7c;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .form-group input {
            width: 100%;
            padding: 14px 18px;
            border: 1.5px solid #e2d5c6;
            border-radius: 60px;
            font-size: 1rem;
            background: #ffffff;
            transition: all 0.2s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #b58d6b;
            box-shadow: 0 0 0 3px rgba(181, 141, 107, 0.1);
        }
        
        .btn-primary {
            background: #b76e3c;
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: 60px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
            white-space: nowrap;
        }
        
        .btn-primary:hover {
            background: #9e5a2e;
        }
        
        .alert {
            padding: 14px 20px;
            border-radius: 60px;
            margin-bottom: 24px;
            font-weight: 500;
            background: #eef5e9;
            color: #3f6b4b;
            border: 1px solid #c8dbc0;
        }
        
        .alert-error {
            background: #fae9e9;
            color: #a1554c;
            border: 1px solid #e3c3c0;
        }
        
        .table-wrapper {
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid #ede3d7;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #f2ebe2;
            color: #4a5a6a;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 16px 20px;
            text-align: left;
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
        
        .btn-hapus {
            background: none;
            border: 1.5px solid #d4b8a5;
            color: #7e5a48;
            padding: 6px 18px;
            border-radius: 40px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-hapus:hover {
            background: #d4b8a5;
            color: white;
        }
        
        @media (max-width: 800px) {
            .form-grid { grid-template-columns: 1fr; }
            .header { flex-direction: column; align-items: start; }
            .content { padding: 24px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><span>👤</span>Data Anggota</h1>
            <a href="index.php" class="btn-outline">← kembali</a>
        </div>
        
        <div class="db-info">
            if0_41327178_perpustakaan_ku · sql210.byetcluster.com
        </div>
        
        <div class="content">
            <div class="card">
                <h2>+ tambah anggota</h2>
                
                <?php if(isset($success)) { ?>
                    <div class="alert"><?php echo $success; ?></div>
                <?php } ?>
                <?php if(isset($error)) { ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php } ?>
                
                <form method="POST" class="form-grid">
                    <div class="form-group">
                        <label>nama</label>
                        <input type="text" name="nama" placeholder="contoh: Salsabila" required>
                    </div>
                    <div class="form-group">
                        <label>alamat</label>
                        <input type="text" name="alamat" placeholder="Jl. Margacinta No. 10" required>
                    </div>
                    <div class="form-group">
                        <label>no. hp</label>
                        <input type="text" name="no_hp" placeholder="081234567890" required>
                    </div>
                    <button type="submit" name="simpan" class="btn-primary">simpan</button>
                </form>
            </div>
            
            <h2 style="margin-bottom: 20px; font-weight: 500;">📋 daftar anggota</h2>
            
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr><th>id</th><th>nama</th><th>alamat</th><th>no. hp</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM anggota ORDER BY id_anggota DESC";
                        $result = mysqli_query($conn, $query);
                        if(mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>".$row['id_anggota']."</td>";
                                echo "<td>".$row['nama']."</td>";
                                echo "<td>".$row['alamat']."</td>";
                                echo "<td>".$row['no_hp']."</td>";
                                echo "<td><a href='?hapus=".$row['id_anggota']."' class='btn-hapus' onclick='return confirm(\"Hapus?\")'>hapus</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center;padding:40px;color:#9aa9b5;'>— belum ada data —</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>