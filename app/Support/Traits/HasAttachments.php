<?php

namespace App\Support\Traits;

use App\Models\Attachment;
use App\Support\Helper;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasAttachments
{
    /**
     * Get all of the model's attachments.
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Boot the trait and add model events.
     */
    public static function bootHasAttachments()
    {
        static::forceDeleting(function ($model) {
            $model->deleteAttachmentsFolder();
            $model->attachments()->delete(); // Delete attachments quietly
        });
    }

    /**
     * Add an attachment to the model.
     *
     * @param array $attributes
     * @return \App\Models\Attachment
     */
    public function addAttachment(array $attributes)
    {
        return $this->attachments()->create($attributes);
    }

    /**
     * Remove an attachment from the model.
     *
     * @param int $attachmentId
     * @return bool|null
     */
    public function removeAttachment(int $attachmentId)
    {
        return $this->attachments()->find($attachmentId)?->delete();
    }

    /**
     * Store attachments from the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function storeAttachments($request)
    {
        $attachments = $request->file('attachments');

        if ($attachments) {
            foreach ($attachments as $attachment) {
                $path = "attachments/" . class_basename($this) . "/{$this->id}/";
                $fileName = $attachment->getClientOriginalName();
                $fileName = Helper::escapeDuplicateFilename($fileName, public_path($path));

                // Save attachment details in the database
                $this->addAttachment([
                    'file_path' => "attachments/" . class_basename($this) . "/{$this->id}/" . $fileName,
                    'file_name' => $fileName,
                    'file_type' => $attachment->getClientMimeType(),
                    'file_size' => $attachment->getSize(),
                ]);

                $attachment->move(public_path($path), $fileName);
            }
        }
    }

    /**
     * Delete the attachments folder with all files inside.
     *
     * @return void
     */
    public function deleteAttachmentsFolder()
    {
        $path = public_path("attachments/" . class_basename($this) . "/{$this->id}/");

        // Check if the directory exists
        if (is_dir($path)) {
            $files = glob($path . '/*'); // Get all files and folders

            // Recursively delete files and folders
            foreach ($files as $file) {
                is_dir($file) ? rmdir($file) : unlink($file);
            }

            rmdir($path);
        }
    }
}
