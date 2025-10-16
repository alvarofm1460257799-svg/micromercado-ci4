<?php

// --------------------------------------------------------------------
// Check PHP version
// --------------------------------------------------------------------
$minPhpVersion = '7.4'; // CodeIgniter 4 mínimo
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    exit(sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION
    ));
}

// --------------------------------------------------------------------
// Path to the front controller
// --------------------------------------------------------------------
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure current directory is front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

// --------------------------------------------------------------------
// Bootstrap the application
// --------------------------------------------------------------------
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();

// Carga bootstrap del framework
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

// Load environment from .env
require_once SYSTEMPATH . 'Config/DotEnv.php';
(new CodeIgniter\Config\DotEnv(ROOTPATH))->load();

// Define ENVIRONMENT
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', env('CI_ENVIRONMENT', 'production'));
}

// --------------------------------------------------------------------
// Initialize CodeIgniter
// --------------------------------------------------------------------
$app = Config\Services::codeigniter();
$app->initialize();

// Detect context: CLI or Web
$context = is_cli() ? 'php-cli' : 'web';
$app->setContext($context);

// --------------------------------------------------------------------
// Run the application
// --------------------------------------------------------------------
$app->run();

// Exit with success code
exit(EXIT_SUCCESS);
