<?php

namespace Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\FrameworkException;
use CodeIgniter\HotReloader\HotReloader;
use Config\Services;

/*
 * --------------------------------------------------------------------
 * Application Events
 * --------------------------------------------------------------------
 * Define application-wide events here.
 */

Events::on('pre_system', static function () {
    if (ENVIRONMENT !== 'testing') {
        if (ini_get('zlib.output_compression')) {
            throw FrameworkException::forEnabledZlibOutputCompression();
        }

        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        ob_start(static fn ($buffer) => $buffer);
    }

    /*
     * --------------------------------------------------------------------
     * Debug Toolbar Listeners.
     * --------------------------------------------------------------------
     */
    if (CI_DEBUG && ! is_cli()) {
        // Captura de queries para el colector Database
        Events::on('DBQuery', 'CodeIgniter\Debug\Toolbar\Collectors\Database::collect');

        // Inicia la Toolbar al final de la petición
        Events::on('post_system', static function () {
            Services::toolbar()->respond();
        });

        // Hot Reload (solo en desarrollo)
        if (ENVIRONMENT === 'development') {
            Services::routes()->get('__hot-reload', static function () {
                (new HotReloader())->run();
            });
        }
    }
});
