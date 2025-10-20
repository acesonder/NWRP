# Web Application Setup Guide

This document provides detailed instructions for setting up the Warming Room Proposal System web application.

## Overview

The webapp provides a complete web-based system for managing warming room proposals with:
- User-friendly interface for viewing and submitting proposals
- Comment system for community engagement
- Administrative configuration panel
- Built-in troubleshooting tools
- Easy installation process

## Prerequisites

Before beginning installation, ensure you have:

### Software Requirements
- **Operating System**: Linux (Ubuntu/Debian recommended) or Windows with XAMPP/WAMP
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7+ or MariaDB 10.2+
- **PHP Extensions**: PDO, PDO_MySQL

### Access Requirements
- Root/administrator access to the server
- MySQL root or administrative credentials
- Ability to create databases and users
- Web server configuration access

## Installation Methods

### Method 1: Standard Linux Installation

#### Step 1: Install Dependencies

**Ubuntu/Debian:**
```bash
sudo apt-get update
sudo apt-get install apache2 php php-mysql php-pdo mysql-server
```

**CentOS/RHEL:**
```bash
sudo yum install httpd php php-mysqlnd php-pdo mariadb-server
sudo systemctl start httpd
sudo systemctl start mariadb
sudo systemctl enable httpd
sudo systemctl enable mariadb
```

#### Step 2: Secure MySQL Installation

```bash
sudo mysql_secure_installation
```

Follow the prompts to:
- Set root password
- Remove anonymous users
- Disallow root login remotely
- Remove test database

#### Step 3: Place Files

```bash
# Navigate to web root
cd /var/www/html/proposals/

# If you're cloning from git, the files are already there
# Otherwise, copy the webapp directory here

# Set ownership
sudo chown -R www-data:www-data webapp/
```

#### Step 4: Create Database

```bash
# Login to MySQL
mysql -u root -p

# Create database and user
CREATE DATABASE warming_room_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'warming_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON warming_room_db.* TO 'warming_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import schema
mysql -u root -p warming_room_db < /var/www/html/proposals/webapp/database_schema.sql
```

#### Step 5: Configure Application

```bash
cd /var/www/html/proposals/webapp/config/
cp database.example.php database.php
nano database.php
```

Edit the following values:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'warming_room_db');
define('DB_USER', 'warming_user');
define('DB_PASS', 'strong_password_here');
define('DEBUG_MODE', false); // Set to true for testing, false for production
```

#### Step 6: Set Permissions

```bash
cd /var/www/html/proposals/webapp/
chmod 644 config/database.php
chmod 755 api/ includes/
chmod 644 .htaccess
```

#### Step 7: Configure Apache

Enable required modules:
```bash
sudo a2enmod rewrite
sudo a2enmod headers
sudo systemctl restart apache2
```

If needed, update Apache config (`/etc/apache2/sites-available/000-default.conf`):
```apache
<Directory /var/www/html/proposals/webapp>
    AllowOverride All
    Require all granted
</Directory>
```

Then restart Apache:
```bash
sudo systemctl restart apache2
```

#### Step 8: Test Installation

Open browser and navigate to:
```
http://your-server-ip/proposals/webapp/install.php
```

Click "Test Database Connection" to verify everything works.

### Method 2: XAMPP Installation (Windows)

#### Step 1: Install XAMPP

1. Download XAMPP from https://www.apachefriends.org/
2. Install with Apache, MySQL, and PHP
3. Start Apache and MySQL from XAMPP Control Panel

#### Step 2: Place Files

Copy the webapp directory to:
```
C:\xampp\htdocs\proposals\webapp\
```

#### Step 3: Create Database

1. Open browser: http://localhost/phpmyadmin
2. Click "New" to create database
3. Name: `warming_room_db`
4. Collation: `utf8mb4_unicode_ci`
5. Click "SQL" tab
6. Copy contents of `database_schema.sql` and execute

#### Step 4: Configure Application

1. Navigate to: `C:\xampp\htdocs\proposals\webapp\config\`
2. Copy `database.example.php` to `database.php`
3. Edit with Notepad:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'warming_room_db');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // Usually empty for XAMPP
   ```

#### Step 5: Access Application

Open browser: http://localhost/proposals/webapp/install.php

