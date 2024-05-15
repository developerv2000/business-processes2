<?php

namespace App\Http\Controllers;

use App\Support\Helper;
use Illuminate\Http\Request;

class TemplatedModelController extends Controller
{
    const DEFAULT_PAGINATION_LIMIT = 50;

    // Base namespace for the models
    const MODELS_BASE_NAMESPACE = 'App\\Models\\';

    /**
     * Collect an array of model definitions.
     *
     * @return \Illuminate\Support\Collection
     */
    private static function collectModelDefinitions()
    {
        return collect([
            collect(['name' => 'ManufacturerBlacklist', 'templates' => ['named']]),
            collect(['name' => 'Country', 'templates' => ['named']]),
            collect(['name' => 'CountryCode', 'templates' => ['named']]),
            collect(['name' => 'ProductShelfLife', 'templates' => ['named']]),
            collect(['name' => 'KvppPriority', 'templates' => ['named']]),
            collect(['name' => 'KvppSource', 'templates' => ['named']]),
            collect(['name' => 'KvppStatus', 'templates' => ['named']]),
            collect(['name' => 'ManufacturerCategory', 'templates' => ['named']]),
            collect(['name' => 'Inn', 'templates' => ['named']]),
            collect(['name' => 'PortfolioManager', 'templates' => ['named']]),
            // collect(['name' => 'ProcessOwner', 'templates' => ['named']]),
            collect(['name' => 'ProductClass', 'templates' => ['named']]),
            collect(['name' => 'MarketingAuthorizationHolder', 'templates' => ['named']]),
            collect(['name' => 'Zone', 'templates' => ['named']]),
        ]);
    }

    /**
     * Display a listing of the models.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $models = self::collectModelDefinitions();

        // Add full namespace and item count for each model
        $models = self::addFullNamespaceToModels($models);
        $models = self::addItemsCountToModels($models);

        return view('templated-models.index', compact('models'));
    }

    /**
     * Display the specified model records.
     */
    public function show(Request $request, $modelName)
    {
        // Collect all model definitions
        $models = self::collectModelDefinitions();

        // Find the specified model
        $model = $models->where('name', $modelName)->first();

        // Add full namespace and item count to the model
        $model = self::addFullNamespaceToModel($model);
        $model = self::addItemsCountToModel($model);

        // Collect model templates for easy access and checking
        $modelTemplates = collect($model['templates']);

        // Get finalized model records
        $records = self::getModelRecordsFinalized($request, $model, $modelTemplates);

        // Get all model records for filtering purposes
        $allRecords = self::getAllModelRecords($model);

        return view('templated-models.show', compact('request', 'model', 'modelTemplates', 'records', 'allRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Get finalized model records after filtering and pagination.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Support\Collection $model
     * @param \Illuminate\Support\Collection $modelTemplates
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private static function getModelRecordsFinalized($request, $model, $modelTemplates)
    {
        $fullNamespace = $model['full_namespace'];
        $query = $fullNamespace::query();

        // Apply filters to the model records
        $query = self::applyFiltersToModelRecords($request, $query, $modelTemplates);

        // Finalize the model records (e.g., apply sorting, pagination)
        $records = self::finalizeModelRecords($request, $query, $modelTemplates);

        return $records;
    }

    /**
     * Apply filters to the model records query.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Support\Collection $modelTemplates
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private static function applyFiltersToModelRecords($request, $query, $modelTemplates)
    {
        $filterAttributes = [];

        // Add filtering attributes based on templates
        if ($modelTemplates->contains('named')) {
            $filterAttributes[] = 'id'; // id related single select is used as filter for name
        }

        if ($modelTemplates->contains('parented')) {
            $filterAttributes[] = 'parent_id';
        }

        // Apply where-equal filters using helper method
        $query = Helper::filterQueryWhereEqualStatements($request, $query, $filterAttributes);

        return $query;
    }

    /**
     * Finalize the model records query by applying sorting and pagination.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Support\Collection $modelTemplates
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private static function finalizeModelRecords($request, $query, $modelTemplates)
    {
        $sortByName = $modelTemplates->contains('named');

        $records = $query->when($sortByName, function ($q) {
            $q->orderBy('name', 'asc');
        })
            ->orderBy('id', 'asc')
            ->paginate(self::DEFAULT_PAGINATION_LIMIT, ['*'], 'page', $request->page)
            ->appends($request->except(['page']));

        return $records;
    }

    /**
     * Get all records for the specified model.
     *
     * @param \Illuminate\Support\Collection $modelDefinition
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private static function getAllModelRecords($modelDefinition)
    {
        $fullNamespace = $modelDefinition['full_namespace'];
        return $fullNamespace::all();
    }

    /**
     * Add full namespace to a single model definition.
     *
     * @param \Illuminate\Support\Collection $model
     * @return \Illuminate\Support\Collection
     */
    private static function addFullNamespaceToModel($model)
    {
        $model['full_namespace'] = self::MODELS_BASE_NAMESPACE . $model['name'];
        return $model;
    }

    /**
     * Add item count to a single model definition.
     *
     * @param \Illuminate\Support\Collection $model
     * @return \Illuminate\Support\Collection
     */
    private static function addItemsCountToModel($model)
    {
        $fullNamespace = $model['full_namespace'];
        $model['items_count'] = $fullNamespace::count();

        return $model;
    }

    /**
     * Add full namespace to each model definition.
     *
     * @param \Illuminate\Support\Collection $models
     * @return \Illuminate\Support\Collection
     */
    private static function addFullNamespaceToModels($models)
    {
        return $models->map(function ($model) {
            return self::addFullNamespaceToModel($model);
        });
    }

    /**
     * Add item count to each model definition.
     *
     * @param \Illuminate\Support\Collection $models
     * @return \Illuminate\Support\Collection
     */
    private static function addItemsCountToModels($models)
    {
        return $models->map(function ($model) {
            return self::addItemsCountToModel($model);
        });
    }
}
