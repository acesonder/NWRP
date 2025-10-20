# System Architecture

## Overview

The Warming Room Proposal System is a three-tier web application with a modern, RESTful architecture.

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                         PRESENTATION LAYER                      │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌───────────┐  ┌───────────┐  ┌───────────┐  ┌───────────┐  │
│  │ index.php │  │ submit_   │  │ view_     │  │ configure │  │
│  │ Dashboard │  │ proposal  │  │ proposal  │  │ Settings  │  │
│  └─────┬─────┘  └─────┬─────┘  └─────┬─────┘  └─────┬─────┘  │
│        │              │              │              │         │
│  ┌───────────┐  ┌───────────┐  ┌───────────┐  ┌───────────┐  │
│  │ install   │  │ trouble-  │  │ CSS/JS    │  │ .htaccess │  │
│  │ Guide     │  │ shoot     │  │ Assets    │  │ Security  │  │
│  └───────────┘  └───────────┘  └───────────┘  └───────────┘  │
│                                                                 │
│        All pages use: CSS (style.css) + JS (main.js)          │
│                                                                 │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             │ AJAX Requests (JSON)
                             │
┌────────────────────────────┴────────────────────────────────────┐
│                        APPLICATION LAYER                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌───────────────────────────────────────────────────────┐    │
│  │                    API Endpoints                       │    │
│  │                                                         │    │
│  │  ┌────────────────┐  ┌────────────────┐  ┌─────────┐ │    │
│  │  │ proposals.php  │  │  config.php    │  │  test_  │ │    │
│  │  │                │  │                │  │  conn   │ │    │
│  │  │ • list()       │  │ • get()        │  │         │ │    │
│  │  │ • get()        │  │ • update()     │  └─────────┘ │    │
│  │  │ • create()     │  └────────────────┘              │    │
│  │  │ • update()     │                                   │    │
│  │  │ • delete()     │                                   │    │
│  │  │ • comments()   │                                   │    │
│  │  │ • add_comment()│                                   │    │
│  │  └────────────────┘                                   │    │
│  └───────────────────────────────────────────────────────┘    │
│                             │                                   │
│  ┌───────────────────────────────────────────────────────┐    │
│  │              includes/database.php                     │    │
│  │                                                         │    │
│  │  • Database Connection (PDO)                           │    │
│  │  • Query Helpers (fetchAll, fetchOne, execute)        │    │
│  │  • Security (Prepared Statements, HTML Escaping)      │    │
│  │  • Activity Logging                                    │    │
│  └───────────────────────────────────────────────────────┘    │
│                                                                 │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             │ PDO Connection
                             │
┌────────────────────────────┴────────────────────────────────────┐
│                          DATA LAYER                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│                    MySQL Database (warming_room_db)            │
│                                                                 │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐        │
│  │  proposals   │  │   comments   │  │ system_      │        │
│  │              │  │              │  │ config       │        │
│  │ • id         │  │ • id         │  │              │        │
│  │ • title      │  │ • proposal_id│  │ • config_key │        │
│  │ • author     │  │ • author     │  │ • config_val │        │
│  │ • status     │  │ • comment    │  │ • description│        │
│  │ • summary    │  │ • created_at │  └──────────────┘        │
│  │ • ...        │  └──────────────┘                           │
│  └──────────────┘                                              │
│                                                                 │
│  ┌──────────────┐                                              │
│  │ activity_log │                                              │
│  │              │                                              │
│  │ • id         │                                              │
│  │ • action     │                                              │
│  │ • description│                                              │
│  │ • ip_address │                                              │
│  │ • created_at │                                              │
│  └──────────────┘                                              │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## Request Flow

### Example: Loading Proposals

```
User                Browser              API                  Database
 |                     |                  |                      |
 |  1. Visit index.php |                  |                      |
 |-------------------->|                  |                      |
 |                     |                  |                      |
 |                     |  2. Page loads   |                      |
 |                     |     with JS      |                      |
 |                     |                  |                      |
 |                     | 3. AJAX Request  |                      |
 |                     |  GET api/proposals.php?action=list     |
 |                     |----------------->|                      |
 |                     |                  |                      |
 |                     |                  | 4. Query proposals   |
 |                     |                  |--------------------->|
 |                     |                  |                      |
 |                     |                  | 5. Return data       |
 |                     |                  |<---------------------|
 |                     |                  |                      |
 |                     | 6. JSON Response |                      |
 |                     |<-----------------|                      |
 |                     |                  |                      |
 |                     | 7. Render table  |                      |
 |  8. See proposals   |     with JS      |                      |
 |<--------------------|                  |                      |
```

### Example: Submitting Proposal

