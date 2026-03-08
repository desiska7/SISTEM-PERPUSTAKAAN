<?php
// Konfigurasi Database InfinityFree
$host = "sql210.byetcluster.com";
$user = "if0_41327178";
$pass = "Desiska0225"; // Ganti dengan password vPanel Anda
$db = "if0_41327178_perpustakaan_ku";  // Database 1";

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8");

// Pesan sukses (opsional, bisa dihapus)
// echo "Koneksi berhasil!";
?>