<?php

namespace App\Support\Traits;

use Illuminate\Http\Request;

/**
 * Trait DestroysModelRecords
 *
 * This trait provides functionality to destroy model records, either by soft deleting or force deleting.
 *
 * @package App\Support\Traits
 */
trait DestroysModelRecords
{
    /**
     * Destroy model records based on the request parameters.
     *
     * If the 'force_delete' parameter is provided and the user is an admin or moderator,
     * the records will be force deleted. Otherwise, they will be soft deleted.
     *
     * @param Request $request The request object.
     * @return \Illuminate\Http\RedirectResponse Redirect back to the previous page.
     */
    public function destroy(Request $request)
    {
        // Extract id or ids from request as array to delete through loop
        $ids = (array) ($request->input('id') ?: $request->input('ids'));

        // Only admins and moderators can force delete
        if ($request->input('force_delete') && request()->user()->isAdminOrModerator()) {
            foreach ($ids as $id) {
                // Check if model exists before force deleting
                $model = $this->model::withTrashed()->find($id);
                if ($model) {
                    $model->forceDelete();
                }
            }
        } else {
            // Soft delete or trash records
            foreach ($ids as $id) {
                // Check if model exists before soft deleting
                $model = $this->model::find($id);
                if ($model) {
                    $model->delete();
                }
            }
        }

        return redirect()->back();
    }
}
