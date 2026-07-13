# ☁️ Highly Available 3-Tier Web Architecture on AWS

![AWS](https://img.shields.io/badge/AWS-%23FF9900.svg?style=for-the-badge&logo=amazon-aws&logoColor=white)
![Linux](https://img.shields.io/badge/Linux-FCC624?style=for-the-badge&logo=linux&logoColor=black)
![MySQL](https://img.shields.io/badge/mysql-4479A1.svg?style=for-the-badge&logo=mysql&logoColor=white)
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![Apache](https://img.shields.io/badge/apache-%23D42029.svg?style=for-the-badge&logo=apache&logoColor=white)

## 📌 Project Overview
This project demonstrates the design and implementation of an enterprise-grade, highly available, and scalable **3-Tier Web Architecture** entirely on Amazon Web Services (AWS). 

The infrastructure is built with a strong focus on **Security (Zero-Trust/Chain of Trust)**, **High Availability (Multi-AZ)**, and **Self-Healing capabilities** utilizing Auto Scaling and Load Balancing to ensure zero downtime during traffic spikes or hardware failures.

**Author:** Lê Gia Hòa - *Cloud & Network Engineering Intern*

---

## 🏛️ Architecture Diagram
*(Please replace this text and the image below with your actual architecture diagram)*

![3-Tier Architecture Diagram](https://via.placeholder.com/800x400.png?text=AWS+3-Tier+Architecture+Diagram)

### 🚀 Core Cloud Concepts Applied:
1. **High Availability (HA):** Infrastructure spans across two Availability Zones (AZs). If one data center goes down, the system continues to operate seamlessly.
2. **Strict Security Isolation:** 
   - Web servers (EC2) and Database (RDS) are isolated in **Private Subnets**. 
   - Direct internet access is blocked. External traffic must flow through the Application Load Balancer (ALB).
   - **Security Group Referencing (Chain of Trust):** RDS only accepts traffic from the EC2 Security Group, and EC2 only accepts traffic from the ALB.
3. **Outbound Internet Access for Private Nodes:** Implemented **NAT Gateways** in Public Subnets to allow private EC2 instances to download patches and dependencies securely.
4. **Auto Scaling & Self-Healing:** Configured an Auto Scaling Group (ASG) coupled with ALB Health Checks to automatically replace failed instances and scale out during high traffic.

---

## 🛠️ Tech Stack & AWS Services
* **Networking:** Amazon VPC, Internet Gateway, NAT Gateway, Route Tables, Subnets (Public, Private App, Private Data).
* **Compute:** Amazon EC2, Auto Scaling Group (ASG), Application Load Balancer (ALB), Launch Templates.
* **Database:** Amazon RDS (MySQL) with Multi-AZ Deployment.
* **Security:** Security Groups.
* **Monitoring & Testing:** Amazon CloudWatch, Artillery (Load Testing).

---

## 🧪 System Validation & Chaos Engineering

To prove the reliability and resilience of this architecture, I conducted the following real-world stress tests and failure simulations:

### Scenario 1: Traffic Spike & Auto Scaling (Stress Test)
* **Action:** Used `Artillery` to simulate a sudden traffic spike by sending 10,000+ requests to the ALB in a short period.
* **Result:** CloudWatch detected CPU utilization exceeding the 50% threshold. The Auto Scaling Group dynamically provisioned additional EC2 instances to handle the load. Zero requests were dropped.
* **Evidence:**
<p align="center">
  <img src="https://via.placeholder.com/800x400.png?text=CloudWatch+CPU+Spike+Screenshot" width="800" alt="CloudWatch CPU Spike">
</p>

### Scenario 2: Hardware Failure & Self-Healing (Chaos Engineering)
* **Action:** Manually terminated an active, healthy EC2 instance running in AZ-A to simulate a sudden hardware crash.
* **Result:** The ALB immediately stopped routing traffic to the dead node (Zero Downtime). Within 2 minutes, the ASG detected the failed health check and automatically launched a fresh EC2 replacement in the same AZ.
* **Evidence:**
<p align="center">
  <img src="https://via.placeholder.com/800x400.png?text=ASG+Self+Healing+Log+Screenshot" width="800" alt="ASG Self Healing Log">
</p>

### Scenario 3: Database Security Isolation
* **Action:** Attempted to access the RDS MySQL Database directly from the public internet using its Endpoint.
* **Result:** Connection timed out and was actively refused. The database is successfully hidden in the Private Data Subnet, and its Security Group strict rules ensure it only communicates with the application layer.

---

## 📁 Repository Structure
```text
aws-3tier-web-architecture/
├── architecture/            
│   └── 3tier-diagram.png     # Visual architecture diagram
├── demo-evidence/           
│   ├── asg-scale-out.png     # Proof of Auto Scaling
│   ├── self-healing.png      # Proof of instance replacement
│   └── db-isolation.png      # Proof of RDS security
├── scripts/                 
│   └── ec2-user-data.sh      # Bash script for automated setup
└── README.md

## HOW TO DELPOY (EC2 USER DATA)
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
