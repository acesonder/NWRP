# Volunteer Coordination System - Setup Guide

## Prerequisites

- Web server (Apache or Nginx)
- PHP 7.4 or higher with extensions:
  - PDO MySQL
  - JSON
  - mbstring
- MySQL 5.7 or higher
- SSL certificate (recommended for production)

## Installation Steps

### 1. Upload Files

Upload all files to your web server directory.

### 2. Set Permissions

Ensure the web server can write to:
```bash
chmod 755 coordination-system/
chmod 666 coordination-system/config/
```

### 3. Database Setup

1. Create a MySQL database
2. Create a database user with full privileges
3. Note the connection details

### 4. Run Installation

1. Navigate to `http://yourdomain.com/coordination-system/install.php`
2. Follow the installation wizard
3. Enter database credentials
4. Create administrator account

### 5. Security Configuration

#### Apache (.htaccess)
Create `.htaccess` in the root directory:
```apache
RewriteEngine On

# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# API routing
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^api/(.*)$ api/$1.php [QSA,L]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"
```

#### Nginx
Add to server block:
```nginx
location /coordination-system/ {
    try_files $uri $uri/ /coordination-system/index.html;
    
    # Security headers
    add_header X-Content-Type-Options nosniff;
    add_header X-Frame-Options DENY;
    add_header X-XSS-Protection "1; mode=block";
}

location ~ /coordination-system/api/(.*)$ {
    try_files $uri /coordination-system/api/$1.php$is_args$args;
}

# Deny access to sensitive files
location ~ /coordination-system/(config|database|includes)/ {
    deny all;
}
```

### 6. Post-Installation

1. **Delete install.php** for security
2. **Backup database** regularly
3. **Configure SSL** certificate
4. **Test all functionality**

## Configuration

### Database Configuration
Edit `config/database.php` if needed:
```php
return [
    'host' => 'localhost',
    'database' => 'volunteer_coordination',
    'username' => 'your_username',
    'password' => 'your_password',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
```

### System Settings
Update system settings through the database or API:
```sql
UPDATE system_settings SET setting_value = 'Your Organization' WHERE setting_key = 'site_name';
UPDATE system_settings SET setting_value = '12' WHERE setting_key = 'notification_lead_time';
```

## Usage Guide

### 1. Initial Setup

1. **Add Volunteers**
   - Go to Volunteers section
   - Click "Add Volunteer"
   - Fill in contact information
   - Add skills and qualifications

2. **Create Shift Templates**
   - Define standard shift patterns
   - Set minimum/maximum volunteers
   - Specify required skills

3. **Set Up Activation**
   - Create warming room activation
   - Define dates and location
   - Assign coordinator

### 2. Scheduling Workflow

1. **Generate Shifts**
   - Select activation and date range
   - Choose shift templates
   - System creates all shifts

2. **Assign Volunteers**
   - View schedule grid
   - Click on shifts to assign volunteers
   - System checks availability and skills

3. **Send Notifications**
   - Automated reminders 24 hours before
   - Manual alerts for changes
   - Check-in confirmations

### 3. During Operations

1. **Check-In Volunteers**
   - Mark attendance as shifts start
   - Note any no-shows
   - Assign backup volunteers

2. **Communication**
   - Send updates to all volunteers
   - Report incidents
   - Coordinate with other services

3. **Monitor Coverage**
   - Dashboard shows real-time status
   - Alerts for understaffed shifts
   - Quick assignment of available volunteers

## API Endpoints

### Volunteers
- `GET api/volunteers.php` - List volunteers
- `POST api/volunteers.php` - Create volunteer
- `PUT api/volunteers.php/{id}` - Update volunteer
- `DELETE api/volunteers.php/{id}` - Deactivate volunteer

### Shifts
- `GET api/shifts.php` - List shifts
- `POST api/shifts.php` - Create shift
- `POST api/shifts.php?action=assign` - Assign volunteer

### Communications
- `GET api/communications.php` - List messages
- `POST api/communications.php` - Send message

## Troubleshooting

### Database Connection Issues
1. Check credentials in `config/database.php`
2. Verify MySQL server is running
3. Check user privileges
4. Test connection manually

### Permission Errors
1. Check file/folder permissions
2. Ensure web server can write to config/
3. Verify PHP has required extensions

### API Not Working
1. Check Apache/Nginx URL rewriting
2. Verify PHP error logs
3. Test endpoints directly
4. Check CORS headers

## Maintenance

### Daily Tasks
- Monitor shift coverage
- Check volunteer confirmations
- Review incident reports

### Weekly Tasks
- Backup database
- Review volunteer hours
- Update contact information
- Clean up old activations

### Monthly Tasks
- Generate reports
- Update volunteer training records
- Review and update procedures
- Plan upcoming activations

## Support

For technical issues:
1. Check system logs
2. Verify database integrity
3. Test API endpoints
4. Review error messages

For operational questions:
1. Review volunteer handbook
2. Check standard procedures
3. Contact system administrator
4. Update documentation as needed