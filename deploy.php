<?php
namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'https://github.com/dhanifudin/lemini.git');
set('php_fpm_service', 'php8.4-fpm');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('production')
    ->set('hostname', getenv('VPS_HOST'))
    ->set('remote_user', getenv('VPS_USER') ?: 'deployer')
    ->set('deploy_path', getenv('VPS_DEPLOY_PATH') ?: '/var/www/lemini')
    ->set('branch', getenv('DEPLOY_BRANCH') ?: 'main');

task('deploy:update_code', function () {
    $artifact = get('deploy_artifact');

    if (!file_exists($artifact)) {
        throw new \RuntimeException(sprintf('Deployment artifact not found at %s', $artifact));
    }

    upload($artifact, '{{release_path}}/release.tar.gz');
    run('cd {{release_path}} && tar -xzf release.tar.gz && rm release.tar.gz');
});

desc('Reload PHP-FPM');
task('php-fpm:reload', function () {
    run('sudo systemctl reload ' . get('php_fpm_service'));
});

after('deploy:failed', 'deploy:unlock');
after('deploy:symlink', 'php-fpm:reload');
