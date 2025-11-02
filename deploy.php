<?php

declare(strict_types=1);

namespace Deployer;

require 'recipe/laravel.php';

set('repository', 'https://github.com/dhanifudin/lemini.git');
set('git_tty', true);
set('keep_releases', 5);
set('allow_anonymous_stats', false);
set('writable_mode', 'chmod');
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
});

desc('Reload PHP-FPM');
task('php-fpm:reload', static function () {
    run('sudo systemctl reload ' . get('php_fpm_service'));
});

after('deploy:failed', 'deploy:unlock');
after('deploy:symlink', 'php-fpm:reload');
