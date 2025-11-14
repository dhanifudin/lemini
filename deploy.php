<?php

declare(strict_types=1);

namespace Deployer;

require 'recipe/laravel.php';

set('repository', 'https://github.com/dhanifudin/lemini.git');
set('git_tty', true);
set('keep_releases', 5);
set('allow_anonymous_stats', false);

// File permissions and ownership
set('php_fpm_service', 'php8.4-fpm');
set('ssh_multiplexing', false);
set('deploy_artifact', static function () {
    $candidates = [];

    $envPath = getenv('DEPLOY_ARTIFACT');
    if (is_string($envPath) && $envPath !== '') {
        $candidates[] = $envPath;
    }

    $candidates[] = getcwd() . '/release.tar.gz';
    $candidates[] = __DIR__ . '/release.tar.gz';

    foreach ($candidates as $path) {
        if ($path && file_exists($path)) {
            return realpath($path) ?: $path;
        }
    }

    throw new \RuntimeException(sprintf(
        'Deployment artifact not found. Checked paths: %s',
        implode(', ', array_filter($candidates))
    ));
});

host('production')
    ->setHostname(getenv('DEPLOY_HOST') ?: '194.127.193.198')
    ->setRemoteUser(getenv('DEPLOY_USER') ?: 'el')
    ->setDeployPath(getenv('DEPLOY_PATH') ?: '/var/www/learning.dhanifudin.com')
    ->set('branch', getenv('DEPLOY_BRANCH') ?: 'main')
    ->set('ssh_options', [
        'StrictHostKeyChecking=accept-new',
    ]);

task('deploy:update_code', static function () {
    $artifact = get('deploy_artifact');

    info(sprintf('Uploading artifact from %s', $artifact));

    upload($artifact, '{{release_path}}/release.tar.gz');
    run('cd {{release_path}} && tar -xzf release.tar.gz && rm release.tar.gz');
    
    info('Artifact extracted successfully');
});

desc('Reload PHP-FPM');
task('php-fpm:reload', static function () {
    run('sudo systemctl reload ' . get('php_fpm_service'));
});

desc('Verify deployment');
task('deploy:verify', static function () {
    $releasePath = get('release_path');
    
    // Check if required directories exist
    $requiredDirs = [
        'storage/app',
        'storage/framework/cache',
        'storage/framework/sessions',
        'storage/framework/views',
        'storage/logs',
        'bootstrap/cache',
    ];
    
    foreach ($requiredDirs as $dir) {
        if (!test("[ -d $releasePath/$dir ]")) {
            warning("Directory $dir does not exist");
        }
    }
    
    // Check if .env file exists
    if (!test("[ -f $releasePath/.env ]")) {
        warning('.env file does not exist in release path');
    }
    
    // Check PHP-FPM is running
    $phpFpmService = get('php_fpm_service');
    $status = run("sudo systemctl is-active $phpFpmService || echo 'inactive'");
    if (trim($status) !== 'active') {
        warning("PHP-FPM service ($phpFpmService) is not active");
    } else {
        info("PHP-FPM service is running");
    }
    
    info('Deployment verification completed');
});

desc('Run database migrations');
task('deploy:migrate', static function () {
    run('cd {{release_path}} && php artisan migrate --force');
    info('Database migrations completed');
});

desc('Health check');
task('deploy:health_check', static function () {
    $currentPath = get('deploy_path') . '/current';
    
    // Check if artisan is executable
    if (!test("[ -f $currentPath/artisan ]")) {
        warning('Artisan file not found');
        return;
    }
    
    // Test artisan command
    try {
        run("cd $currentPath && php artisan --version");
        info('Health check passed: Application is responsive');
    } catch (\Exception $e) {
        warning('Health check failed: Application may not be working properly');
    }
});

// Deployment hooks
after('deploy:failed', 'deploy:unlock');
after('deploy:shared', 'deploy:optimize');
after('deploy:symlink', 'php-fpm:reload');
after('deploy:symlink', 'deploy:verify');
after('deploy:verify', 'deploy:health_check');
