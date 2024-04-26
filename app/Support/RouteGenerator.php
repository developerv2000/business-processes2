<?php

/**
 * @author Bobur Nuridinov <developerv2000@gmail.com>
 */

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Class RouteGenerator
 *
 * The RouteGenerator class provides helper methods for generating routes in Laravel.
 */
class RouteGenerator
{
    /**
     * Get default CRUD route names.
     *
     * @return array The array of default CRUD route names.
     */
    private static function getDefaultCrudRouteNames()
    {
        return [
            'index', 'edit', 'create', 'trash',
            'store', 'update', 'destroy', 'restore', 'export',
        ];
    }

    /**
     * Define a template route by name.
     *
     * @param string $name The name of the route to define.
     * @return void
     */
    public static function defineDefaultCrudRouteByName($name)
    {
        switch ($name) {
            case 'index':
                Route::get('/', 'index')->name('index');
                break;
            case 'edit':
                Route::get('/edit/{instance}', 'edit')->name('edit');
                break;
            case 'create':
                Route::get('/create', 'create')->name('create');
                break;
            case 'trash':
                Route::get('/trash', 'trash')->name('trash');
                break;
            case 'store':
                Route::post('/store', 'store')->name('store');
                break;
            case 'update':
                Route::patch('/update/{instance}', 'update')->name('update');
                break;
            case 'destroy':
                Route::delete('/destroy', 'destroy')->name('destroy');
                break;
            case 'restore':
                Route::patch('/restore', 'restore')->name('restore');
                break;
            case 'export':
                Route::post('/export', 'export')->name('export');
                break;
        }
    }

    /**
     * Define all default templated routes for CRUD operations
     *
     * @return void
     */
    public static function defineAllDefaultCrudRoutes()
    {
        // Define default routes
        $defaultRoutes = self::getDefaultCrudRouteNames();

        // Define routes
        foreach ($defaultRoutes as $route) {
            self::defineDefaultCrudRouteByName($route);
        }
    }

    /**
     * Define default templated routes for CRUD operations, excluding specified routes.
     *
     * @param array $excepts The routes to exclude from the definition.
     * @return void
     */
    public static function defineDefaultCrudRoutesExcept($excepts)
    {
        // Define default routes
        $defaultRoutes = self::getDefaultCrudRouteNames();

        // Remove excluded routes
        $routes = array_diff($defaultRoutes, $excepts);

        // Define routes
        foreach ($routes as $route) {
            self::defineDefaultCrudRouteByName($route);
        }
    }

    /**
     * Define default templated routes for CRUD operations, including only specified routes.
     *
     * @param array $only The routes to include in the definition.
     * @return void
     */
    public static function defineDefaultCrudRoutesOnly($only = [])
    {
        foreach ($only as $name) {
            self::defineDefaultCrudRouteByName($name);
        }
    }
}
