# TiCore Framework

A secure, high-performance MVC framework optimized for **PHP 8.4**. TiCore utilizes a hardened directory structure to physically separate application logic and sensitive configurations from the public web root.

## 📂 Architecture Overview

To prevent unauthorized access to core files, the framework is split into two main sections:

* **`TiCore/`**: (Private) Contains Controllers, Core classes, Templates, and Vendor dependencies. This folder sits **outside** the document root.
* **`www/`**: (Public) The web server's document root (htdocs). Only contains the entry point and static assets.

```text
.
├── TiCore/              # Application Logic (Hidden)
│   ├── .env             # Environment Configuration
|   ├── config.php       # Framework Configuration
│   ├── src/             # Controllers & Core Classes
│   ├── templates/       # View Files
│   └── vendor/          # Composer Autoloader
├── logs/                # System & App Logs (Hidden)
└── www/                 # Public Web Root
    ├── .htaccess        # Server Routing
    ├── index.php        # Entry Point
    ├── sitemap.php      # Automatic Sitemap Creation
    └── assets/          # CSS, JS, Images
```

🚀 Setup Instructions
1. Web Server Routing (www/.htaccess)
Create a .htaccess file in your www/ directory. This configuration handles clean URLs and ensures that the dynamic sitemap is accessible as a standard .xml file.
```htaccess
RewriteEngine On

# 1. Map sitemap.xml to the sitemap feeder script
RewriteRule ^sitemap\.xml$ sitemap.php [L]

# 2. If the file exists physically (images, css, js), serve it directly
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# 3. Otherwise, send all requests to index.php for framework routing
RewriteRule ^(.*)$ index.php [QSA,L]
```

2. Environment Configuration (TiCore/.env)
Create a .env file inside the TiCore/ directory. This file stores sensitive credentials that should never be committed to version control.
```env
# Database Credentials
DB_HOST=localhost
DB_NAME=ticore_db
DB_USER=root
DB_PASS=your_secure_password

# Application Environment (development or production)
APP_ENV=development
```

🔒 Security Features
Zero-Access Core: By keeping TiCore outside of www, your .env, config.php, and source code cannot be accessed via a browser.

PHP 8.4 Optimized: Built to leverage modern performance improvements and strict typing.

Centralized Logging: Logs are stored in a dedicated /logs directory outside the web root to protect sensitive error traces.

TiCore Framework - Built by Tuxxin.com
