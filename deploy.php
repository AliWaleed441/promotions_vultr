<?php
namespace Deployer;

require 'recipe/laravel.php';

// Config
set('application','promotions_laravel');

set('repository', 'https://github.com/AliWaleed441/promotions_vultr.git');

set('git_tty',true);

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('198.13.46.186')
    ->set('deploy_path', '~/var/www/html/promotions_vultr');

// Hooks

after('deploy:failed', 'deploy:unlock');
