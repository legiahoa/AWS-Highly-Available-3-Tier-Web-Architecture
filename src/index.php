
<?php
$dbhost = 'my-3tier-db.cdgkuqc4cggt.ap-southeast-1.rds.amazonaws.com';
$dbuser = 'DB_USER';
$dbpass = 'DB_PASSWORD';
$dbname = 'webappdb';

$connect = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

echo "<div style='text-align: center; margin-top: 50px; font-family: Arial, sans-serif;'>";

if (!$connect) {
    echo "<h1 style='color: red;'>Kết nối tới RDS Database THẤT BẠI!</h1>";
    echo "<p>Lỗi: " . mysqli_connect_error() . "</p>";
} else {
    echo "<h1 style='color: green;'>Chúc mừng Lê Gia Hòa! Kết nối thành công tới RDS Database!</h1>";
    echo "<h2 style='color: #333;'>Hệ thống Kiến trúc 3-Tier Web Architecture (HA) hoạt động hoàn hảo!</h2>";
    echo "<p style='color: #666;'>Traffic đi từ Internet → ALB → EC2 (Private App Subnet) → RDS MySQL (Private Data Subnet).</p>";
}

echo "</div>";

mysqli_close($connect);
?>
