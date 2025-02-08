<?php
namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'https://github.com/digitall-it/archivio.london.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('london')
    ->set('remote_user', 'www')
    ->set('deploy_path', '~/archivio.london');

// Hooks

after('deploy:failed', 'deploy:unlock');
