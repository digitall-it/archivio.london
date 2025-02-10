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

task('artisan:queue:restart', function () {
    run('cd {{release_path}} && php artisan queue:restart');
})->desc('Restart Laravel Queue Workers');

task('supervisor:restart-qrscanner', function () {
    run('sudo supervisorctl restart archivio-london-qrscanner');
})->desc('Restart the QR Scanner daemon in Supervisor');

after('deploy:failed', 'deploy:unlock');

after('deploy:vendors', 'artisan:optimize');
after('deploy:vendors', 'npm:build');
after('deploy:publish', 'artisan:queue:restart');
after('deploy:publish', 'supervisor:restart-qrscanner');
