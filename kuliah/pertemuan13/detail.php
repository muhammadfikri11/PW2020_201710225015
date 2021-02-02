<?php

session_start();

if (!isset($_SESSION['login'])) {
  header("Location: login.php");
  exit;
}

require 'functions.php';



// ambil id dari url 
$id = $_GET['id'];

// query mahasiswa berdasarkan id
$m = query("SELECT * FROM mahasiswa WHERE id=$id");

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Mahasiswa</title>
</head>

<body>
  <h1>Detail mahasiswa</h1>
  <ul>
    <li><img src="img/<?= $m['gambar']; ?>" width="250px"></li>
    <li>Nama : <?= $m['nama']; ?></li>
    <li>NPM : <?= $m['npm']; ?></li>
    <li>EMAIL :<?= $m['email']; ?></li>
    <li>JURUSAN :<?= $m['jurusan']; ?></li>
    <li>
      <a href="ubah.php?id= <?= $m['id']; ?>">Ubah |
        <a href="hapus.php?id= <?= $m['id']; ?>" onclick="return confirm('apakah anda yakin ingin menghapus data');">Hapus
    </li>
    <li><a href="index.php">Kembali ke index</li>
  </ul>
</body>

</html>