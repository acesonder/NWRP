# Warming Room Proposal System - Quick Reference

## URLs

After installation, access these pages:

- **Dashboard**: `http://your-domain/proposals/webapp/index.php`
- **Submit Proposal**: `http://your-domain/proposals/webapp/submit_proposal.php`
- **Installation Guide**: `http://your-domain/proposals/webapp/install.php`
- **Configuration**: `http://your-domain/proposals/webapp/configure.php`
- **Troubleshooting**: `http://your-domain/proposals/webapp/troubleshoot.php`

## Quick Install (Linux)

```bash
# 1. Install dependencies
sudo apt-get install apache2 php php-mysql mysql-server

# 2. Create database
mysql -u root -p < proposals/webapp/database_schema.sql

# 3. Configure
cd proposals/webapp/config/
cp database.example.php database.php
nano database.php  # Edit credentials

# 4. Access
# Navigate to: http://your-domain/proposals/webapp/install.php
```

## Quick Install (XAMPP/Windows)

1. Install XAMPP
2. Copy `webapp` to `C:\xampp\htdocs\proposals\`
3. Create database in phpMyAdmin using `database_schema.sql`
4. Copy and edit `config/database.example.php` to `database.php`
5. Access: `http://localhost/proposals/webapp/install.php`

## Database Credentials

Edit in `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'warming_room_db');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

## API Endpoints

All APIs return JSON responses.

### Proposals
- List: `GET api/proposals.php?action=list`
- Get: `GET api/proposals.php?action=get&id=1`
- Create: `POST api/proposals.php` with `action=create`
- Update: `POST api/proposals.php` with `action=update&id=1`
- Delete: `POST api/proposals.php` with `action=delete&id=1`

### Comments
- List: `GET api/proposals.php?action=comments&proposal_id=1`
- Add: `POST api/proposals.php` with `action=add_comment`

### Configuration
- Get: `GET api/config.php?action=get`
- Update: `POST api/config.php` with `action=update`

## JavaScript Functions

```javascript
// Load proposals
loadProposals(status);  // status optional

// Submit proposal
submitProposal(formId);

// Add comment
submitComment(proposalId);

// Test database
testDatabaseConnection();

// Load/save config
loadConfig();
saveConfig();
```

## Database Tables

1. **proposals** - Core proposal data
2. **comments** - User comments on proposals
3. **system_config** - Configuration key-value pairs
4. **activity_log** - System activity tracking

## Status Values

- Draft
- Under Review
- Approved
- Implemented
- Rejected

## Common Commands

```bash
# Backup database
mysqldump -u username -p warming_room_db > backup.sql

# Restore database
mysql -u username -p warming_room_db < backup.sql

# Check PHP extensions
php -m | grep -i pdo

# View Apache errors
tail -f /var/log/apache2/error.log

# Restart Apache
sudo systemctl restart apache2

# Test MySQL connection
mysql -u username -p warming_room_db
```

## File Structure

```
webapp/
├── index.php              # Dashboard
├── submit_proposal.php    # New proposal form
├── view_proposal.php      # Single proposal view
├── install.php            # Installation guide
├── configure.php          # Configuration
├── troubleshoot.php       # Help & debugging
├── database_schema.sql    # Database schema
├── api/                   # Backend APIs
├── config/                # Configuration files
├── css/                   # Stylesheets
├── js/                    # JavaScript
├── includes/              # PHP includes
└── docs/                  # Documentation
```

## Security Checklist

- [ ] Set `DEBUG_MODE = false` in production
- [ ] Protect `config/database.php` (chmod 640)
- [ ] Use `.htaccess` to deny config directory access
- [ ] Use strong database passwords
- [ ] Keep PHP and MySQL updated
- [ ] Regular backups
- [ ] Review activity logs

## Troubleshooting Quick Fixes

| Problem | Solution |
|---------|----------|
| Database connection fails | Check credentials in `config/database.php` |
| Blank page | Check error log, enable DEBUG_MODE |
| Permission denied | `chmod 644 config/database.php` |
| 404 errors | Enable `mod_rewrite` in Apache |
| API not working | Check browser console for errors |

## Support

- Installation: See `install.php`
- Configuration: See `configure.php`
- Troubleshooting: See `troubleshoot.php`
- Documentation: See `README.md` and `docs/SETUP.md`
- Repository: https://github.com/acesonder/NWRP

---

For detailed information, see the full documentation in `README.md` and `docs/SETUP.md`.
