## How to Deploy (EC2 User Data)
The web application and database connection are bootstrapped automatically upon instance creation using the following bash script in the EC2 Launch Template:

Bash
#!/bin/bash
# Install Apache and PHP
dnf update -y
dnf install -y httpd php php-mysqli

# Start and enable the web server
systemctl start httpd
systemctl enable httpd

# Create the web application testing page
cd /var/www/html
cat <<EOF> index.php
<?php
\$servername = "your-rds-endpoint.amazonaws.com"; // Replace with your RDS Endpoint
\$username = "admin";
\$password = "your-password";
\$dbname = "your-database-name";

// Create connection
\$conn = new mysqli(\$servername, \$username, \$password, \$dbname);

// Check connection
if (\$conn->connect_error) {
  die("Kết nối thất bại: " . \$conn->connect_error);
}
echo "<div style='text-align: center; margin-top: 50px; font-family: Arial, sans-serif;'>";
echo "<h1 style='color: #2e7d32;'>Chúc mừng Lê Gia Hòa! Kết nối thành công tới RDS Database thành công!</h1>";
echo "<h2>Hệ thống Kiến trúc 3-Tier Web Architecture (HA) hoạt động hoàn hảo!</h2>";
echo "<p style='color: #555;'>Traffic đi từ Internet -> ALB -> EC2 (Private App Subnet) -> RDS MySQL (Private Data Subnet).</p>";
echo "</div>";
?>
EOF
