<?php

/**
 * Custom Helper class
 *
 * @author Bobur Nuridinov <bobnuridinov@gmail.com>
 */

namespace App\Support;

use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class Helper
{
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
}
