<?php

/**
 * @author Bobur Nuridinov <developerv2000@gmail.com>
 */

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Class RouteGenerator
 *
 * This class provides helper methods for defining CRUD-related routes with customizable middleware
 * for viewing and editing functionalities.
 */
class RouteGenerator
{
    /**
     * Get the default CRUD route names.
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
     * Define a specific CRUD route by its name.
     *
     * @param string $name The name of the route to define.
     * @param string $viewMiddleware Middleware for viewing actions.
     * @param string $editMiddleware Middleware for editing actions.
     * @return void
     */
    public static function defineDefaultCrudRouteByName($name, $viewMiddleware = null, $editMiddleware = null)
    {
        switch ($name) {
            case 'index':
                Route::get('/', 'index')->name('index')->middleware($viewMiddleware);
                break;
            case 'edit':
                Route::get('/edit/{instance}', 'edit')->name('edit')->middleware($editMiddleware);
                break;
            case 'create':
                Route::get('/create', 'create')->name('create')->middleware($editMiddleware);
                break;
            case 'trash':
                Route::get('/trash', 'trash')->name('trash')->middleware($viewMiddleware);
                break;
            case 'store':
                Route::post('/store', 'store')->name('store')->middleware($editMiddleware);
                break;
            case 'update':
                Route::patch('/update/{instance}', 'update')->name('update')->middleware($editMiddleware);
                break;
            case 'destroy':
                Route::delete('/destroy', 'destroy')->name('destroy')->middleware($editMiddleware);
                break;
            case 'restore':
                Route::patch('/restore', 'restore')->name('restore')->middleware($editMiddleware);
                break;
            case 'export':
                Route::post('/export', 'export')->name('export')->middleware('can:export-as-excel');
                break;
        }
    }

    /**
     * Define all default CRUD routes.
     *
     * @param string $viewMiddleware Middleware for viewing actions.
     * @param string $editMiddleware Middleware for editing actions.
     * @return void
     */
    public static function defineAllDefaultCrudRoutes($viewMiddleware = null, $editMiddleware = null)
    {
        // Get the list of default routes.
        $defaultRoutes = self::getDefaultCrudRouteNames();

        // Define each route.
        foreach ($defaultRoutes as $route) {
            self::defineDefaultCrudRouteByName($route, $viewMiddleware, $editMiddleware);
        }
    }

    /**
     * Define CRUD routes, excluding specific routes.
     *
     * @param array $excepts The routes to exclude from the definition.
     * @param string $viewMiddleware Middleware for viewing actions.
     * @param string $editMiddleware Middleware for editing actions.
     * @return void
     */
    public static function defineDefaultCrudRoutesExcept($excepts = [], $viewMiddleware = null, $editMiddleware = null)
    {
        // Get the list of default routes.
        $defaultRoutes = self::getDefaultCrudRouteNames();

        // Filter out the excluded routes.
        $routes = array_diff($defaultRoutes, $excepts);

        // Define the remaining routes.
        foreach ($routes as $route) {
            self::defineDefaultCrudRouteByName($route, $viewMiddleware, $editMiddleware);
        }
    }

    /**
     * Define only specific CRUD routes.
     *
     * @param array $only The routes to include in the definition.
     * @param string $viewMiddleware Middleware for viewing actions.
     * @param string $editMiddleware Middleware for editing actions.
     * @return void
     */
    public static function defineDefaultCrudRoutesOnly($only = [], $viewMiddleware = null, $editMiddleware = null)
    {
        // Define only the specified routes.
        foreach ($only as $name) {
            self::defineDefaultCrudRouteByName($name, $viewMiddleware, $editMiddleware);
        }
    }
}
