<?php

namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'https://github.com/digitall-it/archivio.london.git');

add('shared_files', ['database/database.sqlite']);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('london')
    ->set('remote_user', 'www')
    ->set('deploy_path', '~/archivio.london');

// Hooks

task('npm:build', function () {
    run('cd {{release_path}} && npm install && npm run build');
})->desc('Compile assets');

task('artisan:filament-optimize', function () {
    run('cd {{release_path}} && php artisan filament:optimize');
})->desc('Optimize Filament');

after('deploy:vendors', 'npm:build');
after('deploy:symlink', 'artisan:filament-optimize');
after('deploy:failed', 'deploy:unlock');