### Method 3: Docker Installation (Advanced)

Create `docker-compose.yml`:

```yaml
version: '3.8'

services:
  web:
    image: php:7.4-apache
    ports:
      - "8080:80"
    volumes:
      - ./webapp:/var/www/html
    depends_on:
      - db
    
  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: rootpassword
      MYSQL_DATABASE: warming_room_db
      MYSQL_USER: warming_user
      MYSQL_PASSWORD: userpassword
    volumes:
      - ./webapp/database_schema.sql:/docker-entrypoint-initdb.d/schema.sql
```

Then run:
```bash
docker-compose up -d
```

Access: http://localhost:8080

## Post-Installation Configuration

### 1. System Configuration

Navigate to: `http://your-domain/proposals/webapp/configure.php`

Configure:
- Site name
- Administrator email
- Items per page
- Submission permissions
- Approval requirements

### 2. Security Hardening

**Change DEBUG_MODE:**
```php
// In config/database.php
define('DEBUG_MODE', false); // Always false in production
```

**Restrict config directory:**

Apache (.htaccess already included):
```apache
<Files "database.php">
    Order deny,allow
    Deny from all
</Files>
```

Nginx (add to server block):
```nginx
location ~ /config/database\.php$ {
    deny all;
    return 403;
}
```

**Set restrictive permissions:**
```bash
chmod 640 config/database.php
```

### 3. Backup Setup

Create backup script (`/usr/local/bin/backup-warming-room.sh`):

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/warming-room"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u warming_user -p'password' warming_room_db > $BACKUP_DIR/db_$DATE.sql

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/html/proposals/webapp/

# Keep only last 30 days
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete
```

Add to crontab:
```bash
0 2 * * * /usr/local/bin/backup-warming-room.sh
```

## Verification Checklist

After installation, verify:

- [ ] Database connection test succeeds
- [ ] Can view proposals on index page
- [ ] Can submit new proposal
- [ ] Can view individual proposal
- [ ] Can add comments to proposals
- [ ] Configuration page loads and saves settings
- [ ] No PHP errors in browser or logs
- [ ] Config files are not accessible via browser
- [ ] Debug mode is disabled in production

## Troubleshooting

### Database Connection Fails

**Check MySQL is running:**
```bash
sudo systemctl status mysql
```

**Verify credentials:**
```bash
mysql -u warming_user -p warming_room_db
```

**Check PHP PDO extension:**
```bash
php -m | grep -i pdo
```

### Permission Denied Errors

**Fix ownership:**
```bash
sudo chown -R www-data:www-data /var/www/html/proposals/webapp/
```

**Fix permissions:**
```bash
find /var/www/html/proposals/webapp/ -type d -exec chmod 755 {} \;
find /var/www/html/proposals/webapp/ -type f -exec chmod 644 {} \;
```

### 404 Not Found

**Enable mod_rewrite:**
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

**Check .htaccess:**
Ensure `AllowOverride All` is set in Apache config.

### Blank Pages

**Check PHP error log:**
```bash
tail -f /var/log/apache2/error.log
```

**Enable error display temporarily:**
```php
// In config/database.php
define('DEBUG_MODE', true);
```

## Maintenance

### Regular Tasks

**Weekly:**
- Review activity logs
- Check disk space
- Verify backups completed

**Monthly:**
- Update PHP and MySQL
- Review and clean old proposals if needed
- Test backup restoration

**As Needed:**
- Update configuration
- Add new proposal statuses
- Customize interface

## Support Resources

- **Installation Guide**: `/proposals/webapp/install.php`
- **Configuration Help**: `/proposals/webapp/configure.php`
- **Troubleshooting**: `/proposals/webapp/troubleshoot.php`
- **Documentation**: `/proposals/webapp/README.md`
- **GitHub Repository**: https://github.com/acesonder/NWRP

## Next Steps

Once installed:

1. **Explore the Interface**: Navigate through all pages to familiarize yourself
2. **Submit Test Proposal**: Create a test proposal to verify functionality
3. **Configure System**: Set up preferences in the configuration page
4. **Share with Team**: Provide URL to team members and stakeholders
5. **Regular Backups**: Ensure backup script is running

---

**Installation Complete!** Your Warming Room Proposal System is ready to use.