```
User                Browser              API                  Database
 |                     |                  |                      |
 |  1. Fill form       |                  |                      |
 |-------------------->|                  |                      |
 |                     |                  |                      |
 |  2. Click Submit    |                  |                      |
 |-------------------->|                  |                      |
 |                     |                  |                      |
 |                     | 3. Validate form |                      |
 |                     | 4. AJAX Request  |                      |
 |                     |  POST api/proposals.php                 |
 |                     |  action=create + form data             |
 |                     |----------------->|                      |
 |                     |                  |                      |
 |                     |                  | 5. Insert proposal   |
 |                     |                  |--------------------->|
 |                     |                  |                      |
 |                     |                  | 6. Log activity      |
 |                     |                  |--------------------->|
 |                     |                  |                      |
 |                     |                  | 7. Return ID         |
 |                     |                  |<---------------------|
 |                     |                  |                      |
 |                     | 8. Success JSON  |                      |
 |                     |<-----------------|                      |
 |                     |                  |                      |
 |                     | 9. Show success  |                      |
 |  10. Confirmation   |    message       |                      |
 |<--------------------|                  |                      |
 |                     |                  |                      |
 |                     | 11. Redirect to  |                      |
 |                     |   view page      |                      |
```

## Technology Stack

### Frontend
- **HTML5** - Semantic markup
- **CSS3** - Modern styling with CSS variables
- **JavaScript** (Vanilla) - AJAX, DOM manipulation
- **Responsive Design** - Mobile-first approach

### Backend
- **PHP 7.4+** - Server-side logic
- **PDO** - Database abstraction layer
- **REST API** - JSON-based communication

### Database
- **MySQL 5.7+** / **MariaDB 10.2+**
- **UTF8MB4** - Full Unicode support
- **InnoDB** - ACID compliance, foreign keys

### Web Server
- **Apache 2.4+** with mod_rewrite
- **Nginx 1.18+** (alternative)
- **.htaccess** - Security configuration

## Security Features

### SQL Injection Prevention
- PDO prepared statements for all queries
- Parameter binding
- No direct SQL string concatenation

### XSS Prevention
- HTML escaping on all user input output
- `htmlspecialchars()` with ENT_QUOTES

### Configuration Security
- Database credentials in separate file
- `.htaccess` blocks direct access to config
- `.gitignore` excludes config file

### Activity Logging
- All actions logged to database
- IP address tracking
- Timestamp recording

### Error Handling
- Debug mode for development
- Silent error mode for production
- Error logging to files

## File Organization

```
webapp/
├── Frontend (HTML/CSS/JS)
│   ├── index.php              # Main pages
│   ├── submit_proposal.php
│   ├── view_proposal.php
│   ├── install.php
│   ├── configure.php
│   ├── troubleshoot.php
│   ├── css/style.css          # Styling
│   └── js/main.js             # Client-side logic
│
├── Backend (PHP)
│   ├── api/                   # REST endpoints
│   │   ├── proposals.php
│   │   ├── config.php
│   │   └── test_connection.php
│   └── includes/              # Shared code
│       └── database.php
│
├── Configuration
│   ├── config/                # Settings
│   │   └── database.example.php
│   └── .htaccess             # Apache config
│
├── Database
│   └── database_schema.sql   # Schema + data
│
└── Documentation
    ├── README.md
    └── docs/
        ├── SETUP.md
        ├── QUICK_REFERENCE.md
        └── ARCHITECTURE.md (this file)
```

## Design Patterns

### Singleton Pattern
- Database connection reused across requests
- Single PDO instance per request

### MVC-inspired Architecture
- Model: Database layer (includes/database.php)
- View: Frontend HTML pages
- Controller: API endpoints (api/*.php)

### RESTful API Design
- Resource-based URLs
- HTTP methods (GET, POST)
- JSON responses
- Stateless communication

## Scalability Considerations

### Current Design
- Single server deployment
- Direct database connections
- File-based sessions (default PHP)

### Future Enhancements
- **Caching**: Add Redis/Memcached for queries
- **Load Balancing**: Multiple web servers
- **Database Replication**: Master-slave setup
- **CDN**: Static asset delivery
- **Authentication**: User login system
- **Authorization**: Role-based access control

## Browser Compatibility

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance

### Optimizations Implemented
- CSS/JS in separate files (browser caching)
- Database indexes on key columns
- AJAX for dynamic updates (no full page reload)
- Responsive images

### Recommended Production Settings
- Enable OPcache for PHP
- Enable gzip compression
- Set cache headers for static assets
- Optimize MySQL configuration
- Use HTTP/2 if available

---

For implementation details, see the source code in respective files.
