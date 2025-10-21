# NWRP Portal - Complete Setup Guide

## 🏠 Northumberland Warming Room Project

This is your complete portal for managing warming room operations, featuring two integrated systems:

### 🌟 **Main Portal** (You are here)
- Central access point for all NWRP applications
- Unified navigation and branding
- System overview and quick access

### 👥 **Volunteer Coordination System**
- Complete volunteer management platform
- Automated shift scheduling and notifications  
- Real-time coverage tracking and reporting
- Skills-based volunteer matching

### 💡 **Community Proposals System**
- Collaborative proposal submission and management
- Community discussion and feedback
- Implementation tracking and status updates

## 🚀 Quick Start

### Development Server (Current Setup)
The development server is now running at:
- **Main Portal**: http://localhost:8080
- **Coordination System**: http://localhost:8080/coordination-system/
- **Proposals System**: http://localhost:8080/proposals/webapp/

### Production Deployment

#### 1. Web Server Setup
```bash
# Copy all files to your web server root
cp -r /workspaces/NWRP/* /var/www/html/nwrp/

# Set proper permissions
chmod 755 /var/www/html/nwrp
chmod 666 /var/www/html/nwrp/coordination-system/config/
chmod 666 /var/www/html/nwrp/proposals/webapp/config/
```

#### 2. Database Configuration
Both systems require MySQL databases:

**For Coordination System:**
1. Visit: `https://yourdomain.com/nwrp/coordination-system/install.php`
2. Follow the installation wizard
3. Create database and admin account

**For Proposals System:**
1. Visit: `https://yourdomain.com/nwrp/proposals/webapp/install.php`
2. Configure database connection
3. Import initial data

#### 3. Apache/Nginx Configuration

**Apache Virtual Host:**
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/html/nwrp
    
    # Redirect to HTTPS
    Redirect permanent / https://yourdomain.com/
</VirtualHost>

<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot /var/www/html/nwrp
    
    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /path/to/certificate.crt
    SSLCertificateKeyFile /path/to/private.key
    
    # Security Headers
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Strict-Transport-Security "max-age=63072000"
    
    # Enable .htaccess files
    <Directory /var/www/html/nwrp>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Nginx Configuration:**
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com;
    root /var/www/html/nwrp;
    index index.html index.php;
    
    # SSL Configuration
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    
    # Security Headers
    add_header X-Content-Type-Options nosniff;
    add_header X-Frame-Options DENY;
    add_header X-XSS-Protection "1; mode=block";
    add_header Strict-Transport-Security "max-age=63072000";
    
    # Main portal
    location / {
        try_files $uri $uri/ /index.html;
    }
    
    # Coordination System API
    location ~ ^/coordination-system/api/(.+)$ {
        try_files $uri /coordination-system/api/$1.php;
        include fastcgi_params;
        fastcgi_pass php-fpm;
        fastcgi_param SCRIPT_FILENAME $document_root/coordination-system/api/$1.php;
    }
    
    # Proposals System API  
    location ~ ^/proposals/webapp/api/(.+)$ {
        try_files $uri /proposals/webapp/api/$1.php;
        include fastcgi_params;
        fastcgi_pass php-fpm;
        fastcgi_param SCRIPT_FILENAME $document_root/proposals/webapp/api/$1.php;
    }
    
    # PHP files
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass php-fpm;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
    
    # Deny access to sensitive files
    location ~ /(config|database|includes)/$ {
        deny all;
    }
}
```

## 📊 System Features

### Volunteer Coordination System
- **Dashboard**: Real-time statistics and coverage monitoring
- **Volunteer Management**: Registration, skills tracking, availability
- **Scheduling**: Automated shift creation and assignment
- **Communications**: Multi-channel notifications (email, SMS, phone)
- **Reporting**: Volunteer hours, coverage metrics, performance analytics
- **Mobile-Friendly**: Responsive design for all devices

### Community Proposals System  
- **Proposal Submission**: Easy form-based proposal creation
- **Status Tracking**: Draft, review, approved, implemented workflow
- **Community Discussion**: Comments and feedback on proposals
- **Categorization**: Organized by proposal type and priority
- **Implementation Tracking**: Progress monitoring and updates

## 🔧 Configuration

### System Settings
Key configuration files:
- `/coordination-system/config/database.php` - Database connection
- `/proposals/webapp/config/database.php` - Database connection  
- `/.htaccess` - Apache URL rewriting and security
- `/assets/css/portal.css` - Portal styling and branding

### Customization
- **Branding**: Update logos and colors in CSS files
- **Email Templates**: Modify notification templates in API files
- **Workflow**: Adjust approval processes and statuses
- **Permissions**: Configure user roles and access levels

## 🛡️ Security Considerations

### Essential Security Measures
1. **SSL Certificate**: Always use HTTPS in production
2. **Database Security**: Use strong passwords and limit privileges
3. **File Permissions**: Restrict write access to config directories
4. **Regular Updates**: Keep PHP and server software updated
5. **Backup Strategy**: Implement automated database backups
6. **Access Logging**: Monitor and log all system access

### Security Headers
Both systems implement:
- Content Security Policy (CSP)
- X-Frame-Options (clickjacking protection)
- X-Content-Type-Options (MIME sniffing protection)
- X-XSS-Protection (cross-site scripting protection)
- HSTS (HTTP Strict Transport Security)

## 📈 Monitoring and Maintenance

### Daily Tasks
- Monitor volunteer shift coverage
- Check system health and performance
- Review and respond to proposals
- Process incident reports

### Weekly Tasks  
- Generate volunteer reports
- Update training records
- Review system security logs
- Backup databases

### Monthly Tasks
- Performance optimization
- Security updates
- User feedback review
- System documentation updates

## 🆘 Troubleshooting

### Common Issues

**"Database connection failed"**
- Check database credentials in config files
- Verify MySQL server is running
- Test database connectivity manually

**"API endpoints not working"**
- Check web server URL rewriting configuration
- Verify .htaccess files are being processed
- Test API endpoints directly

**"Permission denied errors"**
- Check file and directory permissions
- Ensure web server can write to config directories
- Verify PHP has necessary extensions

### Support Resources
- **Documentation**: Complete guides in each system's `/docs/` folder
- **GitHub**: https://github.com/acesonder/NWRP
- **Issues**: Report bugs and feature requests on GitHub
- **Community**: Engage with other NWRP users and contributors

## 🤝 Contributing

We welcome contributions to the NWRP project! See `CONTRIBUTING.md` for guidelines on:
- Code standards and formatting
- Testing procedures  
- Documentation requirements
- Pull request process

## 📄 License

This project is open source and available under the MIT License. See the LICENSE file for details.

---

**🏠 NWRP Portal** - Bringing communities together to support those in need.