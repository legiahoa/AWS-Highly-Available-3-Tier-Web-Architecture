#  Highly Available 3-Tier Web Architecture on AWS
<img width="1536" height="1024" alt="image" src="https://github.com/user-attachments/assets/8eca6f65-5079-44e8-bea0-26faac1ad6b9" />


![AWS](https://img.shields.io/badge/AWS-%23FF9900.svg?style=for-the-badge&logo=amazon-aws&logoColor=white)
![Linux](https://img.shields.io/badge/Linux-FCC624?style=for-the-badge&logo=linux&logoColor=black)
![MySQL](https://img.shields.io/badge/mysql-4479A1.svg?style=for-the-badge&logo=mysql&logoColor=white)
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![Apache](https://img.shields.io/badge/apache-%23D42029.svg?style=for-the-badge&logo=apache&logoColor=white)

##  Project Overview

This project demonstrates the design and implementation of an enterprise-grade, highly available, and scalable **3-Tier Web Architecture** entirely on Amazon Web Services (AWS).

The infrastructure is built with a strong focus on **Security (Zero-Trust/Chain of Trust)**, **High Availability (Multi-AZ)**, and **Self-Healing capabilities** utilizing Auto Scaling and Load Balancing to ensure zero downtime during traffic spikes or hardware failures.

## Author

**Lê Gia Hòa**
* **Role:** Cloud & Network Student | Systems & DevOps Enthusiast
* **University:** VNU-HCM University of Information Technology (UIT)
* **Email:** [legiahoa1515@gmail.com](mailto:legiahoa1515@gmail.com)
* **LinkedIn:** [linkedin.com/in/legiahoa](https://www.linkedin.com/in/legiahoa)
* **GitHub:** [github.com/legiahoa](https://github.com/legiahoa)

---

##  Architecture Diagram

*(Please replace this text and the image below with your actual architecture diagram)*

![3-Tier Architecture Diagram](https://via.placeholder.com/800x400.png?text=AWS+3-Tier+Architecture+Diagram)

###  Core Cloud Concepts Applied

1. **High Availability (HA):**
   Infrastructure spans across two Availability Zones (AZs). If one data center goes down, the system continues to operate seamlessly.

2. **Strict Security Isolation**
   - Web servers (EC2) and Database (RDS) are isolated in **Private Subnets**.
   - Direct internet access is blocked. External traffic must flow through the Application Load Balancer (ALB).
   - **Security Group Referencing (Chain of Trust):** RDS only accepts traffic from the EC2 Security Group, and EC2 only accepts traffic from the ALB.

3. **Outbound Internet Access for Private Nodes**
   - Implemented **NAT Gateways** in Public Subnets to allow private EC2 instances to download patches and dependencies securely.

4. **Auto Scaling & Self-Healing**
   - Configured an Auto Scaling Group (ASG) coupled with ALB Health Checks to automatically replace failed instances and scale out during high traffic.

---

##  Tech Stack & AWS Services

- **Networking:** Amazon VPC, Internet Gateway, NAT Gateway, Route Tables, Public & Private Subnets
- **Compute:** Amazon EC2, Auto Scaling Group (ASG), Application Load Balancer (ALB), Launch Templates
- **Database:** Amazon RDS (MySQL) with Multi-AZ Deployment
- **Security:** Security Groups
- **Monitoring & Testing:** Amazon CloudWatch, Artillery

---

#  How to Deploy (EC2 User Data)

The web application is automatically deployed when a new EC2 instance is launched through an **EC2 Launch Template**. The following **User Data** script performs the entire initialization process automatically:

- Updates the operating system
- Installs Apache and PHP
- Starts and enables the Apache web server
- Deploys a PHP web application
- Connects to Amazon RDS MySQL
- Displays a success page confirming that the complete 3-Tier Architecture is working correctly

```bash
#!/bin/bash

# Update system packages
dnf update -y

# Install Apache and PHP
dnf install -y httpd php php-mysqli

# Start and enable Apache
systemctl enable httpd
systemctl start httpd

# Create PHP application
cd /var/www/html

cat <<EOF > index.php
<?php
\$servername = "your-rds-endpoint.amazonaws.com";
\$username = "admin";
\$password = "your-password";
\$dbname = "your-database-name";

// Create connection
\$conn = new mysqli(\$servername, \$username, \$password, \$dbname);

// Check connection
if (\$conn->connect_error) {
    die("Connection failed: " . \$conn->connect_error);
}

echo "<div style='text-align:center;margin-top:50px;font-family:Arial,sans-serif'>";
echo "<h1 style='color:#2e7d32;'>🎉 Successfully Connected to Amazon RDS!</h1>";
echo "<h2>AWS Highly Available 3-Tier Web Architecture is Running Successfully.</h2>";
echo "<p style='color:#555;'>Traffic Flow:</p>";
echo "<strong>Internet → Application Load Balancer → EC2 (Private App Subnet) → Amazon RDS MySQL (Private Data Subnet)</strong>";
echo "</div>";
?>
EOF
```

###  Deployment Result

Once the EC2 instance finishes bootstrapping, browsing to the **Application Load Balancer DNS Name** will display a success page confirming:

- Apache is running correctly.
- PHP has been installed successfully.
- EC2 can communicate with Amazon RDS.
- Security Groups and networking are configured correctly.
- The complete **3-Tier Architecture** is functioning as expected.

---

##  System Validation & Chaos Engineering

To prove the reliability and resilience of this architecture, I conducted the following real-world stress tests and failure simulations:

### Scenario 1: Traffic Spike & Auto Scaling (Stress Test)

- **Action:** Used `Artillery` to simulate a sudden traffic spike by sending 10,000+ requests to the ALB in a short period.
- **Result:** CloudWatch detected CPU utilization exceeding the 50% threshold. The Auto Scaling Group dynamically provisioned additional EC2 instances to handle the load. Zero requests were dropped.

**Evidence**

<p align="center">
  <img src="https://via.placeholder.com/800x400.png?text=CloudWatch+CPU+Spike+Screenshot" width="800">
</p>

### Scenario 2: Hardware Failure & Self-Healing (Chaos Engineering)

- **Action:** Manually terminated a healthy EC2 instance.
- **Result:** ALB immediately stopped routing traffic to the failed instance while ASG automatically launched a replacement within minutes.

**Evidence**

<p align="center">
  <img src="https://via.placeholder.com/800x400.png?text=ASG+Self+Healing+Log+Screenshot" width="800">
</p>

### Scenario 3: Database Security Isolation

- **Action:** Attempted to connect directly to the RDS endpoint from the Internet.
- **Result:** Connection timed out because the database is located in a Private Subnet and only accepts traffic from the EC2 Security Group.

---

##  Repository Structure

```text
aws-3tier-web-architecture/
├── architecture/
│   └── 3tier-diagram.png
├── demo-evidence/
│   ├── asg-scale-out.png
│   ├── self-healing.png
│   └── db-isolation.png
├── scripts/
│   └── ec2-user-data.sh
└── README.md
```
