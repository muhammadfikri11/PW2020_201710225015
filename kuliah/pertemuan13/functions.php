<?php

function koneksi()
{
  // koneksi ke dalam database 
  return mysqli_connect('localhost', 'root', '', 'pw2020_201710225015');
}

function query($query)
{
  $db = koneksi();

  // menambahakan sintaks query 
  $result  = mysqli_query($db, $query);

  // jika hanya 1 data 
  if (mysqli_num_rows($result) == 1) {
    return mysqli_fetch_assoc($result);
  }

  // memasukkan ke dalam array
  $rows = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
  }


  return $rows;
}

function upload()
{
  $nama_file = $_FILES['gambar']['name'];
  $tipe_file = $_FILES['gambar']['type'];
  $ukuran_file = $_FILES['gambar']['size'];
  $error = $_FILES['gambar']['error'];
  $tmp_file = $_FILES['gambar']['tmp_name'];


  // ketika tidak ada gambar yang dipilih
  if ($error == 4) {
    // echo "<script> 
    //       alert('pilih gambar terlebih dahulu !'); 
    // </script>";
    return 'nophoto.png';
  }

  // cek ekstensi file 
  $daftar_gambar = ['jpg', 'jpeg', 'png'];
  $ekstensi_file = explode('.', $nama_file);
  $ekstensi_file = strtolower(end($ekstensi_file));
  if (!in_array($ekstensi_file, $daftar_gambar)) {
    echo "<script> 
    alert('Yang anda pilih bukan gambar!'); 
    </script>";
    return false;
  }

  // cek type file
  if ($tipe_file != 'image/jpeg' && $tipe_file != 'image/png') {
    echo "<script> 
    alert('Yang anda pilih bukan gambar!'); 
    </script>";
    return false;
  }

  // cek ukuran file max 5mb
  // 5mb = 5000000
  if ($ukuran_file > 1000000) {
    echo "<script> 
      alert('Ukuran file terlalu besar!'); 
  </script>";
    return false;
  }
  // lolos pengecekan siap upload file 
  // generate nama file baru 
  $nama_file_baru = uniqid();
  $nama_file_baru .= '.';
  $nama_file_baru .= $ekstensi_file;

  move_uploaded_file($tmp_file, 'img/' . $nama_file_baru);

  return $nama_file_baru;
}


function tambah($data)
{
  $db = koneksi();

  $nama = htmlspecialchars($data['nama']);
  $npm = htmlspecialchars($data['npm']);
  $email = htmlspecialchars($data['email']);
  $jurusan = htmlspecialchars($data['jurusan']);
  // $gambar = htmlspecialchars($data['gambar']);

  // upload gambar
  $gambar = upload();
  if (!$gambar) {
    return false;
  }

  $query = "INSERT INTO mahasiswa VALUES (null, '$nama', '$npm', '$email', '$jurusan', '$gambar');";

  mysqli_query($db, $query) or die(mysqli_error($db));
  return mysqli_affected_rows($db);
}

function hapus($id)
{
  $db = koneksi();

  // menghapus gambar di folder image 

  $mhs = query("SELECT * FROM mahasiswa WHERE id = $id");
  if ($mhs['gambar'] != 'nophoto.png') {
    unlink('img/' . $mhs['gambar']);
  }


  mysqli_query($db, "DELETE FROM mahasiswa WHERE id=$id") or die(mysqli_error($db));
  return mysqli_affected_rows($db);
}

function ubah($data)
{
  $db = koneksi();

  $id = $data["id"];
  $nama = htmlspecialchars($data['nama']);
  $npm = htmlspecialchars($data['npm']);
  $email = htmlspecialchars($data['email']);
  $jurusan = htmlspecialchars($data['jurusan']);
  $gambar_lama = htmlspecialchars($data['gambar_lama']);

  $gambar = upload();
  if (!$gambar) {
    return false;
  }

  if ($gambar == 'nophoto.png') {
    $gambar = $gambar_lama;
  }

  $query = "UPDATE mahasiswa SET
            
            nama='$nama', 
            npm='$npm', 
            email='$email', 
            jurusan='$jurusan',
            gambar='$gambar' 
          WHERE id=$id";

  mysqli_query($db, $query) or die(mysqli_error($db));
  return mysqli_affected_rows($db);
}

function cari($keyword)
{
  $db = koneksi();

  $query = "SELECT * FROM mahasiswa WHERE 
            nama LIKE '%$keyword%' OR
            npm LIKE '%$keyword%'
            ";

  $result = mysqli_query($db, $query);

  $rows = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
  }

  return $rows;
}

function login($data)
{

  $db = koneksi();

  $username = htmlspecialchars($data['username']);
  $password = htmlspecialchars($data['password']);


  // cek username
  if ($user = query("SELECT * FROM user WHERE username ='$username'")) {
    // cek password
    if (password_verify($password, $user['password'])) {
      // set session 
      $_SESSION['login'] = true;

      header("Location: index.php");
      exit;
    }
  }
  return [
    'error' => true,
    'pesan' => 'username / password salah'
  ];
}


function registrasi($data)
{
  $db = koneksi();

  $username = htmlspecialchars(strtolower($data['username']));
  $password1 = mysqli_real_escape_string($db, $data['password1']);
  $password2 = mysqli_real_escape_string($db, $data['password2']);

  // jika username / password kosong
  if (empty($username) || empty($password1) || empty($password2)) {
    echo "<script>
          alert('username/password tidak boleh kosong!');
          document.location.href ='registrasi.php';
          </script>";
    return false;
  }

  // jika username sudah ada 
  if (query("SELECT * FROM user WHERE username = '$username'")) {
    echo "<script>
        alert('username sudah terdaftar!'); 
        document.location.href = 'registrasi.php';
        </script>";
    return false;
  }

  // jika konfirmasi password tidak sesuai 
  if ($password1 !== $password2) {
    echo "<script>
    alert('konfirmasi password tidak sesuai!'); 
    document.location.href = 'registrasi.php';
    </script>";
    return false;
  }

  // jika password < dari 5 digit 
  if (strlen($password1) < 5) {
    echo "<script>
    alert('password terlalu pendek!'); 
    document.location.href = 'registrasi.php';
    </script>";
    return false;
  }

  // jika username & password sudah sesuai 
  $password_baru = password_hash($password1, PASSWORD_DEFAULT);
  // insert ke tabel user 

  $query = "INSERT INTO user VALUES (null, '$username', '$password_baru')";

  mysqli_query($db, $query) or die(mysqli_error($db));
  return mysqli_affected_rows($db);
}
