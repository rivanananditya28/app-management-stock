<?php
$host       = "localhost";
$user       = "root";
$password   = "";
$database   = "db_inventory";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi ke database gagal : " . $conn->connect_error);
} 