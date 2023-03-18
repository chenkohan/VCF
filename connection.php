<?php
// connection.php
function createConnection() {
    $servername = "localhost";
    $username = "renjuorg_5871224";
    $password = "eZ6M^)9sT5%}"; // 設定帳號密碼
    $dbname = "renjuorg_TEST"; // 資料庫內資料表名稱

    // 建立連線
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 檢查連線
    if ($conn->connect_error) {
        die("連線失敗: " . $conn->connect_error);
    }

    return $conn;
}
?>
