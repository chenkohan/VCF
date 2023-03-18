<?php
$servername = "localhost";
$username = "renjuorg_5871224";
$password = "eZ6M^)9sT5%}";//帳號密碼在SQL頁面 權限的地方設定
$dbname = "renjuorg_TEST";//資料庫內資料表名稱

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>