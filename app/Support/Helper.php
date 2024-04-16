<?php

/**
 * Custom Helper class
 *
 * @author Bobur Nuridinov <developerv2000@gmail.com>
 */

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class Helper
{
    const DEFAULT_MODEL_PAGINATION_LIMITS = [10, 20, 50, 80, 100, 200, 400]; // default pagination limits for models

    /**
     * Used while generating orderBy links
     */
    public static function addReversedSortingUrlToRequest($request)
    {
        $request->merge([
            'reversedSortingUrl' => self::setupReversedSortingUrl($request)
        ]);
    }

    /**
     * Reverses the sorting type in the URL query parameters.
     *
     * @param \Illuminate\Http\Request $request
     * @return string The modified URL with reversed sorting parameters
     */
    public static function setupReversedSortingUrl($request)
    {
        // Get the existing query parameters from the request
        $queryParams = $request->query();

        // Remove the 'orderBy' key if it exists
        self::deleteArrayKeyIfExists($queryParams, 'orderBy');

        // Reverse the 'orderType' (from 'asc' to 'desc' or vice versa)
        $queryParams['orderType'] = $request->orderType == 'asc' ? 'desc' : 'asc';

        // Build the modified URL with the updated query parameters
        $reversedSortUrl = $request->url() . '?' . http_build_query($queryParams);

        return $reversedSortUrl;
    }

    public static function generateSlug($string)
    {
        $string = self::transliterateIntoLatin($string);
        $string = Str::slug($string);

        return $string;
    }

    /**
     * Uploads a file for a model and updates its attribute value in the database.
     *
     * @param \Illuminate\Database\Eloquent\Model $model The model instance.
     * @param string $attribute The name of the input field and the model's attribute.
     * @param string $fileName The desired filename.
     * @param string $storePath The path to store the uploaded file.
     * @param \Illuminate\Http\Request|null $request Optional Request object for file retrieval.
     * @param callable|null $callback Optional callback function for custom processing.
     */
    public static function uploadModelFile($model, $attribute, $fileName, $storePath, $request = null): ?string
    {
        // Use provided request object or fallback to global request
        $request = $request ?: request();

        // Check if the file exists in the request
        if (!$request->hasFile($attribute)) {
            return null; // No file found, return null
        }

        // Retrieve the file from the request
        $file = $request->file($attribute);

        // Ensure the file is valid and move it to the storage path
        if ($file->isValid()) {
            $name = self::cutAndTrimString($fileName, 80); // Cut and trim filename
            $fullName = $name . '.' . $file->getClientOriginalExtension(); // Construct full filename

            // Ensure filename is unique
            $fullName = self::escapeDuplicateFilename($fullName, $storePath);

            // Move uploaded file to storage path
            $file->move($storePath, $fullName);

            // Update model attribute with new filename
            $model->{$attribute} = $fullName;

            // Save model to persist changes
            $model->save();

            // Return full path to uploaded file
            return $storePath . '/' . $fullName;
        }

        return null; // File upload failed, return null
    }

    /**
     * Resize an image using Intervention/Image.
     *
     * @param string $path The path to the image file.
     * @param int|null $width The desired width of the resized image.
     * @param int|null $height The desired height of the resized image.
     * @return void
     */
    public static function resizeImage($path, $width = null, $height = null): void
    {
        // Load the image
        $image = Image::read($path);

        // Perform resizing based on provided width and height
        if ($width && $height) {
            // Fitted Image Resizing | Cropping & Resizing Combined
            $image->cover($width, $height);
        } else {
            // Resizing Images Proportionally
            $image->scale($width, $height);
        }

        // Save the resized image
        $image->save($path);
    }

    /**
     * Rename a file if a file with the given name already exists in the given path.
     * Renaming style: name(i++), where i is an incrementing counter.
     *
     * @param string $filename The original filename.
     * @param string $path The path where the file is located.
     * @return string The new filename with a unique name to avoid duplication.
     */
    public static function escapeDuplicateFilename(string $filename, string $path): string
    {
        // Extract the file extension from the original filename.
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        // Extract the file name without the extension from the original filename.
        $name = pathinfo($filename, PATHINFO_FILENAME);

        // Ensure the path ends with a slash for consistency.
        $path = rtrim($path, '/') . '/';

        // Initialize the counter to append to the filename in case of duplication.
        $counter = 1;

        // Iterate until a unique filename is found.
        while (file_exists($path . $filename)) {
            // Append the counter to the filename and update the filename.
            $filename = sprintf('%s(%d).%s', $name, $counter++, $extension);
        }

        // Return the new filename with a unique name to avoid duplication.
        return $filename;
    }

    public static function deleteArrayKeyIfExists(&$array, $key)
    {
        if (array_key_exists($key, $array)) {
            unset($array[$key]);
        }
    }

    /**
     * Used while generating filenames
     */
    private static function cutAndTrimString($string, $length): string
    {
        if (mb_strlen($string) < $length) {
            $string = mb_substr($string, 0, $length);
        }

        $string = trim($string);

        return $string;
    }

    /**
     * Used while generating slug
     */
    private static function transliterateIntoLatin($string): string
    {
        // manual transilation of tajik letters
        $search = [
            'ӣ', 'ӯ', 'ҳ', 'қ', 'ҷ', 'ғ', 'Ғ', 'Ӣ', 'Ӯ', 'Ҳ', 'Қ', 'Ҷ',
        ];

        $replace = [
            'i', 'u', 'h', 'q', 'j', 'g', 'g', 'i', 'u', 'h', 'q', 'j',
        ];

        $transilation = str_replace($search, $replace, $string);

        // transilation of other languages
        $transilation = Str::ascii($transilation);

        return $transilation;
    }

    /*
    |--------------------------------------------------------------------------
    | Query filtering
    |--------------------------------------------------------------------------
    */

    /**
     * Filter the Eloquent query with WHERE statements based on request attributes.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function filterQueryWhereEqualStatements($request, $query, $attributes)
    {
        foreach ($attributes as $attribute) {
            // Skip to the next attribute if the attribute does not exists in the request
            if (!$request->has($attribute)) {
                continue;
            }

            // Add a WHERE clause to the query using the attribute and its value from the request
            $query = $query->where($attribute, $request->{$attribute});
        }

        return $query;
    }

    /**
     * Filter the Eloquent query with WHERE DATE statements based on request attributes.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function filterQueryDateStatements($request, $query, $attributes)
    {
        foreach ($attributes as $attribute) {
            // Skip to the next attribute if the attribute does not exists in the request
            if (!$request->has($attribute)) {
                continue;
            }

            // Add a WHERE DATE clause to the query using the attribute and its value from the request
            $query = $query->whereDate($attribute, $request->{$attribute});
        }

        return $query;
    }

    /**
     * Filter the Eloquent query with WHERE LIKE statements based on request attributes.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function filterQueryLikeStatements($request, $query, $attributes)
    {
        foreach ($attributes as $attribute) {
            // Skip to the next attribute if the attribute does not exists in the request
            if (!$request->has($attribute)) {
                continue;
            }

            // Add a WHERE LIKE clause to the query using the attribute and its value from the request
            $query = $query->where($attribute, 'LIKE', '%' . $request->{$attribute} . '%');
        }

        return $query;
    }

    /**
     * Filter the Eloquent query with WHERE DATE range statements based on request attributes.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function filterQueryDateRangeStatements($request, $query, $attributes)
    {
        foreach ($attributes as $attribute) {
            // Skip to the next attribute if the attribute does not exists in the request
            if (!$request->has($attribute)) {
                continue;
            }

            $dates = $request->{$attribute};
            // Split from & to dates
            $splitted = explode(' - ', $dates);

            // Ensure both dates are provided
            if (count($splitted) !== 2) {
                continue; // Skip to the next attribute if dates are invalid
            }

            // Parse the dates
            $fromDate = Carbon::createFromFormat('d/m/Y', $splitted[0])->format('Y-m-d');
            $toDate = Carbon::createFromFormat('d/m/Y', $splitted[1])->format('Y-m-d');

            // Add WHERE DATE range clause to the query using the attribute and its value from the request
            $query = $query
                ->whereDate($attribute, '>=', $fromDate)
                ->whereDate($attribute, '<', $toDate);
        }

        return $query;
    }

    /**
     * Filter the Eloquent query by BelongsToMany relations based on request attributes.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $relationNames
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function filterBelongsToManyRelations($request, $query, $relationNames)
    {
        // Loop through each relation name
        foreach ($relationNames as $relationName) {
            // Skip to the next attribute if the attribute does not exists in the request
            if (!$request->has($relationName)) {
                continue;
            }

            // Get the IDs from the request attribute
            $IDs = $request->{$relationName};

            // Add a WHERE clause to the query using whereHas and whereIn
            $query = $query->whereHas($relationName, function ($q) use ($IDs) {
                $q->whereIn('id', $IDs);
            });
        }

        return $query;
    }

    /**
     * Filter the Eloquent query by WHERE relation equal statements based on request attributes.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function filterWhereRelationEqualStatements($request, $query, $relations)
    {
        foreach ($relations as $relation) {
            // Skip to the next attribute if the request does not have the attribute for the relation
            if (!$request->has($relation['attribute'])) {
                continue;
            }

            // Add a WHERE clause to the query using whereHas and where
            $query = $query->whereHas($relation['name'], function ($q) use ($request, $relation) {
                $q->where($relation['attribute'], $request->{$relation['attribute']});
            });
        }

        return $query;
    }

    /**
     * Filter the Eloquent query by WHERE relation LIKE statements based on request attributes.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function filterWhereRelationLikeStatements($request, $query, $relations)
    {
        foreach ($relations as $relation) {
            // Skip to the next attribute if the request does not have the attribute for the relation
            if (!$request->has($relation['attribute'])) {
                continue;
            }

            // Add a WHERE LIKE clause to the query using whereHas and where
            $query = $query->whereHas($relation['name'], function ($q) use ($request, $relation) {
                $q->where($relation['attribute'], 'LIKE', '%' . $request->{$relation['attribute']} . '%');
            });
        }

        return $query;
    }

    /**
     * Filter the Eloquent query by WHERE relation equal statements, handling ambiguous situations.
     *
     * Example: while filtering process,
     * both process and its manufacturer relation got id attributes,
     * and we need to filter by manufacturers.id while filtering id can cause ambiguous where clause
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function filterWhereRelationEqualAmbiguousStatements($request, $query, $relations)
    {
        foreach ($relations as $relation) {
            // Skip to the next attribute if the request does not have the attribute for the relation
            if (!$request->has($relation['attribute'])) {
                continue;
            }

            // Add a WHERE clause to the query using whereHas and where
            $query = $query->whereHas($relation['name'], function ($q) use ($request, $relation) {
                $q->where($relation['ambiguousAttribute'], $request->{$relation['attribute']});
            });
        }

        return $query;
    }
}
