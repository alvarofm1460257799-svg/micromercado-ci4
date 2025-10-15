<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Debug\Toolbar\Collectors\Database;
use CodeIgniter\Debug\Toolbar\Collectors\Events;
use CodeIgniter\Debug\Toolbar\Collectors\Files;
use CodeIgniter\Debug\Toolbar\Collectors\Logs;
use CodeIgniter\Debug\Toolbar\Collectors\Routes;
use CodeIgniter\Debug\Toolbar\Collectors\Timers;
use CodeIgniter\Debug\Toolbar\Collectors\Views;

class Toolbar extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Toolbar Collectors
     * --------------------------------------------------------------------------
     *
     * Lista de colectores que se ejecutarán cuando se muestre la barra.
     * Puedes desactivar los que no necesites para mejorar rendimiento.
     */
    public array $collectors = [
        Timers::class,
        Database::class,
        Logs::class,
        Views::class,
        // \CodeIgniter\Debug\Toolbar\Collectors\Cache::class, // Activa si usas caché
        Files::class,
        Routes::class,
        Events::class,
    ];

    /**
     * --------------------------------------------------------------------------
     * Toolbar Views Path
     * --------------------------------------------------------------------------
     *
     * Ruta completa hacia las vistas de la toolbar.
     * IMPORTANTE: Debe terminar con "/"
     */
    public string $viewsPath = SYSTEMPATH . 'Debug/Toolbar/Views/';

    /**
     * --------------------------------------------------------------------------
     * Collect Var Data
     * --------------------------------------------------------------------------
     *
     * Recolectar las variables pasadas a las vistas.
     * Ponlo en false si tu aplicación maneja arrays de datos muy grandes.
     */
    public bool $collectVarData = true;

    /**
     * --------------------------------------------------------------------------
     * Max History
     * --------------------------------------------------------------------------
     *
     * Límite de peticiones pasadas que se guardan.
     * 0 = no guarda, -1 = ilimitado.
     */
    public int $maxHistory = 20;

    /**
     * --------------------------------------------------------------------------
     * Max Queries
     * --------------------------------------------------------------------------
     *
     * Máximo de consultas SQL que se mostrarán en la barra.
     */
    public int $maxQueries = 100;

    /**
     * --------------------------------------------------------------------------
     * Watched Directories
     * --------------------------------------------------------------------------
     *
     * Directorios a monitorear para hot-reload en desarrollo.
     * ROOTPATH se antepone automáticamente.
     */
    public array $watchedDirectories = [
        'app',
    ];

    /**
     * --------------------------------------------------------------------------
     * Watched File Extensions
     * --------------------------------------------------------------------------
     *
     * Extensiones que se vigilan para recargar la página.
     */
    public array $watchedExtensions = [
        'php', 'css', 'js', 'html', 'svg', 'json', 'env',
    ];
}
