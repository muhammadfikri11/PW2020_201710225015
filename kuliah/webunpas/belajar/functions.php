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
