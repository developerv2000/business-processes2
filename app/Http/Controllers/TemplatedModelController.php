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
            collect(['name' => 'ManufacturerBlacklist', 'attributes' => ['name']]),
            collect(['name' => 'Country', 'attributes' => ['name']]),
            collect(['name' => 'CountryCode', 'attributes' => ['name']]),
            collect(['name' => 'ProductShelfLife', 'attributes' => ['name']]),
            collect(['name' => 'KvppPriority', 'attributes' => ['name']]),
            collect(['name' => 'KvppSource', 'attributes' => ['name']]),
            collect(['name' => 'KvppStatus', 'attributes' => ['name']]),
            collect(['name' => 'ManufacturerCategory', 'attributes' => ['name']]),
            collect(['name' => 'Inn', 'attributes' => ['name']]),
            collect(['name' => 'PortfolioManager', 'attributes' => ['name']]),
            // collect(['name' => 'ProcessOwner', 'attributes' => ['name']]),
            collect(['name' => 'ProductClass', 'attributes' => ['name']]),
            collect(['name' => 'MarketingAuthorizationHolder', 'attributes' => ['name']]),
            collect(['name' => 'Zone', 'attributes' => ['name']]),
            collect(['name' => 'ProductForm', 'attributes' => ['name', 'parent_id']]),
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

        // Collect model attributes for easy access and checking
        $modelAttributes = collect($model['attributes']);

        // Get finalized model records
        $records = self::getModelRecordsFinalized($request, $model, $modelAttributes);

        // Get all model records for filtering purposes
        $allRecords = self::getAllModelRecords($model);

        // Get all parent records for filtering purposes, if model templates contains 'parented'
        $parentRecords = self::getAllModelParentRecords($model, $modelAttributes);

        return view('templated-models.show', compact('request', 'model', 'modelAttributes', 'records', 'allRecords', 'parentRecords'));
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
     * @param \Illuminate\Support\Collection $modelAttributes
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private static function getModelRecordsFinalized($request, $model, $modelAttributes)
    {
        $fullNamespace = $model['full_namespace'];
        $query = $fullNamespace::query();

        // Apply filters to the model records
        $query = self::applyFiltersToModelRecords($request, $query, $modelAttributes);

        // Finalize the model records (e.g., apply sorting, pagination)
        $records = self::finalizeModelRecords($request, $query, $modelAttributes);

        return $records;
    }

    /**
     * Apply filters to the model records query.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Support\Collection $modelAttributes
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private static function applyFiltersToModelRecords($request, $query, $modelAttributes)
    {
        $filterAttributes = [];

        // Add filtering attributes based on attributes of the model
        if ($modelAttributes->contains('name')) {
            $filterAttributes[] = 'id'; // id related single select is used as filter for name
        }

        if ($modelAttributes->contains('parent_id')) {
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
     * @param \Illuminate\Support\Collection $modelAttributes
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private static function finalizeModelRecords($request, $query, $modelAttributes)
    {
        $sortByName = $modelAttributes->contains('name');

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
     * Retrieve all parent records for a given model.
     *
     * @param string $model The model class name.
     * @param \Illuminate\Support\Collection $modelAttributes The collection of model attributes.
     * @return \Illuminate\Database\Eloquent\Collection|null The collection of parent records or null if none found.
     */
    private static function getAllModelParentRecords($model, $modelAttributes)
    {
        // Initialize variable to store parent records
        $parents = null;

        // Check if the model attributes contain a 'parent_id' field
        if ($modelAttributes->contains('parent_id')) {
            // Retrieve the full namespace of the model
            $fullNamespace = $model['full_namespace'];

            // Retrieve only parent records using the model scope
            $parents = $fullNamespace::onlyParents();
        }

        // Return the collection of parent records or null if none found
        return $parents;
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
