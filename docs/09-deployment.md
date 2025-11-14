# Deployment Plan

This document describes how to configure automated deployments for the project using GitHub Actions, Deployer, and SSH access to a VPS host.

---

## Overview

- **Target branch:** `main` (adjust if a different release branch is used)
- **CI runner:** GitHub Actions (`ubuntu-latest`)
- **Deployment orchestrator:** [Deployer](https://deployer.org/) PHP task runner
- **Target host:** Linux-based VPS reachable via SSH with a non-root deploy user
- **Deployment directory layout:** `/var/www/lemini` with Deployer-managed `releases/` and `shared/` directories
- **Expected runtime stack on the VPS:** PHP 8.2+, Composer 2.x, Node 20+ for SSR builds if required, Nginx/Apache configured to serve the current release’s `public/` directory

---

## Prerequisites

1. **Repository**
   - Ensure `.env.example` contains production-ready defaults for missing env vars.
   - Add a `deploy.php` file in the project root (covered below).
2. **VPS**
   - Provision a deploy user with sudo for service restarts.
   - Install system packages: `php8.4-cli`, `php8.4-fpm`, required PHP extensions (`mbstring`, `intl`, `pcntl`, `pdo_mysql`, `curl`, `zip`), `composer`, `git`, `node`, `npm`, and a process supervisor such as `systemd` units or `supervisor` if queues or SSR servers run on the host.
   - **Configure PHP-FPM to run as `www` user** (see PHP-FPM Configuration section below).
   - Configure web server virtual host to point to `/var/www/learning.dhanifudin.com/current/public`.
   - **Set web server (Nginx/Apache) to run as `www` user**.
   - Create shared directories and permissions (`storage/`, `bootstrap/cache`, `public/storage` symlink).
3. **Secrets & credentials**
   - Generate SSH key pair dedicated to GitHub Actions (no passphrase).
   - Add the public key to the VPS deploy user's `~/.ssh/authorized_keys`.
   - Store the private key in the repository secrets as `VPS_SSH_KEY`.
   - Capture host fingerprint with `ssh-keyscan` and add to secret `VPS_SSH_KNOWN_HOSTS`.
   - Add any additional secrets required by the app (database URL, queue connection, third-party API keys) to GitHub environments.

### PHP-FPM Configuration (Important!)

The deployment is configured to use `www` as the web server user (not `www-data`). Configure PHP-FPM accordingly:

```ini
# /etc/php/8.4/fpm/pool.d/www.conf
[www]
user = www
group = www
listen.owner = www
listen.group = www
listen.mode = 0660
```

After editing, restart PHP-FPM:
```bash
sudo systemctl restart php8.4-fpm
```

### Web Server Configuration

Ensure your web server also runs as the `www` user:

**For Nginx:**
```nginx
# /etc/nginx/nginx.conf
user www;
```

**For Apache:**
```apache
# /etc/apache2/envvars
export APACHE_RUN_USER=www
export APACHE_RUN_GROUP=www
```

---

## 1. Prepare the VPS

1. Create the `www` user if it doesn't exist:
   ```bash
   sudo groupadd -f www
   sudo useradd -g www -s /usr/sbin/nologin -M www 2>/dev/null || true
   ```

2. Add deploy user to the `www` group:
   ```bash
   sudo usermod -a -G www el
   ```

3. Create project directories:
   ```bash
   sudo mkdir -p /var/www/learning.dhanifudin.com/{releases,shared}
   sudo chown -R el:www /var/www/learning.dhanifudin.com
   sudo chmod -R 775 /var/www/learning.dhanifudin.com
   ```

4. Inside `/var/www/learning.dhanifudin.com/shared` create persistent resources:
   ```bash
   cd /var/www/learning.dhanifudin.com/shared
   mkdir -p storage/{app,framework/{cache,sessions,views},logs}
   mkdir -p bootstrap/cache
   touch storage/logs/laravel.log
   sudo chown -R el:www storage bootstrap/cache
   sudo chmod -R 775 storage bootstrap/cache
   ```

5. Upload production `.env` to `/var/www/learning.dhanifudin.com/shared/.env`:
   ```bash
   # Keep database credentials and application keys here
   sudo chown el:www /var/www/learning.dhanifudin.com/shared/.env
   sudo chmod 640 /var/www/learning.dhanifudin.com/shared/.env
   ```

6. Ensure the web server points to `/var/www/learning.dhanifudin.com/current/public` (Deployer updates the `current` symlink on each release).

7. Configure sudo permissions for deploy user:
   ```bash
   sudo visudo -f /etc/sudoers.d/deployer
   ```
   
   Add these lines:
   ```
   el ALL=(ALL) NOPASSWD: /usr/bin/systemctl reload php8.4-fpm
   el ALL=(ALL) NOPASSWD: /usr/bin/systemctl is-active php8.4-fpm
   el ALL=(ALL) NOPASSWD: /usr/bin/chown
   el ALL=(ALL) NOPASSWD: /usr/bin/chmod
   ```
5. For queue workers or horizon, ensure services read the `current` release path so restarts are not required on every deploy (or add restart commands in Deployer hooks).

---

## 2. Configure Deployer

The `deploy.php` file has been configured with best practices:

### Key Features

1. **PHP-FPM User Configuration**
   - Set to use `www` user instead of `www-data`
   - Configured via `set('http_user', 'www')`

2. **File Permissions**
   - Writable directories use `0775` permissions
   - Uses sudo for permission changes
   - Proper ownership: `deployment_user:www`

3. **Deployment Tasks**
   - **deploy:backup**: Creates hardlink backup before deployment
   - **deploy:set_permissions**: Sets proper ownership and permissions
   - **deploy:optimize**: Caches config, routes, views, and events
   - **php-fpm:reload**: Reloads PHP-FPM after deployment
   - **deploy:verify**: Verifies deployment structure and services
   - **deploy:health_check**: Tests application responsiveness
   - **deploy:migrate**: Runs database migrations (optional)

4. **Best Practices Implemented**
   - Automatic backup before each deployment
   - Atomic deployments using symlinks
   - Keep 5 releases for easy rollback
   - Zero-downtime deployments
   - Health checks after deployment
   - Optimized caching

### Current Configuration

```php
<?php
// Key settings in deploy.php

// File permissions and ownership
set('writable_mode', 'chmod');
set('writable_chmod_mode', '0775');
set('writable_use_sudo', true);
set('http_user', 'www');  // Changed from www-data to www
set('php_fpm_service', 'php8.4-fpm');

// Deployment settings
set('keep_releases', 5);
set('repository', 'https://github.com/dhanifudin/lemini.git');

// Production host
host('production')
    ->setHostname(getenv('DEPLOY_HOST') ?: '194.127.193.198')
    ->setRemoteUser(getenv('DEPLOY_USER') ?: 'el')
    ->setDeployPath(getenv('DEPLOY_PATH') ?: '/var/www/learning.dhanifudin.com')
    ->set('branch', getenv('DEPLOY_BRANCH') ?: 'main');
```

### Deployment Workflow

1. **Backup**: Hardlink backup of current release
2. **Upload**: Deploy artifact uploaded and extracted
3. **Shared**: Link shared files (.env, storage)
4. **Permissions**: Set ownership to `el:www` with proper modes
5. **Optimize**: Cache application files
6. **Activate**: Symlink updated to new release
7. **Reload**: PHP-FPM reloaded
8. **Verify**: Check directories and services
9. **Health Check**: Test application responsiveness

### Manual Deployment Commands

```bash
# Deploy to production
vendor/bin/dep deploy production

# With verbose output
vendor/bin/dep deploy production -vvv

# Run migrations
vendor/bin/dep deploy:migrate production

# Rollback
vendor/bin/dep rollback production

# SSH into server
vendor/bin/dep ssh production
```

---

## 3. Configure GitHub Actions

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy

on:
  push:
    branches: [main]
  workflow_dispatch:
    inputs:
      branch:
        description: 'Branch to deploy'
        default: 'main'
        required: true

jobs:
  deploy:
    runs-on: ubuntu-latest
    environment: production

    steps:
      - name: Checkout
        uses: actions/checkout@v4
        with:
          fetch-depth: 0

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, intl, pcntl, pdo_mysql, curl, zip
          coverage: none

      - name: Cache Composer dependencies
        uses: actions/cache@v4
        with:
          path: vendor
          key: composer-${{ hashFiles('composer.lock') }}
          restore-keys: composer-

      - name: Install Composer dependencies
        run: composer install --no-dev --prefer-dist --optimize-autoloader

      - name: Setup Node
        uses: actions/setup-node@v4
        with:
          node-version: '20'

      - name: Install Node dependencies
        run: npm ci

      - name: Build front-end assets
        run: npm run build

      - name: Create deployment archive
        run: |
          tar \
            --exclude='.git' \
            --exclude='.github' \
            --exclude='node_modules' \
            --exclude='tests' \
            -czf /tmp/release.tar.gz \
            .
          mv /tmp/release.tar.gz release.tar.gz

      - name: Install Deployer
        run: composer global require deployer/deployer:^7.0

      - name: Configure SSH
        uses: webfactory/ssh-agent@v0.9.0
        with:
          ssh-private-key: ${{ secrets.VPS_SSH_KEY }}

      - name: Add known hosts
        run: |
          mkdir -p ~/.ssh
          if [ -n "${{ secrets.VPS_SSH_KNOWN_HOSTS }}" ]; then
            echo "${{ secrets.VPS_SSH_KNOWN_HOSTS }}" >> ~/.ssh/known_hosts
          elif [ -n "${{ secrets.VPS_HOST }}" ]; then
            ssh-keyscan -H "${{ secrets.VPS_HOST }}" >> ~/.ssh/known_hosts
          else
            echo "No host provided for known_hosts population." >&2
            exit 1
          fi

      - name: Deploy with Deployer
        env:
          DEPLOY_HOST: ${{ secrets.VPS_HOST }}
          DEPLOY_USER: ${{ secrets.VPS_USER }}
          DEPLOY_PATH: ${{ secrets.VPS_DEPLOY_PATH }}
          DEPLOY_BRANCH: ${{ github.event.inputs.branch || 'main' }}
          DEPLOY_ARTIFACT: ${{ github.workspace }}/release.tar.gz
        run: |
          php ~/.composer/vendor/bin/dep deploy production --ansi -vvv
```

Adjustments to consider:

- If assets should be built on the VPS instead, skip the archive step and revert `deploy.php` to run `npm` / Composer tasks remotely.
- Use GitHub Environments to scope secrets (`production`, `staging`).
- Add test steps (e.g., `composer test`) before deployment gate.
- Configure Slack/Teams notifications after successful deployments if needed.

### Required secrets

- `VPS_HOST`, `VPS_USER`, `VPS_DEPLOY_PATH`
- `VPS_SSH_KEY` (private key)
- `VPS_SSH_KNOWN_HOSTS` (`ssh-keyscan -H <host>`) — optional if allowing the workflow to fetch via `ssh-keyscan`
- Application secrets needed for runtime (database credentials, queue, third-party keys) stored as environment variables or managed via `.env` on the server.

---

## 4. Deployment Flow

1. Developer pushes to `main` (or manually triggers the workflow).
2. GitHub Actions installs dependencies, compiles assets, packages the release archive, and validates the build.
3. Deployer connects to the VPS over SSH, creates a new release in `/var/www/lemini/releases/<timestamp>`.
4. Deployer uploads and extracts the pre-built artifact, then executes database migrations and Laravel cache tasks.
5. Shared resources (`storage`, `.env`) are symlinked.
6. `php artisan migrate --force` is executed automatically via Deployer’s Laravel recipe (ensure migrations are idempotent and backward compatible).
7. Once successful, Deployer updates the `current` symlink to the new release and reloads PHP-FPM.
8. Workflow marks success; monitoring alerts (if configured) confirm site health.

---

## 5. Rollback & Recovery

- Deployer keeps previous releases in `/var/www/lemini/releases`. Use GitHub Actions (workflow dispatch) or local CLI to run:
  ```bash
  php vendor/bin/dep rollback production
  ```
- Ensure database migrations are reversible; add `down()` implementations for every migration.
- Maintain backups:
  - Automated database snapshots (e.g., daily via `mysqldump` cron).
  - File backups of `/var/www/lemini/shared`.
- Document manual recovery steps for catastrophic failures (restore from backup, redeploy).

---

## 6. Monitoring & Maintenance

- Add health checks post-deployment (e.g., call `/health` endpoint via curl in a workflow step).
- Track deployment history within GitHub by enabling the “Deployment” status badge.
- Periodically prune old releases with `dep cleanup` or configure `set('keep_releases', 5);`.
- Review GitHub Actions logs for warnings about long-running tasks or failing migrations.
- Rotate SSH keys annually and audit secrets in GitHub.

---

## 7. Recent Improvements (November 2025)

### Changed PHP-FPM User from www-data to www

**Reason**: Better alignment with web server configuration and improved security practices.

**Changes Made**:
1. Updated `deploy.php` to set `http_user` to `www`
2. Added automatic ownership management (`el:www`)
3. Configured proper file permissions (755/644/775)
4. Added sudo permissions for deployment user

**Migration Steps**:
```bash
# On the server
# 1. Create www user if not exists
sudo groupadd -f www
sudo useradd -g www -s /usr/sbin/nologin -M www 2>/dev/null || true

# 2. Update PHP-FPM configuration
sudo nano /etc/php/8.4/fpm/pool.d/www.conf
# Change user and group to 'www'

# 3. Update web server configuration
sudo nano /etc/nginx/nginx.conf  # or Apache config
# Change user to 'www'

# 4. Update existing deployment ownership
sudo chown -R el:www /var/www/learning.dhanifudin.com
sudo chmod -R 775 /var/www/learning.dhanifudin.com/shared/storage

# 5. Restart services
sudo systemctl restart php8.4-fpm
sudo systemctl restart nginx  # or apache2
```

### Added Deployment Safety Features

1. **Automatic Backup**: Creates hardlink backup before each deployment
2. **Permission Management**: Automated ownership and permission setting
3. **Verification Steps**: Post-deployment checks for directories and services
4. **Health Checks**: Application responsiveness testing
5. **Optimization**: Automatic cache generation for better performance

### Security Improvements

- Limited sudo access to specific commands only
- Proper file ownership separation (deploy user vs web server)
- Secure permissions (no 777, using 775 for writable dirs)
- Group-based access control

---

## Next Steps Checklist

- [ ] Create `deploy.php` and commit to repository.
- [ ] Add Deployer as a dev dependency via Composer.
- [ ] Set GitHub Actions workflow file as described.
- [ ] Provision and secure the VPS environment.
- [ ] Populate GitHub secrets (`VPS_*` and app secrets).
- [ ] Test the workflow against a staging host before directing traffic to production.
