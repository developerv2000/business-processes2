<?php

namespace App\Support\Traits;

use Illuminate\Http\Request;

trait MultipleDestroyable
{
    /**
     * Destroy multiple model records.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        // Extract ids from request
        $ids = (array) $request->input('ids');

        // Force delete records
        if ($request->input('force_delete')) {
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
