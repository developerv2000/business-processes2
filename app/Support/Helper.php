<?php

/**
 * @author Bobur Nuridinov <developerv2000@gmail.com>
 */

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

/**
 * class Helper
 *
 * Main custom Helper class
 */
class Helper
{
    const DEFAULT_MODEL_PAGINATION_LIMITS = [10, 20, 50, 80, 100, 200, 400]; // default pagination limits for models

    /**
     * Used while generating orderBy links
     */
    public static function mergeReversedSortingUrlToRequest($request)
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
     * Truncate a string to the specified length and append '...' if necessary.
     *
     * @param string $value The string to be truncated.
     * @param int $length The desired length of the truncated string.
     * @return string The truncated string with '...' appended if it exceeds the length.
     */
    public static function truncateString(string $value, int $length): string
    {
        if (mb_strlen($value) <= $length) {
            return $value;
        }

        return mb_substr($value, 0, $length) . '...';
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

    /**
     * Get an array of boolean options represented by StdClass objects.
     *
     * @return array
     */
    public static function getBooleanOptionsArray()
    {
        return [
            (object) ['caption' => trans('Yes'), 'value' => 1],
            (object) ['caption' => trans('No'), 'value' => 0],
        ];
    }

    /**
     * Convert a numeric price into a formatted string representation.
     *
     * @param float|int $price The numeric price to format.
     * @return string The formatted price string.
     */
    public static function formatPrice($price)
    {
        return number_format((int)$price, 0, ',', ' ');
    }

    /**
     * Collect all calendar months
     *
     * @return \Illuminate\Support\Collection
     */
    public static function collectCalendarMonths()
    {
        return collect([
            collect(['name' => 'January', 'number' => 1]),
            collect(['name' => 'February', 'number' => 2]),
            collect(['name' => 'March', 'number' => 3]),
            collect(['name' => 'April', 'number' => 4]),
            collect(['name' => 'May', 'number' => 5]),
            collect(['name' => 'June', 'number' => 6]),
            collect(['name' => 'July', 'number' => 7]),
            collect(['name' => 'August', 'number' => 8]),
            collect(['name' => 'September', 'number' => 9]),
            collect(['name' => 'October', 'number' => 10]),
            collect(['name' => 'November', 'number' => 11]),
            collect(['name' => 'December', 'number' => 12]),
        ]);
    }

    /**
     * Add the full namespace to a given model name.
     *
     * @param string $model The model name without the namespace.
     * @return string The fully-qualified class name with namespace.
     */
    public static function addFullNamespaceToModel($model)
    {
        // Define the base namespace for the models
        $baseNamespace = 'App\Models\\';

        $fullClassName = $baseNamespace . $model;

        return $fullClassName;
    }

    /**
     * Sanitize a filename by removing unexpected symbols.
     *
     * @param string $filename
     * @return string
     */
    public static function sanitizeFilename($filename)
    {
        // Define a pattern for allowed characters
        $pattern = '/[^a-zA-Z0-9\-\_\.\s]/';

        // Replace all characters that do not match the allowed pattern
        $sanitized = preg_replace($pattern, '', $filename);

        // Return the sanitized filename
        return $sanitized;
    }

    /**
     * Helper function to format dosage & pack of records
     * as a signle format
     */
    public static function formatSpecificString($value)
    {
        // Add spaces before and after '*', '+', '%' and '/' symbols
        $value = preg_replace('/([+%\/\*])/', ' $1 ', $value);
        // Replace consecutive whitespaces with a single space
        $value = preg_replace('/\s+/', ' ', $value);
        // Separate letters from numbers
        $value = preg_replace('/(\d+)([a-zA-Z]+)/', '$1 $2', $value);
        $value = preg_replace('/([a-zA-Z]+)(\d+)/', '$1 $2', $value);
        // Remove non-English characters
        $value = preg_replace('/[^a-zA-Z0-9\s!@#$%^&*()_+\-=\[\]{};\'":\\|,.<>\/?]/', '', $value);
        // Remove inner whitespaces
        $value = preg_replace('/\s+(?=\S)/', ' ', $value);
        // Replace symbols ',' with '.'
        $value = str_replace(',', '.', $value);
        // Convert the entire string to uppercase
        $value = strtoupper($value);

        return $value;
    }

    /**
     * Merge URL query parameters into the given request.
     *
     * This function takes a URL, extracts its query parameters,
     * and merges them into the provided request object.
     *
     * @param \Illuminate\Http\Request $request The request object to merge parameters into.
     * @param string $url The URL from which to extract query parameters.
     * @return void
     */
    public static function mergeUrlParamsToRequest($request, $url)
    {
        // Extract the query string from the given URL
        $queryString = parse_url($url, PHP_URL_QUERY);

        // Parse the query string into an associative array
        parse_str($queryString, $queryParams);

        // Merge the parsed query parameters into the request object
        $request->merge($queryParams);
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
     * Add WHERE IN clauses to the query based on the specified attributes in the request.
     *
     * @param \Illuminate\Http\Request $request   The current request instance.
     * @param \Illuminate\Database\Eloquent\Builder $query    The query builder instance.
     * @param array $attributes  The attributes to check in the request and apply to the query.
     * @return \Illuminate\Database\Eloquent\Builder The modified query builder instance.
     */
    public static function filterQueryWhereInStatements($request, $query, $attributes)
    {
        // Iterate through each attribute provided
        foreach ($attributes as $attribute) {
            // Skip to the next attribute if the current attribute does not exist in the request
            if (!$request->has($attribute)) {
                continue;
            }

            // Add a WHERE IN clause to the query using the attribute and its values from the request
            $query = $query->whereIn($attribute, $request->input($attribute));
        }

        // Return the modified query builder instance
        return $query;
    }

    /**
     * Add WHERE NOT IN clauses to the query based on the specified attributes in the request.
     *
     * @param \Illuminate\Http\Request $request   The current request instance.
     * @param \Illuminate\Database\Eloquent\Builder $query    The query builder instance.
     * @param array $attributes  The attributes to check in the request and apply to the query.
     * @return \Illuminate\Database\Eloquent\Builder The modified query builder instance.
     */
    public static function filterQueryWhereNotInStatements($request, $query, $attributes)
    {
        // Iterate through each attribute provided
        foreach ($attributes as $attribute) {
            // Skip to the next attribute if the current attribute does not exist in the request
            if (!$request->has($attribute['inputName'])) {
                continue;
            }

            // Add a WHERE IN clause to the query using the attribute and its values from the request
            $query = $query->whereNotIn($attribute['attributeName'], $request->input($attribute['inputName']));
        }

        // Return the modified query builder instance
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
     * Add WHERE IN clauses to the query based on the specified relations and their attributes in the request.
     *
     * @param \Illuminate\Http\Request $request   The current request instance.
     * @param \Illuminate\Database\Eloquent\Builder $query    The query builder instance.
     * @param array $relations  The relations and their attributes to check in the request and apply to the query.
     * @return \Illuminate\Database\Eloquent\Builder The modified query builder instance.
     */
    public static function filterWhereRelationInStatements($request, $query, $relations)
    {
        // Iterate through each relation provided
        foreach ($relations as $relation) {
            // Skip to the next relation if the request does not have the attribute for the current relation
            if (!$request->has($relation['attribute'])) {
                continue;
            }

            // Add a WHERE clause to the query using whereHas and whereIn for the relation
            $query = $query->whereHas($relation['name'], function ($q) use ($request, $relation) {
                $q->whereIn($relation['attribute'], $request->input($relation['attribute']));
            });
        }

        // Return the modified query builder instance
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
