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

function tambah($data)
{
  $db = koneksi();

  $nama = htmlspecialchars($data['nama']);
  $npm = htmlspecialchars($data['npm']);
  $email = htmlspecialchars($data['email']);
  $jurusan = htmlspecialchars($data['jurusan']);
  $gambar = htmlspecialchars($data['gambar']);

  $query = "INSERT INTO mahasiswa VALUES (null, '$nama', '$npm', '$email', '$jurusan', '$gambar');";

  mysqli_query($db, $query) or die(mysqli_error($db));
  return mysqli_affected_rows($db);
}

function hapus($id)
{
  $db = koneksi();
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
  $gambar = htmlspecialchars($data['gambar']);

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
