<?php

namespace App\Http\Controllers;

use App\Http\Requests\TemplatedModelStoreRequest;
use App\Http\Requests\TemplatedModelUpdateRequest;
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
    public static function collectModelDefinitions()
    {
        $models = collect([
            collect(['name' => 'ManufacturerBlacklist', 'display_name' => 'Manufacturer black lists', 'attributes' => ['name']]),
            collect(['name' => 'Country', 'display_name' => 'Countries', 'attributes' => ['name']]),
            collect(['name' => 'CountryCode', 'display_name' => 'Country codes', 'attributes' => ['name']]),
            collect(['name' => 'ProductShelfLife', 'display_name' => 'Product shelf lives', 'attributes' => ['name']]),
            collect(['name' => 'KvppPriority', 'display_name' => 'KVPP priorities', 'attributes' => ['name']]),
            collect(['name' => 'KvppSource', 'display_name' => 'KVPP sources', 'attributes' => ['name']]),
            collect(['name' => 'KvppStatus', 'display_name' => 'KVPP statusses', 'attributes' => ['name']]),
            collect(['name' => 'ManufacturerCategory', 'display_name' => 'Manufacturer categories', 'attributes' => ['name']]),
            collect(['name' => 'Inn', 'display_name' => 'Inns', 'attributes' => ['name']]),
            collect(['name' => 'PortfolioManager', 'display_name' => 'Portfolio managers', 'attributes' => ['name']]),
            collect(['name' => 'ProcessResponsiblePerson', 'display_name' => 'Process responsible people', 'attributes' => ['name']]),
            collect(['name' => 'ProductClass', 'display_name' => 'Product classes', 'attributes' => ['name']]),
            collect(['name' => 'MarketingAuthorizationHolder', 'display_name' => 'Marketing authorization holders', 'attributes' => ['name']]),
            collect(['name' => 'Zone', 'display_name' => 'Zones', 'attributes' => ['name']]),
            collect(['name' => 'ProductForm', 'display_name' => 'Product forms', 'attributes' => ['name', 'parent_id']]),
        ]);

        $models = self::addFullNamespaceToModels($models);

        return $models;
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
        // Find the specified model
        $model = self::findModelByName($modelName);

        // Add full namespace and item count to the model
        $model = self::addItemsCountToModel($model);

        // Collect model attributes for easy access and checking
        $modelAttributes = collect($model['attributes']);

        // Get finalized model records
        $records = self::getModelRecordsFinalized($request, $model, $modelAttributes);

        // Get all model records for filtering purposes
        $allRecords = self::getAllModelRecords($model);

        // Get all parent records for filtering purposes, if model attributes contains 'parent_id'
        $parentRecords = self::getAllModelParentRecords($model, $modelAttributes);

        return view('templated-models.show', compact('request', 'model', 'modelAttributes', 'records', 'allRecords', 'parentRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, $modelName)
    {
        $model = self::findModelByName($modelName);
        $modelAttributes = collect($model['attributes']);

        // Get all parent records, if model attributes contains 'parent_id'
        $parentRecords = self::getAllModelParentRecords($model, $modelAttributes);

        return view('templated-models.create', compact('model', 'modelAttributes', 'parentRecords'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TemplatedModelStoreRequest $request, $modelName)
    {
        $model = self::findModelByName($modelName);

        $fullNamespace = $model['full_namespace'];
        $fullNamespace::create($request->all());

        return to_route('templated-models.show', $modelName);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($modelName, $id)
    {
        $model = self::findModelByName($modelName);
        $modelAttributes = collect($model['attributes']);

        // Get all parent records, if model attributes contains 'parent_id'
        $parentRecords = self::getAllModelParentRecords($model, $modelAttributes);

        $fullNamespace = $model['full_namespace'];
        $instance = $fullNamespace::Find($id);

        return view('templated-models.edit', compact('instance', 'model', 'modelAttributes', 'parentRecords'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TemplatedModelUpdateRequest $request, $modelName, $id)
    {
        $model = self::findModelByName($modelName);

        $fullNamespace = $model['full_namespace'];
        $instance = $fullNamespace::find($id);
        $instance->update($request->all());

        return redirect($request->input('previous_url'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $modelName)
    {
        $model = self::findModelByName($modelName);
        $ids = $request->input('ids');

        $fullNamespace = $model['full_namespace'];

        foreach ($ids as $id) {
            $instance = $fullNamespace::find($id);

            // Escape deleting of used records
            if ($instance->usage_count) {
                return redirect()->back()->withErrors([
                    'templated_models_deletion' => trans('validation.custom.templated_models.is_in_use', ['name' => $instance->name ?: $instance->id])
                ]);
            }

            $instance->delete();
        }

        return redirect()->back();
    }

    public static function findModelByName($modelName)
    {
        $models = self::collectModelDefinitions();

        return $models->where('name', $modelName)->first();
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
     * Add full namespace to each model definition.
     *
     * @param \Illuminate\Support\Collection $models
     * @return \Illuminate\Support\Collection
     */
    private static function addFullNamespaceToModels($models)
    {
        return $models->map(function ($model) {
            $model['full_namespace'] = self::MODELS_BASE_NAMESPACE . $model['name'];
            return $model;
        });
    }

    /**
     * Add item count to a single model definition.
     *
     * Requires defined full_namespace attribute of the model!!!
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
