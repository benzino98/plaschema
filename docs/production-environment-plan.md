# PLASCHEMA Production Environment Plan

## Server Requirements

### Hardware Specifications

- **CPU**: 4 cores minimum (8 cores recommended)
- **RAM**: 8GB minimum (16GB recommended)
- **Storage**: 50GB SSD minimum (100GB recommended)
- **Bandwidth**: 1TB/month minimum

### Software Requirements

- **Operating System**: Ubuntu 22.04 LTS
- **Web Server**: Nginx 1.18+ with PHP-FPM
- **PHP Version**: PHP 8.2+
- **Database**: MySQL 8.0+
- **Additional Services**:
  - Redis 6.0+ (for caching)
  - Supervisor (for queue processing)
  - Certbot (for SSL certificates)

### Required PHP Extensions

- php-fpm
- php-mysql
- php-mbstring
- php-xml
- php-bcmath
- php-curl
- php-zip
- php-gd
- php-intl (for multilingual support)
- php-redis

## Hosting Options

### Recommended Option: VPS (Digital Ocean/Linode/AWS EC2)

- **Tier**: Standard 4GB RAM / 2 CPU
- **Advantages**:
  - Full control over server configuration
  - Better performance for the price
  - Ability to scale vertically as needed
  - Custom server optimizations
- **Disadvantages**:
  - Requires more system administration knowledge
  - Security maintenance responsibility

### Alternative: Managed Hosting (Laravel Forge + Digital Ocean)

- **Tier**: Standard 4GB RAM / 2 CPU + Laravel Forge subscription
- **Advantages**:
  - Simplified server management
  - One-click deployments
  - Automatic security updates
  - Pre-configured for Laravel applications
- **Disadvantages**:
  - Additional monthly cost for Forge
  - Slightly less control over configuration

### Fallback: Shared Hosting (cPanel)

- **Requirements**:
  - PHP 8.2+ support
  - MySQL 8.0+
  - SSH access
  - Composer support
  - Redis support (if available)
- **Advantages**:
  - Lower cost
  - Simpler management
  - Suitable for lower traffic sites
- **Disadvantages**:
  - Limited performance
  - Less control over server environment
  - Potential resource limitations

## Server Configuration

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name plaschema.gov.ng www.plaschema.gov.ng;
    root /var/www/plaschema/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }

    # Disable access to .env files
    location ~ \.env {
        deny all;
    }
}
```

### PHP-FPM Configuration

Recommended settings for `php.ini`:

```ini
memory_limit = 256M
upload_max_filesize = 12M
post_max_size = 20M
max_execution_time = 60
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
```

### MySQL Configuration

Recommended settings for `my.cnf`:

```ini
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
max_connections = 150
```

### Redis Configuration

Recommended settings for `redis.conf`:

```
maxmemory 256mb
maxmemory-policy allkeys-lru
```

## Deployment Process

### Initial Server Setup

1. Create server on chosen provider
2. Update system packages
   ```bash
   sudo apt update && sudo apt upgrade -y
   ```
3. Install required software
   ```bash
   sudo apt install -y nginx mysql-server redis-server supervisor unzip git certbot python3-certbot-nginx
   ```
4. Install PHP and extensions
   ```bash
   sudo apt install -y php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd php8.2-intl php8.2-redis
   ```
5. Configure firewall
   ```bash
   sudo ufw allow 'Nginx Full'
   sudo ufw allow 'OpenSSH'
   sudo ufw enable
   ```
6. Secure MySQL installation
   ```bash
   sudo mysql_secure_installation
   ```
7. Create database and user
   ```sql
   CREATE DATABASE plaschema;
   CREATE USER 'plaschema_user'@'localhost' IDENTIFIED BY 'strong_password_here';
   GRANT ALL PRIVILEGES ON plaschema.* TO 'plaschema_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

### Application Deployment

1. Create web directory
   ```bash
   sudo mkdir -p /var/www/plaschema
   sudo chown -R $USER:$USER /var/www/plaschema
   ```
2. Clone repository
   ```bash
   git clone https://github.com/your-org/plaschema.git /var/www/plaschema
   ```
3. Install Composer dependencies
   ```bash
   cd /var/www/plaschema
   composer install --no-dev --optimize-autoloader
   ```
4. Install NPM dependencies and build assets
   ```bash
   npm install
   npm run build
   ```
