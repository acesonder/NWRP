# Warming Room Proposal System - Web Application

A complete web application for managing warming room proposals, built with MySQL, HTML, CSS, JavaScript, and AJAX.

## Features

- 📝 **Proposal Management**: Submit, view, and track warming room proposals
- 💬 **Comments & Discussion**: Engage with proposals through comments
- 🎨 **Modern UI**: Clean, responsive design that works on all devices
- 🔧 **Easy Setup**: Simple installation and configuration process
- 🛠️ **Troubleshooting Tools**: Built-in diagnostic and help pages
- 🔄 **Dynamic Updates**: AJAX-powered interface for smooth interactions
- 🗄️ **MySQL Database**: Robust data storage with full schema

## System Requirements

- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **PHP**: Version 7.4 or higher with PDO MySQL extension
- **MySQL**: Version 5.7+ or MariaDB 10.2+
- **Browser**: Modern browser with JavaScript enabled

## Quick Start

### 1. Installation

1. **Place files on web server**
   ```bash
   cd /var/www/html/proposals/webapp/
   ```

2. **Create the database**
   ```bash
   mysql -u root -p < database_schema.sql
   ```

3. **Configure database connection**
   ```bash
   cd config/
   cp database.example.php database.php
   nano database.php  # Edit with your credentials
   ```

4. **Set permissions**
   ```bash
   chmod 644 config/database.php
   chmod 755 api/ includes/
   ```

5. **Access the application**
   Navigate to: `http://your-domain.com/proposals/webapp/install.php`

### 2. Configuration

Visit `http://your-domain.com/proposals/webapp/configure.php` to adjust:
- Site name and administrator email
- Pagination settings
- Submission permissions
- Approval workflow

### 3. Start Using

- **View proposals**: `index.php`
- **Submit proposal**: `submit_proposal.php`
- **Configure system**: `configure.php`
- **Get help**: `troubleshoot.php`

## File Structure

```
webapp/
├── index.php              # Main dashboard
├── install.php            # Installation guide
├── configure.php          # System configuration
├── troubleshoot.php       # Troubleshooting guide
├── submit_proposal.php    # Proposal submission form
├── view_proposal.php      # View single proposal with comments
├── database_schema.sql    # Database schema and initial data
│
├── api/                   # Backend API endpoints
│   ├── proposals.php      # Proposal CRUD operations
│   ├── config.php         # Configuration management
│   └── test_connection.php # Database connection test
│
├── config/                # Configuration files
│   └── database.example.php # Example database config
│
├── css/                   # Stylesheets
│   └── style.css         # Main stylesheet
│
├── js/                    # JavaScript files
│   └── main.js           # Core JavaScript functionality
│
├── includes/              # PHP includes
│   └── database.php      # Database helper class
│
└── docs/                  # Documentation
    └── README.md         # This file
```

## Database Schema

### Tables

1. **proposals** - Stores all warming room proposals
   - Core fields: title, author, status, summary
   - Detail fields: problem_statement, proposed_solution, benefits, etc.

2. **comments** - Stores comments on proposals
   - Links to proposals via foreign key
   - Includes author, text, and timestamp

3. **system_config** - System configuration key-value pairs
   - Site settings, preferences, and options

4. **activity_log** - Tracks system activities
   - Actions, descriptions, timestamps, IP addresses

## API Endpoints

### Proposals API (`api/proposals.php`)

- `GET ?action=list` - List all proposals (with optional status filter)
- `GET ?action=get&id=1` - Get single proposal
- `POST action=create` - Create new proposal
- `POST action=update` - Update existing proposal
- `POST action=delete` - Delete proposal
- `GET ?action=comments&proposal_id=1` - Get proposal comments
- `POST action=add_comment` - Add comment to proposal

### Configuration API (`api/config.php`)

- `GET ?action=get` - Get all configuration values
- `POST action=update` - Update configuration values

### Test API (`api/test_connection.php`)

- `GET` - Test database connection

## JavaScript Functions

Key functions in `js/main.js`:

- `loadProposals(status)` - Load proposals list via AJAX
- `submitProposal(formId)` - Submit new proposal
- `submitComment(proposalId)` - Add comment to proposal
- `loadComments(proposalId)` - Load proposal comments
- `testDatabaseConnection()` - Test database connectivity
- `loadConfig()` / `saveConfig()` - Manage configuration

## Security Features

- **PDO Prepared Statements**: Prevents SQL injection
- **HTML Escaping**: Prevents XSS attacks
- **Configuration Protection**: Database credentials in separate file
- **Error Handling**: Debug mode for development, silent mode for production
- **Activity Logging**: Tracks system actions

## Customization

### Changing Colors

Edit `css/style.css` and modify the CSS variables:

```css
:root {
    --primary-color: #2c5aa0;
    --secondary-color: #4a90e2;
    --success-color: #4caf50;
    /* ... */
}
```

### Adding Fields

1. Add column to database:
   ```sql
   ALTER TABLE proposals ADD COLUMN new_field TEXT;
   ```

2. Add input to `submit_proposal.php`

3. Update API in `api/proposals.php`

### Modifying Statuses

Edit the status enum in `database_schema.sql`:

```sql
status ENUM('Draft', 'Under Review', 'Approved', 'Implemented', 'Custom Status')
```

## Troubleshooting

### Database Connection Issues

1. Verify credentials in `config/database.php`
2. Check MySQL is running: `systemctl status mysql`
3. Test connection via `api/test_connection.php`

### Permission Errors

```bash
chmod 644 config/database.php
chown -R www-data:www-data /var/www/html/proposals/webapp/
```

### API Not Working

1. Check browser console for errors (F12)
2. Verify PHP extensions: `php -m | grep pdo`
3. Check Apache/Nginx error logs

For more help, see `troubleshoot.php` in the application.

## Backup & Maintenance

### Database Backup

```bash
mysqldump -u username -p warming_room_db > backup_$(date +%Y%m%d).sql
```

### Database Restore

```bash
mysql -u username -p warming_room_db < backup_20250120.sql
```

### Regular Maintenance

- Keep PHP and MySQL updated
- Regularly backup database
- Review activity logs
- Clean old data as needed

## Contributing

This is part of the NWRP (Northumberland Warming Room Proposal) project. See the main repository for contribution guidelines.

## License

Part of the NWRP project. See repository for license information.

## Support

- **Installation Issues**: See `install.php`
- **Configuration Help**: See `configure.php`
- **Technical Problems**: See `troubleshoot.php`
- **GitHub**: https://github.com/acesonder/NWRP

---

Built with ❤️ for the Northumberland Warming Room Initiative
