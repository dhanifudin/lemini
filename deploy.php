<?php

declare(strict_types=1);

namespace Deployer;

require 'recipe/laravel.php';

set('repository', 'https://github.com/dhanifudin/lemini.git');
set('git_tty', true);
set('keep_releases', 5);
set('allow_anonymous_stats', false);
set('writable_mode', 'chmod');
set('php_fpm_service', 'php8.2-fpm');
set('deploy_artifact', getenv('DEPLOY_ARTIFACT') ?: __DIR__ . '/release.tar.gz');
set('ssh_multiplexing', false);

host('production')
    ->setHostname(getenv('DEPLOY_HOST') ?: '0.0.0.0')
    ->setRemoteUser(getenv('DEPLOY_USER') ?: 'deploy')
    ->setDeployPath(getenv('DEPLOY_PATH') ?: '/var/www/lemini')
    ->set('branch', getenv('DEPLOY_BRANCH') ?: 'main')
    ->set('ssh_options', [
        'StrictHostKeyChecking=accept-new',
    ]);

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
