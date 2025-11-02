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
   - Install system packages: `php8.2-cli`, `php8.2-fpm`, required PHP extensions (`mbstring`, `intl`, `pcntl`, `pdo_mysql`, `curl`, `zip`), `composer`, `git`, `node`, `npm`, and a process supervisor such as `systemd` units or `supervisor` if queues or SSR servers run on the host.
   - Configure web server virtual host to point to `/var/www/lemini/current/public`.
   - Create shared directories and permissions (`storage/`, `bootstrap/cache`, `public/storage` symlink).
3. **Secrets & credentials**
   - Generate SSH key pair dedicated to GitHub Actions (no passphrase).
   - Add the public key to the VPS deploy user’s `~/.ssh/authorized_keys`.
   - Store the private key in the repository secrets as `VPS_SSH_KEY`.
   - Capture host fingerprint with `ssh-keyscan` and add to secret `VPS_SSH_KNOWN_HOSTS`.
   - Add any additional secrets required by the app (database URL, queue connection, third-party API keys) to GitHub environments.

---

## 1. Prepare the VPS

1. Create project directories:
   ```bash
   sudo mkdir -p /var/www/lemini/{releases,shared}
   sudo chown -R deploy:deploy /var/www/lemini
   ```
2. Inside `/var/www/lemini/shared` create persistent resources:
   ```bash
   mkdir -p storage bootstrap/cache
   touch storage/logs/laravel.log
   ```
3. Upload production `.env` to `/var/www/lemini/shared/.env`. Keep database credentials and application keys here.
4. Ensure the web server points to `/var/www/lemini/current/public` (Deployer updates the `current` symlink on each release).
5. For queue workers or horizon, ensure services read the `current` release path so restarts are not required on every deploy (or add restart commands in Deployer hooks).

---

## 2. Configure Deployer

Create `deploy.php` in the project root:

```php
<?php
declare(strict_types=1);

namespace Deployer;

require 'recipe/laravel.php';

set('repository', 'https://github.com/dhanifudin/lemini.git');
set('git_tty', true);
set('keep_releases', 5);
set('allow_anonymous_stats', false);
set('writable_mode', 'chmod');
set('php_fpm_service', 'php8.2-fpm'); // customize for the VPS service name
set('deploy_artifact', getenv('DEPLOY_ARTIFACT') ?: __DIR__ . '/release.tar.gz');

host('production')
    ->setHostname(getenv('DEPLOY_HOST') ?: '0.0.0.0')
    ->setRemoteUser(getenv('DEPLOY_USER') ?: 'deploy')
    ->setDeployPath(getenv('DEPLOY_PATH') ?: '/var/www/lemini')
    ->set('branch', getenv('DEPLOY_BRANCH') ?: 'main');

task('deploy:update_code', function () {
    $artifact = get('deploy_artifact');

    if (!file_exists($artifact)) {
        throw new \RuntimeException(sprintf('Deployment artifact not found at %s', $artifact));
    }

    upload($artifact, '{{release_path}}/release.tar.gz');
    run('cd {{release_path}} && tar -xzf release.tar.gz && rm release.tar.gz');
});

task('deploy:vendors', function () {
    // Composer dependencies are packaged in the artifact.
});

desc('Reload PHP-FPM');
task('php-fpm:reload', function () {
    run('sudo systemctl reload ' . get('php_fpm_service'));
});

after('deploy:failed', 'deploy:unlock');
after('deploy:symlink', 'php-fpm:reload');
```

Key notes:

- Update repository URL to match the Git remote (HTTPS or SSH).
- Ensure your CI step packages the `vendor/` directory and built assets before invoking Deployer (the artifact is uploaded during `deploy:update_code`).
- Use `dotenv()` helper or `set()` calls if you prefer storing host info directly in the file rather than environment variables.
- Commit `deploy.php` and ensure `composer.json` includes Deployer: `composer require --dev deployer/deployer:^7.0`.

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

## Next Steps Checklist

- [ ] Create `deploy.php` and commit to repository.
- [ ] Add Deployer as a dev dependency via Composer.
- [ ] Set GitHub Actions workflow file as described.
- [ ] Provision and secure the VPS environment.
- [ ] Populate GitHub secrets (`VPS_*` and app secrets).
- [ ] Test the workflow against a staging host before directing traffic to production.