5. Set up environment file
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
6. Update .env file with production settings

   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://plaschema.gov.ng

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=plaschema
   DB_USERNAME=plaschema_user
   DB_PASSWORD=strong_password_here

   CACHE_DRIVER=redis
   QUEUE_CONNECTION=redis
   SESSION_DRIVER=redis

   REDIS_HOST=127.0.0.1
   REDIS_PASSWORD=null
   REDIS_PORT=6379
   ```

7. Run migrations
   ```bash
   php artisan migrate --force
   ```
8. Set proper permissions
   ```bash
   sudo chown -R www-data:www-data /var/www/plaschema/storage
   sudo chown -R www-data:www-data /var/www/plaschema/bootstrap/cache
   ```
9. Configure Nginx
   ```bash
   sudo nano /etc/nginx/sites-available/plaschema
   # Paste the Nginx configuration shown above
   sudo ln -s /etc/nginx/sites-available/plaschema /etc/nginx/sites-enabled/
   sudo nginx -t
   sudo systemctl reload nginx
   ```
10. Set up SSL with Certbot
    ```bash
    sudo certbot --nginx -d plaschema.gov.ng -d www.plaschema.gov.ng
    ```
11. Configure Cron job for Laravel scheduler
    ```bash
    crontab -e
    # Add the following line:
    * * * * * cd /var/www/plaschema && php artisan schedule:run >> /dev/null 2>&1
    ```
12. Configure Supervisor for queue workers
    ```
    [program:plaschema-worker]
    process_name=%(program_name)s_%(process_num)02d
    command=php /var/www/plaschema/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
    autostart=true
    autorestart=true
    user=www-data
    numprocs=2
    redirect_stderr=true
    stdout_logfile=/var/www/plaschema/storage/logs/worker.log
    stopwaitsecs=3600
    ```
13. Start Supervisor
    ```bash
    sudo supervisorctl reread
    sudo supervisorctl update
    sudo supervisorctl start all
    ```

## Backup and Disaster Recovery

### Backup System

1. Install Spatie Backup package
   ```bash
   composer require spatie/laravel-backup
   ```
2. Configure backup settings in `config/backup.php`
3. Set up scheduled backup task in `app/Console/Kernel.php`
   ```php
   $schedule->command('backup:clean')->daily()->at('01:00');
   $schedule->command('backup:run')->daily()->at('02:00');
   ```
4. Configure off-site backup storage (AWS S3, Dropbox, etc.)

### Backup Retention Policy

- Daily backups for the last 7 days
- Weekly backups for the last 4 weeks
- Monthly backups for the last 3 months

### Disaster Recovery Procedure

1. Restore latest backup from storage
   ```bash
   php artisan backup:restore --path=latest-backup.zip
   ```
2. Verify database integrity
3. Test critical application functions
4. Update DNS if restoring to a new server

## Monitoring Plan

### Server Monitoring

- **Tool**: Netdata or Prometheus + Grafana
- **Metrics**:
  - CPU usage
  - Memory usage
  - Disk usage
  - Network traffic
  - Process count

### Application Monitoring

- **Tool**: Laravel Telescope (development) and Sentry.io (production)
- **Metrics**:
  - Error rates
  - Response times
  - Queue processing
  - Database query performance

### Performance Monitoring

- **Tool**: New Relic or Blackfire.io
- **Metrics**:
  - Page load time
  - Database query performance
  - Cache efficiency
  - Memory usage by request

### Uptime Monitoring

- **Tool**: UptimeRobot or Pingdom
- **Checks**:
  - Homepage availability
  - Admin login page
  - API endpoints

## Security Considerations

### Web Application Security

- Configure Content Security Policy (CSP)
- Enable HTTP Strict Transport Security (HSTS)
- Implement rate limiting for authentication attempts
- Use prepared statements for all database queries
- Sanitize all user inputs

### Server Security

- Keep all packages updated
- Use UFW firewall to restrict access
- Implement fail2ban for SSH protection
- Disable password authentication for SSH
- Use SSH key-based authentication only

### Data Security

- Encrypt sensitive data at rest
- Use HTTPS for all communications
- Regular security audits
- Implement proper data backup procedures

## Scaling Strategy

### Vertical Scaling (Immediate Needs)

- Increase server resources (RAM, CPU) as needed
- Optimize database queries and indexes
- Implement and tune caching

### Horizontal Scaling (Future Growth)

- Separate database server
- Load balancer with multiple application servers
- Dedicated Redis server for session and cache
- Content Delivery Network (CDN) for static assets

## Post-Deployment Checklist

- [ ] Verify SSL certificate is properly installed
- [ ] Test all forms and interactive elements
- [ ] Verify admin login and functionality
- [ ] Check email sending functionality
- [ ] Test search functionality
- [ ] Verify mobile responsiveness
- [ ] Test language switching
- [ ] Check caching functionality
- [ ] Verify scheduled tasks are running
- [ ] Test backup and restore procedures
- [ ] Verify all environment variables are correctly set
- [ ] Run performance benchmarks to establish baseline

## Maintenance Schedule

- **Daily**: Check error logs and monitoring alerts
- **Weekly**: Review backup integrity and application performance
- **Monthly**: Apply system updates and security patches
- **Quarterly**: Perform thorough security audit and update dependencies
